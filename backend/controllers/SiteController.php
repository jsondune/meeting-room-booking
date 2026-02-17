<?php
/**
 * SiteController - Backend site controller for dashboard and authentication
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\User;
use common\models\MeetingRoom;
use common\models\Booking;
use common\models\Equipment;
use common\models\LoginHistory;
use common\models\AuditLog;
use common\models\SystemSetting;
use backend\models\LoginForm;

/**
 * SiteController handles authentication and dashboard
 */
class SiteController extends Controller
{
    /**
     * @var string Default layout
     */
    public $layout = 'main';

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
                        'actions' => ['login', 'error', 'captcha', 'oauth', 'oauth-callback', 'forgot-password', 'reset-password', 'maintenance'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'profile', 'change-password', 'two-factor', 'verify-two-factor', 'notifications'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['dashboard', 'system-settings', 'clear-cache', 'audit-log'],
                        'allow' => true,
                        'roles' => ['admin', 'superadmin', 'staff', 'approver'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                    'clear-cache' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'layout' => 'main-login',
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Dashboard - main admin page
     * @return string
     */
    public function actionIndex()
    {
        return $this->actionDashboard();
    }

    /**
     * Dashboard with statistics and charts
     * @return string
     */
    public function actionDashboard()
    {
        // Get statistics
        $stats = [
            'totalRooms' => MeetingRoom::find()->where(['status' => MeetingRoom::STATUS_ACTIVE])->count(),
            'totalUsers' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
            'todayBookings' => Booking::find()
                ->where(['booking_date' => date('Y-m-d')])
                ->andWhere(['in', 'status', ['pending', 'approved']])
                ->count(),
            'pendingBookings' => Booking::find()->where(['status' => 'pending'])->count(),
            'totalEquipment' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE])->count(),
            'monthlyBookings' => $this->getMonthlyBookings(),
            'roomUsage' => $this->getRoomUsageStats(),
            'recentBookings' => Booking::find()
                ->with(['room', 'user'])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(10)
                ->all(),
            'recentActivity' => AuditLog::find()
                ->orderBy(['created_at' => SORT_DESC])
                ->limit(10)
                ->all(),
            'upcomingBookings' => Booking::find()
                ->with(['room', 'user'])
                ->where(['>=', 'booking_date', date('Y-m-d')])
                ->andWhere(['in', 'status', ['approved']])
                ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
                ->limit(5)
                ->all(),
        ];

        // Get today's detailed schedule
        $todaySchedule = Booking::find()
            ->with(['room', 'user'])
            ->where(['booking_date' => date('Y-m-d')])
            ->andWhere(['in', 'status', ['pending', 'approved', 'completed']])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();

        // Get pending approval bookings
        $pendingApproval = Booking::find()
            ->with(['room', 'user'])
            ->where(['status' => 'pending'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        return $this->render('dashboard', [
            'stats' => $stats,
            'todaySchedule' => $todaySchedule,
            'pendingApproval' => $pendingApproval,
        ]);
    }

    /**
     * Get monthly bookings for chart
     * @return array
     */
    protected function getMonthlyBookings()
    {
        $thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                           'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            // Format as Thai: ม.ค.69
            $monthNum = (int)date('n', strtotime("-$i months"));
            $yearBE = (date('Y', strtotime("-$i months")) + 543) % 100;
            $months[] = $thaiMonthsShort[$monthNum] . $yearBE;
            
            $count = Booking::find()
                ->where(['like', 'booking_date', $date])
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->count();
            $data[] = (int)$count;
        }

        return [
            'labels' => $months,
            'data' => $data,
        ];
    }

    /**
     * Get room usage statistics
     * @return array
     */
    protected function getRoomUsageStats()
    {
        $rooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->orderBy(['name_th' => SORT_ASC])
            ->limit(10)
            ->all();

        $labels = [];
        $data = [];

        foreach ($rooms as $room) {
            $labels[] = $room->name_th;
            $count = Booking::find()
                ->where(['room_id' => $room->id])
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->andWhere(['>=', 'booking_date', date('Y-m-01')])
                ->count();
            $data[] = (int)$count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Login action
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // Log successful login
            LoginHistory::logAttempt(
                Yii::$app->user->id,
                $model->username,
                'password',
                'success'
            );

            // Check if 2FA is required
            $user = Yii::$app->user->identity;
            if ($user->two_factor_enabled) {
                Yii::$app->session->set('2fa_required', true);
                Yii::$app->session->set('2fa_user_id', $user->id);
                return $this->redirect(['verify-two-factor']);
            }

            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        // Log logout
        if (!Yii::$app->user->isGuest) {
            AuditLog::log('logout', User::class, Yii::$app->user->id);
        }

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * User profile action
     * @return string|\yii\web\Response
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;

        if ($user->load(Yii::$app->request->post()) && $user->save()) {
            Yii::$app->session->setFlash('success', 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว');
            return $this->refresh();
        }

        return $this->render('profile', [
            'user' => $user,
        ]);
    }

    /**
     * Change password action
     * @return string|\yii\web\Response
     */
    public function actionChangePassword()
    {
        $model = new \backend\models\ChangePasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->changePassword()) {
            Yii::$app->session->setFlash('success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
            return $this->redirect(['profile']);
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * Two-factor authentication setup
     * @return string|\yii\web\Response
     */
    public function actionTwoFactor()
    {
        $user = Yii::$app->user->identity;

        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action');
            
            if ($action === 'enable') {
                $code = Yii::$app->request->post('code');
                if ($user->enableTwoFactor($code)) {
                    $backupCodes = $user->generateBackupCodes();
                    Yii::$app->session->setFlash('success', 'เปิดใช้งานการยืนยันตัวตนสองขั้นตอนเรียบร้อยแล้ว');
                    return $this->render('two-factor-backup-codes', [
                        'backupCodes' => $backupCodes,
                    ]);
                } else {
                    Yii::$app->session->setFlash('error', 'รหัส OTP ไม่ถูกต้อง');
                }
            } elseif ($action === 'disable') {
                $user->disableTwoFactor();
                Yii::$app->session->setFlash('success', 'ปิดใช้งานการยืนยันตัวตนสองขั้นตอนเรียบร้อยแล้ว');
                return $this->redirect(['profile']);
            } elseif ($action === 'generate-secret') {
                $user->generateTotpSecret();
            }
        }

        // Generate QR code URL for TOTP
        $qrCodeUrl = null;
        if (!$user->two_factor_enabled && $user->two_factor_secret) {
            $qrCodeUrl = $this->generateTotpQrUrl($user);
        }

        return $this->render('two-factor', [
            'user' => $user,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Generate TOTP QR code URL
     * @param User $user
     * @return string
     */
    protected function generateTotpQrUrl($user)
    {
        $issuer = urlencode(Yii::$app->name);
        $account = urlencode($user->email);
        $secret = $user->two_factor_secret;
        
        $otpauthUrl = "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}";
        
        return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauthUrl);
    }

    /**
     * Verify two-factor authentication
     * @return string|\yii\web\Response
     */
    public function actionVerifyTwoFactor()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->session->get('2fa_required')) {
            return $this->goHome();
        }

        $userId = Yii::$app->session->get('2fa_user_id');
        $user = User::findOne($userId);

        if (!$user) {
            Yii::$app->session->remove('2fa_required');
            Yii::$app->session->remove('2fa_user_id');
            return $this->redirect(['login']);
        }

        if (Yii::$app->request->isPost) {
            $code = Yii::$app->request->post('code');
            
            // Try TOTP code first
            $google2fa = new \PragmaRX\Google2FA\Google2FA();
            if ($google2fa->verifyKey($user->two_factor_secret, $code)) {
                Yii::$app->session->remove('2fa_required');
                Yii::$app->session->remove('2fa_user_id');
                Yii::$app->user->login($user);
                return $this->goHome();
            }
            
            // Try backup code
            if ($user->verifyBackupCode($code)) {
                Yii::$app->session->remove('2fa_required');
                Yii::$app->session->remove('2fa_user_id');
                Yii::$app->user->login($user);
                Yii::$app->session->setFlash('warning', 'คุณใช้รหัสสำรอง โปรดสร้างรหัสสำรองใหม่');
                return $this->goHome();
            }
            
            Yii::$app->session->setFlash('error', 'รหัส OTP ไม่ถูกต้อง');
        }

        return $this->render('verify-two-factor', [
            'user' => $user,
        ]);
    }

    /**
     * OAuth login redirect
     * @param string $provider OAuth provider (azure, google, thaid, facebook)
     * @return \yii\web\Response
     */
    public function actionOauth($provider)
    {
        $providers = [
            'azure' => 'Azure AD',
            'google' => 'Google',
            'thaid' => 'ThaID',
            'facebook' => 'Facebook',
        ];

        if (!isset($providers[$provider])) {
            throw new \yii\web\BadRequestHttpException('Invalid OAuth provider');
        }

        // Check if provider is enabled
        if (!SystemSetting::getValue("oauth_{$provider}_enabled", false)) {
            Yii::$app->session->setFlash('error', "การเข้าสู่ระบบด้วย {$providers[$provider]} ไม่ได้เปิดใช้งาน");
            return $this->redirect(['login']);
        }

        // Redirect to OAuth authorization URL
        $authUrl = $this->getOAuthAuthorizationUrl($provider);
        return $this->redirect($authUrl);
    }

    /**
     * OAuth callback handler
     * @param string $provider OAuth provider
     * @return \yii\web\Response
     */
    public function actionOauthCallback($provider)
    {
        $code = Yii::$app->request->get('code');
        $error = Yii::$app->request->get('error');

        if ($error) {
            Yii::$app->session->setFlash('error', 'การเข้าสู่ระบบถูกยกเลิก');
            return $this->redirect(['login']);
        }

        try {
            $userInfo = $this->getOAuthUserInfo($provider, $code);
            $user = $this->findOrCreateOAuthUser($provider, $userInfo);
            
            if ($user) {
                Yii::$app->user->login($user);
                LoginHistory::logAttempt($user->id, $user->username, $provider, 'success');
                return $this->goHome();
            }
        } catch (\Exception $e) {
            Yii::error('OAuth error: ' . $e->getMessage());
            LoginHistory::logAttempt(null, 'oauth_' . $provider, $provider, 'failed', $e->getMessage());
        }

        Yii::$app->session->setFlash('error', 'ไม่สามารถเข้าสู่ระบบได้ โปรดลองใหม่อีกครั้ง');
        return $this->redirect(['login']);
    }

    /**
     * Get OAuth authorization URL
     * @param string $provider
     * @return string
     */
    protected function getOAuthAuthorizationUrl($provider)
    {
        $clientId = SystemSetting::getValue("oauth_{$provider}_client_id");
        $redirectUri = urlencode(Yii::$app->urlManager->createAbsoluteUrl(['site/oauth-callback', 'provider' => $provider]));
        $state = Yii::$app->security->generateRandomString(32);
        Yii::$app->session->set('oauth_state', $state);

        switch ($provider) {
            case 'azure':
                $tenant = SystemSetting::getValue('oauth_azure_tenant', 'common');
                return "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/authorize?" .
                    "client_id={$clientId}&response_type=code&redirect_uri={$redirectUri}" .
                    "&scope=openid%20profile%20email&state={$state}";
                    
            case 'google':
                return "https://accounts.google.com/o/oauth2/v2/auth?" .
                    "client_id={$clientId}&response_type=code&redirect_uri={$redirectUri}" .
                    "&scope=openid%20profile%20email&state={$state}";
                    
            case 'thaid':
                return "https://imauth.bora.dopa.go.th/api/v2/oauth2/auth/?" .
                    "client_id={$clientId}&response_type=code&redirect_uri={$redirectUri}" .
                    "&scope=openid&state={$state}";
                    
            case 'facebook':
                return "https://www.facebook.com/v18.0/dialog/oauth?" .
                    "client_id={$clientId}&response_type=code&redirect_uri={$redirectUri}" .
                    "&scope=email,public_profile&state={$state}";
                    
            default:
                throw new \yii\base\InvalidArgumentException('Invalid OAuth provider');
        }
    }

    /**
     * Get OAuth user info from provider
     * @param string $provider
     * @param string $code
     * @return array
     */
    protected function getOAuthUserInfo($provider, $code)
    {
        // This is a simplified implementation
        // In production, use proper OAuth2 client library
        
        $clientId = SystemSetting::getValue("oauth_{$provider}_client_id");
        $clientSecret = SystemSetting::getValue("oauth_{$provider}_client_secret");
        $redirectUri = Yii::$app->urlManager->createAbsoluteUrl(['site/oauth-callback', 'provider' => $provider]);

        // Exchange code for token and get user info
        // Implementation depends on provider
        
        return [
            'id' => '',
            'email' => '',
            'name' => '',
        ];
    }

    /**
     * Find or create user from OAuth info
     * @param string $provider
     * @param array $userInfo
     * @return User|null
     */
    protected function findOrCreateOAuthUser($provider, $userInfo)
    {
        $field = "{$provider}_id";
        
        // Find existing user by OAuth ID
        $user = User::findOne([$field => $userInfo['id']]);
        
        if (!$user && isset($userInfo['email'])) {
            // Find by email
            $user = User::findOne(['email' => $userInfo['email']]);
            
            if ($user) {
                // Link OAuth account
                $user->$field = $userInfo['id'];
                $user->save(false, [$field]);
            } else {
                // Create new user (if auto-registration is enabled)
                if (SystemSetting::getValue('oauth_auto_register', false)) {
                    $user = new User();
                    $user->$field = $userInfo['id'];
                    $user->email = $userInfo['email'];
                    $user->username = $this->generateUniqueUsername($userInfo['email']);
                    $user->full_name = $userInfo['name'] ?? '';
                    $user->status = User::STATUS_ACTIVE;
                    $user->role = 'user';
                    $user->setPassword(Yii::$app->security->generateRandomString(16));
                    $user->generateAuthKey();
                    $user->save(false);
                }
            }
        }
        
        if ($user && $user->status !== User::STATUS_ACTIVE) {
            return null;
        }
        
        return $user;
    }

    /**
     * Generate unique username from email
     * @param string $email
     * @return string
     */
    protected function generateUniqueUsername($email)
    {
        $base = explode('@', $email)[0];
        $username = $base;
        $counter = 1;
        
        while (User::findOne(['username' => $username])) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * System settings management
     * @return string|\yii\web\Response
     */
    public function actionSystemSettings()
    {
        $categories = [
            'general' => 'ตั้งค่าทั่วไป',
            'security' => 'ความปลอดภัย',
            'booking' => 'การจองห้องประชุม',
            'notification' => 'การแจ้งเตือน',
            'oauth' => 'OAuth / SSO',
        ];

        $settings = SystemSetting::find()
            ->orderBy(['category' => SORT_ASC, 'setting_key' => SORT_ASC])
            ->all();

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('settings', []);
            
            foreach ($data as $key => $value) {
                SystemSetting::setValue($key, $value);
            }
            
            AuditLog::log('update', SystemSetting::class, null, [], $data, 'Updated system settings');
            Yii::$app->session->setFlash('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
            return $this->refresh();
        }

        return $this->render('system-settings', [
            'settings' => $settings,
            'categories' => $categories,
        ]);
    }

    /**
     * Clear cache action
     * @return \yii\web\Response
     */
    public function actionClearCache()
    {
        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
        
        AuditLog::log('clear_cache', null, null, [], [], 'Cleared application cache');
        Yii::$app->session->setFlash('success', 'ล้างแคชเรียบร้อยแล้ว');
        
        return $this->redirect(['dashboard']);
    }

    /**
     * View audit log
     * @return string
     */
    public function actionAuditLog()
    {
        $query = AuditLog::find()->orderBy(['created_at' => SORT_DESC]);

        // Filters
        $action = Yii::$app->request->get('action');
        $userId = Yii::$app->request->get('user_id');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');

        if ($action) {
            $query->andWhere(['action' => $action]);
        }
        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom . ' 00:00:00']);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('audit-log', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * User notifications
     * @return string
     */
    public function actionNotifications()
    {
        $query = \common\models\Notification::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('_notifications', [
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('notifications', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Maintenance mode page
     * @return string
     */
    public function actionMaintenance()
    {
        $this->layout = 'main-login';
        return $this->render('maintenance');
    }

    /**
     * Forgot password action
     * @return string|\yii\web\Response
     */
    public function actionForgotPassword()
    {
        $this->layout = 'main-login';
        
        $model = new \backend\models\ForgotPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'โปรดตรวจสอบอีเมลของคุณสำหรับคำแนะนำในการรีเซ็ตรหัสผ่าน');
            return $this->redirect(['login']);
        }

        return $this->render('forgot-password', [
            'model' => $model,
        ]);
    }

    /**
     * Reset password action
     * @param string $token Password reset token
     * @return string|\yii\web\Response
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';

        try {
            $model = new \backend\models\ResetPasswordForm($token);
        } catch (\yii\base\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ');
            return $this->redirect(['login']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว');
            return $this->redirect(['login']);
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }
}
