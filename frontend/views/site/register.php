<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'สมัครสมาชิก';
?>

<div class="site-register">
    <div class="container">
        <div class="row justify-content-center py-5">
            <div class="col-md-8 col-lg-6">
                <div class="text-center mb-4">
                    <img src="/images/logo.png" alt="Logo" height="60" class="mb-3" onerror="this.style.display='none'">
                    <h2 class="fw-bold text-primary">ระบบจองห้องประชุม</h2>
                    <p class="text-muted">สมัครสมาชิกเพื่อเริ่มใช้งาน</p>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="card-title text-center mb-4">สร้างบัญชีใหม่</h4>

                        <!-- Social Registration -->
                        <div class="d-grid gap-2 mb-4">
                            <a href="<?= Url::to(['site/auth', 'authclient' => 'google']) ?>" class="btn btn-outline-danger">
                                <i class="bi bi-google me-2"></i>สมัครด้วย Google
                            </a>
                            <a href="<?= Url::to(['site/auth', 'authclient' => 'microsoft']) ?>" class="btn btn-outline-primary">
                                <i class="bi bi-microsoft me-2"></i>สมัครด้วย Microsoft
                            </a>
                        </div>

                        <div class="text-center text-muted mb-4">
                            <span class="bg-white px-3 position-relative" style="z-index: 1;">หรือ</span>
                            <hr class="mt-n2">
                        </div>

                        <!-- Registration Form -->
                        <form id="registerForm" method="post">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="full_name" required 
                                           placeholder="กรอก ชื่อ-นามสกุล">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" required 
                                       placeholder="example@email.com">
                                <div class="form-text">ใช้อีเมลหน่วยงาน (@bizco.co.th) เพื่อการยืนยันตัวตน</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" required 
                                       placeholder="08X-XXX-XXXX" pattern="[0-9]{10}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">หน่วยงาน/แผนก <span class="text-danger">*</span></label>
                                <select class="form-select" name="department_id" required>
                                    <option value="">-- เลือกหน่วยงาน --</option>
                                    <option value="1">กลุ่มงานเทคโนโลยีดิจิทัลและปัญญาประดิษฐ์</option>
                                    <option value="2">กลุ่มงานบริหารทั่วไป</option>
                                    <option value="3">กลุ่มงานวิชาการ</option>
                                    <option value="4">กลุ่มงานกิจการนักศึกษา</option>
                                    <option value="5">กลุ่มงานวิจัยและบริการวิชาการ</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ตำแหน่ง</label>
                                <input type="text" class="form-control" name="position" 
                                       placeholder="เช่น นักวิชาการคอมพิวเตอร์">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password" id="password" 
                                           required minlength="8" placeholder="อย่างน้อย 8 ตัวอักษร">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="bi bi-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <div class="password-strength mt-2" id="passwordStrength"></div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_confirm" id="passwordConfirm" 
                                           required placeholder="กรอกรหัสผ่านอีกครั้ง">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('passwordConfirm')">
                                        <i class="bi bi-eye" id="passwordConfirm-icon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="passwordMatchError" style="display: none;">
                                    รหัสผ่านไม่ตรงกัน
                                </div>
                            </div>

                            <!-- Terms and Conditions -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        ข้าพเจ้ายอมรับ <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">ข้อกำหนดการใช้งาน</a> 
                                        และ <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">นโยบายความเป็นส่วนตัว</a>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-person-plus me-2"></i>สมัครสมาชิก
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        มีบัญชีอยู่แล้ว? 
                        <a href="<?= Url::to(['site/login']) ?>" class="text-primary fw-semibold">เข้าสู่ระบบ</a>
                    </p>
                </div>

                <!-- Help Text -->
                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-question-circle me-1"></i>
                        พบปัญหา? ติดต่อ <a href="mailto:support@bizco.co.th">support@bizco.co.th</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ข้อกำหนดการใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. การใช้งานระบบ</h6>
                <p>ผู้ใช้งานตกลงที่จะใช้ระบบจองห้องประชุมเพื่อวัตถุประสงค์ที่ถูกต้องตามกฎหมายและระเบียบของหน่วยงานเท่านั้น</p>
                
                <h6>2. การจองห้องประชุม</h6>
                <p>การจองห้องประชุมจะต้องเป็นไปตามวัตถุประสงค์ที่ระบุ และต้องใช้ห้องประชุมตามเวลาที่จองไว้</p>
                
                <h6>3. การยกเลิกการจอง</h6>
                <p>ผู้ใช้งานสามารถยกเลิกการจองได้ก่อนเวลาที่กำหนด หากไม่ใช้งานโปรดยกเลิกเพื่อให้ผู้อื่นสามารถใช้งานได้</p>
                
                <h6>4. ความรับผิดชอบ</h6>
                <p>ผู้ใช้งานต้องรับผิดชอบต่อความเสียหายที่เกิดจากการใช้ห้องประชุมและอุปกรณ์ต่างๆ</p>
                
                <h6>5. การระงับบัญชี</h6>
                <p>ทางหน่วยงานขอสงวนสิทธิ์ในการระงับบัญชีผู้ใช้ที่ละเมิดข้อกำหนดการใช้งาน</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">นโยบายความเป็นส่วนตัว</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>การเก็บรวบรวมข้อมูล</h6>
                <p>เราเก็บรวบรวมข้อมูลส่วนบุคคลของท่านเพื่อใช้ในการให้บริการระบบจองห้องประชุม ได้แก่ ชื่อ-นามสกุล อีเมล เบอร์โทรศัพท์ และข้อมูลหน่วยงาน</p>
                
                <h6>การใช้ข้อมูล</h6>
                <p>ข้อมูลของท่านจะถูกนำไปใช้เพื่อการจัดการการจองห้องประชุม การแจ้งเตือน และการติดต่อเกี่ยวกับการใช้บริการเท่านั้น</p>
                
                <h6>การเปิดเผยข้อมูล</h6>
                <p>เราจะไม่เปิดเผยข้อมูลส่วนบุคคลของท่านให้บุคคลภายนอก ยกเว้นกรณีที่กฎหมายกำหนด</p>
                
                <h6>การรักษาความปลอดภัย</h6>
                <p>เรามีมาตรการรักษาความปลอดภัยที่เหมาะสมเพื่อปกป้องข้อมูลส่วนบุคคลของท่าน</p>
                
                <h6>สิทธิของเจ้าของข้อมูล</h6>
                <p>ท่านมีสิทธิในการเข้าถึง แก้ไข หรือลบข้อมูลส่วนบุคคลของท่านได้ โดยติดต่อผู้ดูแลระบบ</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
