<?php
/**
 * NotificationHelper - Notification management utility
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace common\components;

use Yii;
use common\models\Notification;
use common\models\User;
use common\models\Booking;
use common\models\SystemSetting;

/**
 * NotificationHelper provides methods for creating and managing notifications
 */
class NotificationHelper
{
    /**
     * Notification types
     */
    const TYPE_BOOKING_CREATED = 'booking_created';
    const TYPE_BOOKING_APPROVED = 'booking_approved';
    const TYPE_BOOKING_REJECTED = 'booking_rejected';
    const TYPE_BOOKING_CANCELLED = 'booking_cancelled';
    const TYPE_BOOKING_REMINDER = 'booking_reminder';
    const TYPE_BOOKING_MODIFIED = 'booking_modified';
    const TYPE_APPROVAL_REQUEST = 'approval_request';
    const TYPE_APPROVAL_ASSIGNMENT = 'approval_assignment';
    const TYPE_SYSTEM_ALERT = 'system_alert';
    const TYPE_ACCOUNT_ALERT = 'account_alert';

    /**
     * Create booking created notification
     * @param Booking $booking
     * @return bool
     */
    public static function notifyBookingCreated(Booking $booking)
    {
        // Notify booking owner
        $result = self::create(
            $booking->user_id,
            self::TYPE_BOOKING_CREATED,
            'การจองสำเร็จ',
            "การจอง {$booking->booking_code} ของคุณถูกสร้างเรียบร้อยแล้ว รอการอนุมัติ",
            [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'room_name' => $booking->room->name ?? '',
                'booking_date' => $booking->booking_date,
            ]
        );

        // Notify approvers
        self::notifyApprovers($booking, 'มีการจองใหม่รอพิจารณา', 
            "การจอง {$booking->booking_code} จาก {$booking->user->full_name} รอการพิจารณา");

        return $result;
    }

    /**
     * Create booking approved notification
     * @param Booking $booking
     * @return bool
     */
    public static function notifyBookingApproved(Booking $booking)
    {
        return self::create(
            $booking->user_id,
            self::TYPE_BOOKING_APPROVED,
            'การจองได้รับการอนุมัติ',
            "การจอง {$booking->booking_code} ได้รับการอนุมัติแล้ว",
            [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'room_name' => $booking->room->name ?? '',
                'booking_date' => $booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
            ]
        );
    }

