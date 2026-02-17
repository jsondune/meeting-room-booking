<?php
/**
 * Backend Booking Index View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var backend\models\BookingSearch $searchModel
 * @var array $rooms
 * @var array $statusOptions
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'รายการจอง';
$this->params['breadcrumbs'][] = $this->title;

// Thai date helpers
$thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

$formatThaiDate = function($date, $format = 'medium') use ($thaiMonths, $thaiMonthsShort) {
    if (empty($date)) return '-';
    $dt = new DateTime($date);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    
    switch ($format) {
        case 'short':
            return $day . '/' . $month . '/' . ($year % 100);
        case 'medium':
            return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
        case 'long':
            return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
        default:
            return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
    }
};

$formatThaiDateTime = function($datetime, $format = 'medium') use ($thaiMonthsShort) {
    if (empty($datetime)) return '-';
    $dt = new DateTime($datetime);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    $time = $dt->format('H:i');
    
    if ($format === 'short') {
        return $day . '/' . $month . '/' . ($year % 100) . ' ' . $time;
    }
    return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year . ' ' . $time . ' น.';
};

// Status labels
$statusLabels = [
    'pending' => ['label' => 'รออนุมัติ', 'class' => 'warning'],
    'approved' => ['label' => 'อนุมัติแล้ว', 'class' => 'success'],
    'rejected' => ['label' => 'ไม่อนุมัติ', 'class' => 'danger'],
    'cancelled' => ['label' => 'ยกเลิก', 'class' => 'secondary'],
    'completed' => ['label' => 'เสร็จสิ้น', 'class' => 'info'],
];
?>

<div class="booking-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-calendar-event me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">จัดการรายการจองห้องประชุมทั้งหมด</p>
        </div>
        <div>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>สร้างการจอง
            </a>
            <a href="<?= Url::to(['calendar']) ?>" class="btn btn-outline-primary ms-2">
                <i class="bi bi-calendar3 me-1"></i>ปฏิทิน
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false">
                <i class="bi bi-funnel me-2"></i>ตัวกรอง
                <i class="bi bi-chevron-down float-end"></i>
            </a>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'action' => ['index'],
                    'method' => 'get',
                    'options' => ['class' => 'row g-3']
                ]); ?>
                
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'booking_code')->textInput(['placeholder' => 'รหัสการจอง'])->label('รหัสการจอง') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'room_id')->dropDownList(
                        $rooms ?? [],
                        ['prompt' => '-- เลือกห้อง --']
                    )->label('ห้องประชุม') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'status')->dropDownList(
                        [
                            'pending' => 'รออนุมัติ',
                            'approved' => 'อนุมัติแล้ว',
                            'rejected' => 'ไม่อนุมัติ',
                            'cancelled' => 'ยกเลิก',
                            'completed' => 'เสร็จสิ้น',
                        ],
                        ['prompt' => '-- ทุกสถานะ --']
                    )->label('สถานะ') ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'booking_date')->input('date')->label('วันที่จอง') ?>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-circle me-1"></i>ล้าง
                    </a>
                </div>
                
                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'booking-grid-pjax']); ?>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสการจอง</th>
                            <th>ห้อง</th>
                            <th>วันที่/เวลา</th>
                            <th>ผู้จอง</th>
                            <th>สถานะ</th>
                            <th class="text-center" style="width: 120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($dataProvider->getCount() > 0): ?>
                            <?php foreach ($dataProvider->getModels() as $model): ?>
                            <tr>
                                <td>
                                    <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" class="fw-semibold text-decoration-none">
                                        <?= Html::encode($model->booking_code ?? '-') ?>
                                    </a>
                                </td>
                                <td><?= Html::encode($model->room->name_th ?? '-') ?></td>
                                <td>
                                    <div><?= $formatThaiDate($model->booking_date, 'medium') ?></div>
                                    <small class="text-muted">
                                        <?= substr($model->start_time ?? '00:00', 0, 5) ?> - <?= substr($model->end_time ?? '00:00', 0, 5) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($model->user): ?>
                                        <div><?= Html::encode($model->user->full_name ?? $model->user->username ?? '-') ?></div>
                                        <?php if ($model->user->department): ?>
                                            <small class="text-muted"><?= Html::encode($model->user->department->name_th ?? '') ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $status = $model->status ?: 'pending';
                                    $statusInfo = $statusLabels[$status] ?? ['label' => ucfirst($status ?: 'Unknown'), 'class' => 'secondary'];
                                    ?>
                                    <span class="badge bg-<?= $statusInfo['class'] ?>">
                                        <?= $statusInfo['label'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" 
                                           class="btn btn-outline-primary" title="ดู">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if (($model->status ?? '') === 'pending'): ?>
                                        <a href="<?= Url::to(['approval/view', 'id' => $model->id]) ?>" 
                                           class="btn btn-outline-warning" title="อนุมัติ">
                                            <i class="bi bi-check-lg"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" 
                                           class="btn btn-outline-secondary" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                    ไม่พบรายการจอง
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($dataProvider->pagination): ?>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= $dataProvider->getTotalCount() ?> รายการ
                </div>
                <?= \yii\widgets\LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                ]) ?>
            </div>
            <?php endif; ?>
            
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>
