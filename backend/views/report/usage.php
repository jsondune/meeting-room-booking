<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $dateRange */
/** @var array $roomStats */
/** @var array $bookingsByStatus */
/** @var array $bookingsByDepartment */
/** @var array $bookingsByHour */
/** @var array $bookingsByDay */
/** @var array $topUsers */
/** @var array $topRooms */

$this->title = 'รายงานการใช้งาน';
$this->params['breadcrumbs'][] = ['label' => 'รายงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Default date range (last 30 days)
$startDate = $dateRange['start'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $dateRange['end'] ?? date('Y-m-d');

// Sample data for demonstration
$totalBookings = 245;
$totalHours = 612;
$avgDuration = 2.5;
$utilizationRate = 68.5;
?>

<div class="report-usage">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-bar-chart-line text-primary me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="exportReport('pdf')">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
                <i class="bi bi-file-excel me-1"></i>Excel
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>พิมพ์
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ห้องประชุม</label>
                    <select name="room_id" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="1">ห้องประชุมใหญ่ A</option>
                        <option value="2">ห้องประชุมย่อย B</option>
                        <option value="3">ห้องประชุม VIP</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">หน่วยงาน</label>
                    <select name="department_id" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="1">ฝ่ายบริหาร</option>
                        <option value="2">ฝ่ายวิชาการ</option>
                        <option value="3">ฝ่ายบริการ</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">วันนี้</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">สัปดาห์นี้</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">เดือนนี้</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('quarter')">ไตรมาสนี้</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">ปีนี้</button>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-1"></i>ดูรายงาน
                    </button>
                    <a href="<?= Url::to(['usage']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">การจองทั้งหมด</h6>
                            <h2 class="card-title mb-0"><?= number_format($totalBookings) ?></h2>
                            <small class="opacity-75">
                                <i class="bi bi-arrow-up"></i> +12% จากช่วงก่อน
                            </small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">ชั่วโมงใช้งาน</h6>
                            <h2 class="card-title mb-0"><?= number_format($totalHours) ?></h2>
                            <small class="opacity-75">
                                <i class="bi bi-arrow-up"></i> +8% จากช่วงก่อน
                            </small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-clock fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">ระยะเวลาเฉลี่ย</h6>
                            <h2 class="card-title mb-0"><?= number_format($avgDuration, 1) ?> ชม.</h2>
                            <small class="opacity-75">
                                <i class="bi bi-dash"></i> เท่าเดิม
                            </small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 opacity-75">อัตราการใช้งาน</h6>
                            <h2 class="card-title mb-0"><?= number_format($utilizationRate, 1) ?>%</h2>
                            <small class="opacity-75">
                                <i class="bi bi-arrow-up"></i> +5% จากช่วงก่อน
                            </small>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="bi bi-graph-up fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Bookings Over Time Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>จำนวนการจองตามช่วงเวลา
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-view="daily">รายวัน</button>
                        <button type="button" class="btn btn-outline-secondary" data-view="weekly">รายสัปดาห์</button>
                        <button type="button" class="btn btn-outline-secondary" data-view="monthly">รายเดือน</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="bookingsTimeChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Bookings by Status -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart text-primary me-2"></i>สถานะการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="260"></canvas>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="badge bg-success me-2">&nbsp;</span>อนุมัติแล้ว</span>
                            <span class="fw-bold">156 (63.7%)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="badge bg-warning me-2">&nbsp;</span>รอดำเนินการ</span>
                            <span class="fw-bold">42 (17.1%)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="badge bg-danger me-2">&nbsp;</span>ปฏิเสธ</span>
                            <span class="fw-bold">18 (7.3%)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><span class="badge bg-secondary me-2">&nbsp;</span>ยกเลิก</span>
                            <span class="fw-bold">20 (8.2%)</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><span class="badge bg-info me-2">&nbsp;</span>เสร็จสิ้น</span>
                            <span class="fw-bold">9 (3.7%)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hourly Usage Heatmap -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-grid-3x3 text-primary me-2"></i>การใช้งานตามช่วงเวลา
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" height="250"></canvas>
                    <div class="text-center mt-2 text-muted small">
                        <i class="bi bi-info-circle me-1"></i>แสดงช่วงเวลาที่มีการจองมากที่สุด
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings by Day of Week -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-week text-primary me-2"></i>การจองตามวันในสัปดาห์
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="dayOfWeekChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Rooms -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-door-open text-primary me-2"></i>ห้องที่ใช้งานมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ห้องประชุม</th>
                                    <th class="text-center">จำนวนครั้ง</th>
                                    <th class="text-center">ชั่วโมง</th>
                                    <th class="text-center">อัตราใช้งาน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">1</span></td>
                                    <td>
                                        <a href="#" class="text-decoration-none">ห้องประชุมใหญ่ A</a>
                                        <br><small class="text-muted">อาคาร 1 ชั้น 3</small>
                                    </td>
                                    <td class="text-center">68</td>
                                    <td class="text-center">180</td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 85%;">85%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">2</span></td>
                                    <td>
                                        <a href="#" class="text-decoration-none">ห้องประชุม VIP</a>
                                        <br><small class="text-muted">อาคาร 1 ชั้น 5</small>
                                    </td>
                                    <td class="text-center">52</td>
                                    <td class="text-center">145</td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 72%;">72%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">3</span></td>
                                    <td>
                                        <a href="#" class="text-decoration-none">ห้องประชุมย่อย B</a>
                                        <br><small class="text-muted">อาคาร 2 ชั้น 1</small>
                                    </td>
                                    <td class="text-center">45</td>
                                    <td class="text-center">98</td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: 65%;">65%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">4</span></td>
                                    <td>
                                        <a href="#" class="text-decoration-none">ห้องประชุมย่อย C</a>
                                        <br><small class="text-muted">อาคาร 2 ชั้น 2</small>
                                    </td>
                                    <td class="text-center">42</td>
                                    <td class="text-center">89</td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: 58%;">58%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">5</span></td>
                                    <td>
                                        <a href="#" class="text-decoration-none">ห้องฝึกอบรม</a>
                                        <br><small class="text-muted">อาคาร 3 ชั้น 1</small>
                                    </td>
                                    <td class="text-center">38</td>
                                    <td class="text-center">100</td>
                                    <td class="text-center">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" style="width: 45%;">45%</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Users -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-people text-primary me-2"></i>ผู้ใช้งานมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>ผู้ใช้งาน</th>
                                    <th>หน่วยงาน</th>
                                    <th class="text-center">จำนวนครั้ง</th>
                                    <th class="text-center">ชั่วโมง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">1</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">สม</div>
                                            <div>
                                                <a href="#" class="text-decoration-none">สมชาย ใจดี</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">ฝ่ายบริหาร</span></td>
                                    <td class="text-center">28</td>
                                    <td class="text-center">72</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">2</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">วร</div>
                                            <div>
                                                <a href="#" class="text-decoration-none">วรรณา สุขใจ</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">ฝ่ายวิชาการ</span></td>
                                    <td class="text-center">24</td>
                                    <td class="text-center">65</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">3</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">ปร</div>
                                            <div>
                                                <a href="#" class="text-decoration-none">ประยุทธ์ มั่นคง</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">ฝ่ายบริการ</span></td>
                                    <td class="text-center">21</td>
                                    <td class="text-center">48</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">4</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">มา</div>
                                            <div>
                                                <a href="#" class="text-decoration-none">มานี ดีใจ</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">ฝ่ายการเงิน</span></td>
                                    <td class="text-center">18</td>
                                    <td class="text-center">42</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-secondary">5</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:12px;">ชา</div>
                                            <div>
                                                <a href="#" class="text-decoration-none">ชาลี รักเรียน</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark">ฝ่ายวิจัย</span></td>
                                    <td class="text-center">15</td>
                                    <td class="text-center">38</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Comparison -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building text-primary me-2"></i>การใช้งานตามหน่วยงาน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Date range quick select
function setDateRange(range) {
    const today = new Date();
    let start, end;
    
    switch(range) {
        case 'today':
            start = end = today.toISOString().split('T')[0];
            break;
        case 'week':
            start = new Date(today.setDate(today.getDate() - today.getDay())).toISOString().split('T')[0];
            end = new Date().toISOString().split('T')[0];
            break;
        case 'month':
            start = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            end = new Date().toISOString().split('T')[0];
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            start = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
            end = new Date().toISOString().split('T')[0];
            break;
        case 'year':
            start = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
            end = new Date().toISOString().split('T')[0];
            break;
    }
    
    document.querySelector('input[name="start_date"]').value = start;
    document.querySelector('input[name="end_date"]').value = end;
}
window.setDateRange = setDateRange;

function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    window.location.href = window.location.pathname + '?' + params.toString();
}
window.exportReport = exportReport;

// Charts
const chartColors = {
    primary: '#0d6efd',
    success: '#198754',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#0dcaf0',
    secondary: '#6c757d'
};

// Bookings Over Time Chart
const bookingsTimeCtx = document.getElementById('bookingsTimeChart').getContext('2d');
new Chart(bookingsTimeCtx, {
    type: 'line',
    data: {
        labels: ['1 ธ.ค.', '5 ธ.ค.', '10 ธ.ค.', '15 ธ.ค.', '20 ธ.ค.', '25 ธ.ค.', '30 ธ.ค.'],
        datasets: [{
            label: 'จำนวนการจอง',
            data: [12, 19, 15, 25, 22, 30, 28],
            borderColor: chartColors.primary,
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'ชั่วโมงใช้งาน',
            data: [30, 45, 38, 62, 55, 75, 70],
            borderColor: chartColors.success,
            backgroundColor: 'transparent',
            borderDash: [5, 5],
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Status Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['อนุมัติแล้ว', 'รอดำเนินการ', 'ปฏิเสธ', 'ยกเลิก', 'เสร็จสิ้น'],
        datasets: [{
            data: [156, 42, 18, 20, 9],
            backgroundColor: [
                chartColors.success,
                chartColors.warning,
                chartColors.danger,
                chartColors.secondary,
                chartColors.info
            ]
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
        cutout: '60%'
    }
});

// Hourly Chart
const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
new Chart(hourlyCtx, {
    type: 'bar',
    data: {
        labels: ['08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00'],
        datasets: [{
            label: 'จำนวนการจอง',
            data: [8, 25, 35, 30, 10, 28, 40, 38, 25, 12],
            backgroundColor: function(context) {
                const value = context.dataset.data[context.dataIndex];
                if (value >= 35) return chartColors.danger;
                if (value >= 25) return chartColors.warning;
                return chartColors.primary;
            }
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
                title: {
                    display: true,
                    text: 'จำนวนครั้ง'
                }
            }
        }
    }
});

// Day of Week Chart
const dayOfWeekCtx = document.getElementById('dayOfWeekChart').getContext('2d');
new Chart(dayOfWeekCtx, {
    type: 'bar',
    data: {
        labels: ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'],
        datasets: [{
            label: 'จำนวนการจอง',
            data: [5, 45, 52, 48, 55, 35, 5],
            backgroundColor: [
                chartColors.secondary,
                chartColors.primary,
                chartColors.primary,
                chartColors.primary,
                chartColors.primary,
                chartColors.primary,
                chartColors.secondary
            ]
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
                beginAtZero: true
            }
        }
    }
});

// Department Chart
const departmentCtx = document.getElementById('departmentChart').getContext('2d');
new Chart(departmentCtx, {
    type: 'bar',
    data: {
        labels: ['ฝ่ายบริหาร', 'ฝ่ายวิชาการ', 'ฝ่ายบริการ', 'ฝ่ายการเงิน', 'ฝ่ายวิจัย', 'ฝ่ายทรัพยากรบุคคล', 'ฝ่ายไอที'],
        datasets: [{
            label: 'จำนวนการจอง',
            data: [68, 52, 45, 38, 32, 28, 22],
            backgroundColor: chartColors.primary
        }, {
            label: 'ชั่วโมงใช้งาน',
            data: [180, 145, 98, 89, 75, 62, 48],
            backgroundColor: chartColors.success
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
JS;
$this->registerJs($js);
?>

<style>
@media print {
    .btn-group, .card-header .btn, form {
        display: none !important;
    }
    .card {
        break-inside: avoid;
    }
}
</style>
