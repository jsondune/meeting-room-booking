<?php
/**
 * My Bookings View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var backend\models\BookingSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $rooms
 * @var array $statusLabels
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\Booking;

$this->title = 'การจองของฉัน';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$models = $dataProvider->getModels();
?>

<div class="booking-my-bookings">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-user-clock text-primary me-2"></i><?= Html::encode($this->title) ?></h4>
            <p class="text-muted mb-0">รายการจองห้องประชุมของคุณ</p>
        </div>
        <?= Html::a(
            '<i class="fas fa-plus me-1"></i> จองห้องประชุมใหม่',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <!-- Quick Stats -->
    <?php
    $user = Yii::$app->user->identity;
    $upcomingCount = Booking::find()
        ->where(['user_id' => $user->id])
        ->andWhere(['>=', 'booking_date', date('Y-m-d')])
        ->andWhere(['in', 'status', ['pending', 'approved']])
        ->count();
    $pendingCount = Booking::find()
        ->where(['user_id' => $user->id, 'status' => 'pending'])
        ->count();
    $completedCount = Booking::find()
        ->where(['user_id' => $user->id, 'status' => 'completed'])
        ->count();
    $cancelledCount = Booking::find()
        ->where(['user_id' => $user->id, 'status' => 'cancelled'])
        ->count();
    ?>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-primary"><?= $upcomingCount ?></h3>
                    <small class="text-muted">การจองที่จะมาถึง</small>
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
        <div class="col-md-3">
            <div class="card border-0 bg-success bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-success"><?= $completedCount ?></h3>
                    <small class="text-muted">เสร็จสิ้น</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-secondary bg-opacity-10 h-100">
                <div class="card-body text-center py-3">
                    <h3 class="mb-1 text-secondary"><?= $cancelledCount ?></h3>
                    <small class="text-muted">ยกเลิก</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-3" id="bookingTabs">
        <li class="nav-item">
            <a class="nav-link <?= empty($searchModel->status) ? 'active' : '' ?>" 
               href="<?= Url::to(['my-bookings']) ?>">
                ทั้งหมด
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $searchModel->status == 'pending' ? 'active' : '' ?>" 
               href="<?= Url::to(['my-bookings', 'BookingSearch[status]' => 'pending']) ?>">
                <i class="fas fa-clock text-warning me-1"></i>รออนุมัติ
                <?php if ($pendingCount > 0): ?>
                <span class="badge bg-warning text-dark"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $searchModel->status == 'approved' ? 'active' : '' ?>" 
               href="<?= Url::to(['my-bookings', 'BookingSearch[status]' => 'approved']) ?>">
                <i class="fas fa-check text-success me-1"></i>อนุมัติแล้ว
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $searchModel->status == 'completed' ? 'active' : '' ?>" 
               href="<?= Url::to(['my-bookings', 'BookingSearch[status]' => 'completed']) ?>">
                <i class="fas fa-check-double text-info me-1"></i>เสร็จสิ้น
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $searchModel->status == 'cancelled' ? 'active' : '' ?>" 
               href="<?= Url::to(['my-bookings', 'BookingSearch[status]' => 'cancelled']) ?>">
                <i class="fas fa-ban text-secondary me-1"></i>ยกเลิก
            </a>
        </li>
    </ul>

    <!-- Bookings List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (!empty($models)): ?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}{pager}</div>",
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                        'options' => ['class' => 'pagination pagination-sm mb-0'],
                    ],
                    'columns' => [
                        [
                            'attribute' => 'booking_code',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a(
                                    '<span class="badge bg-light text-dark">' . Html::encode($model->booking_code) . '</span>',
                                    ['view', 'id' => $model->id],
                                    ['class' => 'text-decoration-none']
                                );
                            },
                            'headerOptions' => ['style' => 'width: 130px;'],
                        ],
                        [
                            'attribute' => 'title',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a(
                                    '<strong>' . Html::encode($model->title) . '</strong>',
                                    ['view', 'id' => $model->id],
                                    ['class' => 'text-decoration-none']
                                );
                            },
                        ],
                        [
                            'attribute' => 'room_id',
                            'label' => 'ห้องประชุม',
                            'format' => 'raw',
                            'filter' => $rooms,
                            'value' => function ($model) {
                                return '<i class="fas fa-door-open text-muted me-1"></i>' 
                                    . Html::encode($model->room->name_th ?? '-');
                            },
                        ],
                        [
                            'attribute' => 'booking_date',
                            'label' => 'วันที่/เวลา',
                            'format' => 'raw',
                            'filter' => Html::activeInput('date', $searchModel, 'booking_date', ['class' => 'form-control form-control-sm']),
                            'value' => function ($model) {
                                $date = Yii::$app->formatter->asDate($model->booking_date);
                                $time = substr($model->start_time, 0, 5) . ' - ' . substr($model->end_time, 0, 5);
                                
                                $isUpcoming = strtotime($model->booking_date) >= strtotime(date('Y-m-d'));
                                $isToday = $model->booking_date == date('Y-m-d');
                                
                                $dateClass = $isToday ? 'text-success fw-bold' : ($isUpcoming ? '' : 'text-muted');
                                
                                return '<span class="' . $dateClass . '">' . $date . '</span>'
                                    . ($isToday ? ' <span class="badge bg-success">วันนี้</span>' : '')
                                    . '<br><small class="text-muted"><i class="fas fa-clock me-1"></i>' . $time . ' น.</small>';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'filter' => $statusLabels,
                            'value' => function ($model) use ($statusLabels) {
                                $colors = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary',
                                    'completed' => 'info',
                                ];
                                $icons = [
                                    'pending' => 'clock',
                                    'approved' => 'check-circle',
                                    'rejected' => 'times-circle',
                                    'cancelled' => 'ban',
                                    'completed' => 'check-double',
                                ];
                                return '<span class="badge bg-' . ($colors[$model->status] ?? 'secondary') . '">'
                                    . '<i class="fas fa-' . ($icons[$model->status] ?? 'circle') . ' me-1"></i>'
                                    . ($statusLabels[$model->status] ?? $model->status) 
                                    . '</span>';
                            },
                            'headerOptions' => ['style' => 'width: 120px;'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view} {update} {cancel}',
                            'headerOptions' => ['style' => 'width: 120px; text-align: center;'],
                            'contentOptions' => ['class' => 'text-center'],
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a(
                                        '<i class="fas fa-eye"></i>',
                                        ['view', 'id' => $model->id],
                                        ['class' => 'btn btn-outline-primary btn-sm me-1', 'title' => 'ดู']
                                    );
                                },
                                'update' => function ($url, $model) {
                                    if (!$model->canBeEdited()) {
                                        return '';
                                    }
                                    return Html::a(
                                        '<i class="fas fa-edit"></i>',
                                        ['update', 'id' => $model->id],
                                        ['class' => 'btn btn-outline-secondary btn-sm me-1', 'title' => 'แก้ไข']
                                    );
                                },
                                'cancel' => function ($url, $model) {
                                    if (!$model->canBeCancelled()) {
                                        return '';
                                    }
                                    return Html::a(
                                        '<i class="fas fa-ban"></i>',
                                        ['cancel', 'id' => $model->id],
                                        ['class' => 'btn btn-outline-danger btn-sm', 'title' => 'ยกเลิก']
                                    );
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>
            <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                </div>
                <h5>ไม่พบการจอง</h5>
                <p class="text-muted">
                    <?php if (!empty($searchModel->status)): ?>
                    ไม่มีการจองที่มีสถานะ "<?= $statusLabels[$searchModel->status] ?? $searchModel->status ?>"
                    <?php else: ?>
                    คุณยังไม่มีการจองห้องประชุม
                    <?php endif; ?>
                </p>
                <?= Html::a(
                    '<i class="fas fa-plus me-1"></i> จองห้องประชุมใหม่',
                    ['create'],
                    ['class' => 'btn btn-primary']
                ) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Bookings Section -->
    <?php
    $upcomingBookings = Booking::find()
        ->where(['user_id' => $user->id])
        ->andWhere(['>=', 'booking_date', date('Y-m-d')])
        ->andWhere(['in', 'status', ['approved']])
        ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
        ->limit(3)
        ->all();
    ?>
    <?php if (!empty($upcomingBookings)): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-transparent border-0">
            <h5 class="mb-0">
                <i class="fas fa-calendar-check text-success me-2"></i>การจองที่จะมาถึง
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <?php foreach ($upcomingBookings as $booking): ?>
                <div class="col-md-4">
                    <div class="card h-100 border-success <?= $booking->booking_date == date('Y-m-d') ? 'bg-success bg-opacity-10' : '' ?>">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-success">
                                    <?= Yii::$app->formatter->asDate($booking->booking_date) ?>
                                </span>
                                <?php if ($booking->booking_date == date('Y-m-d')): ?>
                                <span class="badge bg-warning text-dark">วันนี้</span>
                                <?php endif; ?>
                            </div>
                            <h6 class="card-title mb-2">
                                <?= Html::a(
                                    Html::encode($booking->title),
                                    ['view', 'id' => $booking->id],
                                    ['class' => 'text-decoration-none stretched-link']
                                ) ?>
                            </h6>
                            <p class="card-text small text-muted mb-1">
                                <i class="fas fa-clock me-1"></i>
                                <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?> น.
                            </p>
                            <p class="card-text small text-muted mb-0">
                                <i class="fas fa-door-open me-1"></i>
                                <?= Html::encode($booking->room->name_th ?? '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
