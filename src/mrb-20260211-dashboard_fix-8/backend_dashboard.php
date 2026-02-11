<?php
/**
 * Dashboard - AdminLTE Style
 * backend/views/site/dashboard.php
 * 
 * @var yii\web\View $this
 * @var array $stats
 * @var array $todaySchedule
 * @var array $pendingApproval
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'แดชบอร์ด';

// Thai date helper functions
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
    
    if ($format === 'short') {
        return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
    } elseif ($format === 'medium') {
        return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
    } else {
        return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
    }
};

$formatThaiDateTime = function($datetime) use ($thaiMonthsShort) {
    if (empty($datetime)) return '-';
    $dt = new DateTime($datetime);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    $time = $dt->format('H:i');
    return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year . ' ' . $time . ' น.';
};

// Current Thai date
$today = new DateTime();
$thaiDayName = $thaiDays[(int)$today->format('w')];
$todayThai = 'วัน' . $thaiDayName . 'ที่ ' . $today->format('j') . ' ' . $thaiMonths[(int)$today->format('n')] . ' ' . ($today->format('Y') + 543);
?>

<!-- AdminLTE CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.content-wrapper { margin-left: 0 !important; background: #f4f6f9; }
.small-box { border-radius: 0.5rem; }
.small-box .icon { font-size: 70px; top: 5px; }
.small-box:hover { transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
.card { border-radius: 0.5rem; box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2); }
.card-header { border-bottom: 1px solid rgba(0,0,0,.125); }
.info-box { border-radius: 0.5rem; min-height: 80px; }
.info-box-icon { border-radius: 0.5rem 0 0 0.5rem; }
.timeline-item { margin-left: 10px; }
.badge-pending { background: #ffc107; color: #000; }
.badge-approved { background: #28a745; }
.badge-completed { background: #17a2b8; }
.badge-cancelled { background: #6c757d; }
.badge-rejected { background: #dc3545; }
.table th { font-weight: 600; background: #f8f9fa; }
.nav-tabs .nav-link.active { font-weight: 600; }
</style>

<div class="content-wrapper p-3">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-tachometer-alt mr-2"></i>แดชบอร์ด
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">
                            <i class="far fa-calendar-alt mr-1"></i><?= $todayThai ?>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- Total Rooms -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($stats['totalRooms'] ?? 0) ?></h3>
                            <p>ห้องประชุมทั้งหมด</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <a href="<?= Url::to(['room/index']) ?>" class="small-box-footer">
                            ดูทั้งหมด <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Today's Bookings -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($stats['todayBookings'] ?? 0) ?></h3>
                            <p>การจองวันนี้</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <a href="<?= Url::to(['booking/index', 'date' => date('Y-m-d')]) ?>" class="small-box-footer">
                            ดูรายละเอียด <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Pending Approval -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($stats['pendingBookings'] ?? 0) ?></h3>
                            <p>รออนุมัติ</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="<?= Url::to(['approval/pending']) ?>" class="small-box-footer">
                            ดูรายการ <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Total Users -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stats['totalUsers'] ?? 0) ?></h3>
                            <p>ผู้ใช้งานทั้งหมด</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= Url::to(['user/index']) ?>" class="small-box-footer">
                            จัดการผู้ใช้ <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->

            <div class="row">
                <!-- Left col -->
                <section class="col-lg-8 connectedSortable">
                    
                    <!-- Monthly Bookings Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                สถิติการจองรายเดือน
                            </h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="monthlyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Today's Schedule -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-day mr-1"></i>
                                ตารางการจองวันนี้
                            </h3>
                            <div class="card-tools">
                                <a href="<?= Url::to(['booking/calendar']) ?>" class="btn btn-tool btn-sm">
                                    <i class="fas fa-calendar"></i> ดูปฏิทิน
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (!empty($todaySchedule)): ?>
                            <table class="table table-striped table-valign-middle">
                                <thead>
                                    <tr>
                                        <th>เวลา</th>
                                        <th>หัวข้อ</th>
                                        <th>ห้อง</th>
                                        <th>ผู้จอง</th>
                                        <th>สถานะ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todaySchedule as $booking): ?>
                                    <tr>
                                        <td>
                                            <strong><?= substr($booking->start_time, 0, 5) ?></strong>
                                            <span class="text-muted">- <?= substr($booking->end_time, 0, 5) ?></span>
                                        </td>
                                        <td>
                                            <?= Html::encode(mb_substr($booking->title ?: $booking->meeting_title, 0, 30)) ?>
                                            <?= mb_strlen($booking->title ?: $booking->meeting_title) > 30 ? '...' : '' ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info"><?= Html::encode($booking->room->name_th ?? '-') ?></span>
                                        </td>
                                        <td>
                                            <?= Html::encode($booking->user->full_name ?? $booking->user->username ?? '-') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'pending' => 'badge-pending',
                                                'approved' => 'badge-approved',
                                                'completed' => 'badge-completed',
                                                'cancelled' => 'badge-cancelled',
                                                'rejected' => 'badge-rejected',
                                            ];
                                            $statusLabel = [
                                                'pending' => 'รออนุมัติ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'completed' => 'เสร็จสิ้น',
                                                'cancelled' => 'ยกเลิก',
                                                'rejected' => 'ไม่อนุมัติ',
                                            ];
                                            ?>
                                            <span class="badge <?= $statusBadge[$booking->status] ?? 'badge-secondary' ?>">
                                                <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" class="btn btn-xs btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <p class="mb-0">ไม่มีการจองในวันนี้</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Recent Bookings -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                การจองล่าสุด
                            </h3>
                            <div class="card-tools">
                                <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-list"></i> ดูทั้งหมด
                                </a>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <?php if (!empty($stats['recentBookings'])): ?>
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ห้อง</th>
                                        <th>วันที่</th>
                                        <th>เวลา</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($stats['recentBookings'], 0, 8) as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>">
                                                <strong><?= Html::encode($booking->booking_code) ?></strong>
                                            </a>
                                        </td>
                                        <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                        <td><?= $formatThaiDate($booking->booking_date, 'short') ?></td>
                                        <td><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?></td>
                                        <td>
                                            <?php
                                            $statusBadge = [
                                                'pending' => 'badge-warning',
                                                'approved' => 'badge-success',
                                                'completed' => 'badge-info',
                                                'cancelled' => 'badge-secondary',
                                                'rejected' => 'badge-danger',
                                            ];
                                            $statusLabel = [
                                                'pending' => 'รออนุมัติ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'completed' => 'เสร็จสิ้น',
                                                'cancelled' => 'ยกเลิก',
                                                'rejected' => 'ไม่อนุมัติ',
                                            ];
                                            ?>
                                            <span class="badge <?= $statusBadge[$booking->status] ?? 'badge-secondary' ?>">
                                                <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">ยังไม่มีการจอง</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card -->
                    
                </section>
                <!-- /.Left col -->

                <!-- Right col -->
                <section class="col-lg-4 connectedSortable">
                    
                    <!-- Info Boxes -->
                    <div class="info-box mb-3 bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-building"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">อุปกรณ์พร้อมใช้งาน</span>
                            <span class="info-box-number"><?= number_format($stats['totalEquipment'] ?? 0) ?> รายการ</span>
                        </div>
                    </div>

                    <!-- Pending Approval Card -->
                    <div class="card bg-gradient-warning">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                รออนุมัติ
                            </h3>
                            <div class="card-tools">
                                <?php if (($stats['pendingBookings'] ?? 0) > 0): ?>
                                <span class="badge badge-light"><?= $stats['pendingBookings'] ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($pendingApproval)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($pendingApproval as $booking): ?>
                                <li class="list-group-item bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= Html::encode(mb_substr($booking->title ?: $booking->meeting_title, 0, 25)) ?></strong>
                                            <br>
                                            <small>
                                                <i class="far fa-calendar mr-1"></i><?= $formatThaiDate($booking->booking_date, 'short') ?>
                                                <i class="far fa-clock ml-2 mr-1"></i><?= substr($booking->start_time, 0, 5) ?>
                                            </small>
                                        </div>
                                        <a href="<?= Url::to(['approval/view', 'id' => $booking->id]) ?>" class="btn btn-sm btn-light">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 text-white-50"></i>
                                <p class="mb-0 text-white-50">ไม่มีรายการรออนุมัติ</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php if (($stats['pendingBookings'] ?? 0) > 5): ?>
                        <div class="card-footer bg-transparent text-center">
                            <a href="<?= Url::to(['approval/pending']) ?>" class="text-dark">
                                ดูทั้งหมด (<?= $stats['pendingBookings'] ?> รายการ) <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- /.card -->

                    <!-- Upcoming Bookings -->
                    <div class="card">
                        <div class="card-header bg-gradient-success">
                            <h3 class="card-title">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                กำลังจะมาถึง
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($stats['upcomingBookings'])): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($stats['upcomingBookings'] as $booking): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong><?= Html::encode($booking->room->name_th ?? '-') ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <?= $formatThaiDate($booking->booking_date, 'short') ?>
                                                | <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                            </small>
                                        </div>
                                        <span class="badge badge-success align-self-center">อนุมัติ</span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <p class="mb-0">ไม่มีการจองที่กำลังจะมาถึง</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Room Usage Chart -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                การใช้งานห้องประชุม
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="roomUsageChart" style="min-height: 200px; height: 200px; max-height: 200px;"></canvas>
                        </div>
                    </div>
                    <!-- /.card -->

                    <!-- Quick Links -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-link mr-1"></i>
                                ลิงก์ด่วน
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="nav nav-pills flex-column">
                                <li class="nav-item">
                                    <a href="<?= Url::to(['booking/create']) ?>" class="nav-link">
                                        <i class="fas fa-plus mr-2 text-success"></i> สร้างการจองใหม่
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= Url::to(['room/index']) ?>" class="nav-link">
                                        <i class="fas fa-door-open mr-2 text-info"></i> จัดการห้องประชุม
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= Url::to(['user/index']) ?>" class="nav-link">
                                        <i class="fas fa-users mr-2 text-warning"></i> จัดการผู้ใช้
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= Url::to(['report/index']) ?>" class="nav-link">
                                        <i class="fas fa-chart-line mr-2 text-primary"></i> รายงาน
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= Url::to(['setting/index']) ?>" class="nav-link">
                                        <i class="fas fa-cog mr-2 text-secondary"></i> ตั้งค่าระบบ
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.card -->
                    
                </section>
                <!-- /.Right col -->
            </div>
            <!-- /.row -->
        </div>
    </section>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- AdminLTE JS (optional, for card collapse) -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<?php
$chartLabels = json_encode($stats['monthlyBookings']['labels'] ?? []);
$chartData = json_encode($stats['monthlyBookings']['data'] ?? []);
$roomLabels = json_encode($stats['roomUsage']['labels'] ?? []);
$roomData = json_encode($stats['roomUsage']['data'] ?? []);

$this->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Bookings Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {$chartLabels},
                datasets: [{
                    label: 'จำนวนการจอง',
                    data: {$chartData},
                    backgroundColor: 'rgba(60, 141, 188, 0.8)',
                    borderColor: 'rgba(60, 141, 188, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
    }
    
    // Room Usage Chart
    const roomCtx = document.getElementById('roomUsageChart');
    if (roomCtx) {
        const colors = [
            '#3c8dbc', '#00a65a', '#f39c12', '#dd4b39', '#00c0ef',
            '#605ca8', '#001f3f', '#39cccc', '#3d9970', '#01ff70'
        ];
        new Chart(roomCtx, {
            type: 'doughnut',
            data: {
                labels: {$roomLabels},
                datasets: [{
                    data: {$roomData},
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, padding: 8 }
                    }
                }
            }
        });
    }
});
JS, \yii\web\View::POS_END);
?>
