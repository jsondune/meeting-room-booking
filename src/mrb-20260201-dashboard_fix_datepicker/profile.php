<?php
/**
 * User Profile Page
 * Frontend Profile View
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $stats array */

$this->title = 'โปรไฟล์ของฉัน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-profile">
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
                            <a href="<?= Url::to(['/profile/edit']) ?>" 
                               class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle shadow-sm"
                               title="เปลี่ยนรูปโปรไฟล์">
                                <i class="bi bi-camera"></i>
                            </a>
                        </div>
                        
                        <h4 class="mb-1"><?= Html::encode($user->first_name . ' ' . $user->last_name) ?></h4>
                        <p class="text-muted mb-2"><?= Html::encode($user->position ?? '-') ?></p>
                        <?php if ($user->department): ?>
                            <span class="badge bg-primary"><?= Html::encode($user->department->name_th ?? '-') ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-light">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="fw-bold text-primary"><?= $stats['total'] ?? 0 ?></div>
                                <small class="text-muted">การจองทั้งหมด</small>
                            </div>
                            <div class="col-4 border-start border-end">
                                <div class="fw-bold text-success"><?= $stats['completed'] ?? 0 ?></div>
                                <small class="text-muted">สำเร็จ</small>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-danger"><?= $stats['cancelled'] ?? 0 ?></div>
                                <small class="text-muted">ยกเลิก</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>การดำเนินการด่วน</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="<?= Url::to(['/booking/create']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-plus-circle me-2 text-primary"></i>จองห้องประชุมใหม่
                        </a>
                        <a href="<?= Url::to(['/booking/index']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-calendar-check me-2 text-success"></i>การจองของฉัน
                        </a>
                        <a href="<?= Url::to(['/profile/edit']) ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-pencil me-2 text-warning"></i>แก้ไขโปรไฟล์
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Profile Info Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว</h5>
                        <a href="<?= Url::to(['/profile/edit']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil me-1"></i>แก้ไข
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">ชื่อผู้ใช้</label>
                                <div class="fw-semibold"><?= Html::encode($user->username) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">อีเมล</label>
                                <div class="fw-semibold"><?= Html::encode($user->email) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">ชื่อ</label>
                                <div class="fw-semibold"><?= Html::encode($user->first_name) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">นามสกุล</label>
                                <div class="fw-semibold"><?= Html::encode($user->last_name) ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">โทรศัพท์</label>
                                <div class="fw-semibold"><?= Html::encode($user->phone ?? '-') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">ตำแหน่ง</label>
                                <div class="fw-semibold"><?= Html::encode($user->position ?? '-') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">หน่วยงาน</label>
                                <div class="fw-semibold"><?= Html::encode($user->department->name_th ?? '-') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">สมาชิกตั้งแต่</label>
                                <div class="fw-semibold"><?= Yii::$app->formatter->asDate($user->created_at, 'long') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Info Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>ข้อมูลบัญชี</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">สถานะบัญชี</label>
                                <div>
                                    <?php if ($user->status == 10): ?>
                                        <span class="badge bg-success">ใช้งานได้</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">ถูกระงับ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">บทบาท</label>
                                <div>
                                    <span class="badge bg-primary"><?= Html::encode(ucfirst($user->role ?? 'user')) ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">เข้าสู่ระบบล่าสุด</label>
                                <div class="fw-semibold">
                                    <?= $user->last_login_at ? Yii::$app->formatter->asDatetime($user->last_login_at) : 'ยังไม่เคยเข้าสู่ระบบ' ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small mb-1">IP ล่าสุด</label>
                                <div class="fw-semibold"><?= Html::encode($user->last_login_ip ?? '-') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Security Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-lock me-2"></i>ความปลอดภัย</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div>
                                <h6 class="mb-1">เปลี่ยนรหัสผ่าน</h6>
                                <small class="text-muted">แนะนำให้เปลี่ยนรหัสผ่านเป็นประจำเพื่อความปลอดภัย</small>
                            </div>
                            <a href="<?= Url::to(['/site/change-password']) ?>" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-key me-1"></i>เปลี่ยนรหัสผ่าน
                            </a>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">การเชื่อมต่อบัญชี</h6>
                                <small class="text-muted">เชื่อมต่อกับ Google, Microsoft หรือ ThaiID</small>
                            </div>
                            <a href="<?= Url::to(['/profile/connections']) ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-link-45deg me-1"></i>จัดการการเชื่อมต่อ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.site-profile .card {
    transition: all 0.2s ease;
}
.site-profile .list-group-item {
    border-left: 3px solid transparent;
    transition: all 0.2s ease;
}
.site-profile .list-group-item:hover {
    background-color: #f8f9fa;
    border-left-color: var(--bs-primary);
}
</style>
