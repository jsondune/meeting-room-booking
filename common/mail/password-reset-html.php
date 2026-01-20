<?php
/**
 * Password Reset Email Template
 * Meeting Room Booking System
 * 
 * @var common\models\User $user
 * @var string $resetLink
 * @var string $expiresIn (e.g., "1 hour")
 */

use yii\helpers\Html;

$primaryColor = '#6366f1';
$logoUrl = Yii::$app->params['logoUrl'] ?? '';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รีเซ็ตรหัสผ่าน</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Sarabun', 'Segoe UI', Arial, sans-serif; background-color: #f5f5f5;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f5f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, <?= $primaryColor ?> 0%, #8b5cf6 100%); padding: 40px; text-align: center;">
                            <?php if ($logoUrl): ?>
                                <img src="<?= Html::encode($logoUrl) ?>" alt="Logo" style="height: 50px; margin-bottom: 20px;">
                            <?php endif; ?>
                            <div style="font-size: 48px; margin-bottom: 10px;">🔐</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">รีเซ็ตรหัสผ่าน</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0; font-size: 14px;">
                                Password Reset Request
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
                                เราได้รับคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณในระบบจองห้องประชุม 
                                กรุณาคลิกปุ่มด้านล่างเพื่อตั้งรหัสผ่านใหม่
                            </p>
                            
                            <!-- Reset Button -->
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="<?= Html::encode($resetLink) ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, <?= $primaryColor ?> 0%, #8b5cf6 100%); color: #ffffff; text-decoration: none; padding: 16px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);">
                                    🔑 รีเซ็ตรหัสผ่าน
                                </a>
                            </div>
                            
                            <!-- Warning Box -->
                            <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px 20px; border-radius: 0 8px 8px 0; margin: 25px 0;">
                                <p style="color: #92400e; font-size: 14px; margin: 0; font-weight: 500;">
                                    ⏰ ลิงก์นี้จะหมดอายุใน <?= Html::encode($expiresIn ?? '1 ชั่วโมง') ?>
                                </p>
                            </div>
                            
                            <!-- Alternative Link -->
                            <div style="background-color: #f9fafb; border-radius: 8px; padding: 20px; margin: 25px 0;">
                                <p style="color: #6b7280; font-size: 13px; margin: 0 0 10px;">
                                    หากปุ่มด้านบนไม่ทำงาน กรุณาคัดลอกลิงก์นี้ไปวางในเบราว์เซอร์:
                                </p>
                                <p style="color: #4f46e5; font-size: 12px; margin: 0; word-break: break-all; background-color: #e0e7ff; padding: 10px; border-radius: 4px;">
                                    <?= Html::encode($resetLink) ?>
                                </p>
                            </div>
                            
                            <!-- Security Notice -->
                            <div style="border-top: 1px solid #e5e7eb; padding-top: 25px; margin-top: 25px;">
                                <p style="color: #6b7280; font-size: 14px; margin: 0 0 10px; line-height: 1.6;">
                                    <strong style="color: #374151;">🛡️ ข้อควรระวังด้านความปลอดภัย:</strong>
                                </p>
                                <ul style="color: #6b7280; font-size: 13px; margin: 0; padding-left: 20px; line-height: 1.8;">
                                    <li>หากคุณไม่ได้ขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยอีเมลนี้</li>
                                    <li>อย่าแชร์ลิงก์นี้กับผู้อื่น</li>
                                    <li>เจ้าหน้าที่จะไม่ขอรหัสผ่านของคุณทางอีเมลหรือโทรศัพท์</li>
                                    <li>ควรใช้รหัสผ่านที่มีความยาวอย่างน้อย 8 ตัวอักษร ผสมตัวเลขและอักขระพิเศษ</li>
                                </ul>
                            </div>
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
