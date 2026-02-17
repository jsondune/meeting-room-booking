<?php
/**
 * Email Verification Template
 * Meeting Room Booking System
 * 
 * @var common\models\User $user
 * @var string $verifyLink
 */

use yii\helpers\Html;

$primaryColor = '#10b981';
$logoUrl = Yii::$app->params['logoUrl'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันอีเมล</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Sarabun', 'Segoe UI', Arial, sans-serif; background-color: #f0fdf4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0fdf4; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, <?= $primaryColor ?> 0%, #059669 100%); padding: 40px; text-align: center;">
                            <?php if ($logoUrl): ?>
                                <img src="<?= Html::encode($logoUrl) ?>" alt="Logo" style="height: 50px; margin-bottom: 20px;">
                            <?php endif; ?>
                            <div style="font-size: 48px; margin-bottom: 10px;">✉️</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">ยืนยันอีเมลของคุณ</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 14px;">
                                Verify Your Email Address
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="color: #374151; font-size: 16px; margin: 0 0 20px; line-height: 1.6;">
                                สวัสดีคุณ <?= Html::encode($user->full_name ?? $user->username) ?>,
                            </p>
                            
                            <p style="color: #6b7280; font-size: 15px; margin: 0 0 15px; line-height: 1.6;">
                                ขอบคุณที่สมัครใช้งานระบบจองห้องประชุม! 
                            </p>
                            
                            <p style="color: #6b7280; font-size: 15px; margin: 0 0 25px; line-height: 1.6;">
                                โปรดยืนยันอีเมลของคุณโดยคลิกปุ่มด้านล่าง เพื่อเริ่มใช้งานระบบได้อย่างเต็มรูปแบบ
                            </p>
                            
                            <!-- Verify Button -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="<?= Html::encode($verifyLink) ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, <?= $primaryColor ?> 0%, #059669 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                                    ✅ ยืนยันอีเมล
                                </a>
                            </div>
                            
                            <!-- Features Preview -->
                            <div style="background-color: #ecfdf5; border-radius: 12px; padding: 25px; margin: 25px 0;">
                                <h3 style="color: #065f46; font-size: 16px; margin: 0 0 15px;">
                                    🎉 สิ่งที่คุณสามารถทำได้หลังยืนยันอีเมล:
                                </h3>
                                <ul style="color: #047857; font-size: 14px; margin: 0; padding-left: 20px; line-height: 2;">
                                    <li>จองห้องประชุมได้ทันที</li>
                                    <li>ดูตารางห้องประชุมแบบ Real-time</li>
                                    <li>รับการแจ้งเตือนทางอีเมล</li>
                                    <li>จัดการการจองของคุณได้ง่ายๆ</li>
                                    <li>ขออุปกรณ์เสริมสำหรับการประชุม</li>
                                </ul>
                            </div>
                            
                            <!-- Alternative Link -->
                            <div style="background-color: #f9fafb; border-radius: 8px; padding: 20px; margin: 25px 0;">
                                <p style="color: #6b7280; font-size: 13px; margin: 0 0 10px;">
                                    หากปุ่มด้านบนไม่ทำงาน โปรดคัดลอกลิงก์นี้ไปวางในเบราว์เซอร์:
                                </p>
                                <p style="color: #059669; font-size: 12px; margin: 0; word-break: break-all; background-color: #d1fae5; padding: 10px; border-radius: 4px;">
                                    <?= Html::encode($verifyLink) ?>
                                </p>
                            </div>
                            
                            <!-- Account Info -->
                            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 25px 0;">
                                <h4 style="color: #374151; font-size: 14px; margin: 0 0 15px;">
                                    📋 ข้อมูลบัญชีของคุณ
                                </h4>
                                <table width="100%" cellpadding="5" cellspacing="0" style="font-size: 13px;">
                                    <tr>
                                        <td style="color: #6b7280; width: 120px;">ชื่อผู้ใช้:</td>
                                        <td style="color: #374151; font-weight: 500;"><?= Html::encode($user->username) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="color: #6b7280;">อีเมล:</td>
                                        <td style="color: #374151; font-weight: 500;"><?= Html::encode($user->email) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="color: #6b7280;">วันที่สมัคร:</td>
                                        <td style="color: #374151; font-weight: 500;"><?= Yii::$app->formatter->asDatetime($user->created_at) ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p style="color: #9ca3af; font-size: 13px; margin: 25px 0 0; line-height: 1.6;">
                                หากคุณไม่ได้สมัครใช้งานระบบนี้ โปรดเพิกเฉยอีเมลนี้
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
                                อีเมลนี้ถูกส่งอัตโนมัติ โปรดอย่าตอบกลับ
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
