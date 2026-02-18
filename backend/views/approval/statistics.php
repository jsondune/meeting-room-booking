<?php
/**
 * Approval Statistics - Performance analytics and metrics
 * Meeting Room Booking System - Backend
 * 
 * @var yii\web\View $this
 * @var array $stats
 * @var array $byApprover
 * @var array $byDepartment
 * @var float $avgResponseTime
 * @var string $dateFrom
 * @var string $dateTo
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'สถิติการอนุมัติ';
$this->params['breadcrumbs'][] = ['label' => 'อนุมัติการจอง', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'สถิติ';

// Process by approver data
$approverStats = [];
foreach ($byApprover as $row) {
    $id = $row['approved_by'];
    if (!isset($approverStats[$id])) {
        $approverStats[$id] = ['approved' => 0, 'rejected' => 0, 'total' => 0];
    }
    $approverStats[$id][$row['status']] = (int)$row['total'];
    $approverStats[$id]['total'] += (int)$row['total'];
}

// Process by department data
$departmentStats = [];
foreach ($byDepartment as $row) {
    $id = $row['department_id'];
    if (!isset($departmentStats[$id])) {
        $departmentStats[$id] = ['approved' => 0, 'rejected' => 0, 'total' => 0];
    }
    $departmentStats[$id][$row['status']] = (int)$row['total'];
    $departmentStats[$id]['total'] += (int)$row['total'];
}
?>

<div class="approval-statistics">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-graph-up me-2"></i>
                สถิติการอนุมัติ
            </h1>
            <p class="text-muted mb-0">วิเคราะห์ประสิทธิภาพการอนุมัติการจองห้องประชุม</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>พิมพ์
            </button>
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="date_from" class="form-control" 
                           value="<?= Html::encode($dateFrom) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="date_to" class="form-control" 
                           value="<?= Html::encode($dateTo) ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter me-1"></i>กรองข้อมูล
                    </button>
                    <?= Html::a('รีเซ็ต', ['statistics'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <a href="<?= Url::to(['statistics', 'date_from' => date('Y-m-01'), 'date_to' => date('Y-m-d')]) ?>" 
                           class="btn btn-outline-secondary btn-sm">เดือนนี้</a>
                        <a href="<?= Url::to(['statistics', 'date_from' => date('Y-m-01', strtotime('-1 month')), 'date_to' => date('Y-m-t', strtotime('-1 month'))]) ?>" 
                           class="btn btn-outline-secondary btn-sm">เดือนก่อน</a>
                        <a href="<?= Url::to(['statistics', 'date_from' => date('Y-01-01'), 'date_to' => date('Y-m-d')]) ?>" 
                           class="btn btn-outline-secondary btn-sm">ปีนี้</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data fs-2 text-primary mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['total']) ?></h3>
                    <small class="text-muted">การจองทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-2 text-success mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['approved']) ?></h3>
                    <small class="text-muted">อนุมัติแล้ว</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle fs-2 text-danger mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['rejected']) ?></h3>
                    <small class="text-muted">ปฏิเสธ</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split fs-2 text-warning mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['pending']) ?></h3>
                    <small class="text-muted">รอพิจารณา</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-person-check fs-2 text-info mb-2"></i>
                    <h3 class="mb-0"><?= number_format($stats['myApprovals']) ?></h3>
                    <small class="text-muted">อนุมัติโดยคุณ</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card h-100 border-0 bg-secondary bg-opacity-10">
                <div class="card-body text-center">
                    <i class="bi bi-percent fs-2 text-secondary mb-2"></i>
                    <h3 class="mb-0"><?= $stats['approvalRate'] ?>%</h3>
                    <small class="text-muted">อัตราอนุมัติ</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Charts Column -->
        <div class="col-lg-8">
            <!-- Status Distribution Chart -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        สัดส่วนการพิจารณา
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <canvas id="statusChart" height="200"></canvas>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="badge bg-success me-2">&nbsp;</span>
                                            อนุมัติแล้ว
                                        </td>
                                        <td class="text-end">
                                            <strong><?= number_format($stats['approved']) ?></strong>
                                        </td>
                                        <td class="text-end text-muted">
                                            <?php
                                            $total = $stats['approved'] + $stats['rejected'] + $stats['pending'];
                                            echo $total > 0 ? round(($stats['approved'] / $total) * 100, 1) : 0;
                                            ?>%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge bg-danger me-2">&nbsp;</span>
                                            ปฏิเสธ
                                        </td>
                                        <td class="text-end">
                                            <strong><?= number_format($stats['rejected']) ?></strong>
                                        </td>
                                        <td class="text-end text-muted">
                                            <?= $total > 0 ? round(($stats['rejected'] / $total) * 100, 1) : 0 ?>%
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge bg-warning me-2">&nbsp;</span>
                                            รอพิจารณา
                                        </td>
                                        <td class="text-end">
                                            <strong><?= number_format($stats['pending']) ?></strong>
                                        </td>
                                        <td class="text-end text-muted">
                                            <?= $total > 0 ? round(($stats['pending'] / $total) * 100, 1) : 0 ?>%
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- By Approver Chart -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people me-2"></i>
                        ผลการอนุมัติตามผู้อนุมัติ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($approverStats)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 mb-2"></i>
                            <p class="mb-0">ไม่มีข้อมูลในช่วงเวลาที่เลือก</p>
                        </div>
                    <?php else: ?>
                        <canvas id="approverChart" height="<?= min(count($approverStats) * 40 + 40, 300) ?>"></canvas>
                    <?php endif; ?>
                </div>
            </div>

            <!-- By Department Chart -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building me-2"></i>
                        การจองตามหน่วยงาน
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($departmentStats)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-1 mb-2"></i>
                            <p class="mb-0">ไม่มีข้อมูลในช่วงเวลาที่เลือก</p>
                        </div>
                    <?php else: ?>
                        <canvas id="departmentChart" height="<?= min(count($departmentStats) * 40 + 40, 300) ?>"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Metrics Column -->
        <div class="col-lg-4">
            <!-- Response Time Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-speedometer2 me-2"></i>
                        เวลาตอบสนองเฉลี่ย
                    </h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $responseHours = round($avgResponseTime, 1);
                    $responseDays = floor($responseHours / 24);
                    $remainingHours = round($responseHours - ($responseDays * 24), 1);
                    
                    // Determine color based on response time
                    if ($responseHours <= 4) {
                        $responseColor = 'success';
                        $responseLabel = 'ยอดเยี่ยม';
                    } elseif ($responseHours <= 12) {
                        $responseColor = 'info';
                        $responseLabel = 'ดี';
                    } elseif ($responseHours <= 24) {
                        $responseColor = 'warning';
                        $responseLabel = 'ปานกลาง';
                    } else {
                        $responseColor = 'danger';
                        $responseLabel = 'ควรปรับปรุง';
                    }
                    ?>
                    <div class="display-4 text-<?= $responseColor ?> mb-2">
                        <?php if ($responseDays > 0): ?>
                            <?= $responseDays ?><small class="fs-6">วัน</small>
                            <?= $remainingHours ?><small class="fs-6">ชม.</small>
                        <?php else: ?>
                            <?= $responseHours ?><small class="fs-6">ชม.</small>
                        <?php endif; ?>
                    </div>
                    <span class="badge bg-<?= $responseColor ?>"><?= $responseLabel ?></span>
                    <p class="text-muted small mt-3 mb-0">
                        เวลาเฉลี่ยตั้งแต่สร้างการจองจนกระทั่งได้รับการพิจารณา
                    </p>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bullseye me-2"></i>
                        ตัวชี้วัดประสิทธิภาพ
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Approval Rate -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">อัตราอนุมัติ</span>
                            <span class="small fw-bold"><?= $stats['approvalRate'] ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" 
                                 style="width: <?= $stats['approvalRate'] ?>%"></div>
                        </div>
                    </div>

                    <!-- Processing Rate -->
                    <?php
                    $processed = $stats['approved'] + $stats['rejected'];
                    $processingRate = $stats['total'] > 0 ? round(($processed / $stats['total']) * 100, 1) : 0;
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">อัตราการพิจารณา</span>
                            <span class="small fw-bold"><?= $processingRate ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" 
                                 style="width: <?= $processingRate ?>%"></div>
                        </div>
                    </div>

                    <!-- Your Contribution -->
                    <?php
                    $myContribution = $processed > 0 ? round(($stats['myApprovals'] / $processed) * 100, 1) : 0;
                    ?>
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small">สัดส่วนที่คุณอนุมัติ</span>
                            <span class="small fw-bold"><?= $myContribution ?>%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" 
                                 style="width: <?= $myContribution ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Approvers -->
            <?php if (!empty($approverStats)): ?>
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-trophy me-2"></i>
                            ผู้อนุมัติสูงสุด
                        </h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php
                        arsort($approverStats);
                        $rank = 1;
                        foreach (array_slice($approverStats, 0, 5, true) as $approverId => $data):
                            $approver = \common\models\User::findOne($approverId);
                            if (!$approver) continue;
                        ?>
                            <div class="list-group-item d-flex align-items-center">
                                <span class="badge bg-<?= $rank <= 3 ? 'warning' : 'secondary' ?> me-3">
                                    <?= $rank ?>
                                </span>
                                <div class="flex-grow-1">
                                    <strong class="d-block"><?= Html::encode($approver->full_name) ?></strong>
                                    <small class="text-muted">
                                        <span class="text-success"><?= $data['approved'] ?> อนุมัติ</span>
                                        •
                                        <span class="text-danger"><?= $data['rejected'] ?> ปฏิเสธ</span>
                                    </small>
                                </div>
                                <span class="badge bg-primary"><?= $data['total'] ?></span>
                            </div>
                        <?php 
                            $rank++;
                        endforeach;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    .btn, form, .card-header { 
        display: none !important; 
    }
    .card {
        border: 1px solid #dee2e6 !important;
        break-inside: avoid;
    }
    .card-body {
        padding: 0.5rem !important;
    }
}
</style>

<?php
// Chart.js data preparation
$approverLabels = [];
$approverApproved = [];
$approverRejected = [];

foreach ($approverStats as $approverId => $data) {
    $approver = \common\models\User::findOne($approverId);
    $approverLabels[] = $approver ? $approver->full_name : "ID: {$approverId}";
    $approverApproved[] = $data['approved'];
    $approverRejected[] = $data['rejected'];
}

$departmentLabels = [];
$departmentApproved = [];
$departmentRejected = [];

foreach ($departmentStats as $deptId => $data) {
    $dept = \common\models\Department::findOne($deptId);
    $departmentLabels[] = $dept ? $dept->name_th : "ID: {$deptId}";
    $departmentApproved[] = $data['approved'];
    $departmentRejected[] = $data['rejected'];
}

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJs(<<<JS
// Status Distribution Chart
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['อนุมัติแล้ว', 'ปฏิเสธ', 'รอพิจารณา'],
        datasets: [{
            data: [{$stats['approved']}, {$stats['rejected']}, {$stats['pending']}],
            backgroundColor: ['#198754', '#dc3545', '#ffc107'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        cutout: '60%'
    }
});

// Approver Chart
var approverLabels = JSON.parse('" . json_encode($approverLabels) . "');
var approverApproved = JSON.parse('" . json_encode($approverApproved) . "');
var approverRejected = JSON.parse('" . json_encode($approverRejected) . "');

if (approverLabels.length > 0) {
    new Chart(document.getElementById('approverChart'), {
        type: 'bar',
        data: {
            labels: approverLabels,
            datasets: [
                {
                    label: 'อนุมัติ',
                    data: approverApproved,
                    backgroundColor: '#198754'
                },
                {
                    label: 'ปฏิเสธ',
                    data: approverRejected,
                    backgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true
                },
                y: {
                    stacked: true
                }
            }
        }
    });
}

// Department Chart
var departmentLabels = JSON.parse('" . json_encode($departmentLabels) . "');
var departmentApproved = JSON.parse('" . json_encode($departmentApproved) . "');
var departmentRejected = JSON.parse('" . json_encode($departmentRejected) . "');

if (departmentLabels.length > 0) {
    new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: departmentLabels,
            datasets: [
                {
                    label: 'อนุมัติ',
                    data: departmentApproved,
                    backgroundColor: '#198754'
                },
                {
                    label: 'ปฏิเสธ',
                    data: departmentRejected,
                    backgroundColor: '#dc3545'
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                x: {
                    stacked: true,
                    beginAtZero: true
                },
                y: {
                    stacked: true
                }
            }
        }
    });
}
JS);
?>
