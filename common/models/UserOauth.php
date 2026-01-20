<?php
/**
 * UserOauth Model
 * 
 * Stores OAuth2 provider connections for users
 * Supports multiple providers per user
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 * 
 * @property int $id
 * @property int $user_id
 * @property string $provider Provider name (google, microsoft, thaid)
 * @property string $provider_user_id User ID from provider
 * @property string|null $access_token Current access token
 * @property string|null $refresh_token Refresh token for token renewal
 * @property string|null $token_expires_at Token expiration timestamp
 * @property string|null $profile_data JSON encoded profile data
 * @property string $created_at
 * @property string|null $updated_at
 * 
 * @property User $user
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserOauth extends ActiveRecord
{
    /**
     * Provider constants
     */
    const PROVIDER_GOOGLE = 'google';
    const PROVIDER_MICROSOFT = 'microsoft';
    const PROVIDER_THAID = 'thaid';
    
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%user_oauth}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['user_id', 'provider', 'provider_user_id'], 'required'],
            [['user_id'], 'integer'],
            [['provider'], 'string', 'max' => 50],
            [['provider_user_id'], 'string', 'max' => 255],
            [['access_token', 'refresh_token'], 'string'],
            [['profile_data'], 'string'],
            [['token_expires_at'], 'safe'],
            
            // Provider must be valid
            [['provider'], 'in', 'range' => [
                self::PROVIDER_GOOGLE,
                self::PROVIDER_MICROSOFT,
                self::PROVIDER_THAID,
            ]],
            
            // Unique constraint: user can only have one connection per provider
            [['user_id', 'provider'], 'unique', 'targetAttribute' => ['user_id', 'provider'],
                'message' => 'This provider is already connected to your account.'],
            
            // Unique constraint: provider_user_id must be unique per provider
            [['provider', 'provider_user_id'], 'unique', 'targetAttribute' => ['provider', 'provider_user_id'],
                'message' => 'This account is already connected to another user.'],
            
            // Foreign key
            [['user_id'], 'exist', 'skipOnError' => true, 
                'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'provider' => 'Provider',
            'provider_user_id' => 'Provider User ID',
            'access_token' => 'Access Token',
            'refresh_token' => 'Refresh Token',
            'token_expires_at' => 'Token Expires At',
            'profile_data' => 'Profile Data',
            'created_at' => 'Connected At',
            'updated_at' => 'Updated At',
        ];
    }
    
    /**
     * Get user relation
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * Get profile data as array
     * @return array
     */
    public function getProfileDataArray(): array
    {
        if (empty($this->profile_data)) {
            return [];
        }
        
        return json_decode($this->profile_data, true) ?: [];
    }
    
    /**
     * Set profile data from array
     * @param array $data
     */
    public function setProfileDataArray(array $data): void
    {
        $this->profile_data = json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Check if access token is expired
     * @param int $buffer Buffer time in seconds (default 5 minutes)
     * @return bool
     */
    public function isTokenExpired(int $buffer = 300): bool
    {
        if (empty($this->token_expires_at)) {
            return true;
        }
        
        $expiresAt = strtotime($this->token_expires_at);
        return $expiresAt <= (time() + $buffer);
    }
    
    /**
     * Check if can refresh token
     * @return bool
     */
    public function canRefreshToken(): bool
    {
        return !empty($this->refresh_token);
    }
    
    /**
     * Get provider display name
     * @return string
     */
    public function getProviderDisplayName(): string
    {
        $names = [
            self::PROVIDER_GOOGLE => 'Google',
            self::PROVIDER_MICROSOFT => 'Microsoft',
            self::PROVIDER_THAID => 'ThaiD (Digital ID)',
        ];
        
        return $names[$this->provider] ?? ucfirst($this->provider);
    }
    
    /**
     * Get provider icon class
     * @return string
     */
    public function getProviderIcon(): string
    {
        $icons = [
            self::PROVIDER_GOOGLE => 'bi-google',
            self::PROVIDER_MICROSOFT => 'bi-microsoft',
            self::PROVIDER_THAID => 'bi-shield-check',
        ];
        
        return $icons[$this->provider] ?? 'bi-link';
    }
    
    /**
     * Get provider color
     * @return string
     */
    public function getProviderColor(): string
    {
        $colors = [
            self::PROVIDER_GOOGLE => '#4285F4',
            self::PROVIDER_MICROSOFT => '#00A4EF',
            self::PROVIDER_THAID => '#0066CC',
        ];
        
        return $colors[$this->provider] ?? '#6c757d';
    }
    
    /**
     * Get email from profile data
     * @return string|null
     */
    public function getProviderEmail(): ?string
    {
        $profile = $this->getProfileDataArray();
        return $profile['email'] ?? null;
    }
    
    /**
     * Get name from profile data
     * @return string|null
     */
    public function getProviderName(): ?string
    {
        $profile = $this->getProfileDataArray();
        return $profile['full_name'] ?? $profile['name'] ?? null;
    }
    
    /**
     * Get avatar from profile data
     * @return string|null
     */
    public function getProviderAvatar(): ?string
    {
        $profile = $this->getProfileDataArray();
        return $profile['avatar'] ?? $profile['picture'] ?? null;
    }
    
    /**
     * Find by provider and provider user ID
     * @param string $provider
     * @param string $providerUserId
     * @return static|null
     */
    public static function findByProvider(string $provider, string $providerUserId): ?static
    {
        return static::findOne([
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
        ]);
    }
    
    /**
     * Find all connections for user
     * @param int $userId
     * @return static[]
     */
    public static function findByUser(int $userId): array
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['provider' => SORT_ASC])
            ->all();
    }
    
    /**
     * Get list of available providers
     * @return array
     */
    public static function getProviderList(): array
    {
        return [
            self::PROVIDER_GOOGLE => 'Google',
            self::PROVIDER_MICROSOFT => 'Microsoft',
            self::PROVIDER_THAID => 'ThaiD (Digital ID)',
        ];
    }
    
    /**
     * Update tokens after refresh
     * @param string $accessToken
     * @param string|null $refreshToken
     * @param int $expiresIn Seconds until expiry
     * @return bool
     */
    public function updateTokens(string $accessToken, ?string $refreshToken, int $expiresIn): bool
    {
        $this->access_token = $accessToken;
        
        if ($refreshToken !== null) {
            $this->refresh_token = $refreshToken;
        }
        
        $this->token_expires_at = date('Y-m-d H:i:s', time() + $expiresIn);
        
        return $this->save(false);
    }
    
    /**
     * Before save - encrypt sensitive data
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        
        // Note: In production, consider encrypting access_token and refresh_token
        // using Yii::$app->security->encryptByKey()
        
        return true;
    }
    
    /**
     * After delete - log disconnection
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        
        Yii::info("OAuth disconnected: user={$this->user_id}, provider={$this->provider}", 'oauth');
    }
}
