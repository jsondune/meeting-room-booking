<?php
/**
 * Welcome Email Template
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var common\models\User $user
 */

use yii\helpers\Html;
use yii\helpers\Url;

$appName = Yii::$app->name ?? 'Meeting Room Booking System';
$loginUrl = Url::to(['/site/login'], true);
$homeUrl = Url::to(['/'], true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยินดีต้อนรับสู่ <?= Html::encode($appName) ?></title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Prompt', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f5f7fa; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 40px;">🎉</span>
                            </div>
                            <h1 style="color: #ffffff; font-size: 28px; margin: 0; font-weight: 600;">
                                ยินดีต้อนรับ!
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); font-size: 16px; margin: 10px 0 0;">
                                ขอบคุณที่สมัครใช้งาน <?= Html::encode($appName) ?>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Greeting -->
                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px;">
                                สวัสดีคุณ <strong><?= Html::encode($user->full_name ?? $user->username) ?></strong>,
                            </p>
                            <p style="color: #666; font-size: 15px; line-height: 1.7; margin: 0 0 30px;">
                                ยินดีต้อนรับเข้าสู่ระบบจองห้องประชุม! บัญชีผู้ใช้ของคุณได้ถูกสร้างเรียบร้อยแล้ว 
                                คุณสามารถเริ่มจองห้องประชุมและใช้งานระบบได้ทันที
                            </p>
                            
                            <!-- Account Info Box -->
                            <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px; padding: 25px; margin-bottom: 30px;">
                                <h3 style="color: #333; font-size: 16px; margin: 0 0 15px; display: flex; align-items: center;">
                                    <span style="margin-right: 10px;">👤</span> ข้อมูลบัญชีของคุณ
                                </h3>
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="padding: 8px 0; color: #666; font-size: 14px; width: 130px;">ชื่อผู้ใช้:</td>
                                        <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">
                                            <?= Html::encode($user->username) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #666; font-size: 14px;">อีเมล:</td>
                                        <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">
                                            <?= Html::encode($user->email) ?>
                                        </td>
                                    </tr>
                                    <?php if (!empty($user->department)): ?>
                                    <tr>
                                        <td style="padding: 8px 0; color: #666; font-size: 14px;">หน่วยงาน:</td>
                                        <td style="padding: 8px 0; color: #333; font-size: 14px; font-weight: 600;">
                                            <?= Html::encode($user->department->name_th ?? '-') ?>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            
                            <!-- Getting Started -->
                            <h3 style="color: #333; font-size: 18px; margin: 0 0 20px;">
                                🚀 เริ่มต้นใช้งาน
                            </h3>
                            
                            <div style="margin-bottom: 30px;">
                                <!-- Step 1 -->
                                <div style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                                    <div style="min-width: 32px; height: 32px; background: #667eea; border-radius: 50%; color: #fff; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center; margin-right: 15px;">1</div>
                                    <div>
                                        <strong style="color: #333; font-size: 15px;">ค้นหาห้องประชุม</strong>
                                        <p style="color: #666; font-size: 14px; margin: 5px 0 0; line-height: 1.5;">
                                            เลือกห้องประชุมที่เหมาะสมกับจำนวนผู้เข้าร่วมและอุปกรณ์ที่ต้องการ
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Step 2 -->
                                <div style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                                    <div style="min-width: 32px; height: 32px; background: #667eea; border-radius: 50%; color: #fff; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center; margin-right: 15px;">2</div>
                                    <div>
                                        <strong style="color: #333; font-size: 15px;">จองห้องประชุม</strong>
                                        <p style="color: #666; font-size: 14px; margin: 5px 0 0; line-height: 1.5;">
                                            เลือกวันที่และเวลาที่ต้องการ พร้อมกรอกรายละเอียดการประชุม
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Step 3 -->
                                <div style="display: flex; margin-bottom: 15px; align-items: flex-start;">
                                    <div style="min-width: 32px; height: 32px; background: #667eea; border-radius: 50%; color: #fff; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center; margin-right: 15px;">3</div>
                                    <div>
                                        <strong style="color: #333; font-size: 15px;">รอการอนุมัติ</strong>
                                        <p style="color: #666; font-size: 14px; margin: 5px 0 0; line-height: 1.5;">
                                            ระบบจะแจ้งเตือนเมื่อการจองได้รับการอนุมัติ
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Step 4 -->
                                <div style="display: flex; align-items: flex-start;">
                                    <div style="min-width: 32px; height: 32px; background: #28a745; border-radius: 50%; color: #fff; font-size: 14px; font-weight: bold; display: flex; align-items: center; justify-content: center; margin-right: 15px;">✓</div>
                                    <div>
                                        <strong style="color: #333; font-size: 15px;">เข้าร่วมประชุม</strong>
                                        <p style="color: #666; font-size: 14px; margin: 5px 0 0; line-height: 1.5;">
                                            สแกน QR Code เพื่อเช็คอินเมื่อถึงห้องประชุม
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="<?= $loginUrl ?>" 
                                   style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                                    เข้าสู่ระบบ
                                </a>
                            </div>
                            
                            <!-- Features -->
                            <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; margin-top: 30px;">
                                <h3 style="color: #333; font-size: 16px; margin: 0 0 15px;">
                                    ✨ คุณสมบัติเด่นของระบบ
                                </h3>
                                <ul style="color: #666; font-size: 14px; line-height: 1.8; margin: 0; padding-left: 20px;">
                                    <li>จองห้องประชุมออนไลน์ได้ตลอด 24 ชั่วโมง</li>
                                    <li>ดูปฏิทินห้องประชุมแบบ Real-time</li>
                                    <li>รับการแจ้งเตือนผ่านอีเมลและการแจ้งเตือนในระบบ</li>
                                    <li>เช็คอินด้วย QR Code</li>
                                    <li>จองอุปกรณ์เสริมพร้อมห้องประชุม</li>
                                    <li>ดูประวัติการจองย้อนหลัง</li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Help Section -->
                    <tr>
                        <td style="background: #f8f9fa; padding: 30px; border-top: 1px solid #e9ecef;">
                            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 0 0 10px; text-align: center;">
                                หากมีข้อสงสัยหรือต้องการความช่วยเหลือ
                            </p>
                            <p style="color: #667eea; font-size: 14px; margin: 0; text-align: center;">
                                <a href="mailto:support@example.com" style="color: #667eea; text-decoration: none;">
                                    📧 support@example.com
                                </a>
                                <span style="color: #999; margin: 0 10px;">|</span>
                                <a href="tel:+6621234567" style="color: #667eea; text-decoration: none;">
                                    📞 02-123-4567
                                </a>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background: #333; padding: 25px 30px; text-align: center;">
                            <p style="color: #fff; font-size: 14px; font-weight: 600; margin: 0 0 10px;">
                                <?= Html::encode($appName) ?>
                            </p>
                            <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin: 0;">
                                © <?= date('Y') + 543 ?> All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
                
                <!-- Disclaimer -->
                <table width="600" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                    <tr>
                        <td style="text-align: center; padding: 0 20px;">
                            <p style="color: #999; font-size: 11px; line-height: 1.6;">
                                อีเมลนี้ถูกส่งอัตโนมัติจากระบบ กรุณาอย่าตอบกลับอีเมลนี้โดยตรง
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