    /**
     * Create booking rejected notification
     * @param Booking $booking
     * @param string $reason
     * @return bool
     */
    public static function notifyBookingRejected(Booking $booking, $reason = '')
    {
        return self::create(
            $booking->user_id,
            self::TYPE_BOOKING_REJECTED,
            'การจองถูกปฏิเสธ',
            "การจอง {$booking->booking_code} ถูกปฏิเสธ" . ($reason ? "\nเหตุผล: {$reason}" : ''),
            [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Create booking cancelled notification
     * @param Booking $booking
     * @param int|null $cancelledBy
     * @param string $reason
     * @return bool
     */
    public static function notifyBookingCancelled(Booking $booking, $cancelledBy = null, $reason = '')
    {
        // Notify booking owner (if cancelled by admin)
        if ($cancelledBy && $cancelledBy != $booking->user_id) {
            self::create(
                $booking->user_id,
                self::TYPE_BOOKING_CANCELLED,
                'การจองถูกยกเลิก',
                "การจอง {$booking->booking_code} ถูกยกเลิกโดยผู้ดูแลระบบ" . ($reason ? "\nเหตุผล: {$reason}" : ''),
                [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'reason' => $reason,
                ]
            );
        }

        return true;
    }

    /**
     * Create booking reminder notification
     * @param Booking $booking
     * @param int $minutesBefore Minutes before booking
     * @return bool
     */
    public static function notifyBookingReminder(Booking $booking, $minutesBefore = 30)
    {
        $timeStr = $minutesBefore >= 60 
            ? ($minutesBefore / 60) . ' ชั่วโมง' 
            : $minutesBefore . ' นาที';

        return self::create(
            $booking->user_id,
            self::TYPE_BOOKING_REMINDER,
            'แจ้งเตือนการประชุม',
            "การประชุม {$booking->title} จะเริ่มในอีก {$timeStr}",
            [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'room_name' => $booking->room->name ?? '',
                'start_time' => $booking->start_time,
                'minutes_before' => $minutesBefore,
            ]
        );
    }

    /**
     * Notify all approvers about pending booking
     * @param Booking $booking
     * @param string $title
     * @param string $message
     * @return int Number of notifications sent
     */
    public static function notifyApprovers(Booking $booking, $title, $message)
    {
        $count = 0;
        
        // Get approvers based on department
        $approvers = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->andWhere(['or',
                ['role' => 'admin'],
                ['role' => 'superadmin'],
                ['and', 
                    ['role' => 'manager'], 
                    ['department_id' => $booking->department_id]
                ],
            ])
            ->all();

        foreach ($approvers as $approver) {
            if (self::create(
                $approver->id,
                self::TYPE_APPROVAL_REQUEST,
                $title,
                $message,
                [
                    'booking_id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'user_name' => $booking->user->full_name ?? '',
                ]
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Create system alert notification
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param array $data
     * @return bool
     */
    public static function systemAlert($userId, $title, $message, $data = [])
    {
        return self::create($userId, self::TYPE_SYSTEM_ALERT, $title, $message, $data);
    }

    /**
     * Broadcast system alert to all users
     * @param string $title
     * @param string $message
     * @param array $data
     * @param array $roles Roles to notify (empty = all)
     * @return int Number of notifications sent
     */
    public static function broadcastSystemAlert($title, $message, $data = [], $roles = [])
    {
        $count = 0;
        
        $query = User::find()->where(['status' => User::STATUS_ACTIVE]);
        
        if (!empty($roles)) {
            $query->andWhere(['in', 'role', $roles]);
        }

        foreach ($query->batch(100) as $users) {
            foreach ($users as $user) {
                if (self::systemAlert($user->id, $title, $message, $data)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Create a notification
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $data
     * @return bool
     */
    public static function create($userId, $type, $title, $message, $data = [])
    {
        // Check if user wants this notification type
        if (!self::shouldNotify($userId, $type)) {
            return false;
        }

        try {
            $notification = new Notification();
            $notification->user_id = $userId;
            $notification->type = $type;
            $notification->title = $title;
            $notification->message = $message;
            $notification->data = json_encode($data);
            $notification->is_read = 0;
            $notification->created_at = date('Y-m-d H:i:s');
            
            if ($notification->save()) {
                // Trigger push notification if enabled
                if (SystemSetting::getValue('push_notifications_enabled', false)) {
                    self::sendPushNotification($userId, $title, $message, $data);
                }
                
                return true;
            }
            
            Yii::error('Failed to save notification: ' . json_encode($notification->errors));
            return false;
        } catch (\Exception $e) {
            Yii::error('Notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if user should receive notification type
     * @param int $userId
     * @param string $type
     * @return bool
     */
    protected static function shouldNotify($userId, $type)
    {
        $user = User::findOne($userId);
        if (!$user) {
            return false;
        }

        // Get user notification preferences
        $preferences = $user->notification_preferences ?? [];
        if (is_string($preferences)) {
            $preferences = json_decode($preferences, true) ?? [];
        }

        // Default to enabled if not set
        return $preferences[$type] ?? true;
    }

    /**
     * Send push notification (placeholder for actual implementation)
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param array $data
     */
    protected static function sendPushNotification($userId, $title, $message, $data = [])
    {
        // TODO: Implement push notification via Firebase/OneSignal/etc.
        // This is a placeholder for future implementation
        
        $user = User::findOne($userId);
        if (!$user || !$user->push_token) {
            return;
        }

        // Example Firebase implementation:
        // $fcm = new FirebaseCloudMessaging();
        // $fcm->sendToDevice($user->push_token, [
        //     'notification' => [
        //         'title' => $title,
        //         'body' => $message,
        //     ],
        //     'data' => $data,
        // ]);
    }

    /**
     * Mark notification as read
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public static function markAsRead($notificationId, $userId)
    {
        return Notification::updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['id' => $notificationId, 'user_id' => $userId]
        ) > 0;
    }

    /**
     * Mark all notifications as read for user
     * @param int $userId
     * @return int Number of notifications marked
     */
    public static function markAllAsRead($userId)
    {
        return Notification::updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => $userId, 'is_read' => 0]
        );
    }

    /**
     * Get unread count for user
     * @param int $userId
     * @return int
     */
    public static function getUnreadCount($userId)
    {
        return (int) Notification::find()
            ->where(['user_id' => $userId, 'is_read' => 0])
            ->count();
    }

    /**
     * Get recent notifications for user
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public static function getRecent($userId, $limit = 10)
    {
        return Notification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Delete old notifications
     * @param int $daysOld Delete notifications older than this
     * @return int Number deleted
     */
    public static function deleteOld($daysOld = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        
        return Notification::deleteAll([
            'and',
            ['<', 'created_at', $cutoff],
            ['is_read' => 1],
        ]);
    }
}
