<?php
/**
 * Booking Cancelled Email Template
 * Meeting Room Booking System
 * 
 * @var common\models\Booking $booking
 * @var string $cancelledBy (user/admin/system)
 * @var string $reason
 */

use yii\helpers\Html;

$primaryColor = '#6b7280';
$logoUrl = Yii::$app->params['logoUrl'] ?? '';
$room = $booking->room;
$user = $booking->user;

// Determine who cancelled
$cancellerText = match($cancelledBy ?? 'user') {
    'admin' => 'ผู้ดูแลระบบ',
    'system' => 'ระบบอัตโนมัติ',
    default => 'คุณ',
};
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองถูกยกเลิก</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Sarabun', 'Segoe UI', Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, <?= $primaryColor ?> 0%, #4b5563 100%); padding: 40px; text-align: center;">
                            <?php if ($logoUrl): ?>
                                <img src="<?= Html::encode($logoUrl) ?>" alt="Logo" style="height: 50px; margin-bottom: 20px;">
                            <?php endif; ?>
                            <div style="font-size: 48px; margin-bottom: 10px;">❌</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">การจองถูกยกเลิก</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 14px;">
                                Booking Cancelled
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #374151; font-size: 16px; margin: 0 0 20px; line-height: 1.6;">
                                สวัสดีคุณ <?= Html::encode($user->first_name ?? $user->username) ?>,
                            </p>
                            
                            <p style="color: #6b7280; font-size: 15px; margin: 0 0 25px; line-height: 1.6;">
                                การจองห้องประชุมของคุณได้ถูกยกเลิกโดย<?= $cancellerText ?>
                            </p>
                            
                            <!-- Booking Code -->
                            <div style="text-align: center; margin: 25px 0;">
                                <div style="display: inline-block; background-color: #f3f4f6; padding: 15px 30px; border-radius: 8px; border: 2px dashed #d1d5db;">
                                    <span style="color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">รหัสการจอง</span>
                                    <div style="color: #6b7280; font-size: 24px; font-weight: bold; font-family: monospace; margin-top: 5px; text-decoration: line-through;">
                                        <?= Html::encode($booking->booking_code) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cancellation Reason -->
                            <?php if (!empty($reason)): ?>
                            <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 15px 20px; border-radius: 0 8px 8px 0; margin: 25px 0;">
                                <p style="color: #991b1b; font-size: 13px; margin: 0 0 5px; font-weight: 600;">
                                    เหตุผลการยกเลิก:
                                </p>
                                <p style="color: #b91c1c; font-size: 14px; margin: 0;">
                                    <?= Html::encode($reason) ?>
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Booking Details -->
                            <div style="background-color: #f9fafb; border-radius: 12px; padding: 25px; margin: 25px 0;">
                                <h3 style="color: #6b7280; font-size: 14px; margin: 0 0 20px; text-transform: uppercase; letter-spacing: 1px;">
                                    รายละเอียดการจองที่ถูกยกเลิก
                                </h3>
                                
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <span style="color: #9ca3af; font-size: 13px;">🏢 ห้องประชุม</span>
                                            <div style="color: #374151; font-size: 15px; margin-top: 3px; text-decoration: line-through;">
                                                <?= Html::encode($room->name_th ?? 'N/A') ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <span style="color: #9ca3af; font-size: 13px;">📋 หัวข้อการประชุม</span>
                                            <div style="color: #374151; font-size: 15px; margin-top: 3px; text-decoration: line-through;">
                                                <?= Html::encode($booking->title) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <span style="color: #9ca3af; font-size: 13px;">📅 วันที่</span>
                                            <div style="color: #374151; font-size: 15px; margin-top: 3px; text-decoration: line-through;">
                                                <?= Yii::$app->formatter->asDate($booking->booking_date) ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 10px 0;">
                                            <span style="color: #9ca3af; font-size: 13px;">⏰ เวลา</span>
                                            <div style="color: #374151; font-size: 15px; margin-top: 3px; text-decoration: line-through;">
                                                <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?> น.
                                                <span style="color: #9ca3af; font-size: 13px;">
                                                    (<?= floor($booking->duration_minutes / 60) ?> ชม. <?= $booking->duration_minutes % 60 ?> นาที)
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Refund Notice (if applicable) -->
                            <?php if ($booking->total_cost > 0): ?>
                            <div style="background-color: #f0f9ff; border-radius: 8px; padding: 20px; margin: 25px 0;">
                                <h4 style="color: #0369a1; font-size: 14px; margin: 0 0 10px;">
                                    💰 ข้อมูลการคืนเงิน
                                </h4>
                                <p style="color: #0284c7; font-size: 14px; margin: 0; line-height: 1.6;">
                                    หากมีการชำระเงินล่วงหน้า จะได้รับการคืนเงินตามนโยบายการยกเลิก 
                                    กรุณาติดต่อเจ้าหน้าที่สำหรับรายละเอียดเพิ่มเติม
                                </p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Call to Action -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="<?= Html::encode(Yii::$app->params['frontendUrl'] ?? '') ?>/room" 
                                   style="display: inline-block; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff; text-decoration: none; padding: 14px 35px; border-radius: 8px; font-size: 15px; font-weight: 600; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);">
                                    🔄 จองห้องประชุมใหม่
                                </a>
                            </div>
                            
                            <p style="color: #9ca3af; font-size: 13px; margin: 25px 0 0; line-height: 1.6; text-align: center;">
                                หากมีข้อสงสัยเกี่ยวกับการยกเลิก กรุณาติดต่อเจ้าหน้าที่
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 25px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #9ca3af; font-size: 12px; margin: 0 0 5px;">
                                ระบบจองห้องประชุม | Meeting Room Booking System
                            </p>
                            <p style="color: #9ca3af; font-size: 11px; margin: 0;">
                                อีเมลนี้ถูกส่งอัตโนมัติ กรุณาอย่าตอบกลับ
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
