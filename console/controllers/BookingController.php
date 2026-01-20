<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use common\models\Booking;
use common\models\Notification;

/**
 * Console commands for booking management
 */
class BookingController extends Controller
{
    /**
     * Send booking reminders
     * 
     * @param string $type Reminder type: 1day, 1hour, 30min
     * @return int Exit code
     */
    public function actionSendReminders($type = '1day')
    {
        $this->stdout("Starting booking reminders for type: {$type}\n");

        $today = date('Y-m-d');
        $now = time();

        $query = Booking::find()
            ->where(['status' => Booking::STATUS_APPROVED])
            ->with(['user', 'room', 'room.building']);

        switch ($type) {
            case '1day':
                // Bookings tomorrow
                $targetDate = date('Y-m-d', strtotime('+1 day'));
                $query->andWhere(['booking_date' => $targetDate]);
                $reminderType = '1day';
                break;

            case '1hour':
                // Bookings starting in 1 hour
                $targetDate = $today;
                $targetTimeStart = date('H:i:s', strtotime('+55 minutes'));
                $targetTimeEnd = date('H:i:s', strtotime('+65 minutes'));
                $query->andWhere(['booking_date' => $targetDate])
                    ->andWhere(['between', 'start_time', $targetTimeStart, $targetTimeEnd]);
                $reminderType = '1hour';
                break;

            case '30min':
                // Bookings starting in 30 minutes
                $targetDate = $today;
                $targetTimeStart = date('H:i:s', strtotime('+25 minutes'));
                $targetTimeEnd = date('H:i:s', strtotime('+35 minutes'));
                $query->andWhere(['booking_date' => $targetDate])
                    ->andWhere(['between', 'start_time', $targetTimeStart, $targetTimeEnd]);
                $reminderType = '30min';
                break;

            case 'morning':
                // Today's bookings - send in the morning
                $query->andWhere(['booking_date' => $today]);
                $reminderType = 'morning';
                break;

            default:
                $this->stderr("Unknown reminder type: {$type}\n");
                return ExitCode::UNSPECIFIED_ERROR;
        }

        $bookings = $query->all();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Check if reminder already sent
                $reminderKey = "reminder_{$type}_{$booking->id}";
                if (Yii::$app->cache->get($reminderKey)) {
                    $this->stdout("  - Skipping booking #{$booking->id} (already reminded)\n");
                    continue;
                }

                // Send email reminder
                if ($this->sendReminderEmail($booking, $reminderType)) {
                    // Create notification
                    $this->createReminderNotification($booking, $reminderType);

                    // Mark as reminded
                    Yii::$app->cache->set($reminderKey, true, 86400); // 24 hours

                    $sentCount++;
                    $this->stdout("  ✓ Sent reminder for booking #{$booking->id} ({$booking->booking_code})\n");
                } else {
                    $failedCount++;
                    $this->stderr("  ✗ Failed to send reminder for booking #{$booking->id}\n");
                }
            } catch (\Exception $e) {
                $failedCount++;
                $this->stderr("  ✗ Error for booking #{$booking->id}: {$e->getMessage()}\n");
            }
        }

        $this->stdout("\nReminder summary: {$sentCount} sent, {$failedCount} failed\n");

        return ExitCode::OK;
    }

    /**
     * Auto-complete bookings that have ended
     * 
     * @return int Exit code
     */
    public function actionAutoComplete()
    {
        $this->stdout("Starting auto-complete for ended bookings...\n");

        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Find approved bookings that have ended
        $bookings = Booking::find()
            ->where(['status' => Booking::STATUS_APPROVED])
            ->andWhere([
                'or',
                ['<', 'booking_date', $today],
                [
                    'and',
                    ['=', 'booking_date', $today],
                    ['<', 'end_time', $now],
                ],
            ])
            ->all();

        $completedCount = 0;

        foreach ($bookings as $booking) {
            $booking->status = Booking::STATUS_COMPLETED;
            $booking->actual_end_time = $booking->actual_end_time ?: date('Y-m-d H:i:s');

            if ($booking->save(false)) {
                $completedCount++;
                $this->stdout("  ✓ Completed booking #{$booking->id} ({$booking->booking_code})\n");
            }
        }

        $this->stdout("\nAuto-completed {$completedCount} bookings\n");

        return ExitCode::OK;
    }

    /**
     * Auto-cancel no-show bookings
     * 
     * @param int $minutes Minutes after start time to mark as no-show
     * @return int Exit code
     */
    public function actionAutoCancel($minutes = 30)
    {
        $this->stdout("Starting auto-cancel for no-show bookings...\n");

        $today = date('Y-m-d');
        $cutoffTime = date('H:i:s', strtotime("-{$minutes} minutes"));

        // Find approved bookings that weren't checked in
        $bookings = Booking::find()
            ->where(['status' => Booking::STATUS_APPROVED])
            ->andWhere(['booking_date' => $today])
            ->andWhere(['<', 'start_time', $cutoffTime])
            ->andWhere(['actual_start_time' => null])
            ->all();

        $cancelledCount = 0;

        foreach ($bookings as $booking) {
            $booking->status = Booking::STATUS_CANCELLED;
            $booking->cancellation_reason = "ยกเลิกอัตโนมัติ - ไม่มาใช้ห้อง (No-show หลังจาก {$minutes} นาที)";
            $booking->cancelled_at = date('Y-m-d H:i:s');

            if ($booking->save(false)) {
                $cancelledCount++;
                $this->stdout("  ✓ Cancelled booking #{$booking->id} ({$booking->booking_code}) - No-show\n");

                // Notify user
                $this->createNoShowNotification($booking);
            }
        }

        $this->stdout("\nAuto-cancelled {$cancelledCount} no-show bookings\n");

        return ExitCode::OK;
    }

    /**
     * Cleanup old bookings data
     * 
     * @param int $months Months to keep
     * @return int Exit code
     */
    public function actionCleanup($months = 12)
    {
        $this->stdout("Starting cleanup for bookings older than {$months} months...\n");

        $cutoffDate = date('Y-m-d', strtotime("-{$months} months"));

        // Soft delete or archive old completed/cancelled bookings
        $count = Booking::updateAll(
            ['is_archived' => 1],
            [
                'and',
                ['<', 'booking_date', $cutoffDate],
                ['in', 'status', [Booking::STATUS_COMPLETED, Booking::STATUS_CANCELLED]],
                ['is_archived' => 0],
            ]
        );

        $this->stdout("Archived {$count} old bookings\n");

        return ExitCode::OK;
    }

    /**
     * Generate daily booking report
     * 
     * @param string|null $date Date to generate report for (default: yesterday)
     * @return int Exit code
     */
    public function actionDailyReport($date = null)
    {
        $reportDate = $date ?: date('Y-m-d', strtotime('-1 day'));
        $this->stdout("Generating daily report for {$reportDate}...\n");

        $stats = [
            'total' => Booking::find()->where(['booking_date' => $reportDate])->count(),
            'completed' => Booking::find()->where(['booking_date' => $reportDate, 'status' => Booking::STATUS_COMPLETED])->count(),
            'cancelled' => Booking::find()->where(['booking_date' => $reportDate, 'status' => Booking::STATUS_CANCELLED])->count(),
            'no_show' => Booking::find()
                ->where(['booking_date' => $reportDate, 'status' => Booking::STATUS_CANCELLED])
                ->andWhere(['like', 'cancellation_reason', 'No-show'])
                ->count(),
        ];

        // Calculate utilization
        $totalHours = Booking::find()
            ->where(['booking_date' => $reportDate, 'status' => Booking::STATUS_COMPLETED])
            ->sum('TIMESTAMPDIFF(HOUR, start_time, end_time)');

        $stats['total_hours'] = (int)$totalHours;

        $this->stdout("\n=== Daily Report: {$reportDate} ===\n");
        $this->stdout("Total Bookings: {$stats['total']}\n");
        $this->stdout("Completed: {$stats['completed']}\n");
        $this->stdout("Cancelled: {$stats['cancelled']}\n");
        $this->stdout("No-show: {$stats['no_show']}\n");
        $this->stdout("Total Hours Used: {$stats['total_hours']}\n");
        $this->stdout("=============================\n");

        // TODO: Send report email to admins

        return ExitCode::OK;
    }

    /**
     * Send reminder email
     * 
     * @param Booking $booking
     * @param string $reminderType
     * @return bool
     */
    protected function sendReminderEmail($booking, $reminderType)
    {
        if (!$booking->user || !$booking->user->email) {
            return false;
        }

        try {
            return Yii::$app->mailer->compose([
                'html' => 'booking-reminder-html',
            ], [
                'booking' => $booking,
                'reminderType' => $reminderType,
            ])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setTo($booking->user->email)
                ->setSubject('⏰ เตือนการประชุม: ' . $booking->title)
                ->send();
        } catch (\Exception $e) {
            Yii::error('Failed to send reminder email: ' . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    /**
     * Create reminder notification
     * 
     * @param Booking $booking
     * @param string $reminderType
     */
    protected function createReminderNotification($booking, $reminderType)
    {
        $messages = [
            '1day' => 'การประชุมของคุณจะเริ่มในวันพรุ่งนี้',
            '1hour' => 'การประชุมของคุณจะเริ่มใน 1 ชั่วโมง',
            '30min' => 'การประชุมของคุณจะเริ่มใน 30 นาที',
            'morning' => 'คุณมีการประชุมวันนี้',
        ];

        $notification = new Notification();
        $notification->user_id = $booking->user_id;
        $notification->type = 'booking_reminder';
        $notification->title = 'เตือนการประชุม';
        $notification->message = $messages[$reminderType] . ': ' . $booking->title;
        $notification->icon = 'bi-alarm';
        $notification->url = '/booking/view?id=' . $booking->id;
        $notification->data = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'reminder_type' => $reminderType,
        ];
        $notification->save(false);
    }

    /**
     * Create no-show notification
     * 
     * @param Booking $booking
     */
    protected function createNoShowNotification($booking)
    {
        $notification = new Notification();
        $notification->user_id = $booking->user_id;
        $notification->type = 'booking_cancelled';
        $notification->title = 'การจองถูกยกเลิก';
        $notification->message = "การจอง {$booking->booking_code} ถูกยกเลิกเนื่องจากไม่มาใช้ห้อง";
        $notification->icon = 'bi-x-circle';
        $notification->url = '/booking/view?id=' . $booking->id;
        $notification->save(false);
    }
}
