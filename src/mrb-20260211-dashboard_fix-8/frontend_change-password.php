<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var frontend\models\ChangePasswordForm $model */

$this->title = 'เปลี่ยนรหัสผ่าน';
$this->params['breadcrumbs'][] = ['label' => 'โปรไฟล์', 'url' => ['profile/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-change-password py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="fas fa-key text-primary me-2"></i><?= Html::encode($this->title) ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php $form = ActiveForm::begin([
                            'id' => 'change-password-form',
                            'options' => ['class' => 'form-horizontal'],
                        ]); ?>

                        <div class="mb-3">
                            <?= $form->field($model, 'current_password')
                                ->passwordInput([
                                    'autofocus' => true,
                                    'class' => 'form-control',
                                    'placeholder' => 'กรอกรหัสผ่านปัจจุบัน'
                                ])
                                ->label('<i class="fas fa-lock me-1"></i> รหัสผ่านปัจจุบัน') ?>
                        </div>

                        <hr class="my-4">

                        <div class="mb-3">
                            <?= $form->field($model, 'new_password')
                                ->passwordInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'กรอกรหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร)'
                                ])
                                ->label('<i class="fas fa-key me-1"></i> รหัสผ่านใหม่')
                                ->hint('<small class="text-muted">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร และประกอบด้วยตัวอักษรและตัวเลข</small>') ?>
                        </div>

                        <div class="mb-4">
                            <?= $form->field($model, 'confirm_password')
                                ->passwordInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'กรอกรหัสผ่านใหม่อีกครั้ง'
                                ])
                                ->label('<i class="fas fa-key me-1"></i> ยืนยันรหัสผ่านใหม่') ?>
                        </div>

                        <div class="d-grid gap-2">
                            <?= Html::submitButton('<i class="fas fa-check me-1"></i> เปลี่ยนรหัสผ่าน', [
                                'class' => 'btn btn-primary btn-lg',
                                'name' => 'change-password-button'
                            ]) ?>
                            
                            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> ยกเลิก', ['profile/index'], [
                                'class' => 'btn btn-outline-secondary'
                            ]) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                
                <!-- Security Tips -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-shield-alt me-2"></i>คำแนะนำด้านความปลอดภัย
                        </h6>
                        <ul class="list-unstyled mb-0 small text-muted">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                ใช้รหัสผ่านที่มีความยาวอย่างน้อย 8 ตัวอักษร
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                ผสมผสานตัวพิมพ์เล็ก พิมพ์ใหญ่ ตัวเลข และอักขระพิเศษ
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                หลีกเลี่ยงการใช้ข้อมูลส่วนตัวในรหัสผ่าน
                            </li>
                            <li>
                                <i class="fas fa-check text-success me-2"></i>
                                ไม่ควรใช้รหัสผ่านซ้ำกับบริการอื่น
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
