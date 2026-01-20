<?php

namespace api\controllers\v1;

use Yii;
use yii\web\UploadedFile;
use common\models\User;
use common\models\Booking;

/**
 * ProfileController handles user profile API operations
 */
class ProfileController extends BaseController
{
    /**
     * Get current user profile
     * GET /api/v1/profile
     *
     * @return array
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;

        // Get booking statistics
        $today = date('Y-m-d');
        $stats = [
            'total_bookings' => Booking::find()->where(['user_id' => $user->id])->count(),
            'completed_bookings' => Booking::find()->where([
                'user_id' => $user->id,
                'status' => Booking::STATUS_COMPLETED
            ])->count(),
            'upcoming_bookings' => Booking::find()->where(['user_id' => $user->id])
                ->andWhere(['>=', 'booking_date', $today])
                ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
                ->count(),
        ];

        return $this->success([
            'user' => $this->formatUserProfile($user),
            'statistics' => $stats,
        ]);
    }

    /**
     * Update user profile
     * PUT /api/v1/profile
     *
     * @return array
     */
    public function actionUpdate()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $request = Yii::$app->request;

        // Update allowed fields
        if ($request->post('fullname')) {
            $model->fullname = $request->post('fullname');
        }
        if ($request->post('phone') !== null) {
            $model->phone = $request->post('phone');
        }
        if ($request->post('department_id')) {
            $model->department_id = $request->post('department_id');
        }
        if ($request->post('position') !== null) {
            $model->position = $request->post('position');
        }

        // Email change requires verification
        $newEmail = $request->post('email');
        if ($newEmail && $newEmail !== $model->email) {
            // Check if email is already taken
            $existing = User::find()
                ->where(['email' => $newEmail])
                ->andWhere(['<>', 'id', $model->id])
                ->exists();

            if ($existing) {
                return $this->error('อีเมลนี้ถูกใช้งานแล้ว', 400);
            }

            // Store new email for verification
            $model->pending_email = $newEmail;
            $model->generateEmailVerificationToken();
            $model->sendEmailChangeVerification();
        }

        if ($errors = $this->validateModel($model)) {
            return $this->error('ข้อมูลไม่ถูกต้อง', 400, $errors);
        }

        if ($model->save()) {
            return $this->success([
                'user' => $this->formatUserProfile($model),
            ], 'อัปเดตโปรไฟล์สำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการอัปเดตโปรไฟล์', 500);
    }

    /**
     * Update avatar
     * POST /api/v1/profile/avatar
     *
     * @return array
     */
    public function actionAvatar()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $avatarFile = UploadedFile::getInstanceByName('avatar');

        if (!$avatarFile) {
            return $this->error('กรุณาเลือกไฟล์รูปภาพ', 400);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($avatarFile->type, $allowedTypes)) {
            return $this->error('รองรับไฟล์ประเภท JPG, PNG, GIF, WEBP เท่านั้น', 400);
        }

        // Validate file size (5MB max)
        if ($avatarFile->size > 5 * 1024 * 1024) {
            return $this->error('ขนาดไฟล์ต้องไม่เกิน 5MB', 400);
        }

        $avatarPath = $model->uploadAvatar($avatarFile);
        if ($avatarPath) {
            $model->avatar = $avatarPath;
            if ($model->save(false, ['avatar'])) {
                return $this->success([
                    'avatar_url' => $model->getAvatarUrl(),
                ], 'อัปโหลดรูปโปรไฟล์สำเร็จ');
            }
        }

