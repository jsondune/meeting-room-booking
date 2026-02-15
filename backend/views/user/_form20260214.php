<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $departments */
/** @var array $roles */

$isNewRecord = $model->isNewRecord;
?>

<div class="user-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Account Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-person-badge me-2"></i>ข้อมูลบัญชี</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'username')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่อผู้ใช้งาน',
                                'readonly' => !$isNewRecord
                            ])->label('ชื่อผู้ใช้ <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'email')->textInput([
                                'type' => 'email',
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'email@example.com'
                            ])->label('อีเมล <span class="text-danger">*</span>') ?>
                        </div>
                        <?php if ($isNewRecord): ?>
                            <div class="col-md-6">
                                <?= $form->field($model, 'password')->passwordInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'รหัสผ่าน'
                                ])->label('รหัสผ่าน <span class="text-danger">*</span>') ?>
                                <small class="text-muted">ต้องมีอย่างน้อย 8 ตัวอักษร</small>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'password_confirm')->passwordInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'ยืนยันรหัสผ่าน'
                                ])->label('ยืนยันรหัสผ่าน <span class="text-danger">*</span>') ?>
                            </div>
                        <?php else: ?>
                            <div class="col-md-6">
                                <label class="form-label">รหัสผ่านใหม่</label>
                                <input type="password" class="form-control" name="User[password]" placeholder="เว้นว่างถ้าไม่ต้องการเปลี่ยน">
                                <small class="text-muted">เว้นว่างถ้าไม่ต้องการเปลี่ยนรหัสผ่าน</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" class="form-control" name="User[password_confirm]" placeholder="ยืนยันรหัสผ่านใหม่">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-person me-2"></i>ข้อมูลส่วนตัว</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <?= $form->field($model, 'title')->dropDownList([
                                'นาย' => 'นาย',
                                'นาง' => 'นาง',
                                'นางสาว' => 'นางสาว',
                                'ดร.' => 'ดร.',
                                'ผศ.' => 'ผศ.',
                                'รศ.' => 'รศ.',
                                'ศ.' => 'ศ.',
                            ], [
                                'class' => 'form-select',
                                'prompt' => 'คำนำหน้า'
                            ])->label('คำนำหน้า') ?>
                        </div>
                        <div class="col-md-10">
                            <?= $form->field($model, 'full_name')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่อ-นามสกุล'
                            ])->label('ชื่อ-นามสกุล <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => '0x-xxx-xxxx'
                            ])->label('เบอร์โทรศัพท์') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'position')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ตำแหน่ง'
                            ])->label('ตำแหน่ง') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'department_id')->dropDownList($departments, [
                                'class' => 'form-select',
                                'prompt' => '-- เลือกหน่วยงาน --'
                            ])->label('หน่วยงาน') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>ที่อยู่</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'address')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'ที่อยู่...'
                    ])->label('ที่อยู่') ?>
                </div>
            </div>

            <!-- OAuth Connections (for edit only) -->
            <?php if (!$isNewRecord && !empty($model->oauthAccounts)): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>บัญชีที่เชื่อมต่อ</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($model->oauthAccounts as $oauth): ?>
                                <div class="list-group-item d-flex align-items-center px-0">
                                    <?php
                                    $providerIcons = [
                                        'google' => ['icon' => 'bi-google', 'color' => 'text-danger'],
                                        'microsoft' => ['icon' => 'bi-microsoft', 'color' => 'text-primary'],
                                        'github' => ['icon' => 'bi-github', 'color' => 'text-dark'],
                                    ];
                                    $provider = $providerIcons[$oauth->provider] ?? ['icon' => 'bi-link', 'color' => 'text-secondary'];
                                    ?>
                                    <i class="bi <?= $provider['icon'] ?> <?= $provider['color'] ?> fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium"><?= ucfirst($oauth->provider) ?></div>
                                        <small class="text-muted">เชื่อมต่อเมื่อ <?= Yii::$app->formatter->asDatetime($oauth->created_at) ?></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="if(confirm('ต้องการยกเลิกการเชื่อมต่อ?')) window.location.href='<?= Url::to(['disconnect-oauth', 'id' => $model->id, 'provider' => $oauth->provider]) ?>'">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Role -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-shield-check me-2"></i>สถานะและสิทธิ์</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'status')->dropDownList([
                        10 => 'ใช้งานปกติ',
                        9 => 'รอยืนยันอีเมล',
                        0 => 'ถูกระงับ',
                    ], [
                        'class' => 'form-select'
                    ])->label('สถานะ') ?>

                    <?= $form->field($model, 'role')->dropDownList($roles, [
                        'class' => 'form-select'
                    ])->label('บทบาท') ?>

                    <div class="d-grid gap-2 mt-4">
                        <?= Html::submitButton(
                            $isNewRecord ? '<i class="bi bi-plus-lg me-1"></i> สร้างผู้ใช้' : '<i class="bi bi-check-lg me-1"></i> บันทึกการเปลี่ยนแปลง',
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                        <?= Html::a('<i class="bi bi-x-lg me-1"></i> ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>

            <!-- Avatar Upload -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-camera me-2"></i>รูปโปรไฟล์</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <?php if ($model->avatar): ?>
                            <img id="avatar-preview" src="<?= $model->avatar ?>" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        <?php else: ?>
                            <?php
                            $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                            $colorIndex = $model->id ? ($model->id % count($colors)) : 0;
                            $initials = mb_substr($model->full_name ?? $model->username ?? 'U', 0, 1);
                            ?>
                            <div id="avatar-preview" class="rounded-circle <?= $colors[$colorIndex] ?> text-white d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                <span class="fs-1 fw-bold"><?= mb_strtoupper($initials) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="User[avatarFile]" class="form-control" accept="image/*" id="avatar-input">
                    <small class="text-muted d-block mt-2">รองรับ JPG, PNG ขนาดไม่เกิน 2MB</small>
                    
                    <?php if ($model->avatar): ?>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="User[removeAvatar]" id="remove-avatar">
                            <label class="form-check-label" for="remove-avatar">
                                ลบรูปโปรไฟล์
                            </label>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Settings (for edit only) -->
            <?php if (!$isNewRecord): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-lock me-2"></i>ความปลอดภัย</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-medium">2-Factor Authentication</div>
                                <small class="text-muted">การยืนยันตัวตนสองชั้น</small>
                            </div>
                            <?php if ($model->two_factor_enabled): ?>
                                <span class="badge bg-success">เปิดใช้งาน</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <div class="d-grid gap-2">
                            <?= Html::a('<i class="bi bi-key me-1"></i> รีเซ็ตรหัสผ่าน', ['reset-password', 'id' => $model->id], [
                                'class' => 'btn btn-outline-warning btn-sm',
                                'data' => [
                                    'confirm' => 'ระบบจะส่งลิงก์รีเซ็ตรหัสผ่านไปยังอีเมลของผู้ใช้ ต้องการดำเนินการต่อ?',
                                    'method' => 'post',
                                ],
                            ]) ?>
                            
                            <?php if ($model->two_factor_enabled): ?>
                                <?= Html::a('<i class="bi bi-shield-x me-1"></i> ปิด 2FA', ['disable-2fa', 'id' => $model->id], [
                                    'class' => 'btn btn-outline-danger btn-sm',
                                    'data' => [
                                        'confirm' => 'ต้องการปิดการยืนยันตัวตนสองชั้นของผู้ใช้นี้?',
                                        'method' => 'post',
                                    ],
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Activity Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>กิจกรรม</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted d-block">เข้าใช้งานล่าสุด</small>
                            <span><?= $model->last_login_at ? Yii::$app->formatter->asDatetime($model->last_login_at) : 'ยังไม่เคยเข้าใช้งาน' ?></span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">IP ล่าสุด</small>
                            <span><?= Html::encode($model->last_login_ip) ?: '-' ?></span>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">สร้างเมื่อ</small>
                            <span><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                        </div>
                        <div>
                            <small class="text-muted d-block">แก้ไขล่าสุด</small>
                            <span><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
// Avatar preview
document.getElementById('avatar-input').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('avatar-preview');
            // Replace with img element if it's a div
            if (preview.tagName === 'DIV') {
                var img = document.createElement('img');
                img.id = 'avatar-preview';
                img.className = 'rounded-circle';
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';
                preview.parentNode.replaceChild(img, preview);
                preview = img;
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
JS;
$this->registerJs($js);
?>
