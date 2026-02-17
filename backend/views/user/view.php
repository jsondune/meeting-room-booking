<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var array $bookingStats */

$this->title = $model->fullname ?? $model->username;
$this->params['breadcrumbs'][] = ['label' => 'จัดการผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusClasses = [
    10 => 'bg-success',
    9 => 'bg-warning text-dark',
    0 => 'bg-secondary',
];
$statusLabels = [
    10 => 'ใช้งานปกติ',
    9 => 'รอยืนยันอีเมล',
    0 => 'ถูกระงับ',
];

$roleClasses = [
    'admin' => 'bg-danger',
    'manager' => 'bg-warning text-dark',
    'staff' => 'bg-info',
    'user' => 'bg-secondary',
];
$roleLabels = [
    'admin' => 'ผู้ดูแลระบบ',
    'manager' => 'ผู้จัดการ',
    'staff' => 'เจ้าหน้าที่',
    'user' => 'ผู้ใช้งาน',
];
?>

<div class="user-view">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center">
            <img src="<?= $model->avatarUrl ?>" alt="" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
            <div>
                <h1 class="h3 mb-1"><?= Html::encode($model->fullname ?? $model->username) ?></h1>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge <?= $roleClasses[$model->role] ?? 'bg-secondary' ?>">
                        <?= $roleLabels[$model->role] ?? $model->role ?>
                    </span>
                    <span class="badge <?= $statusClasses[$model->status] ?? 'bg-secondary' ?>">
                        <?= $statusLabels[$model->status] ?? $model->status ?>
                    </span>
                    <?php if ($model->two_factor_enabled): ?>
                        <span class="badge bg-info" title="เปิดใช้งาน 2FA">
                            <i class="bi bi-shield-check"></i> 2FA
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-pencil me-1"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><?= Html::a('<i class="bi bi-key me-1"></i> รีเซ็ตรหัสผ่าน', ['reset-password', 'id' => $model->id], [
                    'class' => 'dropdown-item',
                    'data' => ['confirm' => 'ส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของผู้ใช้?', 'method' => 'post']
                ]) ?></li>
                <li><?= Html::a('<i class="bi bi-envelope me-1"></i> ส่งอีเมลยืนยัน', ['send-verification', 'id' => $model->id], [
                    'class' => 'dropdown-item',
                    'data' => ['method' => 'post']
                ]) ?></li>
                <li><hr class="dropdown-divider"></li>
                <?php if ($model->status == 10): ?>
                    <li><?= Html::a('<i class="bi bi-person-x me-1"></i> ระงับบัญชี', ['suspend', 'id' => $model->id], [
                        'class' => 'dropdown-item text-warning',
                        'data' => ['confirm' => 'ระงับบัญชีผู้ใช้นี้?', 'method' => 'post']
                    ]) ?></li>
                <?php else: ?>
                    <li><?= Html::a('<i class="bi bi-person-check me-1"></i> เปิดใช้งาน', ['activate', 'id' => $model->id], [
                        'class' => 'dropdown-item text-success',
                        'data' => ['confirm' => 'เปิดใช้งานบัญชีผู้ใช้นี้?', 'method' => 'post']
                    ]) ?></li>
                <?php endif; ?>
                <li><?= Html::a('<i class="bi bi-trash me-1"></i> ลบบัญชี', ['delete', 'id' => $model->id], [
                    'class' => 'dropdown-item text-danger',
                    'data' => ['confirm' => 'ต้องการลบบัญชีผู้ใช้นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้', 'method' => 'post']
                ]) ?></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Contact Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>ข้อมูลติดต่อ</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">Username</td>
                                    <td class="fw-medium"><?= Html::encode($model->username) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">อีเมล</td>
                                    <td>
                                        <a href="mailto:<?= Html::encode($model->email) ?>"><?= Html::encode($model->email) ?></a>
                                        <?php if ($model->email_verified_at): ?>
                                            <span class="badge bg-success ms-1"><i class="bi bi-check"></i> ยืนยันแล้ว</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">โทรศัพท์</td>
                                    <td><?= Html::encode($model->phone) ?: '-' ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">ตำแหน่ง</td>
                                    <td><?= Html::encode($model->position) ?: '-' ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">หน่วยงาน</td>
                                    <td>
                                        <?php if ($model->department): ?>
                                            <span class="badge bg-light text-dark"><?= Html::encode($model->department->name_th) ?></span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php if ($model->address): ?>
                        <hr>
                        <div>
                            <small class="text-muted">ที่อยู่</small>
                            <p class="mb-0"><?= nl2br(Html::encode($model->address)) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>สถิติการจอง</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="py-3">
                                <h3 class="mb-0 fw-bold text-primary"><?= $bookingStats['total'] ?? 0 ?></h3>
                                <small class="text-muted">จองทั้งหมด</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="py-3">
                                <h3 class="mb-0 fw-bold text-success"><?= $bookingStats['approved'] ?? 0 ?></h3>
                                <small class="text-muted">อนุมัติ</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="py-3">
                                <h3 class="mb-0 fw-bold text-warning"><?= $bookingStats['pending'] ?? 0 ?></h3>
                                <small class="text-muted">รอดำเนินการ</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="py-3">
                                <h3 class="mb-0 fw-bold text-danger"><?= $bookingStats['cancelled'] ?? 0 ?></h3>
                                <small class="text-muted">ยกเลิก</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>การจองล่าสุด</h6>
                    <?= Html::a('ดูทั้งหมด', ['/booking/index', 'user_id' => $model->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($model->recentBookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ห้อง</th>
                                        <th>วันที่</th>
                                        <th>เวลา</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($model->recentBookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <?= Html::a($booking->booking_code, ['/booking/view', 'id' => $booking->id], ['class' => 'text-decoration-none']) ?>
                                            </td>
                                            <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                            <td><?= Yii::$app->formatter->asDate($booking->booking_date) ?></td>
                                            <td><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?></td>
                                            <td>
                                                <?php
                                                $bookingStatusClasses = [
                                                    'pending' => 'bg-warning text-dark',
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    'cancelled' => 'bg-secondary',
                                                    'completed' => 'bg-info',
                                                ];
                                                $bookingStatusLabels = [
                                                    'pending' => 'รอดำเนินการ',
                                                    'approved' => 'อนุมัติ',
                                                    'rejected' => 'ปฏิเสธ',
                                                    'cancelled' => 'ยกเลิก',
                                                    'completed' => 'เสร็จสิ้น',
                                                ];
                                                ?>
                                                <span class="badge <?= $bookingStatusClasses[$booking->status] ?? 'bg-secondary' ?>">
                                                    <?= $bookingStatusLabels[$booking->status] ?? $booking->status ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            <p class="mb-0">ยังไม่มีประวัติการจอง</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>ประวัติกิจกรรม</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($model->activityLogs)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach (array_slice($model->activityLogs, 0, 10) as $log): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <span><?= Html::encode($log->description) ?></span>
                                        <small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($log->created_at) ?></small>
                                    </div>
                                    <?php if ($log->ip_address): ?>
                                        <small class="text-muted">IP: <?= Html::encode($log->ip_address) ?></small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                            <p class="mb-0">ยังไม่มีประวัติกิจกรรม</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Security Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>ความปลอดภัย</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>2-Factor Auth</span>
                        <?php if ($model->two_factor_enabled): ?>
                            <span class="badge bg-success">เปิดใช้งาน</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ปิดใช้งาน</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>อีเมลยืนยัน</span>
                        <?php if ($model->email_verified_at): ?>
                            <span class="badge bg-success">ยืนยันแล้ว</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">รอยืนยัน</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Connected Accounts -->
            <?php if (!empty($model->oauthAccounts)): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>บัญชีที่เชื่อมต่อ</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($model->oauthAccounts as $oauth): ?>
                            <?php
                            $providerIcons = [
                                'google' => ['icon' => 'bi-google', 'color' => 'text-danger', 'name' => 'Google'],
                                'microsoft' => ['icon' => 'bi-microsoft', 'color' => 'text-primary', 'name' => 'Microsoft'],
                                'github' => ['icon' => 'bi-github', 'color' => 'text-dark', 'name' => 'GitHub'],
                            ];
                            $provider = $providerIcons[$oauth->provider] ?? ['icon' => 'bi-link', 'color' => 'text-secondary', 'name' => ucfirst($oauth->provider)];
                            ?>
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi <?= $provider['icon'] ?> <?= $provider['color'] ?> me-2"></i>
                                <span><?= $provider['name'] ?></span>
                                <span class="badge bg-success ms-auto">เชื่อมต่อแล้ว</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>ข้อมูลการเข้าใช้งาน</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">เข้าใช้งานล่าสุด</small>
                        <span>
                            <?= $model->last_login_at ? Yii::$app->formatter->asDatetime($model->last_login_at) : 'ยังไม่เคยเข้าใช้งาน' ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">IP Address ล่าสุด</small>
                        <span><?= Html::encode($model->last_login_ip) ?: '-' ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block">จำนวนครั้งที่เข้าใช้</small>
                        <span><?= $model->login_count ?? 0 ?> ครั้ง</span>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">User ID</small>
                        <code><?= $model->id ?></code>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">สร้างเมื่อ</small>
                        <span><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                    </div>
                    <div>
                        <small class="text-muted d-block">แก้ไขล่าสุด</small>
                        <span><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
