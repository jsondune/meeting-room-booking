<?php
/**
 * Login History Model
 * Tracks user login attempts
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * LoginHistory model
 *
 * @property int $id
 * @property int $user_id
 * @property string $username
 * @property string $ip_address
 * @property string $user_agent
 * @property string $login_method password|azure|google|thaid|facebook
 * @property string $login_status success|failed|locked|captcha_required
 * @property string $failure_reason
 * @property string $created_at
 */
class LoginHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%login_history}}';
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
                'updatedAtAttribute' => false,
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
            [['user_id'], 'integer'],
            [['ip_address'], 'required'],
            [['ip_address'], 'string', 'max' => 45],
            [['username'], 'string', 'max' => 50],
            [['user_agent'], 'string'],
            [['login_method'], 'string', 'max' => 20],
            [['login_method'], 'default', 'value' => 'password'],
            [['login_status'], 'in', 'range' => ['success', 'failed', 'locked', 'captcha_required']],
            [['login_status'], 'default', 'value' => 'success'],
            [['failure_reason'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
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
            'username' => 'ชื่อผู้ใช้',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'login_method' => 'วิธีเข้าสู่ระบบ',
            'login_status' => 'สถานะ',
            'failure_reason' => 'สาเหตุที่ล้มเหลว',
            'created_at' => 'สร้างเมื่อ',
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
     * Record login attempt
     * 
     * @param int|null $userId
     * @param string $username
     * @param string $status 'success' or 'failed'
     * @param string $method 'password', 'google', etc.
     * @param string|null $failureReason
     * @return bool
     */
    // DUBE-DEBUG
    // public static function record($userId, $username, $status = 'success', $method = 'password', $failureReason = null)
    public static function logAttempt($userId, $username, $status = 'success', $method = 'password', $failureReason = null)
    {
        try {
            $history = new static();
            $history->user_id = $userId;
            $history->username = $username;
            $history->ip_address = Yii::$app->request->userIP;
            $history->user_agent = substr(Yii::$app->request->userAgent ?? '', 0, 65535);
            $history->login_method = $method;
            $history->login_status = $status;
            $history->failure_reason = $failureReason;
            return $history->save(false);
        } catch (\Exception $e) {
            Yii::error('Failed to record login history: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent login history for a user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public static function getRecentLogins($userId, $limit = 10)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Get failed login count for IP in last hour
     * 
     * @param string $ip
     * @param int $minutes
     * @return int
     */
    public static function getFailedCountByIp($ip, $minutes = 60)
    {
        return static::find()
            ->where(['ip_address' => $ip, 'login_status' => 'failed'])
            ->andWhere(['>=', 'created_at', date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"))])
            ->count();
    }
}
