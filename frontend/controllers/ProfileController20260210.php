<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Booking;
use common\models\Department;
use common\models\Notification;

/**
 * ProfileController - User profile and account management
 */
class ProfileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete-account' => ['post'],
                    'update-password' => ['post'],
                    'update-avatar' => ['post'],
                    'disconnect-oauth' => ['post'],
                    'update-notifications' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Display user profile
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $today = date('Y-m-d');

        // Get booking statistics
        $stats = [
            'total' => Booking::find()->where(['user_id' => $user->id])->count(),
            'completed' => Booking::find()->where([
                'user_id' => $user->id, 
                'status' => Booking::STATUS_COMPLETED
            ])->count(),
            'cancelled' => Booking::find()->where([
                'user_id' => $user->id, 
                'status' => Booking::STATUS_CANCELLED
            ])->count(),
            'upcoming' => Booking::find()->where(['user_id' => $user->id])
                ->andWhere(['>=', 'booking_date', $today])
                ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
                ->count(),
        ];

        // Get recent bookings
        $recentBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // Get monthly booking chart data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyData[] = [
                'month' => date('M', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        // Get favorite rooms (most booked)
        $favoriteRooms = Booking::find()
            ->select(['room_id', 'COUNT(*) as booking_count'])
            ->where(['user_id' => $user->id])
            ->groupBy('room_id')
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(3)
            ->with('room')
            ->asArray()
            ->all();

        return $this->render('index', [
            'user' => $user,
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'monthlyData' => $monthlyData,
            'favoriteRooms' => $favoriteRooms,
        ]);
    }

    /**
     * Edit user profile
     *
     * @return string|Response
     */
    public function actionEdit()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        if ($model->load(Yii::$app->request->post())) {
            // Handle avatar upload
            $avatarFile = UploadedFile::getInstance($model, 'avatarFile');
            if ($avatarFile) {
                // Validate file
                if ($avatarFile->size > 5 * 1024 * 1024) {
                    Yii::$app->session->setFlash('error', 'ไฟล์รูปภาพมีขนาดใหญ่เกินไป (สูงสุด 5MB)');
                    return $this->render('edit', [
                        'model' => $model,
                        'departments' => Department::getDropdownList(),
                    ]);
                }
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array(strtolower($avatarFile->extension), $allowedExtensions)) {
                    Yii::$app->session->setFlash('error', 'รองรับเฉพาะไฟล์รูปภาพ (JPG, PNG, GIF, WEBP)');
                    return $this->render('edit', [
                        'model' => $model,
                        'departments' => Department::getDropdownList(),
                    ]);
                }
                
                $avatarPath = $model->uploadAvatar($avatarFile);
                if ($avatarPath) {
                    $model->avatar = $avatarPath;
                } else {
                    Yii::$app->session->setFlash('warning', 'ไม่สามารถอัพโหลดรูปภาพได้ โปรดตรวจสอบสิทธิ์โฟลเดอร์');
                }
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'บันทึกข้อมูลโปรไฟล์สำเร็จ');
                return $this->redirect(['index']);
            } else {
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . implode(', ', $model->getFirstErrors()));
            }
        }

        // Get departments
        $departments = Department::getDropdownList();

        return $this->render('edit', [
            'model' => $model,
            'departments' => $departments,
        ]);
    }

    /**
     * Update password
     *
     * @return Response
     */
    public function actionUpdatePassword()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $currentPassword = Yii::$app->request->post('current_password');
        $newPassword = Yii::$app->request->post('new_password');
        $confirmPassword = Yii::$app->request->post('confirm_password');

        // Validate current password
        if (!$model->validatePassword($currentPassword)) {
            return [
                'success' => false,
                'message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง',
            ];
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            ];
        }

        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน',
            ];
        }

        // Validate password strength
        if (!preg_match('/[A-Z]/', $newPassword) ||
            !preg_match('/[a-z]/', $newPassword) ||
            !preg_match('/[0-9]/', $newPassword) ||
            !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            return [
                'success' => false,
                'message' => 'รหัสผ่านต้องประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษ',
            ];
        }

        // Update password
        $model->setPassword($newPassword);
        $model->generateAuthKey();

        if ($model->save(false)) {
            // Log password change
            $model->logActivity('password_change', 'User changed password');

            return [
                'success' => true,
                'message' => 'เปลี่ยนรหัสผ่านสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน',
        ];
    }

    /**
     * Update avatar via AJAX
     *
     * @return Response
     */
    public function actionUpdateAvatar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $avatarFile = UploadedFile::getInstanceByName('avatar');

        if (!$avatarFile) {
            return [
                'success' => false,
                'message' => 'โปรดเลือกไฟล์รูปภาพ',
            ];
        }

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($avatarFile->type, $allowedTypes)) {
            return [
                'success' => false,
                'message' => 'รองรับไฟล์ประเภท JPG, PNG, GIF, WEBP เท่านั้น',
            ];
        }

        if ($avatarFile->size > 5 * 1024 * 1024) {
            return [
                'success' => false,
                'message' => 'ขนาดไฟล์ต้องไม่เกิน 5MB',
            ];
        }

        $avatarPath = $model->uploadAvatar($avatarFile);
        if ($avatarPath) {
            $model->avatar = $avatarPath;
            if ($model->save(false)) {
                return [
                    'success' => true,
                    'message' => 'อัปโหลดรูปโปรไฟล์สำเร็จ',
                    'avatar_url' => $model->getAvatarUrl(),
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ',
        ];
    }

    /**
     * Update notification preferences
     *
     * @return Response
     */
    public function actionUpdateNotifications()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $settings = Yii::$app->request->post('notifications', []);

        $notificationSettings = [
            'email_booking_confirmation' => !empty($settings['email_booking_confirmation']),
            'email_booking_reminder' => !empty($settings['email_booking_reminder']),
            'email_booking_cancelled' => !empty($settings['email_booking_cancelled']),
            'email_booking_approved' => !empty($settings['email_booking_approved']),
            'email_booking_rejected' => !empty($settings['email_booking_rejected']),
            'push_enabled' => !empty($settings['push_enabled']),
        ];

        $model->notification_settings = json_encode($notificationSettings);

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'บันทึกการตั้งค่าการแจ้งเตือนสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการบันทึกการตั้งค่า',
        ];
    }

    /**
     * Get notifications
     *
     * @return string
     */
    public function actionNotifications()
    {
        $user = Yii::$app->user->identity;

        $notifications = Notification::find()
            ->where(['user_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(50)
            ->all();

        // Mark as read
        Notification::updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => $user->id, 'is_read' => 0]
        );

        return $this->render('notifications', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Manage OAuth connections
     *
     * @return string
     */
    public function actionConnections()
    {
        $user = Yii::$app->user->identity;

        // Get existing OAuth connections
        $oauthConnections = \common\models\UserOauth::find()
            ->where(['user_id' => $user->id])
            ->indexBy('provider')
            ->all();

        // Check available providers
        $providers = [];
        
        if (getenv('GOOGLE_CLIENT_ID')) {
            $providers['google'] = [
                'name' => 'Google',
                'icon' => 'google',
                'description' => 'เชื่อมต่อบัญชี Google เพื่อเข้าสู่ระบบและซิงค์ปฏิทิน',
                'features' => ['เข้าสู่ระบบด้วย Google', 'ซิงค์กับ Google Calendar'],
            ];
        }
        
        if (getenv('MICROSOFT_CLIENT_ID')) {
            $providers['microsoft'] = [
                'name' => 'Microsoft',
                'icon' => 'microsoft',
                'description' => 'เชื่อมต่อบัญชี Microsoft เพื่อเข้าสู่ระบบและซิงค์ปฏิทิน',
                'features' => ['เข้าสู่ระบบด้วย Microsoft', 'ซิงค์กับ Outlook Calendar'],
            ];
        }
        
        if (getenv('THAID_CLIENT_ID')) {
            $providers['thaid'] = [
                'name' => 'ThaiD',
                'icon' => 'thaid',
                'description' => 'เชื่อมต่อบัญชี ThaiD เพื่อยืนยันตัวตนด้วยระบบภาครัฐ',
                'features' => ['ยืนยันตัวตนด้วย ThaiD', 'เข้าสู่ระบบด้วย ThaiD'],
            ];
        }

        // Check if user has password set
        $hasPassword = !empty($user->password_hash);

        return $this->render('connections', [
            'user' => $user,
            'connections' => $oauthConnections,
            'providers' => $providers,
            'hasPassword' => $hasPassword,
        ]);
    }

    /**
     * Get unread notifications count (AJAX)
     *
     * @return Response
     */
    public function actionUnreadCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;

        $count = Notification::find()
            ->where(['user_id' => $user->id, 'is_read' => 0])
            ->count();

        return [
            'success' => true,
            'count' => (int)$count,
        ];
    }

    /**
     * Mark notification as read
     *
     * @param int $id
     * @return Response
     */
    public function actionMarkRead($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;

        $notification = Notification::findOne(['id' => $id, 'user_id' => $user->id]);

        if (!$notification) {
            return [
                'success' => false,
                'message' => 'ไม่พบการแจ้งเตือน',
            ];
        }

        $notification->is_read = 1;
        $notification->read_at = date('Y-m-d H:i:s');

        if ($notification->save(false)) {
            return [
                'success' => true,
                'message' => 'อ่านการแจ้งเตือนแล้ว',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
        ];
    }

    /**
     * Mark all notifications as read
     *
     * @return Response
     */
    public function actionMarkAllRead()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;

        Notification::updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => $user->id, 'is_read' => 0]
        );

        return [
            'success' => true,
            'message' => 'อ่านการแจ้งเตือนทั้งหมดแล้ว',
        ];
    }

    /**
     * Connect OAuth provider
     *
     * @param string $provider
     * @return Response
     */
    public function actionConnectOauth($provider)
    {
        $allowedProviders = ['google', 'microsoft'];

        if (!in_array($provider, $allowedProviders)) {
            Yii::$app->session->setFlash('error', 'ไม่รองรับผู้ให้บริการนี้');
            return $this->redirect(['edit']);
        }

        // Store return URL
        Yii::$app->session->set('oauth_return_url', Yii::$app->request->referrer);

        // Redirect to OAuth authorization
        return $this->redirect(['/auth/' . $provider]);
    }

    /**
     * Disconnect OAuth provider
     *
     * @return Response
     */
    public function actionDisconnectOauth()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $provider = Yii::$app->request->post('provider');

        // Check if user has password before disconnecting
        if (empty($model->password_hash)) {
            return [
                'success' => false,
                'message' => 'โปรดตั้งรหัสผ่านก่อนยกเลิกการเชื่อมต่อ',
            ];
        }

        switch ($provider) {
            case 'google':
                $model->google_id = null;
                break;
            case 'microsoft':
                $model->azure_id = null;
                break;
            default:
                return [
                    'success' => false,
                    'message' => 'ไม่รองรับผู้ให้บริการนี้',
                ];
        }

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'ยกเลิกการเชื่อมต่อสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการยกเลิกการเชื่อมต่อ',
        ];
    }

    /**
     * Enable 2FA
     *
     * @return string|Response
     */
    public function actionEnable2fa()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        if ($model->two_factor_enabled) {
            Yii::$app->session->setFlash('info', 'การยืนยันตัวตนสองขั้นตอนเปิดใช้งานอยู่แล้ว');
            return $this->redirect(['edit']);
        }

        // Generate secret if not exists
        if (empty($model->two_factor_secret)) {
            $ga = new \PHPGangsta_GoogleAuthenticator();
            $model->two_factor_secret = $ga->createSecret();
            $model->save(false);
        }

        if (Yii::$app->request->isPost) {
            $code = Yii::$app->request->post('code');
            $ga = new \PHPGangsta_GoogleAuthenticator();

            if ($ga->verifyCode($model->two_factor_secret, $code, 2)) {
                $model->two_factor_enabled = 1;
                $model->save(false);

                Yii::$app->session->setFlash('success', 'เปิดใช้งานการยืนยันตัวตนสองขั้นตอนสำเร็จ');
                return $this->redirect(['edit']);
            } else {
                Yii::$app->session->setFlash('error', 'รหัสไม่ถูกต้อง โปรดลองใหม่');
            }
        }

        // Generate QR code URL
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(
            'MeetingRoom:' . $model->email,
            $model->two_factor_secret,
            'Meeting Room Booking'
        );

        return $this->render('enable-2fa', [
            'model' => $model,
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $model->two_factor_secret,
        ]);
    }

    /**
     * Disable 2FA
     *
     * @return Response
     */
    public function actionDisable2fa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $password = Yii::$app->request->post('password');

        if (!$model->validatePassword($password)) {
            return [
                'success' => false,
                'message' => 'รหัสผ่านไม่ถูกต้อง',
            ];
        }

        $model->two_factor_enabled = 0;
        $model->two_factor_secret = null;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'ปิดการใช้งานการยืนยันตัวตนสองขั้นตอนสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
        ];
    }

    /**
     * Security settings
     *
     * @return string
     */
    public function actionSecurity()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        // Get login history
        $loginHistory = $model->getLoginHistory(10);

        // Get active sessions
        $activeSessions = $model->getActiveSessions();

        return $this->render('security', [
            'model' => $model,
            'loginHistory' => $loginHistory,
            'activeSessions' => $activeSessions,
        ]);
    }

    /**
     * Revoke session
     *
     * @return Response
     */
    public function actionRevokeSession()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $sessionId = Yii::$app->request->post('session_id');

        // Don't revoke current session
        if ($sessionId === Yii::$app->session->id) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถยกเลิกเซสชันปัจจุบันได้',
            ];
        }

        $model = User::findOne($user->id);
        if ($model->revokeSession($sessionId)) {
            return [
                'success' => true,
                'message' => 'ยกเลิกเซสชันสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
        ];
    }

    /**
     * Revoke all other sessions
     *
     * @return Response
     */
    public function actionRevokeAllSessions()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        if ($model->revokeAllSessions(Yii::$app->session->id)) {
            return [
                'success' => true,
                'message' => 'ยกเลิกเซสชันทั้งหมดสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
        ];
    }

    /**
     * Export user data
     *
     * @return Response
     */
    public function actionExportData()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        // Get all user data
        $userData = [
            'profile' => [
                'username' => $model->username,
                'email' => $model->email,
                'fullname' => $model->fullname,
                'phone' => $model->phone,
                'department' => $model->department ? $model->department->name_th : null,
                'position' => $model->position,
                'created_at' => $model->created_at,
            ],
            'bookings' => [],
        ];

        // Get all bookings
        $bookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        foreach ($bookings as $booking) {
            $userData['bookings'][] = [
                'booking_code' => $booking->booking_code,
                'room' => $booking->room->name_th,
                'date' => $booking->booking_date,
                'time' => $booking->start_time . ' - ' . $booking->end_time,
                'title' => $booking->title,
                'status' => $booking->getStatusLabel(),
                'created_at' => $booking->created_at,
            ];
        }

        $filename = 'user-data-' . date('Y-m-d') . '.json';
        $content = json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return Yii::$app->response->sendContentAsFile(
            $content,
            $filename,
            ['mimeType' => 'application/json']
        );
    }

    /**
     * Delete user account
     *
     * @return Response
     */
    public function actionDeleteAccount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $confirmation = Yii::$app->request->post('confirmation');

        if ($confirmation !== 'DELETE') {
            return [
                'success' => false,
                'message' => 'โปรดพิมพ์ DELETE เพื่อยืนยัน',
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Cancel all pending/approved bookings
            Booking::updateAll(
                [
                    'status' => Booking::STATUS_CANCELLED,
                    'cancellation_reason' => 'บัญชีผู้ใช้ถูกลบ',
                    'cancelled_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'and',
                    ['user_id' => $user->id],
                    ['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]],
                ]
            );

            // Soft delete user
            $model->status = User::STATUS_DELETED;
            $model->email = 'deleted_' . time() . '_' . $model->email;
            $model->username = 'deleted_' . time() . '_' . $model->username;
            $model->save(false);

            $transaction->commit();

            // Logout
            Yii::$app->user->logout();

            return [
                'success' => true,
                'message' => 'ลบบัญชีสำเร็จ',
                'redirect' => Yii::$app->homeUrl,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Activity log
     *
     * @return string
     */
    public function actionActivityLog()
    {
        $user = Yii::$app->user->identity;

        $activities = \common\models\ActivityLog::find()
            ->where(['user_id' => $user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(100)
            ->all();

        return $this->render('activity-log', [
            'activities' => $activities,
        ]);
    }
}
