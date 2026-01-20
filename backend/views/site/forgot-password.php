<?php
/**
 * Forgot Password View - Backend
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var yii\base\DynamicModel $model
 */

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

$this->title = 'ลืมรหัสผ่าน';
$this->context->layout = 'main-login';
?>

<div class="auth-card">
    <div class="auth-header">
        <div class="logo">
            <i class="bi bi-key"></i>
        </div>
        <h1>ลืมรหัสผ่าน</h1>
        <p>ระบบจะส่งลิงก์รีเซ็ตไปยังอีเมลของคุณ</p>
    </div>
    
    <div class="auth-body">
        <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                <?= Html::encode($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
        
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            โปรดกรอกอีเมลที่ใช้ลงทะเบียน ระบบจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปให้
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'forgot-password-form',
            'fieldConfig' => [
                'template' => "{input}\n{error}",
            ],
        ]); ?>

        <div class="form-floating mb-4">
            <?= Html::textInput('email', '', [
                'class' => 'form-control',
                'placeholder' => 'อีเมล',
                'type' => 'email',
                'required' => true,
                'autofocus' => true,
            ]) ?>
            <label>
                <i class="bi bi-envelope me-1"></i>อีเมล
            </label>
        </div>

        <?= Html::submitButton(
            '<i class="bi bi-send me-1"></i> ส่งลิงก์รีเซ็ตรหัสผ่าน',
            ['class' => 'btn btn-primary w-100 btn-lg', 'name' => 'reset-button']
        ) ?>

        <?php ActiveForm::end(); ?>
    </div>
    
    <div class="auth-footer">
        <a href="<?= Yii::$app->urlManager->createUrl(['site/login']) ?>">
            <i class="bi bi-arrow-left me-1"></i>กลับหน้าเข้าสู่ระบบ
        </a>
    </div>
</div>
