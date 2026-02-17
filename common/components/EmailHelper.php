<?php
/**
 * EmailHelper - Email sending utility
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace common\components;

use Yii;
use common\models\Booking;
use common\models\User;
use common\models\EmailTemplate;
use common\models\SystemSetting;
use common\models\EmailLog;

/**
 * EmailHelper provides methods for sending various email types
 */
class EmailHelper
{
    /**
     * Email template types
     */
    const TEMPLATE_WELCOME = 'welcome';
    const TEMPLATE_BOOKING_CONFIRMATION = 'booking_confirmation';
    const TEMPLATE_BOOKING_APPROVED = 'booking_approved';
    const TEMPLATE_BOOKING_REJECTED = 'booking_rejected';
    const TEMPLATE_BOOKING_CANCELLED = 'booking_cancelled';
    const TEMPLATE_BOOKING_REMINDER = 'booking_reminder';
    const TEMPLATE_PASSWORD_RESET = 'password_reset';
    const TEMPLATE_EMAIL_VERIFICATION = 'email_verification';

    /**
     * Send welcome email to new user
     * @param User $user
     * @return bool
     */
    public static function sendWelcomeEmail(User $user)
    {
        $template = self::getTemplate(self::TEMPLATE_WELCOME);
        
        return self::send(
            $user->email,
            $template['subject'] ?? 'ยินดีต้อนรับสู่ระบบจองห้องประชุม',
            'welcome-html',
            [
                'user' => $user,
                'loginUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/site/login']),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send booking confirmation email
     * @param Booking $booking
     * @return bool
     */
    public static function sendBookingConfirmation(Booking $booking)
    {
        $user = $booking->user;
        if (!$user || !$user->email) {
            return false;
        }

        $template = self::getTemplate(self::TEMPLATE_BOOKING_CONFIRMATION);

        return self::send(
            $user->email,
            $template['subject'] ?? "ยืนยันการจอง: {$booking->booking_code}",
            'booking-confirmation-html',
            [
                'booking' => $booking,
                'user' => $user,
                'room' => $booking->room,
                'viewUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/booking/view', 'id' => $booking->id]),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send booking approved email
     * @param Booking $booking
     * @return bool
     */
    public static function sendBookingApproved(Booking $booking)
    {
        $user = $booking->user;
        if (!$user || !$user->email) {
            return false;
        }

        $template = self::getTemplate(self::TEMPLATE_BOOKING_APPROVED);

        return self::send(
            $user->email,
            $template['subject'] ?? "การจองได้รับการอนุมัติ: {$booking->booking_code}",
            'booking-approved-html',
            [
                'booking' => $booking,
                'user' => $user,
                'room' => $booking->room,
                'approver' => $booking->approver,
                'viewUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/booking/view', 'id' => $booking->id]),
                'calendarUrl' => self::generateCalendarUrl($booking),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send booking rejected email
     * @param Booking $booking
     * @param string $reason
     * @return bool
     */
    public static function sendBookingRejected(Booking $booking, $reason = '')
    {
        $user = $booking->user;
        if (!$user || !$user->email) {
            return false;
        }

        $template = self::getTemplate(self::TEMPLATE_BOOKING_REJECTED);

        return self::send(
            $user->email,
            $template['subject'] ?? "การจองถูกปฏิเสธ: {$booking->booking_code}",
            'booking-rejected-html',
            [
                'booking' => $booking,
                'user' => $user,
                'room' => $booking->room,
                'reason' => $reason ?: $booking->rejection_reason,
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send booking cancelled email
     * @param Booking $booking
     * @param string $reason
     * @return bool
     */
    public static function sendBookingCancelled(Booking $booking, $reason = '')
    {
        $user = $booking->user;
        if (!$user || !$user->email) {
            return false;
        }

        $template = self::getTemplate(self::TEMPLATE_BOOKING_CANCELLED);

        return self::send(
            $user->email,
            $template['subject'] ?? "การจองถูกยกเลิก: {$booking->booking_code}",
            'booking-cancelled-html',
            [
                'booking' => $booking,
                'user' => $user,
                'room' => $booking->room,
                'reason' => $reason,
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send booking reminder email
     * @param Booking $booking
     * @param int $minutesBefore
     * @return bool
     */
    public static function sendBookingReminder(Booking $booking, $minutesBefore = 30)
    {
        $user = $booking->user;
        if (!$user || !$user->email) {
            return false;
        }

        $template = self::getTemplate(self::TEMPLATE_BOOKING_REMINDER);

        $timeStr = $minutesBefore >= 60 
            ? ($minutesBefore / 60) . ' ชั่วโมง' 
            : $minutesBefore . ' นาที';

        return self::send(
            $user->email,
            $template['subject'] ?? "แจ้งเตือน: การประชุมจะเริ่มในอีก {$timeStr}",
            'booking-reminder-html',
            [
                'booking' => $booking,
                'user' => $user,
                'room' => $booking->room,
                'minutesBefore' => $minutesBefore,
                'timeStr' => $timeStr,
                'viewUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/booking/view', 'id' => $booking->id]),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send password reset email
     * @param User $user
     * @param string $token
     * @return bool
     */
    public static function sendPasswordReset(User $user, $token)
    {
        $template = self::getTemplate(self::TEMPLATE_PASSWORD_RESET);

        return self::send(
            $user->email,
            $template['subject'] ?? 'รีเซ็ตรหัสผ่าน',
            'password-reset-html',
            [
                'user' => $user,
                'resetUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/site/reset-password', 'token' => $token]),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
                'expireHours' => SystemSetting::getValue('password_reset_expire_hours', 24),
            ]
        );
    }

    /**
     * Send email verification email
     * @param User $user
     * @param string $token
     * @return bool
     */
    public static function sendEmailVerification(User $user, $token)
    {
        $template = self::getTemplate(self::TEMPLATE_EMAIL_VERIFICATION);

        return self::send(
            $user->email,
            $template['subject'] ?? 'ยืนยันอีเมลของคุณ',
            'email-verification-html',
            [
                'user' => $user,
                'verifyUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/site/verify-email', 'token' => $token]),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send approval request email to approver
     * @param Booking $booking
     * @param User $approver
     * @return bool
     */
    public static function sendApprovalRequest(Booking $booking, User $approver)
    {
        if (!$approver->email) {
            return false;
        }

        return self::send(
            $approver->email,
            "มีการจองใหม่รอพิจารณา: {$booking->booking_code}",
            'approval-request-html',
            [
                'booking' => $booking,
                'approver' => $approver,
                'user' => $booking->user,
                'room' => $booking->room,
                'approveUrl' => Yii::$app->urlManager->createAbsoluteUrl(['/approval/view', 'id' => $booking->id]),
                'appName' => SystemSetting::getValue('app_name', 'Meeting Room Booking'),
            ]
        );
    }

    /**
     * Send custom email
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param array $attachments
     * @return bool
     */
    public static function sendCustom($to, $subject, $body, $attachments = [])
    {
        return self::sendRaw($to, $subject, $body, true, $attachments);
    }

    /**
     * Get email template
     * @param string $type
     * @return array
     */
    protected static function getTemplate($type)
    {
        // Try to get from database first
        $template = EmailTemplate::findOne(['slug' => $type, 'is_active' => 1]);
        
        if ($template) {
            return [
                'subject' => $template->subject,
                'body' => $template->body,
            ];
        }

        // Return default
        return [
            'subject' => null,
            'body' => null,
        ];
    }

    /**
     * Send email using Yii mailer
     * @param string|array $to
     * @param string $subject
     * @param string $view
     * @param array $params
     * @return bool
     */
    protected static function send($to, $subject, $view, $params = [])
    {
        // Check if email is enabled
        if (!SystemSetting::getValue('email_enabled', true)) {
            return true; // Return true to not break flow
        }

        try {
            $mailer = Yii::$app->mailer;
            $message = $mailer->compose(['html' => $view], $params)
                ->setTo($to)
                ->setSubject($subject);

            // Set from address
            $fromEmail = SystemSetting::getValue('email_from_address', 'noreply@example.com');
            $fromName = SystemSetting::getValue('email_from_name', 'Meeting Room Booking');
            $message->setFrom([$fromEmail => $fromName]);

            // Send
            $result = $message->send();

            // Log email
            self::logEmail($to, $subject, $view, $result);

            return $result;
        } catch (\Exception $e) {
            Yii::error('Email error: ' . $e->getMessage());
            self::logEmail($to, $subject, $view, false, $e->getMessage());
            return false;
        }
    }

    /**
     * Send raw email without template
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param bool $isHtml
     * @param array $attachments
     * @return bool
     */
    protected static function sendRaw($to, $subject, $body, $isHtml = true, $attachments = [])
    {
        if (!SystemSetting::getValue('email_enabled', true)) {
            return true;
        }

        try {
            $mailer = Yii::$app->mailer;
            
            if ($isHtml) {
                $message = $mailer->compose()
                    ->setHtmlBody($body);
            } else {
                $message = $mailer->compose()
                    ->setTextBody($body);
            }

            $message->setTo($to)->setSubject($subject);

            // Set from
            $fromEmail = SystemSetting::getValue('email_from_address', 'noreply@example.com');
            $fromName = SystemSetting::getValue('email_from_name', 'Meeting Room Booking');
            $message->setFrom([$fromEmail => $fromName]);

            // Add attachments
            foreach ($attachments as $attachment) {
                if (is_array($attachment)) {
                    $message->attach($attachment['path'], [
                        'fileName' => $attachment['name'] ?? basename($attachment['path']),
                        'contentType' => $attachment['type'] ?? null,
                    ]);
                } else {
                    $message->attach($attachment);
                }
            }

            $result = $message->send();
            self::logEmail($to, $subject, 'raw', $result);

            return $result;
        } catch (\Exception $e) {
            Yii::error('Email error: ' . $e->getMessage());
            self::logEmail($to, $subject, 'raw', false, $e->getMessage());
            return false;
        }
    }

    /**
     * Log email sending
     * @param string|array $to
     * @param string $subject
     * @param string $template
     * @param bool $success
     * @param string|null $error
     */
    protected static function logEmail($to, $subject, $template, $success, $error = null)
    {
        try {
            // Check if EmailLog model exists
            if (!class_exists('common\models\EmailLog')) {
                return;
            }

            $log = new EmailLog();
            $log->to_email = is_array($to) ? implode(', ', $to) : $to;
            $log->subject = $subject;
            $log->template = $template;
            $log->status = $success ? 'sent' : 'failed';
            $log->error_message = $error;
            $log->created_at = date('Y-m-d H:i:s');
            $log->save(false);
        } catch (\Exception $e) {
            Yii::error('Email log error: ' . $e->getMessage());
        }
    }

    /**
     * Generate Google Calendar URL for booking
     * @param Booking $booking
     * @return string
     */
    protected static function generateCalendarUrl(Booking $booking)
    {
        $title = urlencode($booking->title);
        $location = urlencode($booking->room->name ?? 'ห้องประชุม');
        $details = urlencode("รหัสการจอง: {$booking->booking_code}\n{$booking->description}");
        
        $startDateTime = new \DateTime($booking->booking_date . ' ' . $booking->start_time);
        $endDateTime = new \DateTime($booking->booking_date . ' ' . $booking->end_time);
        
        $start = $startDateTime->format('Ymd\THis');
        $end = $endDateTime->format('Ymd\THis');

        return "https://calendar.google.com/calendar/render?action=TEMPLATE" .
               "&text={$title}" .
               "&dates={$start}/{$end}" .
               "&details={$details}" .
               "&location={$location}";
    }

    /**
     * Generate ICS calendar file content
     * @param Booking $booking
     * @return string
     */
    public static function generateIcsContent(Booking $booking)
    {
        $uid = $booking->booking_code . '@' . Yii::$app->request->serverName;
        $dtstamp = gmdate('Ymd\THis\Z');
        
        $startDateTime = new \DateTime($booking->booking_date . ' ' . $booking->start_time, new \DateTimeZone('Asia/Bangkok'));
        $endDateTime = new \DateTime($booking->booking_date . ' ' . $booking->end_time, new \DateTimeZone('Asia/Bangkok'));
        
        $startDateTime->setTimezone(new \DateTimeZone('UTC'));
        $endDateTime->setTimezone(new \DateTimeZone('UTC'));
        
        $dtstart = $startDateTime->format('Ymd\THis\Z');
        $dtend = $endDateTime->format('Ymd\THis\Z');

        $description = "รหัสการจอง: {$booking->booking_code}\\n";
        $description .= "ห้องประชุม: " . ($booking->room->name ?? '') . "\\n";
        $description .= "ผู้จอง: " . ($booking->user->full_name ?? '') . "\\n";
        if ($booking->description) {
            $description .= "\\n" . str_replace("\n", "\\n", $booking->description);
        }

        return "BEGIN:VCALENDAR\r\n" .
               "VERSION:2.0\r\n" .
               "PRODID:-//Meeting Room Booking//EN\r\n" .
               "METHOD:PUBLISH\r\n" .
               "BEGIN:VEVENT\r\n" .
               "UID:{$uid}\r\n" .
               "DTSTAMP:{$dtstamp}\r\n" .
               "DTSTART:{$dtstart}\r\n" .
               "DTEND:{$dtend}\r\n" .
               "SUMMARY:{$booking->title}\r\n" .
               "DESCRIPTION:{$description}\r\n" .
               "LOCATION:" . ($booking->room->name ?? '') . "\r\n" .
               "STATUS:CONFIRMED\r\n" .
               "END:VEVENT\r\n" .
               "END:VCALENDAR\r\n";
    }
}