.password-strength .strength-bar {
    height: 4px;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.password-strength .strength-text {
    font-size: 0.75rem;
    margin-top: 4px;
}

.password-strength.weak .strength-bar {
    width: 33%;
    background-color: #dc3545;
}

.password-strength.medium .strength-bar {
    width: 66%;
    background-color: #ffc107;
}

.password-strength.strong .strength-bar {
    width: 100%;
    background-color: #198754;
}
CSS;
$this->registerCss($css);
?>

<?php
$js = <<<JS
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    var icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    var password = this.value;
    var strength = document.getElementById('passwordStrength');
    
    if (password.length === 0) {
        strength.innerHTML = '';
        strength.className = 'password-strength';
        return;
    }
    
    var score = 0;
    if (password.length >= 8) score++;
    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) score++;
    if (password.match(/[0-9]/)) score++;
    if (password.match(/[^a-zA-Z0-9]/)) score++;
    
    var strengthClass, strengthText;
    if (score <= 1) {
        strengthClass = 'weak';
        strengthText = 'รหัสผ่านอ่อนแอ - ควรมีตัวอักษรพิมพ์ใหญ่ ตัวเลข และอักขระพิเศษ';
    } else if (score <= 2) {
        strengthClass = 'medium';
        strengthText = 'รหัสผ่านปานกลาง - เพิ่มความซับซ้อนเพื่อความปลอดภัย';
    } else {
        strengthClass = 'strong';
        strengthText = 'รหัสผ่านแข็งแกร่ง';
    }
    
    strength.className = 'password-strength ' + strengthClass;
    strength.innerHTML = '<div class="strength-bar"></div><div class="strength-text text-' + 
        (strengthClass === 'weak' ? 'danger' : (strengthClass === 'medium' ? 'warning' : 'success')) + 
        '">' + strengthText + '</div>';
});

// Password match validation
document.getElementById('passwordConfirm').addEventListener('input', function() {
    var password = document.getElementById('password').value;
    var confirm = this.value;
    var error = document.getElementById('passwordMatchError');
    
    if (confirm.length > 0 && password !== confirm) {
        this.classList.add('is-invalid');
        error.style.display = 'block';
    } else {
        this.classList.remove('is-invalid');
        error.style.display = 'none';
    }
});

// Form submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    var password = document.getElementById('password').value;
    var confirm = document.getElementById('passwordConfirm').value;
    
    if (password !== confirm) {
        e.preventDefault();
        document.getElementById('passwordConfirm').classList.add('is-invalid');
        document.getElementById('passwordMatchError').style.display = 'block';
        return false;
    }
});
JS;
$this->registerJs($js);
?>
