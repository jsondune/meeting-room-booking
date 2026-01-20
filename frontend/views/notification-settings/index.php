<?php
/**
 * Notification Settings View
 * 
 * Comprehensive notification preferences management
 * 
 * @var yii\web\View $this
 * @var common\models\UserNotificationSetting $settings
 * @var common\models\UserOauth[] $oauthConnections
 * @var array $devices
 * @var bool $pushEnabled
 * @var bool $calendarSyncEnabled
 * @var array $reminderOptions
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ตั้งค่าการแจ้งเตือน';
$this->params['breadcrumbs'][] = ['label' => 'โปรไฟล์', 'url' => ['/profile']];
$this->params['breadcrumbs'][] = $this->title;

// Register CSRF token for AJAX
$csrfToken = Yii::$app->request->csrfToken;
?>

<div class="notification-settings-page">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-bell me-2 text-primary"></i>
                            <?= Html::encode($this->title) ?>
                        </h1>
                        <p class="text-muted mb-0">จัดการการแจ้งเตือนและการซิงค์ปฏิทิน</p>
                    </div>
                    <a href="<?= Url::to(['/profile']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> กลับ
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column - Main Settings -->
            <div class="col-lg-8">
                <!-- Email Notifications -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-envelope text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">การแจ้งเตือนอีเมล</h5>
                                <small class="text-muted">เลือกเหตุการณ์ที่ต้องการรับอีเมลแจ้งเตือน</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_booking_created" 
                                           data-type="email" data-key="booking_created"
                                           <?= $settings->email_booking_created ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_booking_created">
                                        <strong>สร้างการจองสำเร็จ</strong>
                                        <small class="d-block text-muted">เมื่อสร้างการจองใหม่</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_booking_approved" 
                                           data-type="email" data-key="booking_approved"
                                           <?= $settings->email_booking_approved ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_booking_approved">
                                        <strong>การจองได้รับอนุมัติ</strong>
                                        <small class="d-block text-muted">เมื่อการจองถูกอนุมัติ</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_booking_rejected" 
                                           data-type="email" data-key="booking_rejected"
                                           <?= $settings->email_booking_rejected ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_booking_rejected">
                                        <strong>การจองถูกปฏิเสธ</strong>
                                        <small class="d-block text-muted">เมื่อการจองถูกปฏิเสธ</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_booking_cancelled" 
                                           data-type="email" data-key="booking_cancelled"
                                           <?= $settings->email_booking_cancelled ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_booking_cancelled">
                                        <strong>ยกเลิกการจอง</strong>
                                        <small class="d-block text-muted">เมื่อการจองถูกยกเลิก</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_booking_reminder" 
                                           data-type="email" data-key="booking_reminder"
                                           <?= $settings->email_booking_reminder ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_booking_reminder">
                                        <strong>แจ้งเตือนก่อนประชุม</strong>
                                        <small class="d-block text-muted">เตือนก่อนเวลาประชุม</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_pending_approval" 
                                           data-type="email" data-key="pending_approval"
                                           <?= $settings->email_pending_approval ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_pending_approval">
                                        <strong>รอการอนุมัติ (ผู้อนุมัติ)</strong>
                                        <small class="d-block text-muted">เมื่อมีการจองรออนุมัติ</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr class="my-2">
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="email_daily_summary" 
                                           data-type="email" data-key="daily_summary"
                                           <?= $settings->email_daily_summary ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="email_daily_summary">
                                        <strong>สรุปประจำวัน</strong>
                                        <small class="d-block text-muted">รายงานการจองประจำวัน</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Push Notifications -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-phone-vibrate text-success"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">Push Notifications</h5>
                                    <small class="text-muted">การแจ้งเตือนแบบเรียลไทม์บนอุปกรณ์</small>
                                </div>
                            </div>
                            <?php if (!$pushEnabled): ?>
                                <span class="badge bg-secondary">ไม่พร้อมใช้งาน</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body <?= !$pushEnabled ? 'opacity-50' : '' ?>">
                        <?php if (!$pushEnabled): ?>
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle me-2"></i>
                                Push Notifications ยังไม่ได้เปิดใช้งานในระบบ
                            </div>
                        <?php endif; ?>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_booking_created" 
                                           data-type="push" data-key="booking_created"
                                           <?= $settings->push_booking_created ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_booking_created">
                                        <strong>สร้างการจองสำเร็จ</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_booking_approved" 
                                           data-type="push" data-key="booking_approved"
                                           <?= $settings->push_booking_approved ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_booking_approved">
                                        <strong>การจองได้รับอนุมัติ</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_booking_rejected" 
                                           data-type="push" data-key="booking_rejected"
                                           <?= $settings->push_booking_rejected ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_booking_rejected">
                                        <strong>การจองถูกปฏิเสธ</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_booking_cancelled" 
                                           data-type="push" data-key="booking_cancelled"
                                           <?= $settings->push_booking_cancelled ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_booking_cancelled">
                                        <strong>ยกเลิกการจอง</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_booking_reminder" 
                                           data-type="push" data-key="booking_reminder"
                                           <?= $settings->push_booking_reminder ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_booking_reminder">
                                        <strong>แจ้งเตือนก่อนประชุม</strong>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input class="form-check-input notification-toggle" type="checkbox" 
                                           id="push_pending_approval" 
                                           data-type="push" data-key="pending_approval"
                                           <?= $settings->push_pending_approval ? 'checked' : '' ?>
                                           <?= !$pushEnabled ? 'disabled' : '' ?>>
                                    <label class="form-check-label" for="push_pending_approval">
                                        <strong>รอการอนุมัติ (ผู้อนุมัติ)</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($pushEnabled): ?>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">ทดสอบการแจ้งเตือน</span>
                                <button type="button" class="btn btn-outline-success btn-sm" id="testPushBtn">
                                    <i class="bi bi-send me-1"></i> ส่งทดสอบ
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Calendar Sync -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-calendar-check text-info"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">การซิงค์ปฏิทิน</h5>
                                <small class="text-muted">เชื่อมต่อการจองกับปฏิทินภายนอก</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Google Calendar -->
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <svg width="32" height="32" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </div>
                                <div>
                                    <strong>Google Calendar</strong>
                                    <?php if (isset($oauthConnections['google'])): ?>
                                        <span class="badge bg-success ms-2">เชื่อมต่อแล้ว</span>
                                        <small class="d-block text-muted"><?= Html::encode($oauthConnections['google']->profile_data['email'] ?? '') ?></small>
                                    <?php else: ?>
                                        <small class="d-block text-muted">ยังไม่ได้เชื่อมต่อบัญชี</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <?php if (isset($oauthConnections['google'])): ?>
                                    <input class="form-check-input calendar-toggle" type="checkbox" 
                                           id="calendar_sync_google" 
                                           data-provider="google"
                                           <?= $settings->calendar_sync_google ? 'checked' : '' ?>>
                                <?php else: ?>
                                    <a href="<?= Url::to(['/oauth/connect', 'provider' => 'google']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-link-45deg me-1"></i> เชื่อมต่อ
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Microsoft Outlook -->
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <svg width="32" height="32" viewBox="0 0 24 24">
                                        <path fill="#0078D4" d="M24 12c0 6.627-5.373 12-12 12S0 18.627 0 12 5.373 0 12 0s12 5.373 12 12z"/>
                                        <path fill="#fff" d="M7.5 6h9a1.5 1.5 0 0 1 1.5 1.5v9a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 6 16.5v-9A1.5 1.5 0 0 1 7.5 6zm0 1.5v2.25L12 12l4.5-2.25V7.5h-9zm0 3.75v5.25h9v-5.25L12 13.5l-4.5-2.25z"/>
                                    </svg>
                                </div>
                                <div>
                                    <strong>Microsoft Outlook</strong>
                                    <?php if (isset($oauthConnections['microsoft'])): ?>
                                        <span class="badge bg-success ms-2">เชื่อมต่อแล้ว</span>
                                        <small class="d-block text-muted"><?= Html::encode($oauthConnections['microsoft']->profile_data['email'] ?? '') ?></small>
                                    <?php else: ?>
                                        <small class="d-block text-muted">ยังไม่ได้เชื่อมต่อบัญชี</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <?php if (isset($oauthConnections['microsoft'])): ?>
                                    <input class="form-check-input calendar-toggle" type="checkbox" 
                                           id="calendar_sync_microsoft" 
                                           data-provider="microsoft"
                                           <?= $settings->calendar_sync_microsoft ? 'checked' : '' ?>>
                                <?php else: ?>
                                    <a href="<?= Url::to(['/oauth/connect', 'provider' => 'microsoft']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-link-45deg me-1"></i> เชื่อมต่อ
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Auto Sync Option -->
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                   id="calendar_auto_sync" 
                                   data-type="calendar" data-key="auto_sync"
                                   <?= $settings->calendar_auto_sync ? 'checked' : '' ?>>
                            <label class="form-check-label" for="calendar_auto_sync">
                                <strong>ซิงค์อัตโนมัติ</strong>
                                <small class="d-block text-muted">สร้างกิจกรรมในปฏิทินอัตโนมัติเมื่อการจองได้รับอนุมัติ</small>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Reminder & Devices -->
            <div class="col-lg-4">
                <!-- Reminder Settings -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-alarm text-warning"></i>
                            </div>
                            <h5 class="mb-0">การแจ้งเตือนล่วงหน้า</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">เตือนก่อนเวลาประชุม</label>
                            <select class="form-select notification-select" id="reminder_minutes_before" 
                                    data-type="reminder" data-key="minutes_before">
                                <?php foreach ($reminderOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $settings->reminder_minutes_before == $value ? 'selected' : '' ?>>
                                        <?= Html::encode($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <label class="form-label">ช่องทางการเตือน</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                   id="reminder_email" 
                                   data-type="reminder" data-key="email"
                                   <?= $settings->reminder_email ? 'checked' : '' ?>>
                            <label class="form-check-label" for="reminder_email">
                                <i class="bi bi-envelope me-1"></i> อีเมล
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                   id="reminder_push" 
                                   data-type="reminder" data-key="push"
                                   <?= $settings->reminder_push ? 'checked' : '' ?>
                                   <?= !$pushEnabled ? 'disabled' : '' ?>>
                            <label class="form-check-label" for="reminder_push">
                                <i class="bi bi-phone me-1"></i> Push Notification
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Quiet Hours -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-moon text-secondary"></i>
                            </div>
                            <h5 class="mb-0">ช่วงเวลาเงียบ</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="quiet_hours_enabled"
                                   <?= $settings->quiet_hours_enabled ? 'checked' : '' ?>>
                            <label class="form-check-label" for="quiet_hours_enabled">
                                เปิดใช้งานช่วงเวลาเงียบ
                            </label>
                        </div>
                        
                        <div id="quietHoursSettings" class="<?= !$settings->quiet_hours_enabled ? 'd-none' : '' ?>">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">เริ่มต้น</label>
                                    <input type="time" class="form-control form-control-sm" 
                                           id="quiet_hours_start"
                                           value="<?= $settings->quiet_hours_start ? date('H:i', strtotime($settings->quiet_hours_start)) : '22:00' ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">สิ้นสุด</label>
                                    <input type="time" class="form-control form-control-sm" 
                                           id="quiet_hours_end"
                                           value="<?= $settings->quiet_hours_end ? date('H:i', strtotime($settings->quiet_hours_end)) : '07:00' ?>">
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                ระบบจะไม่ส่ง Push Notification ในช่วงเวลานี้
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Real-time Notifications -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-broadcast text-danger"></i>
                            </div>
                            <h5 class="mb-0">การแจ้งเตือนเรียลไทม์</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input notification-toggle" type="checkbox" 
                                   id="realtime_enabled" 
                                   data-type="realtime" data-key="enabled"
                                   <?= $settings->realtime_enabled ? 'checked' : '' ?>>
                            <label class="form-check-label" for="realtime_enabled">
                                เปิดใช้การแจ้งเตือนแบบเรียลไทม์
                            </label>
                        </div>
                        <small class="text-muted d-block mt-2">
                            รับการแจ้งเตือนทันทีผ่าน WebSocket เมื่อมีการเปลี่ยนแปลงสถานะการจอง
                        </small>
                    </div>
                </div>

                <!-- Registered Devices -->
                <?php if ($pushEnabled): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-dark bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-device-hdd text-dark"></i>
                                </div>
                                <h5 class="mb-0">อุปกรณ์ที่ลงทะเบียน</h5>
                            </div>
                            <span class="badge bg-primary"><?= count($devices) ?></span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($devices)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-phone-landscape fs-1"></i>
                                <p class="mb-0 mt-2">ยังไม่มีอุปกรณ์ที่ลงทะเบียน</p>
                                <small>อนุญาตการแจ้งเตือนบนเบราว์เซอร์เพื่อลงทะเบียน</small>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($devices as $device): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-<?= $device['platform'] === 'android' ? 'phone' : ($device['platform'] === 'ios' ? 'phone' : 'laptop') ?> me-2"></i>
                                            <span><?= Html::encode($device['device_name'] ?? ucfirst($device['platform'])) ?></span>
                                            <small class="text-muted d-block">
                                                <?= Html::encode($device['provider']) ?> • 
                                                <?= Yii::$app->formatter->asRelativeTime($device['updated_at']) ?>
                                            </small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-device-btn" 
                                                data-token-id="<?= $device['id'] ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Toast for notifications -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="settingsToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle text-success me-2" id="toastIcon"></i>
            <strong class="me-auto" id="toastTitle">สำเร็จ</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<?php
$saveUrl = Url::to(['/notification-settings/save']);
$toggleCalendarUrl = Url::to(['/notification-settings/toggle-calendar-sync']);
$testPushUrl = Url::to(['/notification-settings/test-push']);
$removeDeviceUrl = Url::to(['/notification-settings/remove-device']);

$js = <<<JS
// Toast helper
function showToast(message, success = true) {
    const toast = document.getElementById('settingsToast');
    const icon = document.getElementById('toastIcon');
    const title = document.getElementById('toastTitle');
    const body = document.getElementById('toastMessage');
    
    icon.className = success ? 'bi bi-check-circle text-success me-2' : 'bi bi-exclamation-circle text-danger me-2';
    title.textContent = success ? 'สำเร็จ' : 'ข้อผิดพลาด';
    body.textContent = message;
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Auto-save notification toggles
document.querySelectorAll('.notification-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const type = this.dataset.type;
        const key = this.dataset.key;
        const value = this.checked ? 1 : 0;
        
        const data = {};
        if (type === 'realtime') {
            data['realtime_enabled'] = value;
        } else {
            data[type] = {};
            data[type][key] = value;
        }
        
        fetch('$saveUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '$csrfToken'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            showToast(result.message, result.success);
        })
        .catch(error => {
            showToast('เกิดข้อผิดพลาดในการบันทึก', false);
            this.checked = !this.checked; // Revert
        });
    });
});

// Calendar sync toggles
document.querySelectorAll('.calendar-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const provider = this.dataset.provider;
        const enabled = this.checked ? 1 : 0;
        
        fetch('$toggleCalendarUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '$csrfToken'
            },
            body: 'provider=' + provider + '&enabled=' + enabled
        })
        .then(response => response.json())
        .then(result => {
            if (result.require_oauth) {
                this.checked = false;
            }
            showToast(result.message, result.success);
        })
        .catch(error => {
            showToast('เกิดข้อผิดพลาด', false);
            this.checked = !this.checked;
        });
    });
});

// Reminder select
document.getElementById('reminder_minutes_before')?.addEventListener('change', function() {
    const data = {
        reminder: {
            minutes_before: this.value
        }
    };
    
    fetch('$saveUrl', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '$csrfToken'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        showToast(result.message, result.success);
    });
});

// Quiet hours toggle
document.getElementById('quiet_hours_enabled')?.addEventListener('change', function() {
    document.getElementById('quietHoursSettings').classList.toggle('d-none', !this.checked);
    saveQuietHours();
});

// Quiet hours time change
['quiet_hours_start', 'quiet_hours_end'].forEach(function(id) {
    document.getElementById(id)?.addEventListener('change', saveQuietHours);
});

function saveQuietHours() {
    const data = {
        quiet_hours: {
            enabled: document.getElementById('quiet_hours_enabled').checked ? 1 : 0,
            start: document.getElementById('quiet_hours_start').value,
            end: document.getElementById('quiet_hours_end').value
        }
    };
    
    fetch('$saveUrl', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '$csrfToken'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        showToast(result.message, result.success);
    });
}

// Test push notification
document.getElementById('testPushBtn')?.addEventListener('click', function() {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> กำลังส่ง...';
    
    fetch('$testPushUrl', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '$csrfToken'
        }
    })
    .then(response => response.json())
    .then(result => {
        showToast(result.message, result.success);
    })
    .finally(() => {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-send me-1"></i> ส่งทดสอบ';
    });
});

// Remove device
document.querySelectorAll('.remove-device-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (!confirm('ต้องการลบอุปกรณ์นี้ออกจากการรับการแจ้งเตือน?')) return;
        
        const tokenId = this.dataset.tokenId;
        const listItem = this.closest('.list-group-item');
        
        fetch('$removeDeviceUrl', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-Token': '$csrfToken'
            },
            body: 'token_id=' + tokenId
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                listItem.remove();
            }
            showToast(result.message, result.success);
        });
    });
});
JS;

$this->registerJs($js);
?>

<style>
.notification-settings-page .card {
    border: none;
    border-radius: 12px;
}

.notification-settings-page .card-header {
    border-bottom: 1px solid rgba(0,0,0,.05);
}

.notification-settings-page .form-check-input:checked {
    background-color: #4F46E5;
    border-color: #4F46E5;
}

.notification-settings-page .form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
}

.notification-settings-page .list-group-item {
    border-left: 0;
    border-right: 0;
}

.notification-settings-page .list-group-item:first-child {
    border-top: 0;
}
</style>
