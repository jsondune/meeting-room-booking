<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $dateRange */
/** @var array $revenueData */

$this->title = 'รายงานรายได้';
$this->params['breadcrumbs'][] = ['label' => 'รายงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Default date range
$startDate = $dateRange['start'] ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $dateRange['end'] ?? date('Y-m-d');

// Thai date formatter helper
$formatThaiDate = function($date) {
    if (empty($date)) return '';
    $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = (int)date('n', $timestamp);
    $year = date('Y', $timestamp) + 543;
    return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
};

// Sample data
$totalRevenue = 125000;
$roomRevenue = 95000;
$equipmentRevenue = 18500;
$serviceRevenue = 11500;
$pendingPayments = 15000;
$cancelledAmount = 8500;
?>

<div class="report-revenue">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-cash-stack text-success me-2"></i><?= Html::encode($this->title) ?>
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
                    <div class="input-group">
                        <input type="text" class="form-control thai-date-input" id="startDateDisplay" 
                               value="<?= $formatThaiDate($startDate) ?>" readonly style="background-color: #fff; cursor: pointer;">
                        <input type="hidden" name="start_date" id="startDate" value="<?= $startDate ?>">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <div class="input-group">
                        <input type="text" class="form-control thai-date-input" id="endDateDisplay" 
                               value="<?= $formatThaiDate($endDate) ?>" readonly style="background-color: #fff; cursor: pointer;">
                        <input type="hidden" name="end_date" id="endDate" value="<?= $endDate ?>">
                        <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">ประเภทรายได้</label>
                    <select name="revenue_type" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="room">ค่าห้องประชุม</option>
                        <option value="equipment">ค่าอุปกรณ์</option>
                        <option value="service">ค่าบริการเสริม</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">สถานะการชำระ</label>
                    <select name="payment_status" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="paid">ชำระแล้ว</option>
                        <option value="pending">รอชำระ</option>
                        <option value="overdue">เกินกำหนด</option>
                        <option value="refunded">คืนเงิน</option>
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
                    <a href="<?= Url::to(['revenue']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ต
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
            <div class="card bg-success text-white shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-wallet2 fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">รายได้รวม</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($totalRevenue) ?></h4>
                    <small class="opacity-75"><i class="bi bi-arrow-up"></i> +15%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card bg-primary text-white shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-door-open fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">ค่าห้องประชุม</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($roomRevenue) ?></h4>
                    <small class="opacity-75"><?= number_format(($roomRevenue/$totalRevenue)*100, 1) ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card bg-info text-white shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-pc-display fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">ค่าอุปกรณ์</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($equipmentRevenue) ?></h4>
                    <small class="opacity-75"><?= number_format(($equipmentRevenue/$totalRevenue)*100, 1) ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card bg-secondary text-white shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cup-hot fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">ค่าบริการเสริม</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($serviceRevenue) ?></h4>
                    <small class="opacity-75"><?= number_format(($serviceRevenue/$totalRevenue)*100, 1) ?>%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card bg-warning text-dark shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">รอชำระ</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($pendingPayments) ?></h4>
                    <small>3 รายการ</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-2">
            <div class="card bg-danger text-white shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-x-circle fs-2 mb-2"></i>
                    <h6 class="card-subtitle mb-2 opacity-75">ยกเลิก/คืนเงิน</h6>
                    <h4 class="card-title mb-0">฿<?= number_format($cancelledAmount) ?></h4>
                    <small class="opacity-75">2 รายการ</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Revenue Trend -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up-arrow text-success me-2"></i>แนวโน้มรายได้
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-view="daily">รายวัน</button>
                        <button type="button" class="btn btn-outline-secondary" data-view="weekly">รายสัปดาห์</button>
                        <button type="button" class="btn btn-outline-secondary" data-view="monthly">รายเดือน</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="revenueTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue by Type -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart text-success me-2"></i>สัดส่วนรายได้
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueTypeChart" height="260"></canvas>
                    <hr>
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-primary me-2"></i>ค่าห้องประชุม</span>
                            <span class="fw-bold">฿<?= number_format($roomRevenue) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-circle-fill text-info me-2"></i>ค่าอุปกรณ์</span>
                            <span class="fw-bold">฿<?= number_format($equipmentRevenue) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-circle-fill text-secondary me-2"></i>ค่าบริการเสริม</span>
                            <span class="fw-bold">฿<?= number_format($serviceRevenue) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Room -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-door-open text-success me-2"></i>รายได้ตามห้องประชุม
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ห้องประชุม</th>
                                    <th class="text-center">จำนวนครั้ง</th>
                                    <th class="text-end">รายได้</th>
                                    <th class="text-center">สัดส่วน</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>ห้องประชุมใหญ่ A</strong>
                                        <br><small class="text-muted">฿1,500/ชม.</small>
                                    </td>
                                    <td class="text-center">45</td>
                                    <td class="text-end">฿38,500</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 40.5%;">40.5%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>ห้องประชุม VIP</strong>
                                        <br><small class="text-muted">฿2,000/ชม.</small>
                                    </td>
                                    <td class="text-center">28</td>
                                    <td class="text-end">฿32,000</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 33.7%;">33.7%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>ห้องประชุมย่อย B</strong>
                                        <br><small class="text-muted">฿500/ชม.</small>
                                    </td>
                                    <td class="text-center">35</td>
                                    <td class="text-end">฿15,000</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: 15.8%;">15.8%</div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>ห้องประชุมย่อย C</strong>
                                        <br><small class="text-muted">฿500/ชม.</small>
                                    </td>
                                    <td class="text-center">19</td>
                                    <td class="text-end">฿9,500</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" style="width: 10%;">10%</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>รวม</th>
                                    <th class="text-center">127</th>
                                    <th class="text-end">฿95,000</th>
                                    <th class="text-center">100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue by Department -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-building text-success me-2"></i>รายได้ตามหน่วยงาน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentRevenueChart" height="280"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-credit-card text-success me-2"></i>สถานะการชำระเงิน
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart" height="200"></canvas>
                    <hr>
                    <div class="row text-center small">
                        <div class="col-6 mb-2">
                            <div class="text-success fw-bold fs-5">฿108,500</div>
                            <div class="text-muted">ชำระแล้ว</div>
                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-warning fw-bold fs-5">฿15,000</div>
                            <div class="text-muted">รอชำระ</div>
                        </div>
                        <div class="col-6">
                            <div class="text-danger fw-bold fs-5">฿1,500</div>
                            <div class="text-muted">เกินกำหนด</div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary fw-bold fs-5">฿0</div>
                            <div class="text-muted">คืนเงิน</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt text-success me-2"></i>ธุรกรรมล่าสุด
                    </h5>
                    <a href="#" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>รหัสการจอง</th>
                                    <th>ผู้จอง</th>
                                    <th>รายการ</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                    <th class="text-center">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>25 ธ.ค. 67</td>
                                    <td><a href="#" class="text-decoration-none">#BK-2024-0245</a></td>
                                    <td>สมชาย ใจดี</td>
                                    <td>ห้องประชุมใหญ่ A (3 ชม.)</td>
                                    <td class="text-end">฿4,500</td>
                                    <td class="text-center"><span class="badge bg-success">ชำระแล้ว</span></td>
                                </tr>
                                <tr>
                                    <td>25 ธ.ค. 67</td>
                                    <td><a href="#" class="text-decoration-none">#BK-2024-0244</a></td>
                                    <td>วรรณา สุขใจ</td>
                                    <td>ห้องประชุม VIP (2 ชม.) + โปรเจคเตอร์</td>
                                    <td class="text-end">฿4,500</td>
                                    <td class="text-center"><span class="badge bg-warning text-dark">รอชำระ</span></td>
                                </tr>
                                <tr>
                                    <td>24 ธ.ค. 67</td>
                                    <td><a href="#" class="text-decoration-none">#BK-2024-0243</a></td>
                                    <td>ประยุทธ์ มั่นคง</td>
                                    <td>ห้องประชุมย่อย B (2 ชม.)</td>
                                    <td class="text-end">฿1,000</td>
                                    <td class="text-center"><span class="badge bg-success">ชำระแล้ว</span></td>
                                </tr>
                                <tr>
                                    <td>24 ธ.ค. 67</td>
                                    <td><a href="#" class="text-decoration-none">#BK-2024-0242</a></td>
                                    <td>มานี ดีใจ</td>
                                    <td>ห้องประชุมใหญ่ A (4 ชม.) + อาหารว่าง</td>
                                    <td class="text-end">฿7,500</td>
                                    <td class="text-center"><span class="badge bg-success">ชำระแล้ว</span></td>
                                </tr>
                                <tr>
                                    <td>23 ธ.ค. 67</td>
                                    <td><a href="#" class="text-decoration-none">#BK-2024-0241</a></td>
                                    <td>ชาลี รักเรียน</td>
                                    <td>ห้องประชุม VIP (3 ชม.)</td>
                                    <td class="text-end">฿6,000</td>
                                    <td class="text-center"><span class="badge bg-danger">เกินกำหนด</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 text-success me-2"></i>เปรียบเทียบรายเดือน (ปี 2567)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyComparisonChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
function setDateRange(range) {
    const today = new Date();
    let start, end;
    switch(range) {
        case 'today': start = end = today.toISOString().split('T')[0]; break;
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
    // Update hidden inputs
    document.getElementById('startDate').value = start;
    document.getElementById('endDate').value = end;
    // Update Thai display
    document.getElementById('startDateDisplay').value = formatThaiDateJS(start);
    document.getElementById('endDateDisplay').value = formatThaiDateJS(end);
}
window.setDateRange = setDateRange;

// Thai date formatter for JavaScript
const thaiMonthsShort = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
function formatThaiDateJS(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    const day = date.getDate();
    const month = date.getMonth() + 1;
    const year = date.getFullYear() + 543;
    return day + ' ' + thaiMonthsShort[month] + ' ' + year;
}

// Initialize Thai Date Pickers
function initThaiDatePicker(displayId, hiddenId) {
    const displayInput = document.getElementById(displayId);
    const hiddenInput = document.getElementById(hiddenId);
    if (!displayInput || !hiddenInput) return;
    
    const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                       'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    const thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
    
    let selectedDate = hiddenInput.value ? new Date(hiddenInput.value) : new Date();
    let viewDate = new Date(selectedDate);
    
    const pickerContainer = document.createElement('div');
    pickerContainer.className = 'thai-datepicker-report';
    pickerContainer.style.cssText = 'position:absolute;top:100%;left:0;z-index:1050;background:#fff;border:1px solid #dee2e6;border-radius:0.5rem;box-shadow:0 0.5rem 1rem rgba(0,0,0,0.15);padding:1rem;min-width:280px;display:none;';
    displayInput.parentElement.style.position = 'relative';
    displayInput.parentElement.appendChild(pickerContainer);
    
    function renderCalendar() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const thaiYear = year + 543;
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        
        let html = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary prev-month"><i class="bi bi-chevron-left"></i></button>';
        html += '<span style="font-weight:600;">' + thaiMonths[month] + ' ' + thaiYear + '</span>';
        html += '<button type="button" class="btn btn-sm btn-outline-secondary next-month"><i class="bi bi-chevron-right"></i></button>';
        html += '</div><div style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;">';
        
        thaiDaysShort.forEach(day => { html += '<div style="font-size:0.75rem;font-weight:600;color:#6c757d;padding:0.25rem;">' + day + '</div>'; });
        for (let i = 0; i < firstDay; i++) { html += '<div></div>'; }
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            const isSelected = dateStr === hiddenInput.value;
            const style = 'padding:0.5rem;border-radius:0.25rem;cursor:pointer;' + (isSelected ? 'background:#0d6efd;color:#fff;' : '');
            html += '<div class="date-selectable" data-date="' + dateStr + '" style="' + style + '">' + day + '</div>';
        }
        html += '</div>';
        pickerContainer.innerHTML = html;
        
        pickerContainer.querySelector('.prev-month').addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); viewDate.setMonth(viewDate.getMonth() - 1); renderCalendar(); });
        pickerContainer.querySelector('.next-month').addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); viewDate.setMonth(viewDate.getMonth() + 1); renderCalendar(); });
        pickerContainer.querySelectorAll('.date-selectable').forEach(el => {
            el.addEventListener('click', function() {
                hiddenInput.value = this.dataset.date;
                displayInput.value = formatThaiDateJS(this.dataset.date);
                pickerContainer.style.display = 'none';
            });
            el.addEventListener('mouseenter', function() { if (!this.style.backgroundColor.includes('13, 110, 253')) this.style.backgroundColor = '#e9ecef'; });
            el.addEventListener('mouseleave', function() { if (!this.style.backgroundColor.includes('13, 110, 253')) this.style.backgroundColor = ''; });
        });
    }
    
    displayInput.addEventListener('click', function(e) { e.stopPropagation(); viewDate = new Date(selectedDate); renderCalendar(); pickerContainer.style.display = pickerContainer.style.display === 'none' ? 'block' : 'none'; });
    document.addEventListener('click', function(e) { if (!pickerContainer.contains(e.target) && e.target !== displayInput) pickerContainer.style.display = 'none'; });
}

