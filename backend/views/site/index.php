<?php

/** @var yii\web\View $this */
/** @var array $stats */
/** @var array $recentBookings */
/** @var array $pendingBookings */
/** @var array $monthlyData */
/** @var array $roomUsageData */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แดชบอร์ด';

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">แดชบอร์ด</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">แดชบอร์ด</li>
            </ol>
        </nav>
    </div>
    <div>
        <span class="text-muted">
            <i class="bi bi-calendar3 me-1"></i>
            <?= Yii::$app->formatter->asDate(time()) ?>
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">การจองวันนี้</div>
                    <div class="stat-value"><?= $stats['todayBookings'] ?? 0 ?></div>
                    <small class="opacity-75">
                        <i class="bi bi-arrow-up"></i> +<?= $stats['todayBookingsChange'] ?? 0 ?>% จากเมื่อวาน
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">รออนุมัติ</div>
                    <div class="stat-value"><?= $stats['pendingBookings'] ?? 0 ?></div>
                    <small class="opacity-75">
                        <i class="bi bi-clock"></i> ต้องดำเนินการ
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">ห้องประชุมทั้งหมด</div>
                    <div class="stat-value"><?= $stats['totalRooms'] ?? 0 ?></div>
                    <small class="opacity-75">
                        <i class="bi bi-check-circle"></i> <?= $stats['activeRooms'] ?? 0 ?> ห้องพร้อมใช้
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-door-open"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">ผู้ใช้งานทั้งหมด</div>
                    <div class="stat-value"><?= $stats['totalUsers'] ?? 0 ?></div>
                    <small class="opacity-75">
                        <i class="bi bi-person-plus"></i> +<?= $stats['newUsersThisMonth'] ?? 0 ?> เดือนนี้
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2"></i>สถิติการจองรายเดือน</span>
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary active" data-range="6">6 เดือน</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="12">12 เดือน</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="bookingTrendChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>การใช้งานแยกตามห้อง
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="roomUsageChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-3">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>รอการอนุมัติ</span>
                <a href="<?= Url::to(['booking/pending']) ?>" class="btn btn-sm btn-outline-primary">
                    ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>รหัสการจอง</th>
                                <th>ห้อง</th>
                                <th>ผู้จอง</th>
                                <th>วันที่</th>
                                <th>การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingBookings)): ?>
                                <?php foreach ($pendingBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= Url::to(['booking/view', 'id' => $booking['id']]) ?>" class="text-decoration-none fw-semibold">
                                                <?= Html::encode($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td><?= Html::encode($booking['room_name']) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar me-2" style="width:28px;height:28px;font-size:0.75rem;">
                                                    <?= strtoupper(substr($booking['username'], 0, 1)) ?>
                                                </div>
                                                <?= Html::encode($booking['username']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <small>
                                                <?= Yii::$app->formatter->asDate($booking['booking_date']) ?><br>
                                                <span class="text-muted"><?= $booking['start_time'] ?> - <?= $booking['end_time'] ?></span>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-success btn-approve" data-id="<?= $booking['id'] ?>" title="อนุมัติ">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-reject" data-id="<?= $booking['id'] ?>" title="ปฏิเสธ">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                                        ไม่มีรายการรออนุมัติ
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2"></i>การจองล่าสุด</span>
                <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-sm btn-outline-primary">
                    ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>รหัสการจอง</th>
                                <th>ห้อง</th>
                                <th>วันที่/เวลา</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= Url::to(['booking/view', 'id' => $booking['id']]) ?>" class="text-decoration-none fw-semibold">
                                                <?= Html::encode($booking['booking_code']) ?>
                                            </a>
                                        </td>
                                        <td><?= Html::encode($booking['room_name']) ?></td>
                                        <td>
                                            <small>
                                                <?= Yii::$app->formatter->asDate($booking['booking_date']) ?><br>
                                                <span class="text-muted"><?= $booking['start_time'] ?> - <?= $booking['end_time'] ?></span>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = match($booking['status']) {
                                                'pending' => 'pending',
                                                'approved' => 'approved',
                                                'rejected' => 'rejected',
                                                'cancelled' => 'cancelled',
                                                'completed' => 'completed',
                                                default => 'secondary'
                                            };
                                            $statusText = match($booking['status']) {
                                                'pending' => 'รออนุมัติ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'rejected' => 'ปฏิเสธ',
                                                'cancelled' => 'ยกเลิก',
                                                'completed' => 'เสร็จสิ้น',
                                                default => $booking['status']
                                            };
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                        ยังไม่มีการจอง
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="row g-3 mt-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-day me-2"></i>ตารางการจองวันนี้</span>
                <a href="<?= Url::to(['booking/calendar']) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-calendar3"></i> ดูปฏิทิน
                </a>
            </div>
            <div class="card-body">
                <div id="todaySchedule">
                    <!-- Timeline will be rendered here -->
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-hourglass fs-1 d-block mb-2"></i>
                        กำลังโหลดข้อมูล...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Chart data
$monthlyLabels = json_encode($monthlyData['labels'] ?? ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.']);
$monthlyApproved = json_encode($monthlyData['approved'] ?? [12, 19, 15, 25, 22, 30]);
$monthlyCancelled = json_encode($monthlyData['cancelled'] ?? [2, 3, 1, 4, 2, 1]);

$roomLabels = json_encode($roomUsageData['labels'] ?? ['ห้องประชุม 1', 'ห้องประชุม 2', 'ห้องประชุม 3', 'ห้องฝึกอบรม', 'อื่นๆ']);
$roomValues = json_encode($roomUsageData['values'] ?? [30, 25, 20, 15, 10]);
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Booking Trend Chart
    const bookingTrendCtx = document.getElementById('bookingTrendChart').getContext('2d');
    new Chart(bookingTrendCtx, {
        type: 'line',
        data: {
            labels: <?= $monthlyLabels ?>,
            datasets: [{
                label: 'อนุมัติแล้ว',
                data: <?= $monthlyApproved ?>,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'ยกเลิก',
                data: <?= $monthlyCancelled ?>,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
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
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Room Usage Chart
    const roomUsageCtx = document.getElementById('roomUsageChart').getContext('2d');
    new Chart(roomUsageCtx, {
        type: 'doughnut',
        data: {
            labels: <?= $roomLabels ?>,
            datasets: [{
                data: <?= $roomValues ?>,
                backgroundColor: [
                    '#4f46e5',
                    '#22c55e',
                    '#f59e0b',
                    '#ef4444',
                    '#8b5cf6'
                ],
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
    
    // Approve/Reject buttons
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            if (confirm('ต้องการอนุมัติการจองนี้หรือไม่?')) {
                window.location.href = '<?= Url::to(['booking/approve']) ?>?id=' + bookingId;
            }
        });
    });
    
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            const reason = prompt('โปรดระบุเหตุผลในการปฏิเสธ:');
            if (reason !== null) {
                window.location.href = '<?= Url::to(['booking/reject']) ?>?id=' + bookingId + '&reason=' + encodeURIComponent(reason);
            }
        });
    });
});
</script>
