<?php
/**
 * Main Login Layout - Backend Authentication
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var string $content
 */

use yii\helpers\Html;
use yii\helpers\Url;

\backend\assets\AppAsset::register($this);

$this->beginPage();
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> | <?= Yii::$app->name ?> - Backend</title>
    <?php $this->head() ?>
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        body {
            background: var(--primary-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            width: 100%;
            max-width: 420px;
            margin: 2rem;
        }
        
        .auth-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        
        .auth-header {
            background: var(--primary-gradient);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .auth-header .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #667eea;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }
        
        .auth-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .auth-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .auth-body {
            padding: 2rem;
        }
        
        .form-floating > label {
            color: #6c757d;
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #667eea;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-primary:hover {
            background: var(--primary-gradient);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #dee2e6;
        }
        
        .divider::before {
            margin-right: 1rem;
        }
        
        .divider::after {
            margin-left: 1rem;
        }
        
        .btn-oauth {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-oauth:hover {
            transform: translateY(-2px);
        }
        
        .btn-google {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
        }
        
        .btn-google:hover {
            background: #f8f9fa;
            border-color: #4285f4;
            color: #4285f4;
        }
        
        .btn-azure {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
        }
        
        .btn-azure:hover {
            background: #f8f9fa;
            border-color: #00a1f1;
            color: #00a1f1;
        }
        
        .btn-thaid {
            background: #fff;
            border: 1px solid #dee2e6;
            color: #333;
        }
        
        .btn-thaid:hover {
            background: #f8f9fa;
            border-color: #003d7c;
            color: #003d7c;
        }
        
        .auth-footer {
            text-align: center;
            padding: 1rem 2rem 2rem;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .version-info {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        @media (max-width: 576px) {
            .auth-container {
                margin: 1rem;
            }
            
            .auth-header {
                padding: 1.5rem;
            }
            
            .auth-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<?php $this->beginBody() ?>

<div class="auth-container">
    <?= $content ?>
</div>

<div class="version-info">
    <?= Yii::$app->name ?> v1.0.0 &copy; <?= date('Y') ?> PBRI
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
