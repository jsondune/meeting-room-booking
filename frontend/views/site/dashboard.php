<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\Booking[] $upcomingBookings */
/** @var common\models\Booking[] $pastBookings */
/** @var int $totalBookings */
/** @var int $completedBookings */
/** @var int $pendingBookings */
/** @var array $monthlyData */
/** @var common\models\MeetingRoom[] $quickBookingRooms */

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Booking;

$this->title = 'แดชบอร์ด';
$this->params['breadcrumbs'][] = $this->title;

// Register Chart.js
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_END]);
?>

<div class="dashboard-page">
    <!-- Welcome Banner -->
    <div class="card border-0 shadow-sm mb-4 welcome-card">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-2">
                        สวัสดี, <?= Html::encode($user->fullname ?? $user->username) ?>! 👋
                    </h4>
                    <p class="text-muted mb-0">
                        <?php
                        $hour = date('H');
                        if ($hour < 12) {
                            echo 'สวัสดีตอนเช้า! พร้อมเริ่มต้นวันใหม่หรือยัง?';
                        } elseif ($hour < 17) {
                            echo 'สวัสดีตอนบ่าย! มีการประชุมอะไรบ้างวันนี้?';
                        } else {
                            echo 'สวัสดีตอนเย็น! มีอะไรให้ช่วยไหม?';
                        }
                        ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= Url::to(['/booking/create']) ?>" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i> จองห้องประชุมใหม่
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-calendar-alt text-primary fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($totalBookings) ?></h3>
                            <small class="text-muted">การจองทั้งหมด</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-clock text-warning fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($pendingBookings) ?></h3>
                            <small class="text-muted">รออนุมัติ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-check-circle text-success fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= number_format($completedBookings) ?></h3>
                            <small class="text-muted">เสร็จสิ้น</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-hourglass-half text-info fa-lg"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= count($upcomingBookings) ?></h3>
                            <small class="text-muted">กำลังจะมาถึง</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Upcoming Bookings -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-calendar-check text-primary me-2"></i> การจองที่กำลังจะมาถึง
                        </h5>
                        <a href="<?= Url::to(['/booking/my-bookings']) ?>" class="btn btn-sm btn-outline-primary">
                            ดูทั้งหมด
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($upcomingBookings)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัส</th>
                                        <th>หัวข้อ</th>
                                        <th>ห้อง</th>
                                        <th>วันที่</th>
                                        <th>เวลา</th>
                                        <th>สถานะ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingBookings as $booking): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    <?= Html::encode($booking->booking_code) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong><?= Html::encode($booking->title) ?></strong>
                                            </td>
                                            <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $bookingDate = new DateTime($booking->booking_date);
                                                $today = new DateTime('today');
                                                $tomorrow = new DateTime('tomorrow');
                                                
                                                if ($bookingDate->format('Y-m-d') === $today->format('Y-m-d')) {
                                                    echo '<span class="badge bg-danger">วันนี้</span>';
                                                } elseif ($bookingDate->format('Y-m-d') === $tomorrow->format('Y-m-d')) {
                                                    echo '<span class="badge bg-warning text-dark">พรุ่งนี้</span>';
                                                } else {
                                                    echo Yii::$app->formatter->asDate($booking->booking_date);
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = [
                                                    Booking::STATUS_PENDING => 'warning',
                                                    Booking::STATUS_APPROVED => 'success',
                                                    Booking::STATUS_REJECTED => 'danger',
                                                    Booking::STATUS_CANCELLED => 'secondary',
                                                    Booking::STATUS_COMPLETED => 'info',
                                                ];
                                                $statusLabel = [
                                                    Booking::STATUS_PENDING => 'รออนุมัติ',
                                                    Booking::STATUS_APPROVED => 'อนุมัติแล้ว',
                                                    Booking::STATUS_REJECTED => 'ไม่อนุมัติ',
                                                    Booking::STATUS_CANCELLED => 'ยกเลิก',
                                                    Booking::STATUS_COMPLETED => 'เสร็จสิ้น',
                                                ];
                                                ?>
                                                <span class="badge bg-<?= $statusClass[$booking->status] ?? 'secondary' ?>">
                                                    <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= Url::to(['/booking/view', 'id' => $booking->id]) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">ไม่มีการจองที่กำลังจะมาถึง</p>
                            <a href="<?= Url::to(['/booking/create']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> สร้างการจองใหม่
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Monthly Chart -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i> สถิติการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="200"></canvas>
                </div>
            </div>

            <!-- Quick Book -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i> จองด่วน
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($quickBookingRooms)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($quickBookingRooms as $room): ?>
                                <a href="<?= Url::to(['/booking/create', 'room_id' => $room->id]) ?>" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= Html::encode($room->name_th) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-users me-1"></i> <?= $room->capacity ?> คน
                                        </small>
                                    </div>
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">ไม่มีห้องว่าง</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Past Bookings -->
    <?php if (!empty($pastBookings)): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                <i class="fas fa-history text-muted me-2"></i> การจองที่ผ่านมา
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>รหัส</th>
                            <th>หัวข้อ</th>
                            <th>ห้อง</th>
                            <th>วันที่</th>
                            <th>สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pastBookings as $booking): ?>
                            <tr class="text-muted">
                                <td><?= Html::encode($booking->booking_code) ?></td>
                                <td><?= Html::encode($booking->title) ?></td>
                                <td><?= Html::encode($booking->room->name_th ?? '-') ?></td>
                                <td><?= Yii::$app->formatter->asDate($booking->booking_date) ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        Booking::STATUS_COMPLETED => 'success',
                                        Booking::STATUS_CANCELLED => 'secondary',
                                    ];
                                    $statusLabel = [
                                        Booking::STATUS_COMPLETED => 'เสร็จสิ้น',
                                        Booking::STATUS_CANCELLED => 'ยกเลิก',
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusClass[$booking->status] ?? 'secondary' ?>">
                                        <?= $statusLabel[$booking->status] ?? $booking->status ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= Url::to(['/booking/view', 'id' => $booking->id]) ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.welcome-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.welcome-card .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.welcome-card .btn-primary {
    background-color: white;
    color: #667eea;
    border: none;
}

.welcome-card .btn-primary:hover {
    background-color: rgba(255, 255, 255, 0.9);
    color: #764ba2;
}

.stat-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<?php
// Chart.js initialization
$chartLabels = array_column($monthlyData, 'month');
$chartData = array_column($monthlyData, 'count');

$chartJs = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: JSON.parse('$1'),
            datasets: [{
                label: 'จำนวนการจอง',
                data: JSON.parse('$2'),
                backgroundColor: 'rgba(102, 126, 234, 0.5)',
                borderColor: 'rgba(102, 126, 234, 1)',
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
});
JS;

$chartJs = str_replace('$1', json_encode($chartLabels), $chartJs);
$chartJs = str_replace('$2', json_encode($chartData), $chartJs);

$this->registerJs($chartJs, \yii\web\View::POS_END);
?>