        return $this->error('เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ', 500);
    }

    /**
     * Update password
     * PUT /api/v1/profile/password
     *
     * @return array
     */
    public function actionPassword()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $currentPassword = Yii::$app->request->post('current_password');
        $newPassword = Yii::$app->request->post('new_password');
        $confirmPassword = Yii::$app->request->post('confirm_password');

        // Validate current password
        if (!$model->validatePassword($currentPassword)) {
            return $this->error('รหัสผ่านปัจจุบันไม่ถูกต้อง', 400);
        }

        // Validate new password
        if (strlen($newPassword) < 8) {
            return $this->error('รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 8 ตัวอักษร', 400);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->error('รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน', 400);
        }

        // Validate password strength
        if (!preg_match('/[A-Z]/', $newPassword) ||
            !preg_match('/[a-z]/', $newPassword) ||
            !preg_match('/[0-9]/', $newPassword) ||
            !preg_match('/[^A-Za-z0-9]/', $newPassword)) {
            return $this->error('รหัสผ่านต้องประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก ตัวเลข และอักขระพิเศษ', 400);
        }

        // Update password
        $model->setPassword($newPassword);
        $model->generateAuthKey();

        if ($model->save(false)) {
            // Log password change
            $model->logActivity('password_change', 'User changed password via API');

            return $this->success(null, 'เปลี่ยนรหัสผ่านสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน', 500);
    }

    /**
     * Get notification settings
     * GET /api/v1/profile/notifications
     *
     * @return array
     */
    public function actionNotifications()
    {
        $user = Yii::$app->user->identity;

        return $this->success([
            'settings' => [
                'email_booking_confirmation' => (bool)($user->notification_settings['email_booking_confirmation'] ?? true),
                'email_booking_reminder' => (bool)($user->notification_settings['email_booking_reminder'] ?? true),
                'email_booking_cancelled' => (bool)($user->notification_settings['email_booking_cancelled'] ?? true),
                'email_booking_approved' => (bool)($user->notification_settings['email_booking_approved'] ?? true),
                'push_enabled' => (bool)($user->notification_settings['push_enabled'] ?? false),
            ],
        ]);
    }

    /**
     * Update notification settings
     * PUT /api/v1/profile/notifications
     *
     * @return array
     */
    public function actionUpdateNotifications()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        $settings = $model->notification_settings ?? [];

        $request = Yii::$app->request;
        if ($request->post('email_booking_confirmation') !== null) {
            $settings['email_booking_confirmation'] = (bool)$request->post('email_booking_confirmation');
        }
        if ($request->post('email_booking_reminder') !== null) {
            $settings['email_booking_reminder'] = (bool)$request->post('email_booking_reminder');
        }
        if ($request->post('email_booking_cancelled') !== null) {
            $settings['email_booking_cancelled'] = (bool)$request->post('email_booking_cancelled');
        }
        if ($request->post('email_booking_approved') !== null) {
            $settings['email_booking_approved'] = (bool)$request->post('email_booking_approved');
        }
        if ($request->post('push_enabled') !== null) {
            $settings['push_enabled'] = (bool)$request->post('push_enabled');
        }

        $model->notification_settings = $settings;

        if ($model->save(false, ['notification_settings'])) {
            return $this->success([
                'settings' => $settings,
            ], 'อัปเดตการตั้งค่าการแจ้งเตือนสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการอัปเดตการตั้งค่า', 500);
    }

    /**
     * Enable 2FA
     * POST /api/v1/profile/enable-2fa
     *
     * @return array
     */
    public function actionEnable2fa()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);

        if ($model->two_factor_enabled) {
            return $this->error('การยืนยันตัวตนสองขั้นตอนเปิดใช้งานอยู่แล้ว', 400);
        }

        // Generate secret
        $ga = new \PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();

        // Store temporarily
        $model->two_factor_secret = $secret;
        $model->save(false, ['two_factor_secret']);

        // Generate QR code URL
        $qrCodeUrl = $ga->getQRCodeGoogleUrl(
            'MeetingRoom:' . $model->email,
            $secret,
            'Meeting Room Booking'
        );

        return $this->success([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ], 'กรุณาสแกน QR Code และยืนยันรหัส');
    }

    /**
     * Verify and activate 2FA
     * POST /api/v1/profile/verify-2fa
     *
     * @return array
     */
    public function actionVerify2fa()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $code = Yii::$app->request->post('code');

        if (!$model->two_factor_secret) {
            return $this->error('กรุณาเริ่มต้นการตั้งค่า 2FA ก่อน', 400);
        }

        if (empty($code)) {
            return $this->error('กรุณาระบุรหัสยืนยัน', 400);
        }

        $ga = new \PHPGangsta_GoogleAuthenticator();
        if (!$ga->verifyCode($model->two_factor_secret, $code, 2)) {
            return $this->error('รหัสไม่ถูกต้อง', 400);
        }

        $model->two_factor_enabled = 1;
        $model->save(false, ['two_factor_enabled']);

        return $this->success(null, 'เปิดใช้งานการยืนยันตัวตนสองขั้นตอนสำเร็จ');
    }

    /**
     * Disable 2FA
     * POST /api/v1/profile/disable-2fa
     *
     * @return array
     */
    public function actionDisable2fa()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $password = Yii::$app->request->post('password');

        if (!$model->two_factor_enabled) {
            return $this->error('การยืนยันตัวตนสองขั้นตอนไม่ได้เปิดใช้งาน', 400);
        }

        if (!$model->validatePassword($password)) {
            return $this->error('รหัสผ่านไม่ถูกต้อง', 400);
        }

        $model->two_factor_enabled = 0;
        $model->two_factor_secret = null;
        $model->save(false, ['two_factor_enabled', 'two_factor_secret']);

        return $this->success(null, 'ปิดการใช้งานการยืนยันตัวตนสองขั้นตอนสำเร็จ');
    }

    /**
     * Delete account
     * DELETE /api/v1/profile
     *
     * @return array
     */
    public function actionDelete()
    {
        $user = Yii::$app->user->identity;
        $model = User::findOne($user->id);
        $confirmation = Yii::$app->request->post('confirmation');

        if ($confirmation !== 'DELETE') {
            return $this->error('กรุณาพิมพ์ DELETE เพื่อยืนยัน', 400);
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

            return $this->success(null, 'ลบบัญชีสำเร็จ');
        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Format user profile data
     *
     * @param User $user
     * @return array
     */
    protected function formatUserProfile($user)
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'fullname' => $user->fullname,
            'phone' => $user->phone,
            'avatar' => $user->getAvatarUrl(),
            'department' => $user->department ? [
                'id' => $user->department->id,
                'name_th' => $user->department->name_th ?? $user->department->name,
                'name_en' => $user->department->name_en ?? '',
            ] : null,
            'position' => $user->position,
            'role' => $user->role,
            'status' => $user->status,
            'google_connected' => !empty($user->google_id),
            'microsoft_connected' => !empty($user->azure_id),
            'created_at' => $user->created_at,
            'last_login_at' => $user->last_login_at,
        ];
    }
}
