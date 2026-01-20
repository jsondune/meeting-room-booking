<?php
/**
 * LoginForm Model - Backend login form
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use common\models\LoginHistory;

/**
 * LoginForm is the model behind the login form in backend.
 */
class LoginForm extends Model
{
    /**
     * @var string Username or email
     */
    public $username;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var bool Remember me checkbox
     */
    public $rememberMe = true;

    /**
     * @var string Two-factor authentication code
     */
    public $twoFactorCode;

    /**
     * @var string Captcha code
     */
    public $captcha;

    /**
     * @var User|null Cached user model
     */
    private $_user;

    /**
     * @var int Max login attempts before lockout
     */
    const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * @var int Lockout duration in seconds
     */
    const LOCKOUT_DURATION = 900; // 15 minutes

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'message' => '{attribute} จำเป็นต้องกรอก'],
            
            // username validation
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'trim'],
            
            // password validation
            ['password', 'string', 'min' => 6],
            ['password', 'validatePassword'],
            
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            
            // twoFactorCode is optional
            ['twoFactorCode', 'string', 'min' => 6, 'max' => 6],
            
            // captcha validation (if enabled)
            ['captcha', 'captcha', 'skipOnEmpty' => !$this->isCaptchaRequired()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'ชื่อผู้ใช้หรืออีเมล',
            'password' => 'รหัสผ่าน',
            'rememberMe' => 'จดจำฉันไว้',
            'twoFactorCode' => 'รหัส 2FA',
            'captcha' => 'รหัสยืนยัน',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            // Check if account is locked
            if ($this->isAccountLocked()) {
                $this->addError($attribute, 'บัญชีถูกล็อคชั่วคราว โปรดลองใหม่ภายหลัง');
                return;
            }

            if (!$user || !$user->validatePassword($this->password)) {
                $this->recordFailedAttempt();
                $this->addError($attribute, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                return;
            }

            // Check if user is active
            if ($user->status != User::STATUS_ACTIVE) {
                $this->addError($attribute, 'บัญชีผู้ใช้ถูกระงับการใช้งาน โปรดติดต่อผู้ดูแลระบบ');
                return;
            }

            // Check if user has backend access
            if (!$this->hasBackendAccess($user)) {
                $this->addError($attribute, 'คุณไม่มีสิทธิ์เข้าถึงระบบหลังบ้าน');
                return;
            }
        }
    }

    /**
     * Check if user has backend access role
     *
     * @param User $user
     * @return bool
     */
    protected function hasBackendAccess($user)
    {
        // Allow admin, superadmin, and manager roles
        $allowedRoles = ['admin', 'superadmin', 'manager', 'staff'];
        
        foreach ($allowedRoles as $role) {
            if (Yii::$app->authManager->checkAccess($user->id, $role)) {
                return true;
            }
        }
        
        // Also check user's role field directly
        return in_array($user->role, $allowedRoles);
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();

        // ปิดการใช้งาน 2FA ใน backend/models/LoginForm.php
        // Check 2FA if enabled
        // if ($user->two_factor_enabled) {
        //     if (empty($this->twoFactorCode)) {
        //         return 'require_2fa';
        //     }
            
        //     if (!$this->validate2FA($user, $this->twoFactorCode)) {
        //         $this->addError('twoFactorCode', 'รหัส 2FA ไม่ถูกต้อง');
        //         return false;
        //     }
        // }

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days or session
        $result = Yii::$app->user->login($user, $duration);

        if ($result) {
            $this->recordSuccessfulLogin($user);
            $this->clearFailedAttempts();
        }

        return $result;
    }

    /**
     * Validate 2FA code
     *
     * @param User $user
     * @param string $code
     * @return bool
     */
    protected function validate2FA($user, $code)
    {
        if (empty($user->two_factor_secret)) {
            return true;
        }

        // Use Google Authenticator or similar TOTP validation
        $ga = new \PHPGangsta_GoogleAuthenticator();
        return $ga->verifyCode($user->two_factor_secret, $code, 2);
    }

    /**
     * Record successful login
     *
     * @param User $user
     */
    protected function recordSuccessfulLogin($user)
    {
        // Update last login info
        $user->last_login_at = date('Y-m-d H:i:s');
        $user->last_login_ip = Yii::$app->request->userIP;
        // 
        // $user->login_count = ($user->login_count ?? 0) + 1;
        // $user->save(false, ['last_login_at', 'last_login_ip', 'login_count']);
        $user->save(false, ['last_login_at', 'last_login_ip']);

        // Record login history
        LoginHistory::record($user->id, $user->username, 'success', 'password');
    }

    /**
     * Record failed login attempt
     */
    protected function recordFailedAttempt()
    {
        $cache = Yii::$app->cache;
        $key = 'login_attempts_' . $this->getClientIdentifier();
        $attempts = $cache->get($key) ?: 0;
        $cache->set($key, $attempts + 1, self::LOCKOUT_DURATION);

        // Record to login history
        $user = $this->getUser();
        LoginHistory::record(
            $user ? $user->id : null,
            $this->username,
            'failed',
            'password',
            'Invalid credentials'
        );
    }

    /**
     * Clear failed login attempts
     */
    protected function clearFailedAttempts()
    {
        $cache = Yii::$app->cache;
        $key = 'login_attempts_' . $this->getClientIdentifier();
        $cache->delete($key);
    }

    /**
     * Check if account is locked due to too many failed attempts
     *
     * @return bool
     */
    public function isAccountLocked()
    {
        $cache = Yii::$app->cache;
        $key = 'login_attempts_' . $this->getClientIdentifier();
        $attempts = $cache->get($key) ?: 0;
        
        return $attempts >= self::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Get remaining lockout time in minutes
     *
     * @return int
     */
    public function getRemainingLockoutTime()
    {
        // This would require tracking when lockout started
        return ceil(self::LOCKOUT_DURATION / 60);
    }

    /**
     * Check if captcha is required
     *
     * @return bool
     */
    public function isCaptchaRequired()
    {
        $cache = Yii::$app->cache;
        $key = 'login_attempts_' . $this->getClientIdentifier();
        $attempts = $cache->get($key) ?: 0;
        
        // Require captcha after 3 failed attempts
        return $attempts >= 3;
    }

    /**
     * Get client identifier for rate limiting
     *
     * @return string
     */
    protected function getClientIdentifier()
    {
        return md5(Yii::$app->request->userIP . '_' . $this->username);
    }

    /**
     * Finds user by [[username]] or [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::find()
                ->where(['or',
                    ['username' => $this->username],
                    ['email' => $this->username]
                ])
                ->one();
        }

        return $this->_user;
    }
}
