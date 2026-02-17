<?php
/**
 * Booking Report View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var array $stats
 * @var array $byRoom
 * @var array $byDepartment
 * @var array $dailyTrend
 * @var string $dateFrom
 * @var string $dateTo
 * @var array $rooms
 * @var array $departments
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'รายงานการจอง';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="booking-report">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-chart-bar text-primary me-2"></i><?= Html::encode($this->title) ?></h4>
            <p class="text-muted mb-0">
                ข้อมูลการจองตั้งแต่ <?= Yii::$app->formatter->asDate($dateFrom, 'long') ?> 
                ถึง <?= Yii::$app->formatter->asDate($dateTo, 'long') ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> พิมพ์
            </button>
            <?= Html::a(
                '<i class="fas fa-file-excel me-1"></i> ส่งออก',
                array_merge(['export'], Yii::$app->request->queryParams),
                ['class' => 'btn btn-success']
            ) ?>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['report']]); ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">ห้องประชุม</label>
                    <?= Html::dropDownList('room_id', Yii::$app->request->get('room_id'), $rooms, [
                        'class' => 'form-select',
                        'prompt' => '-- ทั้งหมด --'
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">หน่วยงาน</label>
                    <?= Html::dropDownList('department_id', Yii::$app->request->get('department_id'), $departments, [
                        'class' => 'form-select',
                        'prompt' => '-- ทั้งหมด --'
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> ค้นหา
                    </button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 bg-primary text-white h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['total']) ?></h2>
                    <small>การจองทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-success text-white h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['approved']) ?></h2>
                    <small>อนุมัติ</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-info text-white h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['completed']) ?></h2>
                    <small>เสร็จสิ้น</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-warning text-dark h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['pending']) ?></h2>
                    <small>รออนุมัติ</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-danger text-white h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['rejected'] + $stats['cancelled']) ?></h2>
                    <small>ปฏิเสธ/ยกเลิก</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 bg-secondary text-white h-100">
                <div class="card-body text-center py-4">
                    <h2 class="mb-1"><?= number_format($stats['totalHours'], 1) ?></h2>
                    <small>ชั่วโมงใช้งาน</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($stats['totalCost'] > 0): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 bg-gradient bg-dark text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <span>รายได้รวมจากการจอง</span>
                        </div>
                        <h3 class="mb-0"><?= Yii::$app->formatter->asCurrency($stats['totalCost'], 'THB') ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mb-4">
        <!-- Daily Trend Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>แนวโน้มการจองรายวัน</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailyTrendChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Status Distribution -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>สถานะการจอง</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- By Room -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-door-open me-2"></i>การใช้งานตามห้องประชุม</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($byRoom)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ห้องประชุม</th>
                                    <th class="text-center">จำนวนครั้ง</th>
                                    <th class="text-center">ชั่วโมง</th>
                                    <th style="width: 30%;">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalRoomCount = array_sum(array_column($byRoom, 'count'));
                                foreach ($byRoom as $room): 
                                    $roomModel = \common\models\MeetingRoom::findOne($room['room_id']);
                                    $percentage = $totalRoomCount > 0 ? ($room['count'] / $totalRoomCount) * 100 : 0;
                                ?>
                                <tr>
                                    <td><?= Html::encode($roomModel->name_th ?? 'ไม่ระบุ') ?></td>
                                    <td class="text-center"><?= number_format($room['count']) ?></td>
                                    <td class="text-center"><?= number_format($room['total_minutes'] / 60, 1) ?></td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: <?= $percentage ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= number_format($percentage, 1) ?>%</small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0">ไม่มีข้อมูล</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- By Department -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>การใช้งานตามหน่วยงาน</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($byDepartment)): ?>
                    <canvas id="departmentChart" height="200"></canvas>
                    <?php else: ?>
                    <p class="text-muted text-center mb-0">ไม่มีข้อมูล</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Efficiency Metrics -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0">
            <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>ตัวชี้วัดประสิทธิภาพ</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <?php
                $approvalRate = $stats['total'] > 0 ? (($stats['approved'] + $stats['completed']) / $stats['total']) * 100 : 0;
                $completionRate = ($stats['approved'] + $stats['completed']) > 0 ? ($stats['completed'] / ($stats['approved'] + $stats['completed'])) * 100 : 0;
                $cancellationRate = $stats['total'] > 0 ? (($stats['cancelled'] + $stats['rejected']) / $stats['total']) * 100 : 0;
                $avgHoursPerBooking = ($stats['approved'] + $stats['completed']) > 0 ? $stats['totalHours'] / ($stats['approved'] + $stats['completed']) : 0;
                ?>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="position-relative d-inline-block mb-2">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="45" stroke="#e9ecef" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="45" stroke="#198754" stroke-width="8" fill="none"
                                        stroke-dasharray="<?= 283 * $approvalRate / 100 ?> 283"
                                        transform="rotate(-90 50 50)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <strong class="h5 mb-0"><?= number_format($approvalRate, 1) ?>%</strong>
                            </div>
                        </div>
                        <h6>อัตราอนุมัติ</h6>
                        <small class="text-muted">การจองที่ได้รับอนุมัติ</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="position-relative d-inline-block mb-2">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="45" stroke="#e9ecef" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="45" stroke="#0d6efd" stroke-width="8" fill="none"
                                        stroke-dasharray="<?= 283 * $completionRate / 100 ?> 283"
                                        transform="rotate(-90 50 50)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <strong class="h5 mb-0"><?= number_format($completionRate, 1) ?>%</strong>
                            </div>
                        </div>
                        <h6>อัตราใช้งานจริง</h6>
                        <small class="text-muted">การจองที่มีการใช้งาน</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="position-relative d-inline-block mb-2">
                            <svg width="100" height="100">
                                <circle cx="50" cy="50" r="45" stroke="#e9ecef" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="45" stroke="#dc3545" stroke-width="8" fill="none"
                                        stroke-dasharray="<?= 283 * $cancellationRate / 100 ?> 283"
                                        transform="rotate(-90 50 50)"/>
                            </svg>
                            <div class="position-absolute top-50 start-50 translate-middle">
                                <strong class="h5 mb-0"><?= number_format($cancellationRate, 1) ?>%</strong>
                            </div>
                        </div>
                        <h6>อัตรายกเลิก</h6>
                        <small class="text-muted">การจองที่ถูกยกเลิก</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="position-relative d-inline-block mb-2">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <div>
                                    <strong class="h4 mb-0 text-primary"><?= number_format($avgHoursPerBooking, 1) ?></strong>
                                    <br><small>ชม.</small>
                                </div>
                            </div>
                        </div>
                        <h6>เฉลี่ยต่อครั้ง</h6>
                        <small class="text-muted">ระยะเวลาการประชุม</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Prepare chart data
$dailyLabels = json_encode(array_column($dailyTrend, 'booking_date'));
$dailyData = json_encode(array_column($dailyTrend, 'count'));

$departmentLabels = [];
$departmentData = [];
foreach ($byDepartment as $dept) {
    $deptModel = \common\models\Department::findOne($dept['department_id']);
    $departmentLabels[] = $deptModel->name_th ?? 'ไม่ระบุ';
    $departmentData[] = (int)$dept['count'];
}
$departmentLabels = json_encode($departmentLabels);
$departmentData = json_encode($departmentData);

$statusLabels = json_encode(['อนุมัติ', 'เสร็จสิ้น', 'รออนุมัติ', 'ปฏิเสธ', 'ยกเลิก']);
$statusData = json_encode([$stats['approved'], $stats['completed'], $stats['pending'], $stats['rejected'], $stats['cancelled']]);
$statusColors = json_encode(['#198754', '#0dcaf0', '#ffc107', '#dc3545', '#6c757d']);

$this->registerJs(<<<JS
// Daily Trend Chart
new Chart(document.getElementById('dailyTrendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: {$dailyLabels},
        datasets: [{
            label: 'จำนวนการจอง',
            data: {$dailyData},
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Status Distribution Chart
new Chart(document.getElementById('statusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: {$statusLabels},
        datasets: [{
            data: {$statusData},
            backgroundColor: {$statusColors}
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Department Chart
var deptCanvas = document.getElementById('departmentChart');
if (deptCanvas) {
    new Chart(deptCanvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels: {$departmentLabels},
            datasets: [{
                label: 'จำนวนการจอง',
                data: {$departmentData},
                backgroundColor: '#6f42c1'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}
JS
);
?>

<style>
@media print {
    .btn, .form-control, .form-select, nav, footer, .sidebar {
        display: none !important;
    }
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}
</style>
