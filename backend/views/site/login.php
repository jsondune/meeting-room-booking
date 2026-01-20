<?php
/**
 * Login View - Backend Authentication
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var backend\models\LoginForm $model
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'เข้าสู่ระบบ';
$this->context->layout = 'main-login';

// Check if OAuth providers are enabled
$googleEnabled = \common\models\SystemSetting::getValue('oauth_google_enabled', false);
$azureEnabled = \common\models\SystemSetting::getValue('oauth_azure_enabled', false);
$thaidEnabled = \common\models\SystemSetting::getValue('oauth_thaid_enabled', false);
$hasOAuth = $googleEnabled || $azureEnabled || $thaidEnabled;

// Check if captcha is required
$showCaptcha = $model->isCaptchaRequired();
?>

<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="bi bi-building"></i>
        </div>
        <h1>ระบบจัดการห้องประชุม</h1>
        <p>Backend Management System</p>
    </div>
    
    <div class="auth-body">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        
        <?php if ($model->isAccountLocked()): ?>
            <div class="alert alert-danger">
                <i class="bi bi-shield-exclamation me-2"></i>
                บัญชีถูกล็อคชั่วคราว โปรดรอสักครู่แล้วลองใหม่อีกครั้ง
            </div>
        <?php endif; ?>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'template' => "{input}\n{error}",
            ],
        ]); ?>

        <div class="form-floating mb-3">
            <?= $form->field($model, 'username')->textInput([
                'class' => 'form-control',
                'placeholder' => 'ชื่อผู้ใช้หรืออีเมล',
                'autofocus' => true,
            ]) ?>
            <label for="loginform-username">
                <i class="bi bi-person me-1"></i>ชื่อผู้ใช้หรืออีเมล
            </label>
        </div>

        <div class="form-floating mb-3">
            <?= $form->field($model, 'password')->passwordInput([
                'class' => 'form-control',
                'placeholder' => 'รหัสผ่าน',
            ]) ?>
            <label for="loginform-password">
                <i class="bi bi-lock me-1"></i>รหัสผ่าน
            </label>
        </div>

        <?php if ($showCaptcha): ?>
            <div class="mb-3">
                <?= $form->field($model, 'captcha')->widget(Captcha::class, [
                    'template' => '<div class="row"><div class="col-5">{image}</div><div class="col-7">{input}</div></div>',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'รหัสตรวจสอบ',
                    ],
                    'imageOptions' => [
                        'style' => 'cursor:pointer; border-radius: 0.5rem;',
                        'title' => 'คลิกเพื่อเปลี่ยนรูป',
                    ],
                ]) ?>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <?= $form->field($model, 'rememberMe')->checkbox([
                'template' => '<div class="form-check">{input} {label}</div>',
                'labelOptions' => ['class' => 'form-check-label'],
            ]) ?>
            
            <?= Html::a('ลืมรหัสผ่าน?', ['forgot-password'], ['class' => 'text-decoration-none']) ?>
        </div>

        <?= Html::submitButton(
            '<i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ',
            ['class' => 'btn btn-primary w-100 btn-lg', 'name' => 'login-button']
        ) ?>

        <?php ActiveForm::end(); ?>

        <?php if ($hasOAuth): ?>
            <div class="divider">หรือเข้าสู่ระบบด้วย</div>
            
            <div class="d-grid gap-2">
                <?php if ($googleEnabled): ?>
                    <?= Html::a(
                        '<svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        เข้าสู่ระบบด้วย Google',
                        ['oauth', 'provider' => 'google'],
                        ['class' => 'btn btn-oauth btn-google']
                    ) ?>
                <?php endif; ?>
                
                <?php if ($azureEnabled): ?>
                    <?= Html::a(
                        '<svg width="20" height="20" viewBox="0 0 23 23"><path fill="#f35325" d="M1 1h10v10H1z"/><path fill="#81bc06" d="M12 1h10v10H12z"/><path fill="#05a6f0" d="M1 12h10v10H1z"/><path fill="#ffba08" d="M12 12h10v10H12z"/></svg>
                        เข้าสู่ระบบด้วย Microsoft',
                        ['oauth', 'provider' => 'azure'],
                        ['class' => 'btn btn-oauth btn-azure']
                    ) ?>
                <?php endif; ?>
                
                <?php if ($thaidEnabled): ?>
                    <?= Html::a(
                        '<svg width="20" height="20" viewBox="0 0 24 24"><circle fill="#003d7c" cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="10" font-weight="bold">ID</text></svg>
                        เข้าสู่ระบบด้วย ThaID',
                        ['oauth', 'provider' => 'thaid'],
                        ['class' => 'btn btn-oauth btn-thaid']
                    ) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="auth-footer">
        <a href="<?= Yii::$app->params['frontendUrl'] ?? '/' ?>">
            <i class="bi bi-arrow-left me-1"></i>กลับหน้าหลัก
        </a>
    </div>
</div>

<?php
$js = <<<JS
// Form floating label animation
document.querySelectorAll('.form-floating input').forEach(function(input) {
    input.addEventListener('focus', function() {
        this.parentElement.classList.add('focused');
    });
    input.addEventListener('blur', function() {
        if (!this.value) {
            this.parentElement.classList.remove('focused');
        }
    });
});

// Show/hide password toggle (optional enhancement)
const passwordField = document.getElementById('loginform-password');
if (passwordField) {
    const wrapper = passwordField.parentElement;
    const toggleBtn = document.createElement('button');
    toggleBtn.type = 'button';
    toggleBtn.className = 'btn btn-link position-absolute end-0 top-50 translate-middle-y text-muted';
    toggleBtn.style.cssText = 'z-index: 10; padding: 0.5rem;';
    toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
    wrapper.style.position = 'relative';
    wrapper.appendChild(toggleBtn);
    
    toggleBtn.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
    });
}
JS;
$this->registerJs($js);
?>
