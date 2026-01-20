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
    <title>ยืนยันการจองห้องประชุม</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .email-body {
            padding: 30px 20px;
        }
        .booking-code {
            background-color: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .booking-code span {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
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
            font-weight: 500;
        }
        .info-value {
            color: #333;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
        }
        .btn-primary {
            background-color: #667eea;
            color: white !important;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white !important;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📅 ยืนยันการจองห้องประชุม</h1>
            <p>ระบบจองห้องประชุม - Meeting Room Booking System</p>
        </div>
        
        <div class="email-body">
            <p>เรียน คุณ<?= Html::encode($booking->user->fullname) ?>,</p>
            
            <p>การจองห้องประชุมของท่านได้รับการบันทึกเรียบร้อยแล้ว</p>
            
            <div class="booking-code">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">รหัสการจอง</p>
                <span><?= Html::encode($booking->booking_code) ?></span>
            </div>
            
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">ห้องประชุม</span>
                    <span class="info-value"><?= Html::encode($booking->room->name_th) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">อาคาร</span>
                    <span class="info-value"><?= Html::encode($booking->room->building->name_th ?? '-') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">ชั้น</span>
                    <span class="info-value"><?= Html::encode($booking->room->floor) ?></span>
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
                    <span class="info-label">หัวข้อการประชุม</span>
                    <span class="info-value"><?= Html::encode($booking->title) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">จำนวนผู้เข้าร่วม</span>
                    <span class="info-value"><?= $booking->attendees_count ?> คน</span>
                </div>
                <div class="info-row">
                    <span class="info-label">สถานะ</span>
                    <span class="info-value">
                        <span class="status-badge <?= $booking->status === 'pending' ? 'status-pending' : 'status-approved' ?>">
                            <?= Html::encode($booking->getStatusLabel()) ?>
                        </span>
                    </span>
                </div>
            </div>
            
            <?php if ($booking->total_cost > 0): ?>
            <div class="info-card">
                <h3 style="margin-top: 0;">💰 รายละเอียดค่าใช้จ่าย</h3>
                <?php if ($booking->room_cost > 0): ?>
                <div class="info-row">
                    <span class="info-label">ค่าห้องประชุม</span>
                    <span class="info-value"><?= Yii::$app->formatter->asCurrency($booking->room_cost, 'THB') ?></span>
                </div>
                <?php endif; ?>
                <?php if ($booking->equipment_cost > 0): ?>
                <div class="info-row">
                    <span class="info-label">ค่าอุปกรณ์</span>
                    <span class="info-value"><?= Yii::$app->formatter->asCurrency($booking->equipment_cost, 'THB') ?></span>
                </div>
                <?php endif; ?>
                <?php if ($booking->service_cost > 0): ?>
                <div class="info-row">
                    <span class="info-label">ค่าบริการ</span>
                    <span class="info-value"><?= Yii::$app->formatter->asCurrency($booking->service_cost, 'THB') ?></span>
                </div>
                <?php endif; ?>
                <div class="info-row" style="font-size: 18px;">
                    <span class="info-label"><strong>รวมทั้งสิ้น</strong></span>
                    <span class="info-value" style="color: #667eea;"><strong><?= Yii::$app->formatter->asCurrency($booking->total_cost, 'THB') ?></strong></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($booking->status === 'approved'): ?>
            <div class="qr-code">
                <p><strong>QR Code สำหรับเช็คอิน</strong></p>
                <img src="<?= $booking->getQrCodeUrl() ?>" alt="QR Code">
                <p style="font-size: 12px; color: #6c757d;">แสดง QR Code นี้เมื่อถึงห้องประชุม</p>
            </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="<?= Yii::$app->urlManager->createAbsoluteUrl(['booking/view', 'id' => $booking->id]) ?>" class="btn btn-primary">
                    ดูรายละเอียดการจอง
                </a>
            </div>
            
            <?php if ($booking->status === 'pending'): ?>
            <p style="color: #856404; background-color: #fff3cd; padding: 15px; border-radius: 5px;">
                <strong>⏳ หมายเหตุ:</strong> การจองของท่านอยู่ระหว่างรอการอนุมัติ ท่านจะได้รับอีเมลแจ้งผลการอนุมัติภายใน 24 ชั่วโมง
            </p>
            <?php endif; ?>
            
            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                หากท่านต้องการยกเลิกหรือเปลี่ยนแปลงการจอง กรุณาดำเนินการก่อนวันประชุมอย่างน้อย 24 ชั่วโมง
            </p>
        </div>
        
        <div class="email-footer">
            <p>ระบบจองห้องประชุม - Meeting Room Booking System</p>
            <p>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            <p style="font-size: 12px;">© <?= date('Y') + 543 ?> All rights reserved.</p>
        </div>
    </div>
</body>
</html>