// Initialize date pickers
initThaiDatePicker('startDateDisplay', 'startDate');
initThaiDatePicker('endDateDisplay', 'endDate');

function exportReport(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('export', format);
    window.location.href = window.location.pathname + '?' + params.toString();
}
window.exportReport = exportReport;

const chartColors = {
    primary: '#0d6efd',
    success: '#198754',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#0dcaf0',
    secondary: '#6c757d'
};

// Revenue Trend Chart
new Chart(document.getElementById('revenueTrendChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['1 ธ.ค.', '5 ธ.ค.', '10 ธ.ค.', '15 ธ.ค.', '20 ธ.ค.', '25 ธ.ค.'],
        datasets: [{
            label: 'รายได้',
            data: [15000, 22000, 18500, 28000, 24000, 32000],
            borderColor: chartColors.success,
            backgroundColor: 'rgba(25, 135, 84, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return '฿' + context.parsed.y.toLocaleString();
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return '฿' + value.toLocaleString(); }
                }
            }
        }
    }
});

// Revenue Type Chart
new Chart(document.getElementById('revenueTypeChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['ค่าห้องประชุม', 'ค่าอุปกรณ์', 'ค่าบริการเสริม'],
        datasets: [{
            data: [95000, 18500, 11500],
            backgroundColor: [chartColors.primary, chartColors.info, chartColors.secondary]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        cutout: '60%'
    }
});

// Department Revenue Chart
new Chart(document.getElementById('departmentRevenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['ฝ่ายบริหาร', 'ฝ่ายวิชาการ', 'ฝ่ายบริการ', 'ฝ่ายการเงิน', 'ฝ่ายวิจัย'],
        datasets: [{
            label: 'รายได้',
            data: [38500, 28000, 22500, 18000, 18000],
            backgroundColor: chartColors.success
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: { legend: { display: false } },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return '฿' + value.toLocaleString(); }
                }
            }
        }
    }
});

// Payment Status Chart
new Chart(document.getElementById('paymentStatusChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: ['ชำระแล้ว', 'รอชำระ', 'เกินกำหนด', 'คืนเงิน'],
        datasets: [{
            data: [108500, 15000, 1500, 0],
            backgroundColor: [chartColors.success, chartColors.warning, chartColors.danger, chartColors.secondary]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

// Monthly Comparison Chart
new Chart(document.getElementById('monthlyComparisonChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
        datasets: [{
            label: 'ปี 2567',
            data: [85000, 92000, 105000, 78000, 98000, 115000, 125000, 118000, 108000, 122000, 130000, 125000],
            backgroundColor: chartColors.success
        }, {
            label: 'ปี 2566',
            data: [72000, 78000, 88000, 65000, 82000, 95000, 102000, 98000, 92000, 105000, 112000, 108000],
            backgroundColor: 'rgba(25, 135, 84, 0.3)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return '฿' + (value/1000) + 'K'; }
                }
            }
        }
    }
});
JS;
$this->registerJs($js);
?>

<style>
@media print {
    .btn-group, .card-header .btn, form { display: none !important; }
    .card { break-inside: avoid; }
}
</style>
