<?php
/**
 * Notification Index View
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'การแจ้งเตือน';
$this->params['breadcrumbs'][] = $this->title;

$typeIcons = [
    'booking_created' => 'bi-calendar-plus text-primary',
    'booking_approved' => 'bi-check-circle text-success',
    'booking_rejected' => 'bi-x-circle text-danger',
    'booking_cancelled' => 'bi-calendar-x text-warning',
    'booking_reminder' => 'bi-alarm text-info',
    'booking_updated' => 'bi-pencil-square text-secondary',
    'system' => 'bi-gear text-dark',
    'info' => 'bi-info-circle text-info',
    'warning' => 'bi-exclamation-triangle text-warning',
];
?>

<div class="notification-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-bell me-2"></i><?= Html::encode($this->title) ?>
        </h4>
        <div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="markAllRead">
                <i class="bi bi-check-all me-1"></i>อ่านทั้งหมด
            </button>
        </div>
    </div>

    <?php if ($dataProvider->count > 0): ?>
        <div class="card border-0 shadow-sm">
            <div class="list-group list-group-flush">
                <?php foreach ($dataProvider->models as $notification): ?>
                    <div class="list-group-item list-group-item-action <?= !$notification->is_read ? 'bg-light' : '' ?>" 
                         data-notification-id="<?= $notification->id ?>">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="notification-icon rounded-circle bg-light p-2" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi <?= $typeIcons[$notification->type] ?? 'bi-bell' ?> fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="mb-1 <?= !$notification->is_read ? 'fw-bold' : '' ?>">
                                        <?= Html::encode($notification->title) ?>
                                        <?php if (!$notification->is_read): ?>
                                            <span class="badge bg-primary ms-1">ใหม่</span>
                                        <?php endif; ?>
                                    </h6>
                                    <small class="text-muted">
                                        <?= Yii::$app->formatter->asRelativeTime($notification->created_at) ?>
                                    </small>
                                </div>
                                <p class="mb-1 text-muted small">
                                    <?= Html::encode($notification->message) ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <?php if ($notification->link): ?>
                                        <a href="<?= $notification->link ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>ดูรายละเอียด
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-4 d-flex justify-content-center">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
                'options' => ['class' => 'pagination'],
                'linkContainerOptions' => ['class' => 'page-item'],
                'linkOptions' => ['class' => 'page-link'],
                'disabledListItemSubTagOptions' => ['class' => 'page-link'],
            ]) ?>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-bell-slash fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">ไม่มีการแจ้งเตือน</h5>
                <p class="text-muted mb-0">คุณยังไม่มีการแจ้งเตือนในขณะนี้</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$markReadUrl = Url::to(['notification/mark-read']);
$markAllReadUrl = Url::to(['notification/mark-all-read']);
$deleteUrl = Url::to(['notification/delete']);
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
// Mark single notification as read
document.querySelectorAll('.btn-mark-read').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const item = this.closest('[data-notification-id]');
        
        fetch('{$markReadUrl}?id=' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '{$csrfToken}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.classList.remove('bg-light');
                item.querySelector('.fw-bold')?.classList.remove('fw-bold');
                item.querySelector('.badge.bg-primary')?.remove();
                this.remove();
            }
        });
    });
});

// Mark all as read
document.getElementById('markAllRead').addEventListener('click', function() {
    fetch('{$markAllReadUrl}', {
        method: 'POST',
        headers: {
            'X-CSRF-Token': '{$csrfToken}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
});

// Delete notification
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('ต้องการลบการแจ้งเตือนนี้?')) return;
        
        const id = this.dataset.id;
        const item = this.closest('[data-notification-id]');
        
        fetch('{$deleteUrl}?id=' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-Token': '{$csrfToken}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.style.transition = 'opacity 0.3s';
                item.style.opacity = '0';
                setTimeout(() => item.remove(), 300);
            }
        });
    });
});
JS;
$this->registerJs($js);
?>
