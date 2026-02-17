<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ChangePasswordForm $model */

$this->title = 'เปลี่ยนรหัสผ่าน';
?>

<div class="site-force-change-password">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-key text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="mb-2">กรุณาเปลี่ยนรหัสผ่าน</h4>
                            <p class="text-muted">เพื่อความปลอดภัย กรุณาตั้งรหัสผ่านใหม่ก่อนใช้งานระบบ</p>
                        </div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'force-change-password-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => true,
                        ]); ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <?= $form->field($model, 'newPassword', ['template' => '{input}{error}'])->passwordInput([
                                'class' => 'form-control form-control-lg',
                                'placeholder' => 'รหัสผ่านใหม่',
                                'autofocus' => true,
                            ]) ?>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <?= $form->field($model, 'confirmPassword', ['template' => '{input}{error}'])->passwordInput([
                                'class' => 'form-control form-control-lg',
                                'placeholder' => 'ยืนยันรหัสผ่านใหม่',
                            ]) ?>
                        </div>

                        <div class="d-grid gap-2">
                            <?= Html::submitButton('<i class="bi bi-check-lg me-2"></i>เปลี่ยนรหัสผ่าน', [
                                'class' => 'btn btn-primary btn-lg',
                                'name' => 'change-password-button',
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                        <hr class="my-4">
                        
                        <div class="text-center">
                            <?= Html::a('<i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ', ['/site/logout'], [
                                'class' => 'text-muted text-decoration-none',
                                'data' => ['method' => 'post'],
                            ]) ?>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-shield-check me-1"></i>
                        การเปลี่ยนรหัสผ่านเป็นการรักษาความปลอดภัยของบัญชีของคุณ
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.site-force-change-password {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
}
.site-force-change-password .card {
    border-radius: 1rem;
}
</style>
