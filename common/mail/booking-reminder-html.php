<?php
/**
 * @var yii\web\View $this
 * @var common\models\Booking $booking
 * @var string $reminderType (1hour, 1day, etc.)
 */

use yii\helpers\Html;

$reminderText = [
    '1hour' => '1 ชั่วโมง',
    '30min' => '30 นาที',
    '1day' => '1 วัน',
    'morning' => 'เช้านี้',
][$reminderType] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เตือนการประชุม</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f7fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .email-body {
            padding: 30px 20px;
        }
        .countdown-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .countdown-box .time {
            font-size: 36px;
            font-weight: bold;
            color: #856404;
        }
        .countdown-box .label {
            font-size: 14px;
            color: #856404;
        }
        .info-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            margin: 5px;
        }
        .btn-primary {
            background-color: #ffc107;
            color: #333 !important;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white !important;
        }
        .checklist {
            background-color: #e7f5ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .checklist h3 {
            margin-top: 0;
            color: #0077b6;
        }
        .checklist ul {
            margin: 0;
            padding-left: 20px;
        }
        .checklist li {
            margin: 8px 0;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 150px;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>⏰ การประชุมของคุณกำลังจะเริ่มแล้ว!</h1>
        </div>
        
        <div class="email-body">
            <p>เรียน คุณ<?= Html::encode($booking->user->fullname) ?>,</p>
            
            <p>เราขอเตือนว่าการประชุมของท่านกำลังจะเริ่มใน <strong><?= $reminderText ?></strong></p>
            
            <div class="countdown-box">
                <p class="label">เริ่มประชุมใน</p>
                <p class="time"><?= $reminderText ?></p>
            </div>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">รหัสการจอง</span>
                    <span class="info-value"><?= Html::encode($booking->booking_code) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">หัวข้อ</span>
                    <span class="info-value"><?= Html::encode($booking->title) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">ห้องประชุม</span>
                    <span class="info-value"><?= Html::encode($booking->room->name_th) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">สถานที่</span>
                    <span class="info-value"><?= Html::encode($booking->room->building->name_th ?? '-') ?> ชั้น <?= $booking->room->floor ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">วันที่</span>
                    <span class="info-value"><?= Yii::$app->formatter->asDate($booking->booking_date) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">เวลา</span>
                    <span class="info-value"><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?> น.</span>
                </div>
                <div class="info-row">
                    <span class="info-label">จำนวนผู้เข้าร่วม</span>
                    <span class="info-value"><?= $booking->attendees_count ?> คน</span>
                </div>
            </div>
            
            <div class="qr-code">
                <p><strong>QR Code สำหรับเช็คอิน</strong></p>
                <img src="<?= $booking->getQrCodeUrl() ?>" alt="QR Code">
            </div>
            
            <div class="checklist">
                <h3>✅ สิ่งที่ควรเตรียม</h3>
                <ul>
                    <li>ตรวจสอบอุปกรณ์นำเสนอ (laptop, adapter)</li>
                    <li>เอกสารประกอบการประชุม</li>
                    <li>เตรียม link สำหรับผู้เข้าร่วมออนไลน์ (ถ้ามี)</li>
                    <li>QR Code หรือรหัสการจองสำหรับเช็คอิน</li>
                </ul>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['booking/view', 'id' => $booking->id]) ?>" class="btn btn-primary">
                    ดูรายละเอียดการจอง
                </a>
                <a href="<?= $booking->getGoogleCalendarUrl() ?>" class="btn btn-secondary">
                    เปิดใน Calendar
                </a>
            </div>
            
            <?php if ($booking->room->contact_phone): ?>
            <p style="color: #6c757d; font-size: 14px; text-align: center;">
                หากต้องการความช่วยเหลือเกี่ยวกับห้องประชุม โทร <?= Html::encode($booking->room->contact_phone) ?>
            </p>
            <?php endif; ?>
        </div>
        
        <div class="email-footer">
            <p>ระบบจองห้องประชุม - Meeting Room Booking System</p>
            <p>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</p>
        </div>
    </div>
</body>
</html>
