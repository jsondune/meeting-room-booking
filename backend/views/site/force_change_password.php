<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ForceChangePasswordForm $model */

$this->title = 'เปลี่ยนรหัสผ่าน';
?>

<div class="site-force-change-password d-flex align-items-center justify-content-center" style="min-height: 100vh; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0" style="border-radius: 1rem;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="bi bi-key text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <h4 class="mb-2">กรุณาเปลี่ยนรหัสผ่าน</h4>
                            <p class="text-muted small">เพื่อความปลอดภัย กรุณาตั้งรหัสผ่านใหม่ก่อนใช้งานระบบ</p>
                        </div>

                        <?php $form = ActiveForm::begin([
                            'id' => 'force-change-password-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => true,
                        ]); ?>

                        <div class="mb-3">
                            <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <?= $form->field($model, 'newPassword', ['template' => '{input}{error}'])->passwordInput([
                                'class' => 'form-control',
                                'placeholder' => 'รหัสผ่านใหม่',
                                'autofocus' => true,
                            ]) ?>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>อย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข
                            </small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
                            <?= $form->field($model, 'confirmPassword', ['template' => '{input}{error}'])->passwordInput([
                                'class' => 'form-control',
                                'placeholder' => 'ยืนยันรหัสผ่านใหม่',
                            ]) ?>
                        </div>

                        <div class="d-grid gap-2">
                            <?= Html::submitButton('<i class="bi bi-check-lg me-2"></i>เปลี่ยนรหัสผ่าน', [
                                'class' => 'btn btn-primary',
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
                    <small class="text-white-50">
                        <i class="bi bi-shield-check me-1"></i>
                        การเปลี่ยนรหัสผ่านเป็นการรักษาความปลอดภัยของบัญชีของคุณ
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
