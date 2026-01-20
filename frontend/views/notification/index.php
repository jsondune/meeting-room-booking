<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'การแจ้งเตือน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="notification-index">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-bell text-primary me-2"></i><?= Html::encode($this->title) ?>
                        </h1>
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger"><?= $unreadCount ?> รายการยังไม่ได้อ่าน</span>
                        <?php endif; ?>
                    </div>
                    <?php if ($unreadCount > 0): ?>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="markAllRead">
                            <i class="bi bi-check-all me-1"></i>อ่านทั้งหมด
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Notifications List -->
                <?php if (empty($notifications)): ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">ไม่มีการแจ้งเตือน</h5>
                            <p class="text-muted">เมื่อมีการแจ้งเตือนใหม่ จะแสดงที่นี่</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item list-group-item-action <?= !$notification->is_read ? 'bg-light' : '' ?>" 
                                     data-notification-id="<?= $notification->id ?>">
                                    <div class="d-flex w-100">
                                        <div class="me-3">
                                            <?php
                                            $iconClass = 'bi-bell';
                                            $iconColor = 'text-primary';
                                            switch ($notification->type) {
                                                case 'booking_approved':
                                                    $iconClass = 'bi-check-circle';
                                                    $iconColor = 'text-success';
                                                    break;
                                                case 'booking_rejected':
                                                    $iconClass = 'bi-x-circle';
                                                    $iconColor = 'text-danger';
                                                    break;
                                                case 'booking_cancelled':
                                                    $iconClass = 'bi-slash-circle';
                                                    $iconColor = 'text-warning';
                                                    break;
                                                case 'booking_reminder':
                                                    $iconClass = 'bi-alarm';
                                                    $iconColor = 'text-info';
                                                    break;
                                                case 'system':
                                                    $iconClass = 'bi-gear';
                                                    $iconColor = 'text-secondary';
                                                    break;
                                            }
                                            ?>
                                            <i class="bi <?= $iconClass ?> <?= $iconColor ?> fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1 <?= !$notification->is_read ? 'fw-bold' : '' ?>">
                                                    <?= Html::encode($notification->title) ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <?= Yii::$app->formatter->asRelativeTime($notification->created_at) ?>
                                                </small>
                                            </div>
                                            <p class="mb-1 text-muted small"><?= Html::encode($notification->message) ?></p>
                                            <div class="d-flex gap-2 mt-2">
                                                <?php if ($notification->url): 
                                                    // Fix old links that don't have /frontend/web prefix
                                                    $notifUrl = $notification->url;
                                                    if (strpos($notifUrl, '/frontend/web') === false && strpos($notifUrl, 'http') !== 0) {
                                                        $notifUrl = '/frontend/web' . $notifUrl;
                                                    }
                                                ?>
                                                    <a href="<?= Html::encode($notifUrl) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                                                    </a>
                                                <?php endif; ?>
                                                <?php if (!$notification->is_read): ?>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-mark-read" 
                                                            data-id="<?= $notification->id ?>">
                                                        <i class="bi bi-check me-1"></i>อ่านแล้ว
                                                    </button>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                                        data-id="<?= $notification->id ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php if (!$notification->is_read): ?>
                                            <div class="ms-2">
                                                <span class="badge bg-primary rounded-pill">ใหม่</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Settings Link -->
                <div class="text-center mt-4">
                    <a href="<?= Url::to(['/notification-settings/index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-gear me-1"></i>ตั้งค่าการแจ้งเตือน
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$markReadUrl = Url::to(['/notification/mark-read']);
$markAllReadUrl = Url::to(['/notification/mark-all-read']);
$deleteUrl = Url::to(['/notification/delete']);
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
// Mark single notification as read
$('.btn-mark-read').on('click', function() {
    const btn = $(this);
    const id = btn.data('id');
    const item = btn.closest('.list-group-item');
    
    $.post('{$markReadUrl}', {id: id, _csrf: '{$csrfToken}'}, function(response) {
        if (response.success) {
            item.removeClass('bg-light');
            item.find('.fw-bold').removeClass('fw-bold');
            item.find('.badge.bg-primary').remove();
            btn.remove();
        }
    });
});

// Mark all as read
$('#markAllRead').on('click', function() {
    $.post('{$markAllReadUrl}', {_csrf: '{$csrfToken}'}, function(response) {
        if (response.success) {
            location.reload();
        }
    });
});

// Delete notification
$('.btn-delete').on('click', function() {
    if (!confirm('ต้องการลบการแจ้งเตือนนี้?')) return;
    
    const btn = $(this);
    const id = btn.data('id');
    const item = btn.closest('.list-group-item');
    
    $.post('{$deleteUrl}', {id: id, _csrf: '{$csrfToken}'}, function(response) {
        if (response.success) {
            item.fadeOut(300, function() { $(this).remove(); });
        }
    });
});
JS;

$this->registerJs($js);
?>
