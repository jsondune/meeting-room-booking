<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\models\Notification;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? 'ระบบจองห้องประชุม - Meeting Room Booking System']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? 'meeting room, booking, reservation, ห้องประชุม, จองห้อง']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

$unreadNotifications = Yii::$app->user->isGuest ? 0 : Notification::getUnreadCount(Yii::$app->user->id);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?> | ระบบจองห้องประชุม</title>
    <?php $this->head() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --secondary-color: #64748b;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --sidebar-width: 280px;
            --header-height: 60px;
        }
        
        body {
            font-family: 'Prompt', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f8fafc;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand h4 {
            color: #fff;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-brand small {
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu-title {
            color: rgba(255,255,255,0.4);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1.5rem 0.5rem;
            margin-top: 0.5rem;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        
        .sidebar-menu a.active {
            background: rgba(79, 70, 229, 0.2);
            color: #fff;
            border-left-color: var(--primary-color);
        }
        
        .sidebar-menu a i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        /* Header */
        .main-header {
            position: sticky;
            top: 0;
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }
        
        .header-search {
            position: relative;
            width: 300px;
        }
        
        .header-search input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        
        .header-search i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-btn {
            position: relative;
            padding: 0.5rem;
            border: none;
            background: none;
            color: #64748b;
            cursor: pointer;
        }
        
        .notification-btn .badge {
            position: absolute;
            top: 0;
            right: 0;
            font-size: 0.65rem;
        }
        
        .user-dropdown .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.5rem;
            border: none;
            background: none;
            cursor: pointer;
        }
        
        .user-dropdown .dropdown-toggle::after {
            display: inline-block;
            margin-left: 0.255em;
            vertical-align: 0.255em;
            content: "";
            border-top: 0.3em solid;
            border-right: 0.3em solid transparent;
            border-bottom: 0;
            border-left: 0.3em solid transparent;
        }
        
        .user-dropdown .dropdown-menu {
            z-index: 1060;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .user-dropdown .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        /* Page Content */
        .page-content {
            padding: 1.5rem;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        
        .breadcrumb {
            margin: 0;
            padding: 0;
            background: none;
            font-size: 0.875rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        
        /* Stats Cards */
        .stat-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            color: #fff;
        }
        
        .stat-card.primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .stat-card.success { background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); }
        .stat-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .stat-card .stat-label {
            opacity: 0.9;
            font-size: 0.875rem;
        }
        
        /* Tables */
        .table {
            margin: 0;
        }
        
        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            color: #475569;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }
        
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }
        
        /* Status Badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.pending { background: #fef3c7; color: #92400e; }
        .status-badge.approved { background: #dcfce7; color: #166534; }
        .status-badge.rejected { background: #fee2e2; color: #991b1b; }
        .status-badge.cancelled { background: #f1f5f9; color: #475569; }
        .status-badge.completed { background: #dbeafe; color: #1e40af; }
        
        /* Buttons */
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        /* Mobile Sidebar Toggle */
        .sidebar-toggle {
            display: none;
            padding: 0.5rem;
            border: none;
            background: none;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
        }
        
        /* Mobile Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            .header-search {
                display: none;
            }
        }
        
        @media (max-width: 575.98px) {
            .page-content {
                padding: 1rem;
            }
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="<?= Yii::$app->homeUrl ?>" style="text-decoration: none; color: inherit;">
            <img src="<?= Yii::getAlias('@web') ?>/images/logo.svg" alt="MeetingRoom" style="height: 45px; width: auto;">
        </a>
    </div>
    
    <nav class="sidebar-menu">
        <div class="sidebar-menu-title">เมนูหลัก</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['site/index']) ?>" class="<?= Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>แดชบอร์ด</span>
        </a>
        
        <div class="sidebar-menu-title">การจัดการห้องประชุม</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['room/index']) ?>" class="<?= Yii::$app->controller->id == 'room' ? 'active' : '' ?>">
            <i class="bi bi-door-open"></i>
            <span>ห้องประชุม</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['building/index']) ?>" class="<?= Yii::$app->controller->id == 'building' ? 'active' : '' ?>">
            <i class="bi bi-building"></i>
            <span>อาคาร</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['equipment/index']) ?>" class="<?= Yii::$app->controller->id == 'equipment' ? 'active' : '' ?>">
            <i class="bi bi-projector"></i>
            <span>อุปกรณ์</span>
        </a>
        
        <div class="sidebar-menu-title">การจอง</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['booking/index']) ?>" class="<?= Yii::$app->controller->id == 'booking' && Yii::$app->controller->action->id == 'index' ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i>
            <span>รายการจอง</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['booking/pending']) ?>" class="<?= Yii::$app->controller->id == 'booking' && Yii::$app->controller->action->id == 'pending' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i>
            <span>รออนุมัติ</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['booking/calendar']) ?>" class="<?= Yii::$app->controller->id == 'booking' && Yii::$app->controller->action->id == 'calendar' ? 'active' : '' ?>">
            <i class="bi bi-calendar3"></i>
            <span>ปฏิทิน</span>
        </a>
        
        <div class="sidebar-menu-title">ผู้ใช้งาน</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['user/index']) ?>" class="<?= Yii::$app->controller->id == 'user' ? 'active' : '' ?>">
            <i class="bi bi-people"></i>
            <span>ผู้ใช้งาน</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['department/index']) ?>" class="<?= Yii::$app->controller->id == 'department' ? 'active' : '' ?>">
            <i class="bi bi-diagram-3"></i>
            <span>หน่วยงาน</span>
        </a>
        
        <div class="sidebar-menu-title">รายงาน</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['report/usage']) ?>" class="<?= Yii::$app->controller->id == 'report' && Yii::$app->controller->action->id == 'usage' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i>
            <span>สถิติการใช้งาน</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['report/revenue']) ?>" class="<?= Yii::$app->controller->id == 'report' && Yii::$app->controller->action->id == 'revenue' ? 'active' : '' ?>">
            <i class="bi bi-cash-stack"></i>
            <span>รายงานรายได้</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['audit-log/index']) ?>" class="<?= Yii::$app->controller->id == 'audit-log' ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i>
            <span>Audit Log</span>
        </a>
        
        <div class="sidebar-menu-title">ตั้งค่าระบบ</div>
        <a href="<?= Yii::$app->urlManager->createUrl(['setting/index']) ?>" class="<?= Yii::$app->controller->id == 'setting' ? 'active' : '' ?>">
            <i class="bi bi-gear"></i>
            <span>ตั้งค่าทั่วไป</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['holiday/index']) ?>" class="<?= Yii::$app->controller->id == 'holiday' ? 'active' : '' ?>">
            <i class="bi bi-calendar-x"></i>
            <span>วันหยุด</span>
        </a>
        <a href="<?= Yii::$app->urlManager->createUrl(['email-template/index']) ?>" class="<?= Yii::$app->controller->id == 'email-template' ? 'active' : '' ?>">
            <i class="bi bi-envelope"></i>
            <span>Email Templates</span>
        </a>
    </nav>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <header class="main-header">
        <div class="d-flex align-items-center">
            <button class="sidebar-toggle me-3" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="header-search">
                <i class="bi bi-search"></i>
                <input type="text" placeholder="ค้นหา..." id="globalSearch">
            </div>
        </div>
        
        <div class="header-actions">
            <!-- Notifications -->
            <div class="dropdown">
                <button type="button" class="notification-btn" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdown">
                    <i class="bi bi-bell fs-5"></i>
                    <?php if ($unreadNotifications > 0): ?>
                        <span class="badge bg-danger" id="notificationBadge"><?= $unreadNotifications ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger d-none" id="notificationBadge">0</span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-0" style="width: 360px; max-height: 450px;" aria-labelledby="notificationDropdown">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <strong><i class="bi bi-bell me-2"></i>การแจ้งเตือน</strong>
                        <div>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 me-2" id="markAllReadBtn" title="อ่านทั้งหมด">
                                <i class="bi bi-check-all"></i>
                            </button>
                            <a href="<?= Yii::$app->urlManager->createUrl(['notification/index']) ?>" class="text-decoration-none small">ดูทั้งหมด</a>
                        </div>
                    </div>
                    <div id="notificationList" style="max-height: 350px; overflow-y: auto;">
                        <div class="p-4 text-center text-muted">
                            <div class="spinner-border spinner-border-sm mb-2" role="status"></div>
                            <div>กำลังโหลด...</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Dropdown -->
            <div class="dropdown user-dropdown">
                <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="userDropdown">
                    <div class="user-avatar">
                        <?= Yii::$app->user->isGuest ? 'G' : strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                    </div>
                    <div class="d-none d-md-block text-start">
                        <div class="fw-semibold" style="font-size: 0.875rem;">
                            <?= Yii::$app->user->isGuest ? 'Guest' : Html::encode(Yii::$app->user->identity->displayName ?: Yii::$app->user->identity->username) ?>
                        </div>
                        <div class="text-muted" style="font-size: 0.75rem;">
                            <?= Yii::$app->user->isGuest ? '' : Html::encode(Yii::$app->user->identity->role) ?>
                        </div>
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li class="dropdown-header">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar me-2" style="width: 40px; height: 40px; font-size: 1rem;">
                                <?= Yii::$app->user->isGuest ? 'G' : strtoupper(substr(Yii::$app->user->identity->username, 0, 1)) ?>
                            </div>
                            <div>
                                <div class="fw-semibold"><?= Yii::$app->user->isGuest ? 'Guest' : Html::encode(Yii::$app->user->identity->displayName ?: Yii::$app->user->identity->username) ?></div>
                                <small class="text-muted"><?= Yii::$app->user->isGuest ? '' : Html::encode(Yii::$app->user->identity->email) ?></small>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['site/profile']) ?>"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
                    <li><a class="dropdown-item" href="<?= Yii::$app->urlManager->createUrl(['site/change-password']) ?>"><i class="bi bi-key me-2"></i>เปลี่ยนรหัสผ่าน</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-grid']) ?>
                            <?= Html::submitButton('<i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ', ['class' => 'dropdown-item text-danger']) ?>
                        <?= Html::endForm() ?>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    
    <!-- Page Content -->
    <main class="page-content">
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="text-center py-3 border-top bg-white">
        <small class="text-muted">
            &copy; <?= date('Y') + 543 ?> ระบบจองห้องประชุม - Meeting Room Booking System v1.0
        </small>
    </footer>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Notification System
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationList = document.getElementById('notificationList');
    const notificationBadge = document.getElementById('notificationBadge');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    let notificationsLoaded = false;
    
    // Load notifications when dropdown opens
    if (notificationDropdown) {
        notificationDropdown.addEventListener('show.bs.dropdown', function() {
            if (!notificationsLoaded) {
                loadNotifications();
            }
        });
    }
    
    function loadNotifications() {
        fetch('<?= Yii::$app->urlManager->createUrl(['notification/recent']) ?>')
            .then(response => response.json())
            .then(data => {
                notificationsLoaded = true;
                
                if (data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <a href="${item.link || '#'}" class="list-group-item list-group-item-action ${!item.is_read ? 'bg-light' : ''}" 
                               data-notification-id="${item.id}" onclick="markAsRead(${item.id})">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-2">
                                        <span class="rounded-circle bg-${item.color}-subtle text-${item.color} p-2 d-inline-flex">
                                            <i class="bi ${item.icon}"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex justify-content-between">
                                            <strong class="text-truncate ${!item.is_read ? 'fw-bold' : ''}" style="font-size: 0.875rem;">
                                                ${item.title}
                                            </strong>
                                            ${!item.is_read ? '<span class="badge bg-primary ms-1">ใหม่</span>' : ''}
                                        </div>
                                        <p class="mb-0 text-muted small text-truncate">${item.message || ''}</p>
                                        <small class="text-muted">${item.created_at}</small>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    notificationList.innerHTML = `<div class="list-group list-group-flush">${html}</div>`;
                } else {
                    notificationList.innerHTML = `
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                            <div>ไม่มีการแจ้งเตือนใหม่</div>
                        </div>
                    `;
                }
                
                // Update badge
                if (data.count > 0) {
                    notificationBadge.textContent = data.count;
                    notificationBadge.classList.remove('d-none');
                } else {
                    notificationBadge.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = `
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-exclamation-circle fs-1 d-block mb-2"></i>
                        <div>ไม่สามารถโหลดการแจ้งเตือนได้</div>
                    </div>
                `;
            });
    }
    
    // Mark single notification as read
    window.markAsRead = function(id) {
        fetch('<?= Yii::$app->urlManager->createUrl(['notification/mark-read']) ?>?id=' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>',
            }
        }).then(() => {
            // Update badge count
            const currentCount = parseInt(notificationBadge.textContent) || 0;
            if (currentCount > 1) {
                notificationBadge.textContent = currentCount - 1;
            } else {
                notificationBadge.classList.add('d-none');
            }
        });
    };
    
    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            fetch('<?= Yii::$app->urlManager->createUrl(['notification/mark-all-read']) ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationBadge.classList.add('d-none');
                    notificationsLoaded = false;
                    loadNotifications();
                }
            });
        });
    }
});
</script>

<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Initialize Bootstrap Dropdowns -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns
    var dropdownElementList = document.querySelectorAll('[data-bs-toggle="dropdown"]');
    dropdownElementList.forEach(function(dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Specifically initialize user dropdown
    var userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            var dropdown = bootstrap.Dropdown.getOrCreateInstance(this);
            dropdown.toggle();
        });
    }
});
</script>

<!-- Thai Date Formatter -->
<script src="<?= Yii::getAlias('@web') ?>/js/thai-date.js"></script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
