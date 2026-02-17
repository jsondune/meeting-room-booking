<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\Department $model */
/** @var yii\data\ActiveDataProvider $usersDataProvider */
/** @var common\models\Department[] $childDepartments */
/** @var array $bookingStats */
/** @var common\models\Booking[] $recentBookings */
/** @var array $monthlyData */

$this->title = $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการหน่วยงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusColors = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'completed' => 'info',
];

$statusLabels = [
    'pending' => 'รออนุมัติ',
    'approved' => 'อนุมัติแล้ว',
    'rejected' => 'ปฏิเสธ',
    'cancelled' => 'ยกเลิก',
    'completed' => 'เสร็จสิ้น',
];
?>

<div class="department-view">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div class="d-flex align-items-center">
            <div class="rounded-3 p-3 me-3" style="background-color: #3b82f620;">
                <i class="bi bi-building" style="color: #3b82f6; font-size: 2rem;"></i>
            </div>
            <div>
                <h1 class="h3 mb-1"><?= Html::encode($model->name_th) ?></h1>
                <?php if ($model->name_en): ?>
                    <p class="text-muted mb-1"><?= Html::encode($model->name_en) ?></p>
                <?php endif; ?>
                <div class="d-flex gap-2">
                    <?php if ($model->code): ?>
                        <span class="badge bg-primary"><?= Html::encode($model->code) ?></span>
                    <?php endif; ?>
                    <?php if ($model->is_active): ?>
                        <span class="badge bg-success">ใช้งาน</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">ไม่ใช้งาน</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-pencil me-1"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i> ลบ', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'คุณแน่ใจหรือไม่ที่จะลบหน่วยงานนี้?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Department Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลหน่วยงาน</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">ชื่อหน่วยงาน</label>
                            <p class="mb-0 fw-medium"><?= Html::encode($model->name_th) ?></p>
                        </div>
                        <?php if ($model->name_en): ?>
                        <div class="col-md-6">
                            <label class="text-muted small">ชื่อภาษาอังกฤษ</label>
                            <p class="mb-0"><?= Html::encode($model->name_en) ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if ($model->code): ?>
                        <div class="col-md-6">
                            <label class="text-muted small">รหัสหน่วยงาน</label>
                            <p class="mb-0"><code><?= Html::encode($model->code) ?></code></p>
                        </div>
                        <?php endif; ?>
                        <?php if ($model->parent): ?>
                        <div class="col-md-6">
                            <label class="text-muted small">หน่วยงานต้นสังกัด</label>
                            <p class="mb-0">
                                <?= Html::a($model->parent->name_th, ['view', 'id' => $model->parent_id], ['class' => 'text-decoration-none']) ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Child Departments -->
            <?php if (!empty($childDepartments)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>หน่วยงานย่อย</h6>
                    <span class="badge bg-primary"><?= count($childDepartments) ?> หน่วยงาน</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อหน่วยงาน</th>
                                    <th class="text-center">สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($childDepartments as $child): ?>
                                <tr>
                                    <td><code><?= Html::encode($child->code ?? '-') ?></code></td>
                                    <td><?= Html::encode($child->name_th) ?></td>
                                    <td class="text-center">
                                        <?php if ($child->is_active): ?>
                                            <span class="badge bg-success">ใช้งาน</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">ไม่ใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $child->id], [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'ดูรายละเอียด',
                                        ]) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Users in Department -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-people me-2"></i>บุคลากรในหน่วยงาน</h6>
                    <span class="badge bg-primary"><?= $usersDataProvider->getTotalCount() ?> คน</span>
                </div>
                <div class="card-body p-0">
                    <?php if ($usersDataProvider->getTotalCount() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ตำแหน่ง</th>
                                    <th>อีเมล</th>
                                    <th>โทรศัพท์</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usersDataProvider->getModels() as $user): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:14px;">
                                                <?= mb_substr($user->full_name ?? 'U', 0, 1) ?>
                                            </div>
                                            <div class="fw-medium"><?= Html::encode($user->fullName) ?></div>
                                        </div>
                                    </td>
                                    <td><?= Html::encode($user->position ?? '-') ?></td>
                                    <td><a href="mailto:<?= Html::encode($user->email) ?>"><?= Html::encode($user->email) ?></a></td>
                                    <td><?= Html::encode($user->phone ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-people fs-1 mb-2 d-block"></i>
                        <p class="mb-0">ยังไม่มีบุคลากรในหน่วยงานนี้</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>การจองล่าสุด</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentBookings)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสการจอง</th>
                                    <th>ห้องประชุม</th>
                                    <th>หัวข้อ</th>
                                    <th>วันที่</th>
                                    <th>เวลา</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td>
                                        <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>">
                                            <?= Html::encode($booking->booking_code) ?>
                                        </a>
                                    </td>
                                    <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                    <td><?= Html::encode($booking->title) ?></td>
                                    <td><?= Yii::$app->formatter->asDate($booking->booking_date, 'php:d/m/Y') ?></td>
                                    <td><?= Html::encode($booking->start_time) ?> - <?= Html::encode($booking->end_time) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= $statusColors[$booking->status] ?? 'secondary' ?>">
                                            <?= $statusLabels[$booking->status] ?? $booking->status ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-calendar-x fs-1 mb-2 d-block"></i>
                        <p class="mb-0">ยังไม่มีการจองห้องประชุม</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column - Stats -->
        <div class="col-lg-4">
            <!-- Booking Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>สถิติการจอง</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-3 fw-bold text-primary"><?= number_format($bookingStats['total'] ?? 0) ?></div>
                                <div class="text-muted small">จองทั้งหมด</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-3 fw-bold text-success"><?= number_format($bookingStats['this_month'] ?? 0) ?></div>
                                <div class="text-muted small">เดือนนี้</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-3 fw-bold text-warning"><?= number_format($bookingStats['pending'] ?? 0) ?></div>
                                <div class="text-muted small">รออนุมัติ</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="fs-3 fw-bold text-info"><?= number_format($bookingStats['upcoming'] ?? 0) ?></div>
                                <div class="text-muted small">กำลังจะมาถึง</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Chart -->
            <?php if (!empty($monthlyData)): ?>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>การจองรายเดือน</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>
            <?php endif; ?>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>ข้อมูลระบบ</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">รหัส ID</span>
                        <span><?= $model->id ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">ลำดับการแสดง</span>
                        <span><?= $model->sort_order ?? '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">สร้างเมื่อ</span>
                        <span><?= $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at, 'short') : '-' ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">แก้ไขล่าสุด</span>
                        <span><?= $model->updated_at ? Yii::$app->formatter->asDatetime($model->updated_at, 'short') : '-' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($monthlyData)): ?>
<?php
$labels = array_map(function($item) {
    return $item['month'] . ' ' . ($item['year'] + 543);
}, $monthlyData);
$data = array_column($monthlyData, 'count');
$labelsJson = json_encode($labels);
$dataJson = json_encode($data);
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('monthlyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= $labelsJson ?>,
                datasets: [{
                    label: 'จำนวนการจอง',
                    data: <?= $dataJson ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
<?php endif; ?>
