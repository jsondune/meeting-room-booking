<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\bootstrap5\Html;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ระบบจองห้องประชุม - Meeting Room Booking System">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> | ระบบจองห้องประชุม</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= Yii::getAlias('@web') ?>/images/favicon.png">
    
    <!-- Google Fonts - Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <?php $this->head() ?>
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a58ca;
        }
        
        body {
            font-family: 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .auth-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        
        .auth-card {
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            overflow: hidden;
        }
        
        /* Wider card for signup page */
        .auth-card.auth-card-wide {
            max-width: 600px;
        }
        
        @media (min-width: 768px) {
            .auth-card.auth-card-wide {
                max-width: 700px;
            }
        }
        
        .auth-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        
        .auth-header .logo {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }
        
        .auth-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .auth-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 0.8rem;
        }
        
        .auth-body {
            padding: 1.5rem 2rem 2rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #495057;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }
        
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
            transform: translateY(-1px);
        }
        
        .btn-oauth {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .btn-oauth:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-microsoft {
            background-color: #fff;
            border: 1px solid #dee2e6;
            color: #333;
        }
        
        .btn-google {
            background-color: #fff;
            border: 1px solid #dee2e6;
            color: #333;
        }
        
        .btn-thaid {
            background-color: #1a237e;
            border: none;
            color: #fff;
        }
        
        .btn-thaid:hover {
            background-color: #0d1659;
            color: #fff;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1rem 0;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .divider span {
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .auth-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .back-link {
            position: absolute;
            top: 1rem;
            left: 1rem;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .back-link:hover {
            color: #fff;
        }
        
        .password-toggle {
            cursor: pointer;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
        
        .invalid-feedback {
            font-size: 0.8rem;
        }
        
        @media (max-width: 575.98px) {
            .auth-card {
                border-radius: 0;
                box-shadow: none;
            }
            
            .auth-wrapper {
                padding: 0;
            }
            
            .auth-body {
                padding: 1.25rem 1.5rem 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<a href="<?= Yii::$app->homeUrl ?>" class="back-link">
    <i class="fas fa-arrow-left"></i> กลับหน้าแรก
</a>

<div class="auth-wrapper">
    <?php
    // Use wider card for signup page
    $isSignupPage = strpos($this->title, 'ลงทะเบียน') !== false || strpos($this->title, 'Signup') !== false;
    $cardClass = $isSignupPage ? 'auth-card auth-card-wide' : 'auth-card';
    ?>
    <div class="<?= $cardClass ?>">
        <div class="auth-header">
            <div class="logo">
                <i class="fas fa-door-open"></i>
            </div>
            <h1><?= Html::encode(Yii::$app->name) ?></h1>
            <p>ระบบจองห้องประชุมออนไลน์</p>
        </div>
        
        <div class="auth-body">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endBody() ?>

<script>
// Password visibility toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.password-toggle');
    toggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
});
</script>
</body>
</html>
<?php $this->endPage() ?>
