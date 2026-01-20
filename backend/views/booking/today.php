<?php
/**
 * Today's Bookings View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'การจองวันนี้';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$models = $dataProvider->getModels();
$currentTime = date('H:i:s');
?>

<div class="booking-today">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-calendar-day text-primary me-2"></i><?= Html::encode($this->title) ?>
            </h4>
            <p class="text-muted mb-0">
                <?= Yii::$app->formatter->asDate(date('Y-m-d'), 'full') ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a(
                '<i class="fas fa-calendar me-1"></i> ปฏิทิน',
                ['calendar'],
                ['class' => 'btn btn-outline-primary']
            ) ?>
            <?= Html::a(
                '<i class="fas fa-plus me-1"></i> จองห้อง',
                ['create', 'date' => date('Y-m-d')],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <!-- Time Progress Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">
                    <i class="fas fa-clock me-1"></i>เวลาปัจจุบัน: <strong><?= date('H:i') ?> น.</strong>
                </span>
                <span class="badge bg-primary">
                    <?= $dataProvider->totalCount ?> การจอง
                </span>
            </div>
            <?php
            $currentMinutes = date('H') * 60 + date('i');
            $startMinutes = 8 * 60; // 08:00
            $endMinutes = 18 * 60; // 18:00
            $progress = max(0, min(100, (($currentMinutes - $startMinutes) / ($endMinutes - $startMinutes)) * 100));
            ?>
            <div class="position-relative">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" style="width: <?= $progress ?>%"></div>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">08:00</small>
                    <small class="text-muted">18:00</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <?php
    $totalBookings = count($models);
    $approvedCount = count(array_filter($models, fn($m) => $m->status == 'approved'));
    $pendingCount = count(array_filter($models, fn($m) => $m->status == 'pending'));
    $ongoingCount = count(array_filter($models, fn($m) => 
        $m->status == 'approved' && $m->start_time <= $currentTime && $m->end_time >= $currentTime
    ));
    $upcomingCount = count(array_filter($models, fn($m) => 
        $m->status == 'approved' && $m->start_time > $currentTime
    ));
    ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-primary"><?= $totalBookings ?></h3>
                    <small class="text-muted">ทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-success"><?= $ongoingCount ?></h3>
                    <small class="text-muted">กำลังประชุม</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-info"><?= $upcomingCount ?></h3>
                    <small class="text-muted">รอประชุม</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-warning"><?= $pendingCount ?></h3>
                    <small class="text-muted">รออนุมัติ</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline View -->
    <?php if (!empty($models)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list-timeline me-2"></i>ตารางการจองวันนี้</h5>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary active" id="view-timeline">
                    <i class="fas fa-bars"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="view-grid">
                    <i class="fas fa-th"></i>
                </button>
            </div>
        </div>
        <div class="card-body" id="timeline-view">
            <div class="timeline">
                <?php foreach ($models as $model): ?>
                <?php
                $isOngoing = $model->status == 'approved' && $model->start_time <= $currentTime && $model->end_time >= $currentTime;
                $isPast = $model->end_time < $currentTime;
                $isPending = $model->status == 'pending';
                
                $statusClass = 'secondary';
                $statusIcon = 'clock';
                $statusText = 'รออนุมัติ';
                
                if ($isOngoing) {
                    $statusClass = 'success';
                    $statusIcon = 'play-circle';
                    $statusText = 'กำลังประชุม';
                } elseif ($isPast) {
                    $statusClass = 'secondary';
                    $statusIcon = 'check-circle';
                    $statusText = 'เสร็จสิ้น';
                } elseif ($model->status == 'approved') {
                    $statusClass = 'primary';
                    $statusIcon = 'calendar-check';
                    $statusText = 'รอประชุม';
                } elseif ($isPending) {
                    $statusClass = 'warning';
                }
                ?>
                <div class="timeline-item <?= $isPast ? 'opacity-50' : '' ?> <?= $isOngoing ? 'ongoing' : '' ?>">
                    <div class="row align-items-center py-3 border-bottom">
                        <!-- Time -->
                        <div class="col-md-2 text-center">
                            <div class="d-flex flex-column align-items-center">
                                <span class="h5 mb-0"><?= substr($model->start_time, 0, 5) ?></span>
                                <small class="text-muted">ถึง <?= substr($model->end_time, 0, 5) ?></small>
                                <span class="badge bg-<?= $statusClass ?> mt-1">
                                    <i class="fas fa-<?= $statusIcon ?> me-1"></i><?= $statusText ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Room Info -->
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($model->room->image_url)): ?>
                                <img src="<?= $model->room->image_url ?>" 
                                     class="rounded me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-door-open text-muted"></i>
                                </div>
                                <?php endif; ?>
                                <div>
                                    <strong><?= Html::encode($model->room->name_th ?? '-') ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        <?= Html::encode($model->room->building->name_th ?? '') ?>
                                        <?php if ($model->room->floor): ?>
                                        ชั้น <?= $model->room->floor ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Booking Info -->
                        <div class="col-md-4">
                            <h6 class="mb-1">
                                <?= Html::a(
                                    Html::encode($model->title),
                                    ['view', 'id' => $model->id],
                                    ['class' => 'text-decoration-none']
                                ) ?>
                            </h6>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    <i class="fas fa-user me-1"></i>
                                    <?= Html::encode($model->user->full_name ?? '-') ?>
                                </span>
                                <span>
                                    <i class="fas fa-users me-1"></i>
                                    <?= $model->attendee_count ?> คน
                                </span>
                            </div>
                            <?php if ($model->description): ?>
                            <small class="text-muted d-block mt-1 text-truncate" style="max-width: 300px;">
                                <?= Html::encode($model->description) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Actions -->
                        <div class="col-md-3 text-end">
                            <?php if ($isPending): ?>
                            <?= Html::a(
                                '<i class="fas fa-check"></i>',
                                ['approve', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-success btn-sm me-1',
                                    'title' => 'อนุมัติ',
                                    'data-confirm' => 'ต้องการอนุมัติการจองนี้ใช่หรือไม่?',
                                    'data-method' => 'post',
                                ]
                            ) ?>
                            <?= Html::a(
                                '<i class="fas fa-times"></i>',
                                ['reject', 'id' => $model->id],
                                ['class' => 'btn btn-danger btn-sm me-1', 'title' => 'ปฏิเสธ']
                            ) ?>
                            <?php endif; ?>
                            
                            <?= Html::a(
                                '<i class="fas fa-eye"></i>',
                                ['view', 'id' => $model->id],
                                ['class' => 'btn btn-outline-secondary btn-sm', 'title' => 'ดูรายละเอียด']
                            ) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Grid View (Hidden by default) -->
        <div class="card-body d-none" id="grid-view">
            <div class="row g-3">
                <?php foreach ($models as $model): ?>
                <?php
                $isOngoing = $model->status == 'approved' && $model->start_time <= $currentTime && $model->end_time >= $currentTime;
                $isPast = $model->end_time < $currentTime;
                $borderClass = $isOngoing ? 'border-success' : ($model->status == 'pending' ? 'border-warning' : '');
                ?>
                <div class="col-md-4">
                    <div class="card h-100 <?= $borderClass ?> <?= $isPast ? 'opacity-50' : '' ?>" 
                         style="<?= $isOngoing ? 'border-width: 2px;' : '' ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-secondary">
                                    <?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?>
                                </span>
                                <?php if ($isOngoing): ?>
                                <span class="badge bg-success">
                                    <i class="fas fa-play-circle me-1"></i>กำลังประชุม
                                </span>
                                <?php endif; ?>
                            </div>
                            <h6 class="card-title">
                                <?= Html::a(
                                    Html::encode($model->title),
                                    ['view', 'id' => $model->id],
                                    ['class' => 'text-decoration-none stretched-link']
                                ) ?>
                            </h6>
                            <p class="card-text small text-muted mb-2">
                                <i class="fas fa-door-open me-1"></i><?= Html::encode($model->room->name_th ?? '-') ?>
                            </p>
                            <p class="card-text small text-muted mb-0">
                                <i class="fas fa-user me-1"></i><?= Html::encode($model->user->full_name ?? '-') ?>
                                <span class="ms-2">
                                    <i class="fas fa-users me-1"></i><?= $model->attendee_count ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Empty State -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-3">
                <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5>ไม่มีการจองวันนี้</h5>
            <p class="text-muted">ยังไม่มีการจองห้องประชุมในวันนี้</p>
            <?= Html::a(
                '<i class="fas fa-plus me-1"></i> จองห้องประชุม',
                ['create', 'date' => date('Y-m-d')],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.timeline-item.ongoing {
    background: linear-gradient(to right, rgba(25, 135, 84, 0.1), transparent);
    border-left: 3px solid #198754;
    margin-left: -1px;
}
</style>

<?php
$this->registerJs(<<<JS
// Toggle view
$('#view-timeline').on('click', function() {
    $(this).addClass('active').siblings().removeClass('active');
    $('#timeline-view').removeClass('d-none');
    $('#grid-view').addClass('d-none');
});

$('#view-grid').on('click', function() {
    $(this).addClass('active').siblings().removeClass('active');
    $('#grid-view').removeClass('d-none');
    $('#timeline-view').addClass('d-none');
});

// Auto refresh every 60 seconds
setTimeout(function() {
    location.reload();
}, 60000);
JS
);
?>
