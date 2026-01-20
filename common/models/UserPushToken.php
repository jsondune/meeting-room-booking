<?php
/**
 * UserPushToken Model
 * 
 * Stores push notification tokens for users (FCM, OneSignal)
 * 
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $provider
 * @property string $platform
 * @property string|null $device_id
 * @property string|null $device_name
 * @property string|null $app_version
 * @property bool $is_active
 * @property string|null $last_used_at
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property User $user
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserPushToken extends ActiveRecord
{
    const PROVIDER_FCM = 'fcm';
    const PROVIDER_ONESIGNAL = 'onesignal';
    
    const PLATFORM_ANDROID = 'android';
    const PLATFORM_IOS = 'ios';
    const PLATFORM_WEB = 'web';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_push_tokens}}';
    }
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'provider', 'platform'], 'required'],
            [['user_id'], 'integer'],
            [['is_active'], 'boolean'],
            [['last_used_at'], 'safe'],
            [['token'], 'string', 'max' => 500],
            [['provider'], 'string', 'max' => 50],
            [['platform'], 'string', 'max' => 20],
            [['device_id', 'device_name'], 'string', 'max' => 255],
            [['app_version'], 'string', 'max' => 50],
            [['token'], 'unique'],
            [['provider'], 'in', 'range' => [self::PROVIDER_FCM, self::PROVIDER_ONESIGNAL]],
            [['platform'], 'in', 'range' => [self::PLATFORM_ANDROID, self::PLATFORM_IOS, self::PLATFORM_WEB]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'token' => 'Token',
            'provider' => 'ผู้ให้บริการ',
            'platform' => 'แพลตฟอร์ม',
            'device_id' => 'รหัสอุปกรณ์',
            'device_name' => 'ชื่ออุปกรณ์',
            'app_version' => 'เวอร์ชันแอป',
            'is_active' => 'สถานะใช้งาน',
            'last_used_at' => 'ใช้งานล่าสุด',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }
    
    /**
     * Get user relation
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * Get provider options
     * @return array
     */
    public static function getProviderOptions(): array
    {
        return [
            self::PROVIDER_FCM => 'Firebase Cloud Messaging',
            self::PROVIDER_ONESIGNAL => 'OneSignal',
        ];
    }
    
    /**
     * Get platform options
     * @return array
     */
    public static function getPlatformOptions(): array
    {
        return [
            self::PLATFORM_ANDROID => 'Android',
            self::PLATFORM_IOS => 'iOS',
            self::PLATFORM_WEB => 'Web',
        ];
    }
    
    /**
     * Register or update token for user
     * @param int $userId User ID
     * @param string $token Push token
     * @param string $provider Provider (fcm, onesignal)
     * @param string $platform Platform (android, ios, web)
     * @param array $deviceInfo Device information
     * @return UserPushToken|null
     */
    public static function register(int $userId, string $token, string $provider, string $platform, array $deviceInfo = []): ?UserPushToken
    {
        // Check if token exists
        $existing = self::findOne(['token' => $token]);
        
        if ($existing) {
            // Update existing token
            $existing->user_id = $userId;
            $existing->is_active = true;
            $existing->last_used_at = date('Y-m-d H:i:s');
            
            if (isset($deviceInfo['device_id'])) {
                $existing->device_id = $deviceInfo['device_id'];
            }
            if (isset($deviceInfo['device_name'])) {
                $existing->device_name = $deviceInfo['device_name'];
            }
            if (isset($deviceInfo['app_version'])) {
                $existing->app_version = $deviceInfo['app_version'];
            }
            
            return $existing->save(false) ? $existing : null;
        }
        
        // Create new token
        $model = new self([
            'user_id' => $userId,
            'token' => $token,
            'provider' => $provider,
            'platform' => $platform,
            'device_id' => $deviceInfo['device_id'] ?? null,
            'device_name' => $deviceInfo['device_name'] ?? null,
            'app_version' => $deviceInfo['app_version'] ?? null,
            'is_active' => true,
        ]);
        
        return $model->save() ? $model : null;
    }
    
    /**
     * Unregister token
     * @param string $token Push token
     * @return bool
     */
    public static function unregister(string $token): bool
    {
        $model = self::findOne(['token' => $token]);
        if ($model) {
            return $model->delete() !== false;
        }
        return true;
    }
    
    /**
     * Deactivate token (soft delete)
     * @param string $token Push token
     * @return bool
     */
    public static function deactivate(string $token): bool
    {
        $model = self::findOne(['token' => $token]);
        if ($model) {
            $model->is_active = false;
            return $model->save(false);
        }
        return true;
    }
    
    /**
     * Get active tokens for user
     * @param int $userId User ID
     * @param string|null $provider Filter by provider
     * @return array
     */
    public static function getActiveTokensForUser(int $userId, ?string $provider = null): array
    {
        $query = self::find()
            ->where(['user_id' => $userId, 'is_active' => true]);
        
        if ($provider) {
            $query->andWhere(['provider' => $provider]);
        }
        
        return $query->all();
    }
    
    /**
     * Get tokens grouped by provider
     * @param int $userId User ID
     * @return array
     */
    public static function getTokensByProvider(int $userId): array
    {
        $tokens = self::find()
            ->where(['user_id' => $userId, 'is_active' => true])
            ->all();
        
        $result = [
            self::PROVIDER_FCM => [],
            self::PROVIDER_ONESIGNAL => [],
        ];
        
        foreach ($tokens as $token) {
            $result[$token->provider][] = $token->token;
        }
        
        return $result;
    }
    
    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): void
    {
        $this->last_used_at = date('Y-m-d H:i:s');
        $this->save(false, ['last_used_at', 'updated_at']);
    }
    
    /**
     * Clean up old inactive tokens
     * @param int $daysOld Number of days
     * @return int Number of deleted tokens
     */
    public static function cleanupOldTokens(int $daysOld = 90): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        return self::deleteAll([
            'and',
            ['is_active' => false],
            ['<', 'updated_at', $cutoffDate],
        ]);
    }
    
    /**
     * Clean up tokens not used for a period
     * @param int $daysUnused Number of days
     * @return int Number of deleted tokens
     */
    public static function cleanupUnusedTokens(int $daysUnused = 180): int
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysUnused} days"));
        
        return self::deleteAll([
            'or',
            ['and', ['is not', 'last_used_at', null], ['<', 'last_used_at', $cutoffDate]],
            ['and', ['last_used_at' => null], ['<', 'created_at', $cutoffDate]],
        ]);
    }
    
    /**
     * Get device count for user
     * @param int $userId User ID
     * @return int
     */
    public static function getDeviceCount(int $userId): int
    {
        return self::find()
            ->where(['user_id' => $userId, 'is_active' => true])
            ->count();
    }
    
    /**
     * Get user devices for management
     * @param int $userId User ID
     * @return array
     */
    public static function getUserDevices(int $userId): array
    {
        return self::find()
            ->where(['user_id' => $userId, 'is_active' => true])
            ->orderBy(['last_used_at' => SORT_DESC])
            ->asArray()
            ->all();
    }
}
