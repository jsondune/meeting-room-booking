<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="email-template-form">
    <?php $form = ActiveForm::begin([
        'id' => 'email-template-form',
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลเทมเพลต</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <?= $form->field($model, 'template_key')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'เช่น booking_created, welcome_email',
                            ])->hint('ใช้ตัวอักษรภาษาอังกฤษพิมพ์เล็ก ตัวเลข และ _ เท่านั้น') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'name')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่อที่แสดง',
                            ]) ?>
                        </div>
                        <div class="col-12">
                            <?= $form->field($model, 'subject')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'หัวเรื่องอีเมล',
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HTML Body -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-code me-2"></i>เนื้อหา HTML <span class="text-danger">*</span></h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'body_html')->textarea([
                        'rows' => 15,
                        'class' => 'form-control font-monospace',
                        'placeholder' => '<html>...</html>',
                    ])->label(false) ?>
                </div>
            </div>

            <!-- Text Body -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-file-text me-2"></i>เนื้อหา Text (ไม่บังคับ)</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'body_text')->textarea([
                        'rows' => 8,
                        'class' => 'form-control font-monospace',
                        'placeholder' => 'เนื้อหาสำหรับอีเมลที่ไม่รองรับ HTML',
                    ])->label(false)->hint('หากไม่กรอก ระบบจะสร้างจาก HTML อัตโนมัติ') ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>ตั้งค่า</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'is_active')->checkbox([
                        'class' => 'form-check-input',
                    ]) ?>
                </div>
            </div>

            <!-- Available Variables -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-braces me-2"></i>ตัวแปรที่ใช้ได้</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">คลิกเพื่อคัดลอก</p>
                    <div class="d-flex flex-wrap gap-2" id="variable-list">
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{app_name}">{app_name}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{user_name}">{user_name}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{user_email}">{user_email}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{booking_code}">{booking_code}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{room_name}">{room_name}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{booking_date}">{booking_date}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{start_time}">{start_time}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{end_time}">{end_time}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{reset_link}">{reset_link}</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary copy-var" data-var="{verify_link}">{verify_link}</button>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::submitButton(
                            '<i class="bi bi-check-lg me-1"></i> ' . ($model->isNewRecord ? 'สร้างเทมเพลต' : 'บันทึกการเปลี่ยนแปลง'),
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                        <?= Html::a(
                            '<i class="bi bi-x-lg me-1"></i> ยกเลิก',
                            ['index'],
                            ['class' => 'btn btn-outline-secondary']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
// Copy variable to clipboard
document.querySelectorAll('.copy-var').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var varText = this.dataset.var;
        navigator.clipboard.writeText(varText).then(function() {
            // Show feedback
            var originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check"></i> คัดลอกแล้ว';
            btn.classList.add('btn-success');
            btn.classList.remove('btn-outline-secondary');
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-secondary');
            }, 1500);
        });
    });
});
JS;
$this->registerJs($js);
?>
