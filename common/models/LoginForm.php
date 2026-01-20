<?php
/**
 * LoginForm Model - Common login form
 * Meeting Room Booking System
 */

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
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
            [['username', 'password'], 'required', 'message' => '{attribute} จำเป็นต้องกรอก'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['username', 'trim'],
            ['password', 'string', 'min' => 6],
            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
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
        ];
    }

    /**
     * Validates the password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            // Check if account is locked
            if ($this->isAccountLocked()) {
                $this->addError($attribute, 'บัญชีถูกล็อคชั่วคราว กรุณาลองใหม่ภายหลัง');
                return;
            }

            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->recordFailedAttempt();
                $this->addError($attribute, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
                return;
            }

            // Check if user is active
            if ($user->status != User::STATUS_ACTIVE) {
                $this->addError($attribute, 'บัญชีผู้ใช้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ');
                return;
            }
        }
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
        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days or session
        $result = Yii::$app->user->login($user, $duration);

        if ($result) {
            $this->recordSuccessfulLogin($user);
            $this->clearFailedAttempts();
        }

        return $result;
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
        return ceil(self::LOCKOUT_DURATION / 60);
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
