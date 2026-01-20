<?php

namespace api\controllers\v1;

use Yii;
use common\models\User;
use common\models\LoginForm;

/**
 * AuthController handles authentication operations
 */
class AuthController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $authRequired = false;

    /**
     * Login with username/email and password
     * POST /api/v1/auth/login
     *
     * @return array
     */
    public function actionLogin()
    {
        $username = Yii::$app->request->post('username');
        $password = Yii::$app->request->post('password');

        if (empty($username) || empty($password)) {
            return $this->error('โปรดระบุชื่อผู้ใช้และรหัสผ่าน', 400);
        }

        // Find user by username or email
        $user = User::findByUsername($username) ?: User::findByEmail($username);

        if (!$user) {
            return $this->error('ไม่พบบัญชีผู้ใช้', 401);
        }

        if ($user->status !== User::STATUS_ACTIVE) {
            return $this->error('บัญชีผู้ใช้ถูกระงับ', 401);
        }

        if (!$user->validatePassword($password)) {
            // Log failed attempt
            $user->logLoginAttempt(false, Yii::$app->request->userIP);
            return $this->error('รหัสผ่านไม่ถูกต้อง', 401);
        }

        // Check 2FA if enabled
        if ($user->two_factor_enabled) {
            $code = Yii::$app->request->post('two_factor_code');
            if (empty($code)) {
                return [
                    'success' => false,
                    'requires_2fa' => true,
                    'message' => 'โปรดระบุรหัสยืนยันตัวตน',
                ];
            }

            if (!$user->validate2FACode($code)) {
                return $this->error('รหัสยืนยันตัวตนไม่ถูกต้อง', 401);
            }
        }

        // Log successful login
        $user->logLoginAttempt(true, Yii::$app->request->userIP);

        // Generate tokens
        $accessToken = $this->generateToken($user);
        $refreshToken = $this->generateToken($user, true);

        // Update last login
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->last_login_ip = Yii::$app->request->userIP;
        $user->save(false, ['last_login_at', 'last_login_ip']);

        return $this->success([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => Yii::$app->params['jwt']['expire'],
            'user' => $this->formatUser($user),
        ], 'เข้าสู่ระบบสำเร็จ');
    }

    /**
     * Register new user
     * POST /api/v1/auth/register
     *
     * @return array
     */
    public function actionRegister()
    {
        $model = new User();
        $model->scenario = 'register';

        $model->username = Yii::$app->request->post('username');
        $model->email = Yii::$app->request->post('email');
        $model->fullname = Yii::$app->request->post('fullname');
        $model->phone = Yii::$app->request->post('phone');
        $model->department_id = Yii::$app->request->post('department_id');
        $model->position = Yii::$app->request->post('position');

        $password = Yii::$app->request->post('password');
        $passwordConfirm = Yii::$app->request->post('password_confirm');

        // Validate password
        if (empty($password) || strlen($password) < 8) {
            return $this->error('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร', 400);
        }

        if ($password !== $passwordConfirm) {
            return $this->error('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน', 400);
        }

        // Validate password strength
        if (!preg_match('/[A-Z]/', $password) ||
            !preg_match('/[a-z]/', $password) ||
            !preg_match('/[0-9]/', $password)) {
            return $this->error('รหัสผ่านต้องประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข', 400);
        }

        $model->setPassword($password);
        $model->generateAuthKey();
        $model->generateEmailVerificationToken();
        $model->status = User::STATUS_INACTIVE; // Requires email verification

        if ($errors = $this->validateModel($model)) {
            return $this->error('ข้อมูลไม่ถูกต้อง', 400, $errors);
        }

        if ($model->save()) {
            // Send verification email
            $model->sendVerificationEmail();

            return $this->success([
                'user' => $this->formatUser($model),
            ], 'ลงทะเบียนสำเร็จ โปรดตรวจสอบอีเมลเพื่อยืนยันบัญชี');
        }

        return $this->error('เกิดข้อผิดพลาดในการลงทะเบียน', 500);
    }

    /**
     * Refresh access token
     * POST /api/v1/auth/refresh
     *
     * @return array
     */
    public function actionRefresh()
    {
        $refreshToken = Yii::$app->request->post('refresh_token');

        if (empty($refreshToken)) {
            return $this->error('โปรดระบุ refresh token', 400);
        }

        $decoded = $this->validateToken($refreshToken);

        if (!$decoded || $decoded->type !== 'refresh') {
            return $this->error('Refresh token ไม่ถูกต้องหรือหมดอายุ', 401);
        }

        $user = User::findOne($decoded->sub);

        if (!$user || $user->status !== User::STATUS_ACTIVE) {
            return $this->error('บัญชีผู้ใช้ไม่พร้อมใช้งาน', 401);
        }

        // Generate new tokens
        $accessToken = $this->generateToken($user);
        $newRefreshToken = $this->generateToken($user, true);

        return $this->success([
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => Yii::$app->params['jwt']['expire'],
        ], 'Token รีเฟรชสำเร็จ');
    }

    /**
     * Logout (invalidate token)
     * POST /api/v1/auth/logout
     *
     * @return array
     */
    public function actionLogout()
    {
        // In stateless JWT, logout is client-side
        // Optionally add token to blacklist here

        return $this->success(null, 'ออกจากระบบสำเร็จ');
    }

    /**
     * Request password reset
     * POST /api/v1/auth/forgot-password
     *
     * @return array
     */
    public function actionForgotPassword()
    {
        $email = Yii::$app->request->post('email');

        if (empty($email)) {
            return $this->error('โปรดระบุอีเมล', 400);
        }

        $user = User::findByEmail($email);

        if (!$user) {
            // Don't reveal if email exists
            return $this->success(null, 'หากอีเมลนี้ลงทะเบียนอยู่ในระบบ จะได้รับลิงก์รีเซ็ตรหัสผ่าน');
        }

        if ($user->status !== User::STATUS_ACTIVE) {
            return $this->success(null, 'หากอีเมลนี้ลงทะเบียนอยู่ในระบบ จะได้รับลิงก์รีเซ็ตรหัสผ่าน');
        }

        $user->generatePasswordResetToken();
        $user->save(false, ['password_reset_token']);

        // Send password reset email
        $user->sendPasswordResetEmail();

        return $this->success(null, 'หากอีเมลนี้ลงทะเบียนอยู่ในระบบ จะได้รับลิงก์รีเซ็ตรหัสผ่าน');
    }

    /**
     * Reset password with token
     * POST /api/v1/auth/reset-password
     *
     * @return array
     */
    public function actionResetPassword()
    {
        $token = Yii::$app->request->post('token');
        $password = Yii::$app->request->post('password');
        $passwordConfirm = Yii::$app->request->post('password_confirm');

        if (empty($token)) {
            return $this->error('โปรดระบุ token', 400);
        }

        if (empty($password) || strlen($password) < 8) {
            return $this->error('รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร', 400);
        }

        if ($password !== $passwordConfirm) {
            return $this->error('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน', 400);
        }

        $user = User::findByPasswordResetToken($token);

        if (!$user) {
            return $this->error('ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ', 400);
        }

        $user->setPassword($password);
        $user->removePasswordResetToken();
        $user->generateAuthKey();

        if ($user->save(false)) {
            return $this->success(null, 'รีเซ็ตรหัสผ่านสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน', 500);
    }

    /**
     * Verify email
     * GET /api/v1/auth/verify-email
     *
     * @return array
     */
    public function actionVerifyEmail()
    {
        $token = Yii::$app->request->get('token');

        if (empty($token)) {
            return $this->error('โปรดระบุ token', 400);
        }

        $user = User::findByVerificationToken($token);

        if (!$user) {
            return $this->error('ลิงก์ยืนยันอีเมลไม่ถูกต้องหรือหมดอายุ', 400);
        }

        $user->status = User::STATUS_ACTIVE;
        $user->verification_token = null;

        if ($user->save(false)) {
            return $this->success(null, 'ยืนยันอีเมลสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการยืนยันอีเมล', 500);
    }

    /**
     * Format user data for response
     *
     * @param User $user
     * @return array
     */
    protected function formatUser($user)
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
                'name' => $user->department->name_th ?? $user->department->name,
            ] : null,
            'position' => $user->position,
            'role' => $user->role,
            'status' => $user->status,
            'email_verified' => true,
            'two_factor_enabled' => (bool)$user->two_factor_enabled,
            'created_at' => $user->created_at,
        ];
    }
}
