<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Setting $model */

$this->title = 'ตั้งค่าทั่วไป';
$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่า', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="setting-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-gear text-primary me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <!-- Settings Navigation -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-list me-2"></i>หมวดหมู่
                    </h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#general" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                        <i class="bi bi-sliders me-2"></i>ทั่วไป
                    </a>
                    <a href="#booking" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-calendar-check me-2"></i>การจอง
                    </a>
                    <a href="#notification" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-bell me-2"></i>การแจ้งเตือน
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-shield-lock me-2"></i>ความปลอดภัย
                    </a>
                    <a href="#integration" class="list-group-item list-group-item-action" data-bs-toggle="list">
                        <i class="bi bi-plug me-2"></i>การเชื่อมต่อ
                    </a>
                    <a href="<?= \yii\helpers\Url::to(['setting/holidays']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-calendar-heart me-2"></i>วันหยุด
                    </a>
                    <a href="<?= \yii\helpers\Url::to(['setting/email-templates']) ?>" class="list-group-item list-group-item-action">
                        <i class="bi bi-envelope me-2"></i>แม่แบบอีเมล
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="tab-content">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-sliders text-primary me-2"></i>ตั้งค่าทั่วไป
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อระบบ</label>
                                        <input type="text" name="site_name" class="form-control" value="ระบบจองห้องประชุม">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อหน่วยงาน</label>
                                        <input type="text" name="organization_name" class="form-control" value="BiZCO">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">อีเมลติดต่อ</label>
                                        <input type="email" name="contact_email" class="form-control" value="meeting@bizco.co.th">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" name="contact_phone" class="form-control" value="02-590-1234">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">ที่อยู่</label>
                                        <textarea name="address" class="form-control" rows="2">BiZCO 999/999 นนทบุรี 11000</textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">โลโก้</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        <div class="form-text">ขนาดแนะนำ: 200x60 พิกเซล, รูปแบบ PNG หรือ SVG</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Favicon</label>
                                        <input type="file" name="favicon" class="form-control" accept="image/*">
                                        <div class="form-text">ขนาด: 32x32 พิกเซล, รูปแบบ ICO หรือ PNG</div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">เขตเวลา</label>
                                        <select name="timezone" class="form-select">
                                            <option value="Asia/Bangkok" selected>Asia/Bangkok (GMT+7)</option>
                                            <option value="Asia/Singapore">Asia/Singapore (GMT+8)</option>
                                            <option value="UTC">UTC (GMT+0)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">รูปแบบวันที่</label>
                                        <select name="date_format" class="form-select">
                                            <option value="d/m/Y" selected>DD/MM/YYYY (25/12/2567)</option>
                                            <option value="Y-m-d">YYYY-MM-DD (2567-12-25)</option>
                                            <option value="d M Y">DD MMM YYYY (25 ธ.ค. 2567)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">รูปแบบเวลา</label>
                                        <select name="time_format" class="form-select">
                                            <option value="H:i" selected>24 ชั่วโมง (14:30)</option>
                                            <option value="h:i A">12 ชั่วโมง (2:30 PM)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ภาษาเริ่มต้น</label>
                                        <select name="default_language" class="form-select">
                                            <option value="th" selected>ภาษาไทย</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">สกุลเงิน</label>
                                        <select name="currency" class="form-select">
                                            <option value="THB" selected>บาท (THB)</option>
                                            <option value="USD">US Dollar (USD)</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Booking Settings -->
                <div class="tab-pane fade" id="booking">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-check text-primary me-2"></i>ตั้งค่าการจอง
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">เวลาเปิดทำการ</label>
                                        <input type="time" name="opening_time" class="form-control" value="07:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">เวลาปิดทำการ</label>
                                        <input type="time" name="closing_time" class="form-control" value="20:00">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ระยะเวลาการจองขั้นต่ำ (นาที)</label>
                                        <select name="min_booking_duration" class="form-select">
                                            <option value="30">30 นาที</option>
                                            <option value="60" selected>1 ชั่วโมง</option>
                                            <option value="120">2 ชั่วโมง</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ระยะเวลาการจองสูงสุด (ชั่วโมง)</label>
                                        <select name="max_booking_duration" class="form-select">
                                            <option value="4">4 ชั่วโมง</option>
                                            <option value="8" selected>8 ชั่วโมง</option>
                                            <option value="12">12 ชั่วโมง</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จองล่วงหน้าได้ไม่เกิน (วัน)</label>
                                        <input type="number" name="max_advance_days" class="form-control" value="30">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ต้องจองล่วงหน้าอย่างน้อย (ชั่วโมง)</label>
                                        <input type="number" name="min_advance_hours" class="form-control" value="2">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ยกเลิกได้ก่อนเวลาจอง (ชั่วโมง)</label>
                                        <input type="number" name="cancellation_deadline" class="form-control" value="24">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนการจองต่อวันสูงสุด/ผู้ใช้</label>
                                        <input type="number" name="max_bookings_per_day" class="form-control" value="3">
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="requireApproval" name="require_approval" checked>
                                            <label class="form-check-label" for="requireApproval">ต้องขออนุมัติก่อนจอง</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowRecurring" name="allow_recurring" checked>
                                            <label class="form-check-label" for="allowRecurring">อนุญาตการจองซ้ำ (Recurring)</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowWeekends" name="allow_weekends">
                                            <label class="form-check-label" for="allowWeekends">อนุญาตจองในวันหยุดสุดสัปดาห์</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowHolidays" name="allow_holidays">
                                            <label class="form-check-label" for="allowHolidays">อนุญาตจองในวันหยุดราชการ</label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="tab-pane fade" id="notification">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bell text-primary me-2"></i>ตั้งค่าการแจ้งเตือน
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <h6 class="mb-3">การแจ้งเตือนทางอีเมล</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailBookingCreated" name="email_booking_created" checked>
                                            <label class="form-check-label" for="emailBookingCreated">แจ้งเตือนเมื่อสร้างการจอง</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailBookingApproved" name="email_booking_approved" checked>
                                            <label class="form-check-label" for="emailBookingApproved">แจ้งเตือนเมื่ออนุมัติการจอง</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailBookingRejected" name="email_booking_rejected" checked>
                                            <label class="form-check-label" for="emailBookingRejected">แจ้งเตือนเมื่อปฏิเสธการจอง</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailBookingReminder" name="email_booking_reminder" checked>
                                            <label class="form-check-label" for="emailBookingReminder">แจ้งเตือนก่อนถึงเวลาจอง</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">แจ้งเตือนล่วงหน้า (ชั่วโมง)</label>
                                        <input type="number" name="reminder_hours" class="form-control" value="24">
                                    </div>
                                </div>

                                <h6 class="mb-3">การแจ้งเตือนสำหรับผู้ดูแล</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="adminNewBooking" name="admin_new_booking" checked>
                                            <label class="form-check-label" for="adminNewBooking">แจ้งเตือนเมื่อมีการจองใหม่</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="adminCancellation" name="admin_cancellation" checked>
                                            <label class="form-check-label" for="adminCancellation">แจ้งเตือนเมื่อมีการยกเลิก</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">อีเมลผู้รับแจ้งเตือน (คั่นด้วยเครื่องหมายจุลภาค)</label>
                                        <input type="text" name="admin_emails" class="form-control" value="admin@bizco.co.th, manager@bizco.co.th">
                                    </div>
                                </div>

                                <h6 class="mb-3">ตั้งค่า SMTP</h6>
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">SMTP Host</label>
                                        <input type="text" name="smtp_host" class="form-control" value="smtp.gmail.com">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">SMTP Port</label>
                                        <input type="number" name="smtp_port" class="form-control" value="587">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">SMTP Username</label>
                                        <input type="text" name="smtp_username" class="form-control" value="noreply@bizco.co.th">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">SMTP Password</label>
                                        <input type="password" name="smtp_password" class="form-control" value="">
                                        <div class="form-text">ปล่อยว่างถ้าไม่ต้องการเปลี่ยน</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">การเข้ารหัส</label>
                                        <select name="smtp_encryption" class="form-select">
                                            <option value="tls" selected>TLS</option>
                                            <option value="ssl">SSL</option>
                                            <option value="">ไม่มี</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อผู้ส่ง</label>
                                        <input type="text" name="smtp_from_name" class="form-control" value="ระบบจองห้องประชุม">
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-outline-secondary" onclick="testEmail()">
                                            <i class="bi bi-send me-1"></i>ทดสอบส่งอีเมล
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="tab-pane fade" id="security">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-shield-lock text-primary me-2"></i>ตั้งค่าความปลอดภัย
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <h6 class="mb-3">การยืนยันตัวตน</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require2FA" name="require_2fa">
                                            <label class="form-check-label" for="require2FA">บังคับใช้ 2FA สำหรับทุกคน</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require2FAAdmin" name="require_2fa_admin" checked>
                                            <label class="form-check-label" for="require2FAAdmin">บังคับใช้ 2FA สำหรับ Admin/Manager</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowOAuth" name="allow_oauth" checked>
                                            <label class="form-check-label" for="allowOAuth">อนุญาตการเข้าสู่ระบบด้วย OAuth</label>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">นโยบายรหัสผ่าน</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">ความยาวรหัสผ่านขั้นต่ำ</label>
                                        <input type="number" name="min_password_length" class="form-control" value="8">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">อายุรหัสผ่าน (วัน)</label>
                                        <input type="number" name="password_expiry_days" class="form-control" value="90">
                                        <div class="form-text">0 = ไม่หมดอายุ</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="requireUppercase" name="require_uppercase" checked>
                                            <label class="form-check-label" for="requireUppercase">ต้องมีตัวพิมพ์ใหญ่</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="requireNumber" name="require_number" checked>
                                            <label class="form-check-label" for="requireNumber">ต้องมีตัวเลข</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="requireSpecial" name="require_special">
                                            <label class="form-check-label" for="requireSpecial">ต้องมีอักขระพิเศษ</label>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="mb-3">Session และการล็อค</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">หมดเวลา Session (นาที)</label>
                                        <input type="number" name="session_timeout" class="form-control" value="60">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ล็อคหลังจากเข้าสู่ระบบผิด (ครั้ง)</label>
                                        <input type="number" name="max_login_attempts" class="form-control" value="5">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ระยะเวลาล็อค (นาที)</label>
                                        <input type="number" name="lockout_duration" class="form-control" value="15">
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Integration Settings -->
                <div class="tab-pane fade" id="integration">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-plug text-primary me-2"></i>การเชื่อมต่อภายนอก
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <h6 class="mb-3">Google OAuth</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableGoogle" name="enable_google" checked>
                                            <label class="form-check-label" for="enableGoogle">เปิดใช้งาน Google Login</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Client ID</label>
                                        <input type="text" name="google_client_id" class="form-control" value="your-client-id.apps.googleusercontent.com">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Client Secret</label>
                                        <input type="password" name="google_client_secret" class="form-control">
                                    </div>
                                </div>

                                <h6 class="mb-3">Microsoft OAuth</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableMicrosoft" name="enable_microsoft">
                                            <label class="form-check-label" for="enableMicrosoft">เปิดใช้งาน Microsoft Login</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Client ID</label>
                                        <input type="text" name="microsoft_client_id" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Client Secret</label>
                                        <input type="password" name="microsoft_client_secret" class="form-control">
                                    </div>
                                </div>

                                <h6 class="mb-3">LINE Notify</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableLine" name="enable_line">
                                            <label class="form-check-label" for="enableLine">เปิดใช้งาน LINE Notify</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">LINE Notify Token</label>
                                        <input type="password" name="line_notify_token" class="form-control">
                                    </div>
                                </div>

                                <h6 class="mb-3">Google Calendar Sync</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enableCalendarSync" name="enable_calendar_sync">
                                            <label class="form-check-label" for="enableCalendarSync">เปิดใช้งานการ Sync กับ Google Calendar</label>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>บันทึกการตั้งค่า
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
function testEmail() {
    alert('กำลังส่งอีเมลทดสอบ...');
    // TODO: Implement AJAX call to test email
}
window.testEmail = testEmail;

// Handle tab switching via URL hash
if (window.location.hash) {
    const hash = window.location.hash;
    const trigger = document.querySelector('a[href="' + hash + '"]');
    if (trigger) {
        trigger.click();
    }
}

// Update URL hash when tab changes
document.querySelectorAll('[data-bs-toggle="list"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function(e) {
        history.pushState(null, null, e.target.getAttribute('href'));
    });
});
JS;
$this->registerJs($js);
?>
