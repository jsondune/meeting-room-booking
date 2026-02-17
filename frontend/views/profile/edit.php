<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var array $departments */

$this->title = Yii::t('app', 'แก้ไขโปรไฟล์');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'โปรไฟล์ของฉัน'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .profile-edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }
    .avatar-upload {
        position: relative;
        display: inline-block;
    }
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        border: 4px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    .avatar-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: transform 0.3s;
    }
    .avatar-overlay:hover {
        transform: scale(1.1);
    }
    .form-card {
        border-radius: 1rem;
    }
    .form-section {
        margin-bottom: 2rem;
    }
    .form-section-title {
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f3f4;
    }
    .password-requirements {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .password-requirements li {
        margin-bottom: 0.25rem;
    }
    .password-requirements li.valid {
        color: #198754;
    }
    .password-requirements li.valid::before {
        content: '✓ ';
    }
");
?>

<div class="profile-edit">
    <!-- Header -->
    <div class="profile-edit-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="avatar-upload">
                    <img src="<?= Html::encode($model->getAvatarUrl()) ?>" class="avatar-preview" id="avatarPreview" alt="Avatar">
                    <label for="user-avatarfile" class="avatar-overlay">
                        <i class="bi bi-camera text-primary"></i>
                    </label>
                </div>
            </div>
            <div class="col">
                <h2 class="mb-1"><?= Html::encode($this->title) ?></h2>
                <p class="mb-0 opacity-75">อัพเดทข้อมูลส่วนตัวและการตั้งค่าของคุณ</p>
            </div>
            <div class="col-auto">
                <?= Html::a('<i class="bi bi-arrow-left me-2"></i>กลับ', ['index'], ['class' => 'btn btn-light']) ?>
            </div>
        </div>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'profile-form',
        'options' => ['enctype' => 'multipart/form-data'],
    ]); ?>
    
    <!-- Hidden avatar file input -->
    <div style="display: none;">
        <?= $form->field($model, 'avatarFile')->fileInput(['accept' => 'image/*']) ?>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card form-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control bg-light" value="<?= Html::encode($model->username) ?>" readonly>
                            <small class="text-muted">ไม่สามารถเปลี่ยนชื่อผู้ใช้ได้</small>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true, 'placeholder' => 'ชื่อ-นามสกุล'])->label('ชื่อ-นามสกุล <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'placeholder' => 'อีเมล'])->label('อีเมล <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'placeholder' => 'เบอร์โทรศัพท์'])->label('เบอร์โทรศัพท์') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'department_id')->dropDownList(
                                $departments,
                                ['prompt' => '-- เลือกแผนก --']
                            )->label('แผนก/หน่วยงาน') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'position')->textInput(['maxlength' => true, 'placeholder' => 'ตำแหน่ง'])->label('ตำแหน่ง') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card form-card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน</h5>
                    <small class="text-muted">เว้นว่างไว้หากไม่ต้องการเปลี่ยน</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">รหัสผ่านปัจจุบัน</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="current_password" id="currentPassword" placeholder="รหัสผ่านปัจจุบัน">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPassword')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">รหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password" id="newPassword" placeholder="รหัสผ่านใหม่">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ยืนยันรหัสผ่านใหม่</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="confirm_password" id="confirmPassword" placeholder="ยืนยันรหัสผ่านใหม่">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="password-requirements">
                                <p class="mb-2">รหัสผ่านต้องประกอบด้วย:</p>
                                <ul class="list-unstyled mb-0">
                                    <li id="req-length">อย่างน้อย 8 ตัวอักษร</li>
                                    <li id="req-uppercase">ตัวพิมพ์ใหญ่อย่างน้อย 1 ตัว</li>
                                    <li id="req-lowercase">ตัวพิมพ์เล็กอย่างน้อย 1 ตัว</li>
                                    <li id="req-number">ตัวเลขอย่างน้อย 1 ตัว</li>
                                    <li id="req-special">อักขระพิเศษอย่างน้อย 1 ตัว (!@#$%^&*)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="d-flex justify-content-between mb-4">
                <?= Html::a('ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary btn-lg']) ?>
                <?= Html::submitButton('<i class="bi bi-check-lg me-2"></i>บันทึกการเปลี่ยนแปลง', ['class' => 'btn btn-primary btn-lg']) ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Status -->
            <div class="card form-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>สถานะบัญชี</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>สถานะ</span>
                        <?php if ($model->status == 10): ?>
                            <span class="badge bg-success">ใช้งานได้</span>
                        <?php else: ?>
                            <span class="badge bg-danger">ถูกระงับ</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>ยืนยันอีเมล</span>
                        <?php if ($model->email_verified_at): ?>
                            <span class="badge bg-success">ยืนยันแล้ว</span>
                        <?php else: ?>
                            <span class="badge bg-warning">ยังไม่ยืนยัน</span>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>2FA</span>
                        <?php if ($model->two_factor_enabled): ?>
                            <span class="badge bg-success">เปิดใช้งาน</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">ปิด</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Connected Accounts -->
            <div class="card form-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-link-45deg me-2"></i>บัญชีที่เชื่อมต่อ</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-google text-danger fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Google</div>
                                <small class="text-muted"><?= $model->google_id ? 'เชื่อมต่อแล้ว' : 'ไม่ได้เชื่อมต่อ' ?></small>
                            </div>
                        </div>
                        <?php if ($model->google_id): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger">ยกเลิก</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-primary">เชื่อมต่อ</button>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-microsoft text-primary fs-4 me-3"></i>
                            <div>
                                <div class="fw-bold">Microsoft</div>
                                <small class="text-muted"><?= $model->azure_id ? 'เชื่อมต่อแล้ว' : 'ไม่ได้เชื่อมต่อ' ?></small>
                            </div>
                        </div>
                        <?php if ($model->azure_id): ?>
                            <button type="button" class="btn btn-sm btn-outline-danger">ยกเลิก</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-primary">เชื่อมต่อ</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="card form-card border-danger mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>โซนอันตราย</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">การดำเนินการเหล่านี้ไม่สามารถย้อนกลับได้</p>
                    <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash me-2"></i>ลบบัญชี
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>ยืนยันการลบบัญชี</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>คำเตือน!</strong> การลบบัญชีจะไม่สามารถย้อนกลับได้ ข้อมูลทั้งหมดของคุณจะถูกลบอย่างถาวร รวมถึง:
                </div>
                <ul>
                    <li>ข้อมูลโปรไฟล์ทั้งหมด</li>
                    <li>ประวัติการจองทั้งหมด</li>
                    <li>รีวิวและคะแนนทั้งหมด</li>
                </ul>
                <div class="mb-3">
                    <label class="form-label">พิมพ์ "DELETE" เพื่อยืนยัน</label>
                    <input type="text" class="form-control" id="deleteConfirmation" placeholder="DELETE">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDelete" disabled>ลบบัญชีถาวร</button>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
