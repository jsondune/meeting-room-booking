<?php

namespace console\components;

use Yii;
use yii\base\Component;
use yii\helpers\Json;

/**
 * Notification Bridge
 * Bridges the application with WebSocket server via Redis queue
 */
class NotificationBridge extends Component
{
    /**
     * @var string Redis key for notification queue
     */
    const QUEUE_KEY = 'ws:notifications';

    /**
     * @var string Redis key for broadcast queue
     */
    const BROADCAST_KEY = 'ws:broadcasts';

    /**
     * @var string Redis key for channel queue
     */
    const CHANNEL_KEY = 'ws:channels';

    /**
     * @var WebSocketServer|null
     */
    protected $wsServer;

    /**
     * @var \Redis|null
     */
    protected $redis;

    /**
     * Constructor
     *
     * @param WebSocketServer|null $wsServer
     * @param array $config
     */
    public function __construct(?WebSocketServer $wsServer = null, $config = [])
    {
        $this->wsServer = $wsServer;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->initRedis();
    }

    /**
     * Initialize Redis connection
     */
    protected function initRedis()
    {
        try {
            if (Yii::$app->has('redis')) {
                $this->redis = Yii::$app->redis;
            } else {
                // Create Redis connection manually
                $this->redis = new \Redis();
                $host = getenv('REDIS_HOST') ?: '127.0.0.1';
                $port = getenv('REDIS_PORT') ?: 6379;
                $this->redis->connect($host, $port);
                
                $password = getenv('REDIS_PASSWORD');
                if ($password) {
                    $this->redis->auth($password);
                }
            }
        } catch (\Exception $e) {
            Yii::warning("Redis not available: {$e->getMessage()}");
            $this->redis = null;
        }
    }

    /**
     * Queue a notification for a specific user
     *
     * @param int $userId User ID
     * @param array $data Notification data
     * @return bool
     */
    public function queueNotification(int $userId, array $data): bool
    {
        $notification = Json::encode([
            'userId' => $userId,
            'data' => $data,
            'createdAt' => time(),
        ]);

        return $this->push(self::QUEUE_KEY, $notification);
    }

    /**
     * Queue a notification for a channel
     *
     * @param string $channel Channel name
     * @param array $data Notification data
     * @return bool
     */
    public function queueChannelNotification(string $channel, array $data): bool
    {
        $notification = Json::encode([
            'channel' => $channel,
            'data' => $data,
            'createdAt' => time(),
        ]);

        return $this->push(self::CHANNEL_KEY, $notification);
    }

    /**
     * Queue a broadcast notification
     *
     * @param array $data Notification data
     * @return bool
     */
    public function queueBroadcast(array $data): bool
    {
        $notification = Json::encode([
            'data' => $data,
            'createdAt' => time(),
        ]);

        return $this->push(self::BROADCAST_KEY, $notification);
    }

