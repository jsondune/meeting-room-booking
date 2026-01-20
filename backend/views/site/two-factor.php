<?php
/**
 * Two-Factor Authentication View - Backend
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'ยืนยันตัวตนสองชั้น';
$this->context->layout = 'main-login';
?>

<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="bi bi-shield-check"></i>
        </div>
        <h1>ยืนยันตัวตน 2FA</h1>
        <p>กรอกรหัส 6 หลักจากแอป Authenticator</p>
    </div>
    
    <div class="auth-body">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        
        <div class="text-center mb-4">
            <i class="bi bi-phone text-primary" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">
                เปิดแอป Google Authenticator หรือ Microsoft Authenticator 
                <br>แล้วกรอกรหัส 6 หลักที่แสดงอยู่
            </p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => '2fa-form',
            'fieldConfig' => [
                'template' => "{input}\n{error}",
            ],
        ]); ?>

        <div class="mb-4">
            <div class="d-flex justify-content-center gap-2">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" 
                           class="form-control text-center code-input" 
                           style="width: 50px; height: 60px; font-size: 1.5rem; font-weight: bold;"
                           maxlength="1" 
                           pattern="[0-9]"
                           inputmode="numeric"
                           name="code[]"
                           id="code-<?= $i ?>"
                           <?= $i === 1 ? 'autofocus' : '' ?>>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="two_factor_code" id="two-factor-code">
        </div>

        <?= Html::submitButton(
            '<i class="bi bi-check-lg me-1"></i> ยืนยัน',
            ['class' => 'btn btn-primary w-100 btn-lg', 'name' => 'verify-button']
        ) ?>

        <?php ActiveForm::end(); ?>
        
        <div class="text-center mt-4">
            <p class="text-muted small mb-2">มีปัญหาในการยืนยันตัวตน?</p>
            <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#helpModal">
                <i class="bi bi-question-circle me-1"></i>ขอความช่วยเหลือ
            </a>
        </div>
    </div>
    
    <div class="auth-footer">
        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>">
            <i class="bi bi-arrow-left me-1"></i>กลับหน้าเข้าสู่ระบบ
        </a>
    </div>
</div>

<!-- Help Modal -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-question-circle me-2"></i>ความช่วยเหลือ 2FA
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>ไม่สามารถเข้าถึงแอป Authenticator ได้?</h6>
                <p class="text-muted small">กรุณาติดต่อผู้ดูแลระบบเพื่อขอรีเซ็ตการยืนยันตัวตนสองชั้น</p>
                
                <hr>
                
                <h6>รหัสไม่ถูกต้อง?</h6>
                <ul class="text-muted small">
                    <li>ตรวจสอบว่าเวลาในโทรศัพท์ถูกต้อง</li>
                    <li>รหัสจะเปลี่ยนทุก 30 วินาที กรุณารอจนกว่ารหัสใหม่จะปรากฏ</li>
                    <li>ตรวจสอบว่าใช้บัญชีที่ถูกต้องในแอป Authenticator</li>
                </ul>
                
                <hr>
                
                <h6>ติดต่อผู้ดูแลระบบ</h6>
                <p class="text-muted small mb-0">
                    <i class="bi bi-envelope me-1"></i> admin@bizco.co.th
                    <br>
                    <i class="bi bi-telephone me-1"></i> 02-XXX-XXXX
                </p>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Auto-focus next input
const inputs = document.querySelectorAll('.code-input');
inputs.forEach((input, index) => {
    input.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateHiddenField();
    });
    
    input.addEventListener('keydown', function(e) {
        if (e.key === 'Backspace' && this.value === '' && index > 0) {
            inputs[index - 1].focus();
        }
    });
    
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
        [...pastedData].forEach((char, i) => {
            if (inputs[i]) {
                inputs[i].value = char;
            }
        });
        updateHiddenField();
        if (pastedData.length === 6) {
            inputs[5].focus();
        }
    });
});

function updateHiddenField() {
    const code = Array.from(inputs).map(i => i.value).join('');
    document.getElementById('two-factor-code').value = code;
}
JS;
$this->registerJs($js);
?>
