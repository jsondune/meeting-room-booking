<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

$this->title = 'เข้าสู่ระบบ';
?>

<h4 class="text-center mb-4">เข้าสู่ระบบ</h4>

<!-- OAuth Buttons -->
<div class="oauth-buttons mb-4">
    <a href="<?= Url::to(['/auth/azure']) ?>" class="btn btn-oauth btn-microsoft w-100 mb-2">
        <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/Microsoft_logo.svg" 
             alt="Microsoft" width="20" height="20">
        เข้าสู่ระบบด้วย Microsoft 365
    </a>
    <a href="<?= Url::to(['/auth/google']) ?>" class="btn btn-oauth btn-google w-100 mb-2">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" 
             alt="Google" width="20" height="20">
        เข้าสู่ระบบด้วย Google
    </a>
    <a href="<?= Url::to(['/auth/thaid']) ?>" class="btn btn-oauth btn-thaid w-100">
        <i class="fas fa-id-card"></i>
        เข้าสู่ระบบด้วย ThaID
    </a>
</div>

<div class="divider">
    <span>หรือ</span>
</div>

<!-- Login Form -->
<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]); ?>

<div class="mb-3">
    <?= $form->field($model, 'username')->textInput([
        'autofocus' => true,
        'placeholder' => 'ชื่อผู้ใช้หรืออีเมล',
        'class' => 'form-control',
    ])->label('ชื่อผู้ใช้ / อีเมล') ?>
</div>

<div class="mb-3">
    <?= $form->field($model, 'password')->passwordInput([
        'placeholder' => 'รหัสผ่าน',
        'class' => 'form-control password-input',
    ])->label('รหัสผ่าน') ?>
    <div class="input-group mt-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="showPassword">
            <label class="form-check-label text-muted small" for="showPassword">
                แสดงรหัสผ่าน
            </label>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col">
        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
            'labelOptions' => ['class' => 'form-check-label'],
        ]) ?>
    </div>
    <div class="col text-end">
        <a href="<?= Url::to(['/site/request-password-reset']) ?>" class="text-decoration-none small">
            ลืมรหัสผ่าน?
        </a>
    </div>
</div>

<?= Html::submitButton('<i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ', [
    'class' => 'btn btn-primary w-100',
    'name' => 'login-button',
]) ?>

<?php ActiveForm::end(); ?>

<div class="auth-footer">
    <p class="mb-0">
        ยังไม่มีบัญชี? 
        <a href="<?= Url::to(['/site/signup']) ?>">ลงทะเบียน</a>
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showPassword = document.getElementById('showPassword');
    const passwordInput = document.querySelector('.password-input');
    
    if (showPassword && passwordInput) {
        showPassword.addEventListener('change', function() {
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    }
});
</script>
