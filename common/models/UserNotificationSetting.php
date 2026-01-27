<?php
/**
 * UserNotificationSetting Model
 * 
 * Manages user notification preferences for email, push, and calendar sync
 * 
 * @property int $id
 * @property int $user_id
 * @property bool $email_booking_created
 * @property bool $email_booking_approved
 * @property bool $email_booking_rejected
 * @property bool $email_booking_cancelled
 * @property bool $email_booking_reminder
 * @property bool $email_pending_approval
 * @property bool $email_daily_summary
 * @property bool $push_booking_created
 * @property bool $push_booking_approved
 * @property bool $push_booking_rejected
 * @property bool $push_booking_cancelled
 * @property bool $push_booking_reminder
 * @property bool $push_pending_approval
 * @property bool $realtime_enabled
 * @property bool $calendar_sync_google
 * @property bool $calendar_sync_microsoft
 * @property bool $calendar_auto_sync
 * @property int $reminder_minutes_before
 * @property bool $reminder_email
 * @property bool $reminder_push
 * @property bool $quiet_hours_enabled
 * @property string|null $quiet_hours_start
 * @property string|null $quiet_hours_end
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property User $user
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class UserNotificationSetting extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_notification_settings}}';
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
            [['user_id'], 'required'],
            [['user_id', 'reminder_minutes_before'], 'integer'],
            [
                [
                    'email_booking_created', 'email_booking_approved', 'email_booking_rejected',
                    'email_booking_cancelled', 'email_booking_reminder', 'email_pending_approval',
                    'email_daily_summary',
                    'push_booking_created', 'push_booking_approved', 'push_booking_rejected',
                    'push_booking_cancelled', 'push_booking_reminder', 'push_pending_approval',
                    'realtime_enabled',
                    'calendar_sync_google', 'calendar_sync_microsoft', 'calendar_auto_sync',
                    'reminder_email', 'reminder_push',
                    'quiet_hours_enabled',
                ],
                'boolean',
            ],
            [['quiet_hours_start', 'quiet_hours_end'], 'safe'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['reminder_minutes_before'], 'in', 'range' => [5, 10, 15, 30, 60, 120, 1440]],
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
            'email_booking_created' => 'อีเมลเมื่อสร้างการจอง',
            'email_booking_approved' => 'อีเมลเมื่อการจองอนุมัติ',
            'email_booking_rejected' => 'อีเมลเมื่อการจองถูกปฏิเสธ',
            'email_booking_cancelled' => 'อีเมลเมื่อยกเลิกการจอง',
            'email_booking_reminder' => 'อีเมลแจ้งเตือนก่อนประชุม',
            'email_pending_approval' => 'อีเมลเมื่อมีการจองรออนุมัติ',
            'email_daily_summary' => 'อีเมลสรุปประจำวัน',
            'push_booking_created' => 'แจ้งเตือนเมื่อสร้างการจอง',
            'push_booking_approved' => 'แจ้งเตือนเมื่อการจองอนุมัติ',
            'push_booking_rejected' => 'แจ้งเตือนเมื่อการจองถูกปฏิเสธ',
            'push_booking_cancelled' => 'แจ้งเตือนเมื่อยกเลิกการจอง',
            'push_booking_reminder' => 'แจ้งเตือนก่อนประชุม',
            'push_pending_approval' => 'แจ้งเตือนเมื่อมีการจองรออนุมัติ',
            'realtime_enabled' => 'เปิดใช้การแจ้งเตือนแบบเรียลไทม์',
            'calendar_sync_google' => 'ซิงค์กับ Google Calendar',
            'calendar_sync_microsoft' => 'ซิงค์กับ Outlook Calendar',
            'calendar_auto_sync' => 'ซิงค์อัตโนมัติเมื่อได้รับอนุมัติ',
            'reminder_minutes_before' => 'เตือนก่อนประชุม (นาที)',
            'reminder_email' => 'เตือนผ่านอีเมล',
            'reminder_push' => 'เตือนผ่าน Push Notification',
            'quiet_hours_enabled' => 'เปิดใช้ช่วงเวลาเงียบ',
            'quiet_hours_start' => 'เริ่มช่วงเวลาเงียบ',
            'quiet_hours_end' => 'สิ้นสุดช่วงเวลาเงียบ',
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
     * Get or create settings for user
     * @param int $userId User ID
     * @return UserNotificationSetting
     */
    public static function getForUser(int $userId): UserNotificationSetting
    {
        $setting = self::findOne(['user_id' => $userId]);
        
        if (!$setting) {
            $setting = new self([
                'user_id' => $userId,
            ]);
            $setting->save(false);
        }
        
        return $setting;
    }
    
    /**
     * Check if notification type is enabled for email
     * @param string $type Notification type
     * @return bool
     */
    public function isEmailEnabled(string $type): bool
    {
        $attribute = 'email_' . $type;
        return isset($this->$attribute) ? (bool)$this->$attribute : true;
    }
    
    /**
     * Check if notification type is enabled for push
     * @param string $type Notification type
     * @return bool
     */
    public function isPushEnabled(string $type): bool
    {
        $attribute = 'push_' . $type;
        return isset($this->$attribute) ? (bool)$this->$attribute : true;
    }
    
    /**
     * Check if currently in quiet hours
     * @return bool
     */
    public function isInQuietHours(): bool
    {
        if (!$this->quiet_hours_enabled || !$this->quiet_hours_start || !$this->quiet_hours_end) {
            return false;
        }
        
        $now = date('H:i:s');
        $start = $this->quiet_hours_start;
        $end = $this->quiet_hours_end;
        
        // Handle overnight quiet hours (e.g., 22:00 to 07:00)
        if ($start > $end) {
            return $now >= $start || $now <= $end;
        }
        
        return $now >= $start && $now <= $end;
    }
    
    /**
     * Check if calendar sync is enabled for provider
     * @param string $provider Provider name (google, microsoft)
     * @return bool
     */
    public function isCalendarSyncEnabled(string $provider): bool
    {
        $attribute = 'calendar_sync_' . $provider;
        return isset($this->$attribute) ? (bool)$this->$attribute : false;
    }
    
    /**
     * Get available reminder options
     * @return array
     */
    public static function getReminderOptions(): array
    {
        return [
            5 => '5 นาที',
            10 => '10 นาที',
            15 => '15 นาที',
            30 => '30 นาที',
            60 => '1 ชั่วโมง',
            120 => '2 ชั่วโมง',
            1440 => '1 วัน',
        ];
    }
    
    /**
     * Get email settings as array
     * @return array
     */
    public function getEmailSettings(): array
    {
        return [
            'booking_created' => $this->email_booking_created,
            'booking_approved' => $this->email_booking_approved,
            'booking_rejected' => $this->email_booking_rejected,
            'booking_cancelled' => $this->email_booking_cancelled,
            'booking_reminder' => $this->email_booking_reminder,
            'pending_approval' => $this->email_pending_approval,
            'daily_summary' => $this->email_daily_summary,
        ];
    }
    
    /**
     * Get push settings as array
     * @return array
     */
    public function getPushSettings(): array
    {
        return [
            'booking_created' => $this->push_booking_created,
            'booking_approved' => $this->push_booking_approved,
            'booking_rejected' => $this->push_booking_rejected,
            'booking_cancelled' => $this->push_booking_cancelled,
            'booking_reminder' => $this->push_booking_reminder,
            'pending_approval' => $this->push_pending_approval,
        ];
    }
    
    /**
     * Get calendar sync settings as array
     * @return array
     */
    public function getCalendarSettings(): array
    {
        return [
            'google' => [
                'enabled' => $this->calendar_sync_google,
            ],
            'microsoft' => [
                'enabled' => $this->calendar_sync_microsoft,
            ],
            'auto_sync' => $this->calendar_auto_sync,
        ];
    }
    
    /**
     * Update from form data
     * @param array $data Form data
     * @return bool
     */
    public function updateFromForm(array $data): bool
    {
        // Email settings
        if (isset($data['email'])) {
            foreach ($data['email'] as $key => $value) {
                $attribute = 'email_' . $key;
                if ($this->hasAttribute($attribute)) {
                    $this->$attribute = (bool)$value;
                }
            }
        }
        
        // Push settings
        if (isset($data['push'])) {
            foreach ($data['push'] as $key => $value) {
                $attribute = 'push_' . $key;
                if ($this->hasAttribute($attribute)) {
                    $this->$attribute = (bool)$value;
                }
            }
        }
        
        // Calendar sync settings
        if (isset($data['calendar'])) {
            if (isset($data['calendar']['google'])) {
                $this->calendar_sync_google = (bool)$data['calendar']['google'];
            }
            if (isset($data['calendar']['microsoft'])) {
                $this->calendar_sync_microsoft = (bool)$data['calendar']['microsoft'];
            }
            if (isset($data['calendar']['auto_sync'])) {
                $this->calendar_auto_sync = (bool)$data['calendar']['auto_sync'];
            }
        }
        
        // Reminder settings
        if (isset($data['reminder'])) {
            if (isset($data['reminder']['minutes_before'])) {
                $this->reminder_minutes_before = (int)$data['reminder']['minutes_before'];
            }
            if (isset($data['reminder']['email'])) {
                $this->reminder_email = (bool)$data['reminder']['email'];
            }
            if (isset($data['reminder']['push'])) {
                $this->reminder_push = (bool)$data['reminder']['push'];
            }
        }
        
        // Quiet hours
        if (isset($data['quiet_hours'])) {
            $this->quiet_hours_enabled = (bool)($data['quiet_hours']['enabled'] ?? false);
            $this->quiet_hours_start = $data['quiet_hours']['start'] ?? null;
            $this->quiet_hours_end = $data['quiet_hours']['end'] ?? null;
        }
        
        // Realtime
        if (isset($data['realtime_enabled'])) {
            $this->realtime_enabled = (bool)$data['realtime_enabled'];
        }
        
        return $this->save();
    }
}
