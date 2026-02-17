<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property string|null $password_reset_token
 * @property string|null $verification_token
 * @property string $full_name
 * @property string|null $phone
 * @property string|null $avatar
 * @property int|null $department_id
 * @property string|null $position
 * @property string|null $azure_id
 * @property string|null $google_id
 * @property string|null $thaid_id
 * @property string|null $facebook_id
 * @property string|null $two_factor_secret
 * @property bool $two_factor_enabled
 * @property string|null $backup_codes
 * @property int $failed_login_attempts
 * @property string|null $locked_until
 * @property string|null $password_changed_at
 * @property string|null $last_login_at
 * @property string|null $last_login_ip
 * @property int $status
 * @property string $role
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property Department $department
 * @property Booking[] $bookings
 * @property UserSession[] $sessions
 * @property LoginHistory[] $loginHistory
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    
    const ROLE_VIEWER = 'viewer';
    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERADMIN = 'superadmin';

    /**
     * @var string Password for validation
     */
    public $password;
    
    /**
     * @var string Password confirmation
     */
    public $password_confirm;

    /**
     * @var \yii\web\UploadedFile Avatar file for upload
     */
    public $avatarFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            
            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_VIEWER, self::ROLE_USER, self::ROLE_MANAGER, self::ROLE_ADMIN, self::ROLE_SUPERADMIN]],
            
            [['username', 'email', 'full_name'], 'required'],
            [['username', 'email'], 'trim'],
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Username can only contain letters, numbers, and underscores.'],
            ['username', 'unique', 'targetClass' => self::class, 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
            }],
            
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => self::class, 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
            }],
            
            [['full_name'], 'string', 'max' => 200],
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^[0-9\-\+\s]+$/', 'message' => 'Invalid phone number format.'],
            
            ['position', 'string', 'max' => 100],
            ['department_id', 'integer'],
            ['department_id', 'exist', 'targetClass' => Department::class, 'targetAttribute' => 'id'],
            
            ['avatar', 'string', 'max' => 255],
            
            // Avatar file upload
            ['avatarFile', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'gif', 'webp'], 'maxSize' => 5 * 1024 * 1024],
            
            // OAuth IDs
            [['azure_id', 'google_id', 'thaid_id', 'facebook_id'], 'string', 'max' => 255],
            
            // 2FA
            ['two_factor_enabled', 'boolean'],
            ['two_factor_secret', 'string', 'max' => 255],
            
            // Password validation (for new users or password change)
            ['password', 'required', 'on' => ['create', 'admin-create']],
            ['password', 'string', 'min' => 8],
            ['password', 'validatePasswordStrength'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'message' => 'Passwords do not match.'],
            
            ['failed_login_attempts', 'integer', 'min' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        
        // Default scenario
        $scenarios[self::SCENARIO_DEFAULT] = ['username', 'email', 'full_name', 'phone', 
            'position', 'department_id', 'avatar', 'avatarFile', 'status', 'role',
            'azure_id', 'google_id', 'thaid_id', 'facebook_id', 'two_factor_enabled'];
        
        // Create scenario (regular registration)
        $scenarios['create'] = ['username', 'email', 'password', 'password_confirm', 
            'full_name', 'phone', 'position', 'department_id'];
        
        // Admin create scenario
        $scenarios['admin-create'] = ['username', 'email', 'password', 'password_confirm',
            'full_name', 'phone', 'position', 'department_id', 
            'avatar', 'avatarFile', 'status', 'role'];
        
        // Admin update scenario
        $scenarios['admin-update'] = ['username', 'email', 'password', 'password_confirm',
            'full_name', 'phone', 'position', 'department_id',
            'avatar', 'avatarFile', 'status', 'role'];
        
        // Profile update scenario
        $scenarios['profile'] = ['full_name', 'phone', 'position', 'avatar', 'avatarFile'];
        
        // Password change scenario
        $scenarios['password'] = ['password', 'password_confirm'];
        
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ชื่อผู้ใช้',
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
            'password_confirm' => 'ยืนยันรหัสผ่าน',
            'full_name' => 'ชื่อ-นามสกุล',
            'phone' => 'เบอร์โทรศัพท์',
            'avatar' => 'รูปโปรไฟล์',
            'department_id' => 'หน่วยงาน',
            'position' => 'ตำแหน่ง',
            'status' => 'สถานะ',
            'role' => 'บทบาท',
            'two_factor_enabled' => 'เปิดใช้ 2FA',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
            'last_login_at' => 'เข้าสู่ระบบล่าสุด',
        ];
    }

    /**
     * Validate password strength (OWASP compliant)
     */
    public function validatePasswordStrength($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $password = $this->$attribute;
            
            // Get settings from system settings or use defaults
            $minLength = 8;
            $requireUppercase = true;
            $requireNumber = true;
            $requireSpecial = true;
            
            if (strlen($password) < $minLength) {
                $this->addError($attribute, "Password must be at least {$minLength} characters.");
            }
            
            if ($requireUppercase && !preg_match('/[A-Z]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one uppercase letter.');
            }
            
            if (!preg_match('/[a-z]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one lowercase letter.');
            }
            
            if ($requireNumber && !preg_match('/[0-9]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one number.');
            }
            
            if ($requireSpecial && !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $this->addError($attribute, 'Password must contain at least one special character.');
            }
            
            // Check for common passwords
            $commonPasswords = ['password', '123456', 'password123', 'admin123', 'letmein'];
            if (in_array(strtolower($password), $commonPasswords)) {
                $this->addError($attribute, 'Password is too common. Please choose a stronger password.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->where(['id' => $id, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['username' => $username, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()
            ->where(['email' => $email, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * Finds user by OAuth ID
     */
    public static function findByOAuthId($provider, $id)
    {
        $attribute = $provider . '_id';
        return static::find()
            ->where([$attribute => $id, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * Finds user by password reset token
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::find()
            ->where(['password_reset_token' => $token, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * Finds user by verification email token
     */
    public static function findByVerificationToken($token)
    {
        return static::find()
            ->where(['verification_token' => $token, 'status' => self::STATUS_INACTIVE])
            ->andWhere(['deleted_at' => null])
            ->one();
    }

    /**
     * Finds out if password reset token is valid
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'] ?? 3600;
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash using Argon2id
     */
    public function setPassword($password)
    {
        // Use Argon2id for password hashing (OWASP recommended)
        $this->password_hash = Yii::$app->security->generatePasswordHash($password, PASSWORD_ARGON2ID);
        $this->password_changed_at = date('Y-m-d H:i:s');
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Alias for fullName (to support $user->full_name)
     */
    public function getFull_name()
    {
        return $this->getFullName();
    }

    /**
     * Get employee (backward compatibility - returns null)
     * @deprecated This property doesn't exist in database
     */
    public function getEmployee()
    {
        return null;
    }

    /**
     * Get employeeId (backward compatibility - returns null)
     * Allows access via $user->employeeId or $user->employee_id
     * @deprecated This property doesn't exist in database
     */
    public function getEmployeeId()
    {
        return null;
    }

    /**
     * Get email_verified_at (backward compatibility - returns created_at as verified date)
     * @deprecated This property doesn't exist in database
     */
    public function getEmailVerifiedAt()
    {
        // Return created_at as verification date (assume verified on registration)
        return $this->created_at;
    }

    /**
     * Check if email is verified
     * @return bool
     */
    public function isEmailVerified()
    {
        return true; // Assume all users are verified
    }

    /**
     * Get display name with position
     */
    public function getDisplayName()
    {
        $name = $this->getFullName();
        if ($this->position) {
            $name .= ' (' . $this->position . ')';
        }
        return $name;
    }

    /**
     * Upload avatar file
     * @param \yii\web\UploadedFile $file
     * @return string|null The uploaded file path or null on failure
     */
    public function uploadAvatar($file)
    {
        if (!$file) {
            return null;
        }

        // Create upload directory using shared @uploads alias
        $uploadPath = Yii::getAlias('@uploads/avatars');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Delete old avatar first
        $this->deleteOldAvatar();

        // Generate unique filename
        $filename = 'avatar_' . $this->id . '_' . time() . '.' . $file->extension;
        $filePath = $uploadPath . DIRECTORY_SEPARATOR . $filename;

        if ($file->saveAs($filePath)) {
            return 'avatars/' . $filename;
        }

        return null;
    }

    /**
     * Get avatar URL
     * @return string The avatar URL or default avatar
     */
    public function getAvatarUrl()
    {
        if (!empty($this->avatar)) {
            // Check if it's already a full URL (OAuth avatar)
            if (strpos($this->avatar, 'http') === 0) {
                return $this->avatar;
            }
            
            // For local files, use @uploadsUrl alias
            $uploadsUrl = Yii::getAlias('@uploadsUrl');
            return $uploadsUrl . '/' . ltrim($this->avatar, '/');
        }
        
        // Return default avatar with initials
        return $this->getDefaultAvatarUrl();
    }

    /**
     * Get default avatar URL (placeholder with initials)
     * @return string
     */
    public function getDefaultAvatarUrl()
    {
        $fullName = $this->full_name ?? 'U';
        $initials = mb_substr($fullName, 0, 1);
        $initials = strtoupper($initials);
        
        // Use UI Avatars service for default avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&size=150&background=0D6EFD&color=fff&rounded=true';
    }

    /**
     * Delete old avatar file
     * @return bool
     */
    public function deleteOldAvatar()
    {
        if (!empty($this->avatar) && strpos($this->avatar, 'http') !== 0) {
            $filePath = Yii::getAlias('@uploads') . '/' . ltrim($this->avatar, '/');
            if (file_exists($filePath)) {
                return @unlink($filePath);
            }
        }
        return false;
    }

    /**
     * Check if account is locked
     */
    public function isLocked()
    {
        if ($this->locked_until === null) {
            return false;
        }
        return strtotime($this->locked_until) > time();
    }

    /**
     * Lock account
     */
    public function lockAccount($minutes = 30)
    {
        $this->locked_until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        $this->save(false, ['locked_until']);
    }

    /**
     * Unlock account
     */
    public function unlockAccount()
    {
        $this->locked_until = null;
        $this->failed_login_attempts = 0;
        $this->save(false, ['locked_until', 'failed_login_attempts']);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts()
    {
        $this->failed_login_attempts++;
        
        // Lock account after max attempts
        $maxAttempts = Yii::$app->params['user.maxLoginAttempts'] ?? 5;
        if ($this->failed_login_attempts >= $maxAttempts) {
            $lockoutDuration = Yii::$app->params['user.lockoutDuration'] ?? 30;
            $this->lockAccount($lockoutDuration);
        }
        
        $this->save(false, ['failed_login_attempts']);
    }

    /**
     * Reset failed login attempts on successful login
     */
    public function resetFailedAttempts()
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->save(false, ['failed_login_attempts', 'locked_until']);
    }

    /**
     * Record login
     */
    public function recordLogin($ip)
    {
        $this->last_login_at = date('Y-m-d H:i:s');
        $this->last_login_ip = $ip;
        $this->resetFailedAttempts();
        $this->save(false, ['last_login_at', 'last_login_ip', 'failed_login_attempts', 'locked_until']);
    }

    /**
     * Generate TOTP secret for 2FA
     */
    public function generateTotpSecret()
    {
        $this->two_factor_secret = Yii::$app->security->generateRandomString(32);
        return $this->two_factor_secret;
    }

    /**
     * Enable 2FA
     */
    public function enableTwoFactor()
    {
        if ($this->two_factor_secret) {
            $this->two_factor_enabled = true;
            $this->generateBackupCodes();
            return $this->save(false, ['two_factor_enabled', 'backup_codes']);
        }
        return false;
    }

    /**
     * Disable 2FA
     */
    public function disableTwoFactor()
    {
        $this->two_factor_enabled = false;
        $this->two_factor_secret = null;
        $this->backup_codes = null;
        return $this->save(false, ['two_factor_enabled', 'two_factor_secret', 'backup_codes']);
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes($count = 10)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Yii::$app->security->generateRandomString(8));
        }
        $this->backup_codes = json_encode($codes);
        return $codes;
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode($code)
    {
        $codes = json_decode($this->backup_codes, true) ?: [];
        $key = array_search(strtoupper($code), $codes);
        
        if ($key !== false) {
            unset($codes[$key]);
            $this->backup_codes = json_encode(array_values($codes));
            $this->save(false, ['backup_codes']);
            return true;
        }
        
        return false;
    }

    /**
     * Soft delete user
     */
    public function softDelete()
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        $this->status = self::STATUS_DELETED;
        return $this->save(false, ['deleted_at', 'status']);
    }

    /**
     * Restore soft deleted user
     */
    public function restore()
    {
        $this->deleted_at = null;
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false, ['deleted_at', 'status']);
    }

    /**
     * Check if user has specific role
     */
    public function hasRole($role)
    {
        $roleHierarchy = [
            self::ROLE_VIEWER => 1,
            self::ROLE_USER => 2,
            self::ROLE_MANAGER => 3,
            self::ROLE_ADMIN => 4,
            self::ROLE_SUPERADMIN => 5,
        ];
        
        $userLevel = $roleHierarchy[$this->role] ?? 0;
        $requiredLevel = $roleHierarchy[$role] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers()
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    /**
     * Check if user can approve bookings
     */
    public function canApproveBookings()
    {
        return $this->hasRole(self::ROLE_MANAGER);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_DELETED => '<span class="badge bg-danger">ลบแล้ว</span>',
            self::STATUS_INACTIVE => '<span class="badge bg-warning text-dark">ไม่ได้ใช้งาน</span>',
            self::STATUS_ACTIVE => '<span class="badge bg-success">ใช้งาน</span>',
        ];
        return $labels[$this->status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get role label
     */
    public function getRoleLabel()
    {
        $labels = [
            self::ROLE_VIEWER => '<span class="badge bg-secondary">ผู้ดู</span>',
            self::ROLE_USER => '<span class="badge bg-info">ผู้ใช้</span>',
            self::ROLE_MANAGER => '<span class="badge bg-primary">ผู้จัดการ</span>',
            self::ROLE_ADMIN => '<span class="badge bg-warning text-dark">ผู้ดูแล</span>',
            self::ROLE_SUPERADMIN => '<span class="badge bg-danger">ผู้ดูแลระบบ</span>',
        ];
        return $labels[$this->role] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get available roles for dropdown
     */
    public static function getRoleOptions()
    {
        return [
            self::ROLE_VIEWER => 'ผู้ดู (Viewer)',
            self::ROLE_USER => 'ผู้ใช้ (User)',
            self::ROLE_MANAGER => 'ผู้จัดการ (Manager)',
            self::ROLE_ADMIN => 'ผู้ดูแล (Admin)',
            self::ROLE_SUPERADMIN => 'ผู้ดูแลระบบ (Super Admin)',
        ];
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'ใช้งาน',
            self::STATUS_INACTIVE => 'ไม่ได้ใช้งาน',
            self::STATUS_DELETED => 'ลบแล้ว',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(UserSession::class, ['user_id' => 'id']);
    }

    /**
     * Before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->generateAuthKey();
            }
            
            if ($this->password) {
                $this->setPassword($this->password);
            }
            
            return true;
        }
        return false;
    }

    /**
     * After save - log audit
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Log audit
        AuditLog::log(
            $insert ? 'create' : 'update',
            static::class,
            $this->id,
            $insert ? [] : $changedAttributes,
            $this->attributes
        );
    }
}
