<?php
/**
 * ActivityLog Model
 * Logs user activities in the system
 * 
 * Meeting Room Booking System
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * ActivityLog model
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $type
 * @property string $description
 * @property string|null $data
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $created_at
 */
class ActivityLog extends ActiveRecord
{
    // Activity Types
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_LOGIN_FAILED = 'login_failed';
    const TYPE_PASSWORD_CHANGE = 'password_change';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_PROFILE_UPDATE = 'profile_update';
    const TYPE_USER_CREATED = 'user_created';
    const TYPE_USER_UPDATED = 'user_updated';
    const TYPE_USER_DELETED = 'user_deleted';
    const TYPE_STATUS_CHANGE = 'status_change';
    const TYPE_ROLE_ASSIGNED = 'role_assigned';
    const TYPE_ROLE_REVOKED = 'role_revoked';
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_UPDATED = 'booking_updated';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_APPROVED = 'booking_approved';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_SETTING_CHANGED = 'setting_changed';
    const TYPE_DATA_EXPORT = 'data_export';
    const TYPE_DATA_IMPORT = 'data_import';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%activity_log}}';
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
            [['type', 'description'], 'required'],
            [['user_id'], 'integer'],
            [['data'], 'string'],
            ['type', 'string', 'max' => 50],
            ['description', 'string', 'max' => 500],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string', 'max' => 500],
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
            'type' => 'ประเภท',
            'description' => 'รายละเอียด',
            'data' => 'ข้อมูลเพิ่มเติม',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'created_at' => 'วันที่',
        ];
    }

    /**
     * Get the user that performed the activity
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Log an activity
     * 
     * @param string $type Activity type constant
     * @param string $description Human readable description
     * @param array $data Additional data to store as JSON
     * @param int|null $userId User ID (defaults to current user)
     * @return bool
     */
    public static function log($type, $description, $data = [], $userId = null)
    {
        try {
            // Check if table exists
            $tableExists = static::getDb()->getTableSchema(static::tableName(), true) !== null;
            
            if (!$tableExists) {
                // Log to Yii logger instead if table doesn't exist
                Yii::info("ActivityLog [{$type}]: {$description} " . json_encode($data), 'activity');
                return true;
            }

            $log = new static();
            $log->user_id = $userId ?? (Yii::$app->user->isGuest ? null : Yii::$app->user->id);
            $log->type = $type;
            $log->description = mb_substr($description, 0, 500);
            $log->data = !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : null;
            $log->ip_address = Yii::$app->request->userIP ?? null;
            $log->user_agent = mb_substr(Yii::$app->request->userAgent ?? '', 0, 500);

            return $log->save(false);
        } catch (\Exception $e) {
            // If database error, just log to Yii logger
            Yii::warning("ActivityLog failed: {$e->getMessage()}", 'activity');
            Yii::info("ActivityLog [{$type}]: {$description} " . json_encode($data), 'activity');
            return false;
        }
    }

    /**
     * Get activity type label in Thai
     */
    public static function getTypeLabel($type)
    {
        $labels = [
            self::TYPE_LOGIN => 'เข้าสู่ระบบ',
            self::TYPE_LOGOUT => 'ออกจากระบบ',
            self::TYPE_LOGIN_FAILED => 'เข้าสู่ระบบล้มเหลว',
            self::TYPE_PASSWORD_CHANGE => 'เปลี่ยนรหัสผ่าน',
            self::TYPE_PASSWORD_RESET => 'รีเซ็ตรหัสผ่าน',
            self::TYPE_PROFILE_UPDATE => 'อัปเดตโปรไฟล์',
            self::TYPE_USER_CREATED => 'สร้างผู้ใช้',
            self::TYPE_USER_UPDATED => 'แก้ไขผู้ใช้',
            self::TYPE_USER_DELETED => 'ลบผู้ใช้',
            self::TYPE_STATUS_CHANGE => 'เปลี่ยนสถานะ',
            self::TYPE_ROLE_ASSIGNED => 'กำหนดบทบาท',
            self::TYPE_ROLE_REVOKED => 'ยกเลิกบทบาท',
            self::TYPE_BOOKING_CREATED => 'สร้างการจอง',
            self::TYPE_BOOKING_UPDATED => 'แก้ไขการจอง',
            self::TYPE_BOOKING_CANCELLED => 'ยกเลิกการจอง',
            self::TYPE_BOOKING_APPROVED => 'อนุมัติการจอง',
            self::TYPE_BOOKING_REJECTED => 'ปฏิเสธการจอง',
            self::TYPE_SETTING_CHANGED => 'เปลี่ยนการตั้งค่า',
            self::TYPE_DATA_EXPORT => 'ส่งออกข้อมูล',
            self::TYPE_DATA_IMPORT => 'นำเข้าข้อมูล',
        ];

        return $labels[$type] ?? $type;
    }

    /**
     * Get badge class for activity type
     */
    public static function getTypeBadgeClass($type)
    {
        $classes = [
            self::TYPE_LOGIN => 'bg-success',
            self::TYPE_LOGOUT => 'bg-secondary',
            self::TYPE_LOGIN_FAILED => 'bg-danger',
            self::TYPE_PASSWORD_CHANGE => 'bg-warning',
            self::TYPE_PASSWORD_RESET => 'bg-warning',
            self::TYPE_USER_CREATED => 'bg-primary',
            self::TYPE_USER_UPDATED => 'bg-info',
            self::TYPE_USER_DELETED => 'bg-danger',
            self::TYPE_BOOKING_CREATED => 'bg-primary',
            self::TYPE_BOOKING_APPROVED => 'bg-success',
            self::TYPE_BOOKING_REJECTED => 'bg-danger',
            self::TYPE_BOOKING_CANCELLED => 'bg-secondary',
        ];

        return $classes[$type] ?? 'bg-secondary';
    }

    /**
     * Get recent activity for a user
     */
    public static function getRecentForUser($userId, $limit = 10)
    {
        try {
            $tableExists = static::getDb()->getTableSchema(static::tableName(), true) !== null;
            if (!$tableExists) {
                return [];
            }
            
            return static::find()
                ->where(['user_id' => $userId])
                ->orderBy(['created_at' => SORT_DESC])
                ->limit($limit)
                ->all();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get decoded data attribute
     */
    public function getDecodedData()
    {
        if (empty($this->data)) {
            return [];
        }
        return json_decode($this->data, true) ?? [];
    }
}
