<?php
/**
 * Notification Model
 * User notifications for the Meeting Room Booking System
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Notification model
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property string $data JSON additional data
 * @property string $link
 * @property bool $is_read
 * @property string $read_at
 * @property bool $sent_email
 * @property string $created_at
 *
 * @property User $user
 */
class Notification extends ActiveRecord
{
    // Notification types
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_APPROVED = 'booking_approved';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_REMINDER = 'booking_reminder';
    const TYPE_BOOKING_UPDATED = 'booking_updated';
    const TYPE_SYSTEM = 'system';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%notification}}';
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
            [['user_id', 'type', 'title'], 'required'],
            [['user_id'], 'integer'],
            [['message', 'data'], 'string'],
            [['type'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 255],
            [['link'], 'string', 'max' => 500],
            [['is_read', 'sent_email'], 'boolean'],
            [['is_read'], 'default', 'value' => false],
            [['sent_email'], 'default', 'value' => false],
            [['read_at', 'created_at'], 'safe'],
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
            'type' => 'ประเภท',
            'title' => 'หัวข้อ',
            'message' => 'ข้อความ',
            'data' => 'ข้อมูลเพิ่มเติม',
            'link' => 'ลิงก์',
            'is_read' => 'อ่านแล้ว',
            'read_at' => 'อ่านเมื่อ',
            'sent_email' => 'ส่งอีเมลแล้ว',
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
     * Get decoded data
     * @return array|null
     */
    public function getDecodedData()
    {
        if (empty($this->data)) {
            return null;
        }
        return json_decode($this->data, true);
    }

    /**
     * Set data from array
     * @param array $data
     */
    public function setDataFromArray($data)
    {
        $this->data = json_encode($data);
    }

    /**
     * Mark as read
     * @return bool
     */
    public function markAsRead()
    {
        if ($this->is_read) {
            return true;
        }
        
        $this->is_read = true;
        $this->read_at = date('Y-m-d H:i:s');
        return $this->save(false, ['is_read', 'read_at']);
    }

    /**
     * Create notification for user
     * 
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @param string|null $link
     * @return Notification|null
     */
    public static function notify($userId, $type, $title, $message = '', $data = [], $link = null)
    {
        $notification = new static();
        $notification->user_id = $userId;
        $notification->type = $type;
        $notification->title = $title;
        $notification->message = $message;
        $notification->data = !empty($data) ? json_encode($data) : null;
        $notification->link = $link;
        
        if ($notification->save()) {
            return $notification;
        }
        
        Yii::error('Failed to create notification: ' . json_encode($notification->errors));
        return null;
    }

    /**
     * Get unread count for user
     * 
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount($userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'is_read' => false])
            ->count();
    }

    /**
     * Get recent notifications for user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public static function getRecent($userId, $limit = 10)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Mark all as read for user
     * 
     * @param int $userId
     * @return int Number of updated records
     */
    public static function markAllAsRead($userId)
    {
        return static::updateAll(
            ['is_read' => true, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => $userId, 'is_read' => false]
        );
    }

    /**
     * Delete old notifications
     * 
     * @param int $days Keep notifications for this many days
     * @return int Number of deleted records
     */
    public static function deleteOld($days = 30)
    {
        return static::deleteAll(
            ['and',
                ['is_read' => true],
                ['<', 'created_at', date('Y-m-d H:i:s', strtotime("-{$days} days"))]
            ]
        );
    }

    /**
     * Get type options for dropdown
     * @return array
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_BOOKING_CREATED => 'สร้างการจอง',
            self::TYPE_BOOKING_APPROVED => 'อนุมัติการจอง',
            self::TYPE_BOOKING_REJECTED => 'ปฏิเสธการจอง',
            self::TYPE_BOOKING_CANCELLED => 'ยกเลิกการจอง',
            self::TYPE_BOOKING_REMINDER => 'แจ้งเตือนการจอง',
            self::TYPE_BOOKING_UPDATED => 'อัปเดตการจอง',
            self::TYPE_SYSTEM => 'ระบบ',
            self::TYPE_INFO => 'ข้อมูล',
            self::TYPE_WARNING => 'คำเตือน',
        ];
    }

    /**
     * Get type label
     * @return string
     */
    public function getTypeLabel()
    {
        $options = self::getTypeOptions();
        return $options[$this->type] ?? $this->type;
    }

    /**
     * Get icon class based on type
     * @return string
     */
    public function getIconClass()
    {
        $icons = [
            self::TYPE_BOOKING_CREATED => 'bi-calendar-plus text-primary',
            self::TYPE_BOOKING_APPROVED => 'bi-check-circle text-success',
            self::TYPE_BOOKING_REJECTED => 'bi-x-circle text-danger',
            self::TYPE_BOOKING_CANCELLED => 'bi-calendar-x text-warning',
            self::TYPE_BOOKING_REMINDER => 'bi-bell text-info',
            self::TYPE_BOOKING_UPDATED => 'bi-pencil text-secondary',
            self::TYPE_SYSTEM => 'bi-gear text-dark',
            self::TYPE_INFO => 'bi-info-circle text-info',
            self::TYPE_WARNING => 'bi-exclamation-triangle text-warning',
        ];
        
        return $icons[$this->type] ?? 'bi-bell text-secondary';
    }
}
