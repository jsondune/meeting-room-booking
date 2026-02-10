<?php
/**
 * Backend Dashboard View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var array $stats
 * @var array $todaySchedule
 * @var array $pendingApproval
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แดชบอร์ด';

// Thai date helpers
$thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
$thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];

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

// Today in Thai
$today = new DateTime();
$todayThai = $thaiDays[$today->format('w')] . 'ที่ ' . $today->format('j') . ' ' . 
             $thaiMonths[(int)$today->format('n')] . ' ' . ($today->format('Y') + 543);
?>

<div class="backend-dashboard">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-speedometer2 me-2"></i>แดชบอร์ด
            </h1>
            <p class="text-muted mb-0">
                <i class="bi bi-calendar3 me-1"></i>วัน<?= $todayThai ?>
            </p>
        </div>
        <div>
            <a href="<?= Url::to(['booking/create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>สร้างการจอง
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">ห้องประชุมทั้งหมด</div>
                            <div class="h2 mb-0 fw-bold"><?= number_format($stats['totalRooms']) ?></div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-door-open text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">การจองวันนี้</div>
                            <div class="h2 mb-0 fw-bold"><?= number_format($stats['todayBookings']) ?></div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-calendar-check text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">รออนุมัติ</div>
                            <div class="h2 mb-0 fw-bold text-warning"><?= number_format($stats['pendingBookings']) ?></div>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                    </div>
                    <?php if ($stats['pendingBookings'] > 0): ?>
                    <a href="<?= Url::to(['approval/pending']) ?>" class="btn btn-sm btn-warning mt-2">
                        <i class="bi bi-eye me-1"></i>ดูรายการ
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small mb-1">ผู้ใช้งานทั้งหมด</div>
                            <div class="h2 mb-0 fw-bold"><?= number_format($stats['totalUsers']) ?></div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-people text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Today's Schedule -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-day me-2"></i>ตารางวันนี้
                    </h5>
                    <a href="<?= Url::to(['booking/calendar']) ?>" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-calendar3 me-1"></i>ดูปฏิทิน
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($todaySchedule)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>เวลา</th>
                                    <th>ห้อง</th>
                                    <th>หัวข้อ</th>
                                    <th>ผู้จอง</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todaySchedule as $booking): ?>
                                <tr>
                                    <td>
                                        <span class="fw-semibold"><?= substr($booking->start_time, 0, 5) ?></span>
                                        <span class="text-muted">- <?= substr($booking->end_time, 0, 5) ?></span>
                                    </td>
                                    <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                    <td>
                                        <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" class="text-decoration-none">
                                            <?= Html::encode($booking->meeting_title ?: $booking->title) ?>
                                        </a>
                                    </td>
                                    <td><?= Html::encode($booking->user->full_name ?? $booking->user->username ?? '-') ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'completed' => 'info',
                                        ];
                                        $statusLabel = [
                                            'pending' => 'รออนุมัติ',
                                            'approved' => 'อนุมัติแล้ว',
                                            'completed' => 'เสร็จสิ้น',
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusClass[$booking->status] ?? 'secondary' ?>">
                                            <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-calendar-x d-block mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">ไม่มีการจองในวันนี้</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Monthly Chart -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>สถิติการจอง 6 เดือนล่าสุด
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Pending Approval -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-hourglass-split text-warning me-2"></i>รออนุมัติ
                    </h5>
                    <?php if ($stats['pendingBookings'] > 0): ?>
                    <span class="badge bg-warning"><?= $stats['pendingBookings'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($pendingApproval)): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($pendingApproval as $booking): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold"><?= Html::encode($booking->meeting_title ?: $booking->title) ?></div>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i><?= $formatThaiDate($booking->booking_date, 'medium') ?>
                                        <i class="bi bi-clock ms-2 me-1"></i><?= substr($booking->start_time, 0, 5) ?>
                                    </small>
                                </div>
                                <a href="<?= Url::to(['approval/view', 'id' => $booking->id]) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-check-circle d-block mb-2" style="font-size: 1.5rem;"></i>
                        <small>ไม่มีรายการรออนุมัติ</small>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($stats['pendingBookings'] > 5): ?>
                <div class="card-footer bg-light text-center">
                    <a href="<?= Url::to(['approval/pending']) ?>" class="text-decoration-none">
                        ดูทั้งหมด (<?= $stats['pendingBookings'] ?> รายการ) <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Upcoming Bookings -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event text-primary me-2"></i>การจองที่กำลังจะมาถึง
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($stats['upcomingBookings'])): ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($stats['upcomingBookings'] as $booking): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold"><?= Html::encode($booking->room->name_th ?? '-') ?></div>
                                    <small class="text-muted">
                                        <?= $formatThaiDate($booking->booking_date, 'medium') ?>
                                        | <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                    </small>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x d-block mb-2" style="font-size: 1.5rem;"></i>
                        <small>ไม่มีการจองที่กำลังจะมาถึง</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>การจองล่าสุด
                    </h5>
                    <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-sm btn-outline-primary">
                        ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($stats['recentBookings'])): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัสการจอง</th>
                                    <th>ห้อง</th>
                                    <th>วันที่/เวลา</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['recentBookings'] as $booking): ?>
                                <tr>
                                    <td>
                                        <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" class="text-decoration-none fw-semibold">
                                            <?= Html::encode($booking->booking_code) ?>
                                        </a>
                                    </td>
                                    <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                    <td>
                                        <div><?= $formatThaiDate($booking->booking_date, 'medium') ?></div>
                                        <small class="text-muted"><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'cancelled' => 'secondary',
                                            'completed' => 'info',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'รออนุมัติ',
                                            'approved' => 'อนุมัติแล้ว',
                                            'rejected' => 'ไม่อนุมัติ',
                                            'cancelled' => 'ยกเลิก',
                                            'completed' => 'เสร็จสิ้น',
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusClasses[$booking->status] ?? 'secondary' ?>">
                                            <?= $statusLabels[$booking->status] ?? $booking->status ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                        <p class="mb-0">ยังไม่มีการจอง</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Chart.js
$chartLabels = json_encode($stats['monthlyBookings']['labels'] ?? []);
$chartData = json_encode($stats['monthlyBookings']['data'] ?? []);

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);
$this->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {$chartLabels},
                datasets: [{
                    label: 'จำนวนการจอง',
                    data: {$chartData},
                    backgroundColor: 'rgba(79, 70, 229, 0.5)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                    borderRadius: 4
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
JS, \yii\web\View::POS_END);
?>
