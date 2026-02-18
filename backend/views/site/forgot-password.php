<?php
/**
 * Forgot Password View (Backend)
 * Standalone page with proper layout
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'ลืมรหัสผ่าน';

// Disable layout
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
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-card {
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
            background: #e3f2fd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .icon-circle i {
            font-size: 2.5rem;
            color: #1976d2;
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
    <div class="reset-card">
        <div class="text-center mb-4">
            <div class="icon-circle">
                <i class="bi bi-envelope-fill"></i>
            </div>
            <h4 class="mb-2">ลืมรหัสผ่าน</h4>
            <p class="text-muted small mb-0">กรุณากรอกอีเมลของคุณ ระบบจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านไปยังอีเมลดังกล่าว</p>
        </div>

        <?php $form = ActiveForm::begin([
            'id' => 'forgot-password-form',
            'enableClientValidation' => true,
        ]); ?>

        <div class="mb-4">
            <label class="form-label fw-semibold">อีเมล <span class="text-danger">*</span></label>
            <?= $form->field($model, 'email', ['template' => '{input}{error}'])->textInput([
                'class' => 'form-control form-control-lg',
                'placeholder' => 'กรอกอีเมลของคุณ',
                'autofocus' => true,
                'type' => 'email',
            ]) ?>
        </div>

        <div class="d-grid mb-4">
            <?= Html::submitButton('<i class="bi bi-send me-2"></i>ส่งลิงก์รีเซ็ตรหัสผ่าน', [
                'class' => 'btn btn-primary btn-lg',
            ]) ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="text-center text-muted small mb-3">
            <i class="bi bi-info-circle me-1"></i>
            หากคุณจำอีเมลไม่ได้ กรุณาติดต่อผู้ดูแลระบบ
        </div>

        <hr>
        
        <div class="text-center">
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับไปหน้าเข้าสู่ระบบ', ['login'], [
                'class' => 'text-decoration-none',
            ]) ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php Yii::$app->end(); ?>
