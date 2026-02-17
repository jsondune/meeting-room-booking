<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var array $stats */
/** @var array $recentBookings */

$this->title = 'โปรไฟล์';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="profile-index">
    <div class="container py-4">
        <div class="row">
            <!-- Left Sidebar - Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <!-- Avatar -->
                        <div class="position-relative d-inline-block mb-3">
                            <img src="<?= Html::encode($user->getAvatarUrl()) ?>" alt="Avatar" 
                                 class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                            <a href="<?= Url::to(['edit']) ?>" class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow-sm"
                               title="เปลี่ยนรูปโปรไฟล์">
                                <i class="bi bi-camera"></i>
                            </a>
                        </div>
                        
                        <h4 class="mb-1"><?= Html::encode($user->first_name . ' ' . $user->last_name) ?></h4>
                        <p class="text-muted mb-2"><?= Html::encode($user->position) ?></p>
                        <span class="badge bg-primary"><?= Html::encode(($user->department->name_th ?? '-')) ?></span>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fw-bold text-primary"><?= ($stats['total'] ?? 0) ?></div>
                                <small class="text-muted">การจองทั้งหมด</small>
                            </div>
                            <div class="col-4 border-start border-end">
                                <div class="fw-bold text-success"><?= ($stats['this_month'] ?? 0) ?></div>
                                <small class="text-muted">เดือนนี้</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-info"><?= ($stats['upcoming'] ?? 0) ?></div>
                                <small class="text-muted">กำลังจะมาถึง</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-lightning me-2"></i>เมนูลัด
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= Url::to(['booking/create']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-calendar-plus me-2 text-primary"></i>จองห้องประชุม
                        </a>
                        <a href="<?= Url::to(['booking/index']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-list-check me-2 text-success"></i>การจองของฉัน
                        </a>
                        <a href="<?= Url::to(['room/index']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-door-open me-2 text-info"></i>ห้องประชุมทั้งหมด
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="bi bi-key me-2 text-warning"></i>เปลี่ยนรหัสผ่าน
                        </a>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>ข้อมูลบัญชี
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">สมัครสมาชิกเมื่อ</small>
                            <div><?= Yii::$app->formatter->asDatetime($user->created_at, 'medium') ?></div>
                        </div>
                        <div>
                            <small class="text-muted">เข้าสู่ระบบล่าสุด</small>
                            <div><?= Yii::$app->formatter->asDatetime($user->last_login_at, 'medium') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="col-lg-8">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#profileInfo">
                            <i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#security">
                            <i class="bi bi-shield-lock me-2"></i>ความปลอดภัย
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#notifications">
                            <i class="bi bi-bell me-2"></i>การแจ้งเตือน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#history">
                            <i class="bi bi-clock-history me-2"></i>ประวัติการจอง
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Profile Info Tab -->
                    <div class="tab-pane fade show active" id="profileInfo">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">ข้อมูลส่วนตัว</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="editProfileBtn">
                                    <i class="bi bi-pencil me-1"></i>แก้ไข
                                </button>
                            </div>
                            <div class="card-body">
                                <form id="profileForm">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" name="first_name" 
                                                   value="<?= Html::encode($user->first_name) ?>" disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">นามสกุล</label>
                                            <input type="text" class="form-control" name="last_name" 
                                                   value="<?= Html::encode($user->last_name) ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">อีเมล</label>
                                            <input type="email" class="form-control" name="email" 
                                                   value="<?= Html::encode($user->email) ?>" disabled>
                                            <div class="form-text">
                                                <i class="bi bi-check-circle text-success me-1"></i>อีเมลยืนยันแล้ว
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">เบอร์โทรศัพท์</label>
                                            <input type="tel" class="form-control" name="phone" 
                                                   value="<?= Html::encode($user->phone) ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">หน่วยงาน/แผนก</label>
                                            <select class="form-select" name="department_id" disabled>
                                                <option value="">-- เลือกหน่วยงาน --</option>
                                                <?php
                                                $departments = \common\models\Department::find()
                                                    ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
                                                    ->all();
                                                foreach ($departments as $dept):
                                                ?>
                                                <option value="<?= $dept->id ?>" <?= $user->department_id == $dept->id ? 'selected' : '' ?>>
                                                    <?= Html::encode($dept->name_th) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ตำแหน่ง</label>
                                            <input type="text" class="form-control" name="position" 
                                                   value="<?= Html::encode($user->position) ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="d-none" id="profileActions">
                                        <hr>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-1"></i>บันทึก
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="cancelEditBtn">
                                            ยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">รหัสผ่าน</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">เปลี่ยนรหัสผ่านเพื่อความปลอดภัยของบัญชี</p>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                                </button>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">การยืนยันตัวตนสองชั้น (2FA)</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-1">เพิ่มความปลอดภัยให้บัญชีของคุณด้วยการยืนยันตัวตนสองชั้น</p>
                                        <small class="text-muted">
                                            <?php if ($user->two_factor_enabled): ?>
                                                <i class="bi bi-shield-check text-success me-1"></i>เปิดใช้งานแล้ว
                                            <?php else: ?>
                                                <i class="bi bi-shield-x text-warning me-1"></i>ยังไม่ได้เปิดใช้งาน
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="twoFactorSwitch"
                                               <?= $user->two_factor_enabled ? 'checked' : '' ?>>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">บัญชีที่เชื่อมต่อ</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-google text-danger me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <div class="fw-semibold">Google</div>
                                            <small class="text-muted">ยังไม่ได้เชื่อมต่อ</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger">เชื่อมต่อ</button>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-microsoft text-primary me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <div class="fw-semibold">Microsoft</div>
                                            <small class="text-muted">ยังไม่ได้เชื่อมต่อ</small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary">เชื่อมต่อ</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notifications Tab -->
                    <div class="tab-pane fade" id="notifications">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">ตั้งค่าการแจ้งเตือน</h5>
                            </div>
                            <div class="card-body">
                                <form id="notificationForm">
                                    <div class="mb-4">
                                        <h6 class="mb-3">การแจ้งเตือนทางอีเมล</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailBookingCreated" checked>
                                            <label class="form-check-label" for="emailBookingCreated">
                                                เมื่อสร้างการจองใหม่
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailBookingApproved" checked>
                                            <label class="form-check-label" for="emailBookingApproved">
                                                เมื่อการจองได้รับการอนุมัติ
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailBookingRejected" checked>
                                            <label class="form-check-label" for="emailBookingRejected">
                                                เมื่อการจองถูกปฏิเสธ
                                            </label>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="emailBookingReminder" checked>
                                            <label class="form-check-label" for="emailBookingReminder">
                                                แจ้งเตือนก่อนการประชุม
                                            </label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailBookingCancelled" checked>
                                            <label class="form-check-label" for="emailBookingCancelled">
                                                เมื่อการจองถูกยกเลิก
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-3">การแจ้งเตือนทาง LINE</h6>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="lineBookingReminder" checked>
                                            <label class="form-check-label" for="lineBookingReminder">
                                                แจ้งเตือนก่อนการประชุม
                                            </label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="lineBookingStatus" checked>
                                            <label class="form-check-label" for="lineBookingStatus">
                                                อัพเดทสถานะการจอง
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">แจ้งเตือนล่วงหน้า (นาที)</label>
                                        <select class="form-select" style="width: 200px;">
                                            <option value="15">15 นาที</option>
                                            <option value="30" selected>30 นาที</option>
                                            <option value="60">1 ชั่วโมง</option>
                                            <option value="1440">1 วัน</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>บันทึก
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- History Tab -->
                    <div class="tab-pane fade" id="history">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">ประวัติการจอง</h5>
                                <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-sm btn-outline-primary">
                                    ดูทั้งหมด
                                </a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>รหัส</th>
                                            <th>ห้องประชุม</th>
                                            <th>วันที่</th>
                                            <th>เวลา</th>
                                            <th>สถานะ</th>
                                            <th class="text-end">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBookings as $booking): ?>
                                        <tr>
                                            <td><a href="<?= Url::to(['/booking/view', 'id' => $booking->id]) ?>">#<?= $booking->id ?></a></td>
                                            <td><?= Html::encode($booking->room->name ?? '-') ?></td>
                                            <td><?= Yii::$app->formatter->asDate($booking->booking_date) ?></td>
                                            <td><?= Html::encode(substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5)) ?></td>
                                            <td>
                                                <?php
                                                $statusBadge = [
                                                    'pending' => 'warning',
                                                    'approved' => 'success',
                                                    'completed' => 'info',
                                                    'cancelled' => 'danger',
                                                    'rejected' => 'danger',
                                                ];
                                                $statusLabel = [
                                                    'pending' => 'รออนุมัติ',
                                                    'approved' => 'อนุมัติแล้ว',
                                                    'completed' => 'เสร็จสิ้น',
                                                    'cancelled' => 'ยกเลิก',
                                                    'rejected' => 'ปฏิเสธ',
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $statusBadge[$booking->status] ?? 'secondary' ?>">
                                                    <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <a href="<?= Url::to(['/booking/view', 'id' => $booking->id]) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePasswordForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านปัจจุบัน <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="new_password" required minlength="8">
                        <div class="form-text">อย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวอักษรพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>เปลี่ยนรหัสผ่าน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Toggle edit mode for profile form
var isEditing = false;
document.getElementById('editProfileBtn').addEventListener('click', function() {
    isEditing = true;
    var form = document.getElementById('profileForm');
    form.querySelectorAll('input, select').forEach(function(el) {
        if (el.name !== 'email') { // Email cannot be changed
            el.disabled = false;
        }
    });
    document.getElementById('profileActions').classList.remove('d-none');
    this.classList.add('d-none');
});

document.getElementById('cancelEditBtn').addEventListener('click', function() {
    isEditing = false;
    var form = document.getElementById('profileForm');
    form.querySelectorAll('input, select').forEach(function(el) {
        el.disabled = true;
    });
    document.getElementById('profileActions').classList.add('d-none');
    document.getElementById('editProfileBtn').classList.remove('d-none');
    form.reset();
});

// Handle hash for tabs
var hash = window.location.hash;
if (hash) {
    var tab = document.querySelector('a[href="' + hash + '"]');
    if (tab) {
        new bootstrap.Tab(tab).show();
    }
}

// Update hash on tab change
document.querySelectorAll('#profileTabs a[data-bs-toggle="tab"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function(e) {
        history.pushState(null, null, e.target.getAttribute('href'));
    });
});
JS;
$this->registerJs($js);
?>