    /**
     * Push to Redis queue
     *
     * @param string $key Queue key
     * @param string $data Serialized data
     * @return bool
     */
    protected function push(string $key, string $data): bool
    {
        if (!$this->redis) {
            return false;
        }

        try {
            $this->redis->lPush($key, $data);
            return true;
        } catch (\Exception $e) {
            Yii::error("Failed to push to Redis: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Process the notification queue
     * Called periodically by the WebSocket server
     */
    public function processQueue()
    {
        if (!$this->redis || !$this->wsServer) {
            return;
        }

        // Process user notifications
        $this->processUserQueue();

        // Process channel notifications
        $this->processChannelQueue();

        // Process broadcasts
        $this->processBroadcastQueue();
    }

    /**
     * Process user notification queue
     */
    protected function processUserQueue()
    {
        $maxItems = 100; // Process max 100 items per cycle
        $processed = 0;

        while ($processed < $maxItems) {
            $item = $this->redis->rPop(self::QUEUE_KEY);
            if (!$item) {
                break;
            }

            try {
                $notification = Json::decode($item);
                $this->wsServer->sendToUser(
                    $notification['userId'],
                    $notification['data']
                );
                $processed++;
            } catch (\Exception $e) {
                Yii::error("Failed to process user notification: {$e->getMessage()}");
            }
        }
    }

    /**
     * Process channel notification queue
     */
    protected function processChannelQueue()
    {
        $maxItems = 100;
        $processed = 0;

        while ($processed < $maxItems) {
            $item = $this->redis->rPop(self::CHANNEL_KEY);
            if (!$item) {
                break;
            }

            try {
                $notification = Json::decode($item);
                $this->wsServer->sendToChannel(
                    $notification['channel'],
                    $notification['data']
                );
                $processed++;
            } catch (\Exception $e) {
                Yii::error("Failed to process channel notification: {$e->getMessage()}");
            }
        }
    }

    /**
     * Process broadcast queue
     */
    protected function processBroadcastQueue()
    {
        $maxItems = 10; // Fewer broadcasts allowed per cycle
        $processed = 0;

        while ($processed < $maxItems) {
            $item = $this->redis->rPop(self::BROADCAST_KEY);
            if (!$item) {
                break;
            }

            try {
                $notification = Json::decode($item);
                $this->wsServer->broadcast($notification['data']);
                $processed++;
            } catch (\Exception $e) {
                Yii::error("Failed to process broadcast: {$e->getMessage()}");
            }
        }
    }

    // ========================
    // Convenience Methods
    // ========================

    /**
     * Notify user of booking approval
     */
    public function notifyBookingApproved(int $userId, array $bookingData): bool
    {
        return $this->queueNotification($userId, [
            'type' => 'booking_approved',
            'title' => 'การจองได้รับการอนุมัติ',
            'message' => "การจอง {$bookingData['room_name']} ได้รับการอนุมัติแล้ว",
            'icon' => 'check-circle',
            'color' => 'success',
            'booking' => $bookingData,
        ]);
    }

    /**
     * Notify user of booking rejection
     */
    public function notifyBookingRejected(int $userId, array $bookingData, string $reason = ''): bool
    {
        return $this->queueNotification($userId, [
            'type' => 'booking_rejected',
            'title' => 'การจองถูกปฏิเสธ',
            'message' => "การจอง {$bookingData['room_name']} ถูกปฏิเสธ" . ($reason ? ": {$reason}" : ''),
            'icon' => 'x-circle',
            'color' => 'danger',
            'booking' => $bookingData,
            'reason' => $reason,
        ]);
    }

    /**
     * Notify user of booking cancellation
     */
    public function notifyBookingCancelled(int $userId, array $bookingData): bool
    {
        return $this->queueNotification($userId, [
            'type' => 'booking_cancelled',
            'title' => 'การจองถูกยกเลิก',
            'message' => "การจอง {$bookingData['room_name']} ถูกยกเลิก",
            'icon' => 'x-circle',
            'color' => 'warning',
            'booking' => $bookingData,
        ]);
    }

    /**
     * Notify approvers of new pending booking
     */
    public function notifyNewPendingBooking(array $bookingData): bool
    {
        return $this->queueChannelNotification('approvals', [
            'type' => 'new_booking',
            'title' => 'การจองใหม่รอการอนุมัติ',
            'message' => "{$bookingData['user_name']} ขอจอง {$bookingData['room_name']}",
            'icon' => 'calendar-plus',
            'color' => 'info',
            'booking' => $bookingData,
        ]);
    }

    /**
     * Notify user of upcoming booking reminder
     */
    public function notifyUpcomingBooking(int $userId, array $bookingData, int $minutesBefore): bool
    {
        return $this->queueNotification($userId, [
            'type' => 'booking_reminder',
            'title' => 'แจ้งเตือนการจอง',
            'message' => "การจอง {$bookingData['room_name']} จะเริ่มใน {$minutesBefore} นาที",
            'icon' => 'bell',
            'color' => 'info',
            'booking' => $bookingData,
        ]);
    }

    /**
     * Notify admins of system event
     */
    public function notifyAdmins(string $title, string $message, string $type = 'info'): bool
    {
        return $this->queueChannelNotification('admin', [
            'type' => 'system',
            'title' => $title,
            'message' => $message,
            'icon' => 'shield',
            'color' => $type,
        ]);
    }

    /**
     * Broadcast system maintenance notification
     */
    public function broadcastMaintenance(string $message, int $minutesUntil = 0): bool
    {
        return $this->queueBroadcast([
            'type' => 'maintenance',
            'title' => 'แจ้งปิดปรับปรุงระบบ',
            'message' => $message,
            'icon' => 'wrench',
            'color' => 'warning',
            'minutesUntil' => $minutesUntil,
        ]);
    }
}
