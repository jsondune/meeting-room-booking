<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\SignupForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;
use common\models\Department;

$this->title = 'ลงทะเบียน';
?>

<h5 class="text-center mb-3">สร้างบัญชีใหม่</h5>

<!-- OAuth Buttons - Compact Row -->
<div class="oauth-buttons mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <a href="<?= Url::to(['/auth/azure']) ?>" class="btn btn-oauth btn-microsoft w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 23 23">
                    <rect x="1" y="1" width="10" height="10" fill="#f25022"/>
                    <rect x="12" y="1" width="10" height="10" fill="#7fba00"/>
                    <rect x="1" y="12" width="10" height="10" fill="#00a4ef"/>
                    <rect x="12" y="12" width="10" height="10" fill="#ffb900"/>
                </svg>
                <span class="d-none d-md-inline">Microsoft</span>
                <span class="d-md-none">MS</span>
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['/auth/google']) ?>" class="btn btn-oauth btn-google w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                    <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                    <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                    <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                    <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                </svg>
                Google
            </a>
        </div>
        <div class="col-md-4">
            <a href="<?= Url::to(['/auth/thaid']) ?>" class="btn btn-oauth btn-thaid w-100">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white">
                    <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 12h4v2H4v-2zm10 6H4v-2h10v2zm6 0h-4v-2h4v2zm0-4H10v-2h10v2z"/>
                </svg>
                ThaID
            </a>
        </div>
    </div>
</div>

<div class="divider">
    <span>หรือกรอกข้อมูลด้านล่าง</span>
</div>

<!-- Signup Form -->
<?php $form = ActiveForm::begin([
    'id' => 'signup-form',
    'enableClientValidation' => true,
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'errorOptions' => ['class' => 'invalid-feedback d-block'],
    ],
]); ?>

<!-- Row 1: Name -->
<div class="row">
    <div class="col-md-12 mb-2">
        <?= $form->field($model, 'full_name')->textInput([
            'placeholder' => 'ชื่อ-นามสกุล',
            'class' => 'form-control',
        ])->label('ชื่อ <span class="text-danger">*</span>') ?>
    </div>
</div>

<!-- Row 2: Username & Email -->
<div class="row">
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'username')->textInput([
            'placeholder' => 'ภาษาอังกฤษหรือตัวเลข',
            'class' => 'form-control',
        ])->label('ชื่อผู้ใช้ <span class="text-danger">*</span>') ?>
    </div>
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'email')->textInput([
            'type' => 'email',
            'placeholder' => 'example@email.com',
            'class' => 'form-control',
        ])->label('อีเมล <span class="text-danger">*</span>') ?>
    </div>
</div>

<!-- Row 3: Phone & Department -->
<div class="row">
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'phone')->textInput([
            'placeholder' => '08X-XXX-XXXX',
            'class' => 'form-control',
        ])->label('เบอร์โทรศัพท์') ?>
    </div>
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'department_id')->dropDownList(
            Department::getDropdownList(),
            [
                'prompt' => '-- เลือกหน่วยงาน --',
                'class' => 'form-select',
            ]
        )->label('หน่วยงาน') ?>
    </div>
</div>

<!-- Row 4: Password -->
<div class="row">
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'อย่างน้อย 8 ตัวอักษร',
            'class' => 'form-control',
        ])->label('รหัสผ่าน <span class="text-danger">*</span>') ?>
    </div>
    <div class="col-md-6 mb-2">
        <?= $form->field($model, 'password_confirm')->passwordInput([
            'placeholder' => 'ยืนยันรหัสผ่าน',
            'class' => 'form-control',
        ])->label('ยืนยันรหัสผ่าน <span class="text-danger">*</span>') ?>
    </div>
</div>

<!-- Terms Agreement -->
<div class="mb-3">
    <?= $form->field($model, 'agree_terms')->checkbox([
        'template' => '<div class="form-check">{input}{label}{error}</div>',
        'labelOptions' => ['class' => 'form-check-label small'],
    ])->label('ฉันยอมรับ <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal" class="text-primary">เงื่อนไขการใช้งาน</a> และ <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal" class="text-primary">นโยบายความเป็นส่วนตัว</a>') ?>
</div>

<!-- Submit Button -->
<div class="d-grid mb-3">
    <?= Html::submitButton('<i class="fas fa-user-plus me-2"></i>ลงทะเบียน', [
        'class' => 'btn btn-primary btn-lg', 
        'name' => 'signup-button'
    ]) ?>
</div>

<?php ActiveForm::end(); ?>

<p class="text-center text-muted mb-0 small">
    มีบัญชีอยู่แล้ว? <a href="<?= Url::to(['site/login']) ?>" class="fw-semibold">เข้าสู่ระบบ</a>
</p>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-file-contract me-2"></i>เงื่อนไขการใช้งาน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold text-primary">1. การใช้งานระบบ</h6>
                <p class="small">ผู้ใช้งานตกลงที่จะใช้ระบบจองห้องประชุมนี้เพื่อวัตถุประสงค์ที่ถูกต้องตามกฎหมายและระเบียบขององค์กรเท่านั้น</p>
                
                <h6 class="fw-bold text-primary">2. ข้อมูลส่วนบุคคล</h6>
                <p class="small">ข้อมูลที่ท่านให้ไว้จะถูกเก็บรักษาอย่างปลอดภัยและใช้เพื่อการจัดการระบบจองห้องประชุมเท่านั้น</p>
                
                <h6 class="fw-bold text-primary">3. การจองห้องประชุม</h6>
                <p class="small">การจองห้องประชุมต้องเป็นไปตามระเบียบที่กำหนด หากไม่ใช้งานตามเวลาที่จอง ระบบอาจยกเลิกการจองโดยอัตโนมัติ</p>
                
                <h6 class="fw-bold text-primary">4. ความรับผิดชอบ</h6>
                <p class="small mb-0">ผู้ใช้งานต้องรับผิดชอบในการดูแลรักษาห้องประชุมและอุปกรณ์ที่ใช้งาน</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i>รับทราบ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-shield-alt me-2"></i>นโยบายความเป็นส่วนตัว</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold text-primary">1. ข้อมูลที่เราเก็บรวบรวม</h6>
                <p class="small">เราเก็บรวบรวมข้อมูลที่จำเป็นสำหรับการใช้งานระบบ ได้แก่ ชื่อ-นามสกุล อีเมล เบอร์โทรศัพท์ และหน่วยงาน</p>
                
                <h6 class="fw-bold text-primary">2. วัตถุประสงค์การใช้ข้อมูล</h6>
                <p class="small">ข้อมูลของท่านจะถูกใช้เพื่อการยืนยันตัวตน การจองห้องประชุม และการติดต่อสื่อสารที่เกี่ยวข้องเท่านั้น</p>
                
                <h6 class="fw-bold text-primary">3. การรักษาความปลอดภัย</h6>
                <p class="small">เราใช้มาตรการรักษาความปลอดภัยที่เหมาะสมเพื่อปกป้องข้อมูลส่วนบุคคลของท่าน</p>
                
                <h6 class="fw-bold text-primary">4. สิทธิของท่าน</h6>
                <p class="small mb-0">ท่านมีสิทธิในการเข้าถึง แก้ไข หรือลบข้อมูลส่วนบุคคลของท่านได้ตลอดเวลา</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i>รับทราบ
                </button>
            </div>
        </div>
    </div>
</div>
