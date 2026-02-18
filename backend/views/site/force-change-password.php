<?php
/**
 * Force Change Password View
 * Standalone page - no layout required
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'เปลี่ยนรหัสผ่าน';

// Disable layout completely
Yii::$app->controller->layout = false;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Html::encode($this->title) ?> - ระบบจองห้องประชุม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Sarabun', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .password-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 420px;
            padding: 40px;
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #fff3cd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-circle i {
            font-size: 2.5rem;
            color: #ffc107;
        }
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        .btn-primary {
            background: #1e3c72;
            border-color: #1e3c72;
        }
        .btn-primary:hover {
            background: #2a5298;
            border-color: #2a5298;
        }
    </style>
</head>
<body>
    <div class="password-card">
        <div class="text-center mb-4">
            <div class="icon-circle">
                <i class="bi bi-key-fill"></i>
            </div>
            <h4 class="mb-2">กรุณาเปลี่ยนรหัสผ่าน</h4>
            <p class="text-muted small mb-0">เพื่อความปลอดภัย กรุณาตั้งรหัสผ่านใหม่ก่อนใช้งานระบบ</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'force-change-password-form',
            'enableClientValidation' => true,
            'options' => ['autocomplete' => 'off'],
        ]); ?>

        <div class="mb-3">
            <label class="form-label fw-semibold">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
            <?= $form->field($model, 'newPassword', ['template' => '{input}{error}'])->passwordInput([
                'class' => 'form-control form-control-lg',
                'placeholder' => 'รหัสผ่านใหม่',
                'autofocus' => true,
            ]) ?>
            <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>อย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ พิมพ์เล็ก และตัวเลข
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่ <span class="text-danger">*</span></label>
            <?= $form->field($model, 'confirmPassword', ['template' => '{input}{error}'])->passwordInput([
                'class' => 'form-control form-control-lg',
                'placeholder' => 'ยืนยันรหัสผ่านใหม่',
            ]) ?>
        </div>

        <div class="d-grid mb-4">
            <?= Html::submitButton('<i class="bi bi-check-lg me-2"></i>เปลี่ยนรหัสผ่าน', [
                'class' => 'btn btn-primary btn-lg',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <hr>
        
        <div class="text-center">
            <?= Html::a('<i class="bi bi-box-arrow-right me-1"></i>ออกจากระบบ', ['/site/logout'], [
                'class' => 'text-muted text-decoration-none',
                'data' => ['method' => 'post'],
            ]) ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// End output - prevent any further content
Yii::$app->end();
