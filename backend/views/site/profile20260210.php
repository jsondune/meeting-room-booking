<?php
/**
 * Profile View - Backend User Profile
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var common\models\User $user
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'โปรไฟล์ของฉัน';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
?>

<div class="profile-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-person-circle me-2"></i>
                <?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">จัดการข้อมูลส่วนตัวและการตั้งค่าบัญชี</p>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <?php if ($user->avatar): ?>
                        <img src="<?= $user->getAvatarUrl() ?>" 
                             class="rounded-circle mb-3" 
                             style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #e9ecef;"
                             alt="<?= Html::encode($user->full_name) ?>">
                    <?php else: ?>
                        <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center text-white"
                             style="width: 120px; height: 120px; font-size: 3rem;">
                            <?= mb_substr($user->full_name ?? 'U', 0, 1) ?>
                        </div>
                    <?php endif; ?>
                    
                    <h4 class="mb-1"><?= Html::encode($user->full_name) ?></h4>
                    <p class="text-muted mb-2">@<?= Html::encode($user->username) ?></p>
                    
                    <?php
                    $roleLabels = [
                        'superadmin' => ['label' => 'ผู้ดูแลระบบสูงสุด', 'class' => 'danger'],
                        'admin' => ['label' => 'ผู้ดูแลระบบ', 'class' => 'primary'],
                        'manager' => ['label' => 'ผู้จัดการ', 'class' => 'info'],
                        'staff' => ['label' => 'เจ้าหน้าที่', 'class' => 'success'],
                        'user' => ['label' => 'ผู้ใช้งาน', 'class' => 'secondary'],
                    ];
                    $role = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'secondary'];
                    ?>
                    <span class="badge bg-<?= $role['class'] ?> mb-3"><?= $role['label'] ?></span>
                    
                    <hr>
                    
                    <div class="text-start">
                        <p class="mb-2">
                            <i class="bi bi-envelope text-muted me-2"></i>
                            <?= Html::encode($user->email) ?>
                        </p>
                        <?php if ($user->phone): ?>
                            <p class="mb-2">
                                <i class="bi bi-telephone text-muted me-2"></i>
                                <?= Html::encode($user->phone) ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($user->department): ?>
                            <p class="mb-2">
                                <i class="bi bi-building text-muted me-2"></i>
                                <?= Html::encode($user->department->name_th ?? '-') ?>
                            </p>
                        <?php endif; ?>
                        <?php if ($user->position): ?>
                            <p class="mb-0">
                                <i class="bi bi-briefcase text-muted me-2"></i>
                                <?= Html::encode($user->position) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Account Stats -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>สถิติบัญชี
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">เข้าสู่ระบบครั้งล่าสุด</span>
                        <span><?= $user->last_login_at ? Yii::$app->formatter->asDatetime($user->last_login_at, 'short') : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">จำนวนครั้งที่เข้าสู่ระบบ</span>
                        <span><?= $user->login_count ?? 0 ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">สร้างบัญชีเมื่อ</span>
                        <span><?= Yii::$app->formatter->asDate($user->created_at, 'medium') ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">2FA</span>
                        <?php if ($user->two_factor_enabled): ?>
                            <span class="badge bg-success">เปิดใช้งาน</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ปิดใช้งาน</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'profile-form', 'action' => ['site/update-profile']]); ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ-นามสกุล</label>
                            <?= Html::textInput('User[full_name]', $user->full_name, ['class' => 'form-control']) ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ชื่อผู้ใช้</label>
                            <?= Html::textInput('User[username]', $user->username, ['class' => 'form-control', 'readonly' => true]) ?>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <?= Html::textInput('User[email]', $user->email, ['class' => 'form-control', 'type' => 'email']) ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทรศัพท์</label>
                            <?= Html::textInput('User[phone]', $user->phone, ['class' => 'form-control']) ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">ตำแหน่ง</label>
                        <?= Html::textInput('User[position]', $user->position, ['class' => 'form-control']) ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รูปโปรไฟล์</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                        <div class="form-text">รองรับไฟล์ JPG, PNG, GIF ขนาดไม่เกิน 2MB</div>
                    </div>
                    
                    <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i> บันทึกการเปลี่ยนแปลง', ['class' => 'btn btn-primary']) ?>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['id' => 'password-form', 'action' => ['site/change-password']]); ?>
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านปัจจุบัน</label>
                        <?= Html::passwordInput('current_password', '', ['class' => 'form-control', 'required' => true]) ?>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่านใหม่</label>
                            <?= Html::passwordInput('new_password', '', ['class' => 'form-control', 'required' => true, 'minlength' => 8]) ?>
                            <div class="form-text">อย่างน้อย 8 ตัวอักษร</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <?= Html::passwordInput('confirm_password', '', ['class' => 'form-control', 'required' => true]) ?>
                        </div>
                    </div>
                    
                    <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i> เปลี่ยนรหัสผ่าน', ['class' => 'btn btn-warning']) ?>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-check me-2"></i>การยืนยันตัวตนสองชั้น (2FA)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($user->two_factor_enabled): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            2FA เปิดใช้งานอยู่ บัญชีของคุณได้รับการป้องกันเพิ่มเติม
                        </div>
                        
                        <?= Html::a(
                            '<i class="bi bi-x-lg me-1"></i> ปิดการใช้งาน 2FA',
                            ['site/disable-2fa'],
                            [
                                'class' => 'btn btn-outline-danger',
                                'data' => [
                                    'confirm' => 'ยืนยันการปิดการใช้งาน 2FA?',
                                    'method' => 'post',
                                ],
                            ]
                        ) ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            2FA ยังไม่ได้เปิดใช้งาน แนะนำให้เปิดใช้งานเพื่อเพิ่มความปลอดภัย
                        </div>
                        
                        <?= Html::a(
                            '<i class="bi bi-shield-plus me-1"></i> เปิดใช้งาน 2FA',
                            ['site/setup-2fa'],
                            ['class' => 'btn btn-success']
                        ) ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Connected Accounts -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-link-45deg me-2"></i>บัญชีที่เชื่อมต่อ
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <svg width="24" height="24" viewBox="0 0 24 24" class="me-3">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                <div>
                                    <strong>Google</strong>
                                    <?php if (!empty($user->google_id)): ?>
                                        <br><small class="text-muted">เชื่อมต่อแล้ว</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($user->google_id)): ?>
                                <span class="badge bg-success">เชื่อมต่อแล้ว</span>
                            <?php else: ?>
                                <?= Html::a('เชื่อมต่อ', ['site/oauth', 'provider' => 'google'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <svg width="24" height="24" viewBox="0 0 23 23" class="me-3">
                                    <path fill="#f35325" d="M1 1h10v10H1z"/>
                                    <path fill="#81bc06" d="M12 1h10v10H12z"/>
                                    <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                                    <path fill="#ffba08" d="M12 12h10v10H12z"/>
                                </svg>
                                <div>
                                    <strong>Microsoft</strong>
                                    <?php if (!empty($user->azure_id)): ?>
                                        <br><small class="text-muted">เชื่อมต่อแล้ว</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if (!empty($user->azure_id)): ?>
                                <span class="badge bg-success">เชื่อมต่อแล้ว</span>
                            <?php else: ?>
                                <?= Html::a('เชื่อมต่อ', ['site/oauth', 'provider' => 'azure'], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
