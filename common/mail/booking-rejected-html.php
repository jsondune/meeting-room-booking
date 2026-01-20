<?php
/**
 * @var yii\web\View $this
 * @var common\models\Booking $booking
 * @var string $reason
 */

use yii\helpers\Html;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองไม่ได้รับการอนุมัติ</title>
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
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
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
        .rejection-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .rejection-box h3 {
            color: #721c24;
            margin-top: 0;
        }
        .rejection-box p {
            color: #721c24;
            margin-bottom: 0;
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
        }
        .btn-primary {
            background-color: #667eea;
            color: white !important;
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
            <h1>❌ การจองไม่ได้รับการอนุมัติ</h1>
        </div>
        
        <div class="email-body">
            <p>เรียน คุณ<?= Html::encode($booking->user->fullname) ?>,</p>
            
            <p>ขออภัย การจองห้องประชุมของท่านไม่ได้รับการอนุมัติ</p>
            
            <div class="rejection-box">
                <h3>📝 เหตุผลในการปฏิเสธ</h3>
                <p><?= Html::encode($reason ?? $booking->cancellation_reason ?? 'ไม่ระบุเหตุผล') ?></p>
            </div>
            
            <div class="info-card">
                <h4 style="margin-top: 0;">รายละเอียดการจองที่ถูกปฏิเสธ</h4>
                <div class="info-row">
                    <span class="info-label">รหัสการจอง</span>
                    <span class="info-value"><?= Html::encode($booking->booking_code) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">ห้องประชุม</span>
                    <span class="info-value"><?= Html::encode($booking->room->name_th) ?></span>
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
            
            <p>ท่านสามารถทำการจองใหม่ได้โดยเลือกห้องประชุมหรือเวลาอื่น</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['booking/create']) ?>" class="btn btn-primary">
                    จองห้องประชุมใหม่
                </a>
            </div>
            
            <p style="color: #6c757d; font-size: 14px;">
                หากท่านมีข้อสงสัยเกี่ยวกับการปฏิเสธการจอง กรุณาติดต่อผู้ดูแลระบบ
            </p>
        </div>
        
        <div class="email-footer">
            <p>ระบบจองห้องประชุม - Meeting Room Booking System</p>
            <p>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</p>
        </div>
    </div>
</body>
</html>
