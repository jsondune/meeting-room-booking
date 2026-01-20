<?php
/**
 * @var yii\web\View $this
 * @var common\models\Booking $booking
 */

use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองได้รับการอนุมัติ</title>
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
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin: 20px 0;
        }
        .booking-code {
            background-color: #d4edda;
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .booking-code span {
            font-size: 24px;
            font-weight: bold;
            color: #155724;
            letter-spacing: 2px;
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
            background-color: #28a745;
            color: white !important;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white !important;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .qr-code img {
            max-width: 200px;
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
            <h1>✅ การจองได้รับการอนุมัติแล้ว</h1>
        </div>
        
        <div class="email-body">
            <div class="success-icon">🎉</div>
            
            <p>เรียน คุณ<?= Html::encode($booking->user->fullname) ?>,</p>
            
            <p><strong>ยินดีด้วย!</strong> การจองห้องประชุมของท่านได้รับการอนุมัติเรียบร้อยแล้ว</p>
            
            <div class="booking-code">
                <p style="margin: 0; color: #155724; font-size: 14px;">รหัสการจอง</p>
                <span><?= Html::encode($booking->booking_code) ?></span>
            </div>
            
            <div class="info-card">
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
                    <span class="info-label">หัวข้อ</span>
                    <span class="info-value"><?= Html::encode($booking->title) ?></span>
                </div>
            </div>
            
            <div class="qr-code">
                <p><strong>📱 QR Code สำหรับเช็คอิน</strong></p>
                <img src="<?= $booking->getQrCodeUrl() ?>" alt="QR Code">
                <p style="font-size: 12px; color: #6c757d;">โปรดแสดง QR Code นี้เมื่อถึงห้องประชุม<br>สามารถเช็คอินได้ก่อนเวลาเริ่ม 15 นาที</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['booking/view', 'id' => $booking->id]) ?>" class="btn btn-primary">
                    ดูรายละเอียดการจอง
                </a>
                <a href="<?= $booking->getGoogleCalendarUrl() ?>" class="btn btn-secondary">
                    เพิ่มลง Google Calendar
                </a>
            </div>
            
            <div style="background-color: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 20px;">
                <strong>📋 สิ่งที่ควรเตรียม:</strong>
                <ul style="margin: 10px 0 0; padding-left: 20px;">
                    <li>เตรียมอุปกรณ์ที่ต้องการใช้ในการประชุม</li>
                    <li>มาถึงห้องประชุมก่อนเวลาอย่างน้อย 5-10 นาที</li>
                    <li>เช็คอินด้วย QR Code หรือผ่านระบบ</li>
                </ul>
            </div>
        </div>
        
        <div class="email-footer">
            <p>ระบบจองห้องประชุม - Meeting Room Booking System</p>
            <p>อีเมลนี้ส่งโดยอัตโนมัติ โปรดอย่าตอบกลับ</p>
        </div>
    </div>
</body>
</html>