// Avatar preview
document.getElementById('user-avatarfile').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Toggle password visibility
window.togglePassword = function(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
};

// Password validation
document.getElementById('newPassword').addEventListener('input', function() {
    const password = this.value;
    
    // Length check
    const lengthValid = password.length >= 8;
    document.getElementById('req-length').classList.toggle('valid', lengthValid);
    
    // Uppercase check
    const uppercaseValid = /[A-Z]/.test(password);
    document.getElementById('req-uppercase').classList.toggle('valid', uppercaseValid);
    
    // Lowercase check
    const lowercaseValid = /[a-z]/.test(password);
    document.getElementById('req-lowercase').classList.toggle('valid', lowercaseValid);
    
    // Number check
    const numberValid = /[0-9]/.test(password);
    document.getElementById('req-number').classList.toggle('valid', numberValid);
    
    // Special char check
    const specialValid = /[!@#$%^&*]/.test(password);
    document.getElementById('req-special').classList.toggle('valid', specialValid);
});

// Delete confirmation
document.getElementById('deleteConfirmation').addEventListener('input', function() {
    document.getElementById('confirmDelete').disabled = this.value !== 'DELETE';
});

document.getElementById('confirmDelete').addEventListener('click', function() {
    // Submit delete request
    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบัญชี? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
        window.location.href = '<?= Url::to(['profile/delete']) ?>';
    }
});
JS);
?>
