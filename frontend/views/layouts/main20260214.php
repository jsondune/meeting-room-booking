<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\bootstrap5\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ระบบจองห้องประชุม - Meeting Room Booking System">
    <meta name="author" content="Dunyawat & AI">
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
            --secondary-color: #6c757d;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f5f7fa;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar-brand {
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .navbar-brand i {
            color: var(--primary-color);
        }
        
        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 0.375rem;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .dropdown-item:hover {
            background-color: rgba(13, 110, 253, 0.1);
            color: var(--primary-color);
        }
        
        .dropdown-item i {
            width: 1.25rem;
        }
        
        main.flex-fill {
            flex: 1;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        
        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--secondary-color);
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 0.5rem;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
        }
        
        .btn {
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 0.375rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
        }
        
        .footer {
            background-color: #fff;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
            margin-top: auto;
        }
        
        .footer-links a {
            color: var(--secondary-color);
            text-decoration: none;
            margin: 0 0.75rem;
        }
        
        .footer-links a:hover {
            color: var(--primary-color);
        }
        
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            transform: translate(50%, -50%);
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .user-avatar-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        @media (max-width: 991.98px) {
            .navbar-collapse {
                padding: 1rem 0;
            }
            
            .nav-link {
                padding: 0.75rem 0 !important;
            }
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    NavBar::begin([
        'brandLabel' => '<img src="' . Yii::getAlias('@web') . '/images/logo.svg" alt="ระบบจองห้องประชุม" style="height: 55px; width: auto;">',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top',
        ],
        'innerContainerOptions' => ['class' => 'container'],
    ]);
    
    // Left menu items
    $menuItems = [
        ['label' => '<i class="fas fa-home me-1"></i> หน้าแรก', 'url' => ['/site/index'], 'encode' => false],
        ['label' => '<i class="fas fa-building me-1"></i> ห้องประชุม', 'url' => ['/room/index'], 'encode' => false],
    ];
    
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '<i class="fas fa-calendar-alt me-1"></i> ปฏิทิน', 'url' => ['/site/calendar'], 'encode' => false];
    } else {
        $menuItems[] = ['label' => '<i class="fas fa-calendar-plus me-1"></i> จองห้องประชุม', 'url' => ['/booking/create'], 'encode' => false];
        $menuItems[] = ['label' => '<i class="fas fa-list-alt me-1"></i> การจองของฉัน', 'url' => ['/booking/my-bookings'], 'encode' => false];
    }
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    
    // Right menu items
    $rightMenuItems = [];
    
    if (Yii::$app->user->isGuest) {
        $rightMenuItems[] = ['label' => '<i class="fas fa-sign-in-alt me-1"></i> เข้าสู่ระบบ', 'url' => ['/site/login'], 'encode' => false];
        $rightMenuItems[] = ['label' => '<i class="fas fa-user-plus me-1"></i> ลงทะเบียน', 'url' => ['/site/signup'], 'encode' => false];
    } else {
        $user = Yii::$app->user->identity;
        $avatarHtml = '<img src="' . Html::encode($user->getAvatarUrl()) . '" class="user-avatar me-2" alt="">';
        
        // Notifications dropdown
        $notificationCount = 0; // TODO: Get from Notification model
        $rightMenuItems[] = [
            'label' => '<span class="position-relative"><i class="fas fa-bell"></i>' . 
                      ($notificationCount > 0 ? '<span class="badge bg-danger notification-badge">' . $notificationCount . '</span>' : '') .
                      '</span>',
            'url' => ['/notification/index'],
            'encode' => false,
            'linkOptions' => ['class' => 'nav-link position-relative'],
        ];
        
        // User dropdown
        $rightMenuItems[] = [
            'label' => $avatarHtml . Html::encode($user->fullname ?? $user->username),
            'encode' => false,
            'items' => [
                [
                    'label' => '<i class="fas fa-tachometer-alt me-2"></i> แดชบอร์ด', 
                    'url' => ['/site/dashboard'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="fas fa-user me-2"></i> โปรไฟล์', 
                    'url' => ['/site/profile'], 
                    'encode' => false
                ],
                [
                    'label' => '<i class="fas fa-cog me-2"></i> ตั้งค่า', 
                    'url' => ['/profile/edit'], 
                    'encode' => false
                ],
                '<hr class="dropdown-divider">',
                [
                    'label' => '<i class="fas fa-sign-out-alt me-2"></i> ออกจากระบบ',
                    'url' => ['/site/logout'],
                    'encode' => false,
                    'linkOptions' => [
                        'data-method' => 'post',
                    ],
                ],
            ],
        ];
        
        // Admin link for managers/admins
        if ($user->hasRole('admin') || $user->hasRole('manager') || $user->hasRole('superadmin')) {
            $rightMenuItems[] = [
                'label' => '<i class="fas fa-cogs text-warning"></i>',
                'url' => '/backend/web/',
                'encode' => false,
                'linkOptions' => [
                    'title' => 'ระบบจัดการ',
                    'target' => '_blank',
                ],
            ];
        }
    }
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto mb-2 mb-lg-0'],
        'items' => $rightMenuItems,
        'encodeLabels' => false,
    ]);
    
    NavBar::end();
    ?>
</header>

<main role="main" class="flex-fill" style="margin-top: 76px;">
    <div class="container py-4">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
                'options' => ['class' => 'breadcrumb mb-4'],
            ]) ?>
        <?php endif ?>
        
        <?= Alert::widget() ?>
        
        <?= $content ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <span class="text-muted">
                    &copy; <?= date('Y') + 543 ?> <?= Html::encode(Yii::$app->name) ?>
                </span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <div class="footer-links">
                    <a href="<?= \yii\helpers\Url::to(['/site/about']) ?>">
                        <i class="fas fa-info-circle me-1"></i> เกี่ยวกับ
                    </a>
                    <a href="<?= \yii\helpers\Url::to(['/site/contact']) ?>">
                        <i class="fas fa-envelope me-1"></i> ติดต่อเรา
                    </a>
                    <a href="<?= \yii\helpers\Url::to(['/site/help']) ?>">
                        <i class="fas fa-question-circle me-1"></i> ช่วยเหลือ
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>

<!-- Custom Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
</script>

<!-- Thai Date Formatter -->
<script src="<?= Yii::getAlias('@web') ?>/js/thai-date.js"></script>
</body>
</html>
<?php $this->endPage() ?>
