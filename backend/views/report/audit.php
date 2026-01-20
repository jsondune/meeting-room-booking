<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var array $dateRange */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'รายงาน Audit Log';
$this->params['breadcrumbs'][] = ['label' => 'รายงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Default date range
$startDate = $dateRange['start'] ?? date('Y-m-d', strtotime('-7 days'));
$endDate = $dateRange['end'] ?? date('Y-m-d');

// Sample stats
$totalLogs = 1248;
$todayLogs = 87;
$criticalEvents = 5;
$uniqueUsers = 42;
?>

<div class="report-audit">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-shield-check text-info me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="exportReport('pdf')">
                <i class="bi bi-file-pdf me-1"></i>PDF
            </button>
            <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
                <i class="bi bi-file-excel me-1"></i>Excel
            </button>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">บันทึกทั้งหมด</h6>
                            <h3 class="card-title mb-0"><?= number_format($totalLogs) ?></h3>
                        </div>
                        <i class="bi bi-journal-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">วันนี้</h6>
                            <h3 class="card-title mb-0"><?= number_format($todayLogs) ?></h3>
                        </div>
                        <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">เหตุการณ์สำคัญ</h6>
                            <h3 class="card-title mb-0"><?= number_format($criticalEvents) ?></h3>
                        </div>
                        <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ผู้ใช้ที่ใช้งาน</h6>
                            <h3 class="card-title mb-0"><?= number_format($uniqueUsers) ?></h3>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#filterPanel">
                <i class="bi bi-funnel me-1"></i>ตัวกรอง
                <i class="bi bi-chevron-down float-end"></i>
            </a>
        </div>
        <div class="collapse show" id="filterPanel">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ประเภทกิจกรรม</label>
                        <select name="action" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="login">เข้าสู่ระบบ</option>
                            <option value="logout">ออกจากระบบ</option>
                            <option value="create">สร้าง</option>
                            <option value="update">แก้ไข</option>
                            <option value="delete">ลบ</option>
                            <option value="approve">อนุมัติ</option>
                            <option value="reject">ปฏิเสธ</option>
                            <option value="export">ส่งออก</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">โมดูล</label>
                        <select name="module" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="booking">การจอง</option>
                            <option value="room">ห้องประชุม</option>
                            <option value="user">ผู้ใช้</option>
                            <option value="equipment">อุปกรณ์</option>
                            <option value="department">หน่วยงาน</option>
                            <option value="setting">ตั้งค่า</option>
                            <option value="auth">Authentication</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ผู้ใช้</label>
                        <select name="user_id" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="1">admin</option>
                            <option value="2">สมชาย ใจดี</option>
                            <option value="3">วรรณา สุขใจ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">ระดับ</label>
                        <select name="level" class="form-select">
                            <option value="">ทั้งหมด</option>
                            <option value="info">Info</option>
                            <option value="warning">Warning</option>
                            <option value="error">Error</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>ค้นหา
                        </button>
                        <a href="<?= Url::to(['audit']) ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ต
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Activity Timeline Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity text-info me-2"></i>กิจกรรมตามช่วงเวลา
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="activityTimelineChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity by Type -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pie-chart text-info me-2"></i>ประเภทกิจกรรม
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="activityTypeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Audit Log Table -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul text-info me-2"></i>รายการ Audit Log
                    </h5>
                    <span class="badge bg-secondary">แสดง 1-20 จาก <?= number_format($totalLogs) ?> รายการ</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 150px;">วันที่/เวลา</th>
                                    <th style="width: 120px;">ผู้ใช้</th>
                                    <th style="width: 100px;">กิจกรรม</th>
                                    <th style="width: 100px;">โมดูล</th>
                                    <th>รายละเอียด</th>
                                    <th style="width: 120px;">IP Address</th>
                                    <th style="width: 80px;">ระดับ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>14:32:15</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">สม</div>
                                            <small>สมชาย ใจดี</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">สร้าง</span></td>
                                    <td><span class="badge bg-primary">การจอง</span></td>
                                    <td>
                                        <small>สร้างการจอง #BK-2024-0245 ห้องประชุมใหญ่ A วันที่ 26 ธ.ค. 67</small>
                                    </td>
                                    <td><code class="small">192.168.1.105</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>14:28:42</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">AD</div>
                                            <small>admin</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">อนุมัติ</span></td>
                                    <td><span class="badge bg-primary">การจอง</span></td>
                                    <td>
                                        <small>อนุมัติการจอง #BK-2024-0244</small>
                                    </td>
                                    <td><code class="small">192.168.1.10</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>14:15:08</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">AD</div>
                                            <small>admin</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">แก้ไข</span></td>
                                    <td><span class="badge bg-secondary">ตั้งค่า</span></td>
                                    <td>
                                        <small>เปลี่ยนแปลงการตั้งค่าระบบ: เปิดใช้งาน 2FA บังคับสำหรับ Admin</small>
                                    </td>
                                    <td><code class="small">192.168.1.10</code></td>
                                    <td><span class="badge bg-warning text-dark">Warning</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>13:45:22</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">วร</div>
                                            <small>วรรณา สุขใจ</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">เข้าสู่ระบบ</span></td>
                                    <td><span class="badge bg-dark">Auth</span></td>
                                    <td>
                                        <small>เข้าสู่ระบบสำเร็จ (OAuth: Google)</small>
                                    </td>
                                    <td><code class="small">203.150.45.78</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr class="table-danger">
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>12:58:33</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">??</div>
                                            <small class="text-muted">ไม่ทราบ</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">ล้มเหลว</span></td>
                                    <td><span class="badge bg-dark">Auth</span></td>
                                    <td>
                                        <small>พยายามเข้าสู่ระบบล้มเหลว 5 ครั้งติดต่อกัน (username: admin)</small>
                                    </td>
                                    <td><code class="small">185.220.101.45</code></td>
                                    <td><span class="badge bg-danger">Critical</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>11:42:18</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">AD</div>
                                            <small>admin</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">ลบ</span></td>
                                    <td><span class="badge bg-info">อุปกรณ์</span></td>
                                    <td>
                                        <small>ลบอุปกรณ์: โปรเจคเตอร์เก่า (ID: 15)</small>
                                    </td>
                                    <td><code class="small">192.168.1.10</code></td>
                                    <td><span class="badge bg-warning text-dark">Warning</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>10:25:55</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">ปร</div>
                                            <small>ประยุทธ์ มั่นคง</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">แก้ไข</span></td>
                                    <td><span class="badge bg-primary">การจอง</span></td>
                                    <td>
                                        <small>แก้ไขการจอง #BK-2024-0240: เปลี่ยนเวลาจาก 10:00-12:00 เป็น 14:00-16:00</small>
                                    </td>
                                    <td><code class="small">192.168.1.88</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>09:15:32</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">AD</div>
                                            <small>admin</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success">สร้าง</span></td>
                                    <td><span class="badge bg-warning text-dark">ผู้ใช้</span></td>
                                    <td>
                                        <small>สร้างผู้ใช้ใหม่: นายใหม่ ทดสอบ (new.test@example.com)</small>
                                    </td>
                                    <td><code class="small">192.168.1.10</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="text-muted">25 ธ.ค. 67</span><br>
                                        <small>08:45:10</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">AD</div>
                                            <small>admin</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">เข้าสู่ระบบ</span></td>
                                    <td><span class="badge bg-dark">Auth</span></td>
                                    <td>
                                        <small>เข้าสู่ระบบสำเร็จ (2FA verified)</small>
                                    </td>
                                    <td><code class="small">192.168.1.10</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                                <tr class="table-warning">
                                    <td>
                                        <span class="text-muted">24 ธ.ค. 67</span><br>
                                        <small>23:58:45</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:28px;height:28px;font-size:10px;">SY</div>
                                            <small>System</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">ระบบ</span></td>
                                    <td><span class="badge bg-secondary">ตั้งค่า</span></td>
                                    <td>
                                        <small>สำรองข้อมูลอัตโนมัติเสร็จสมบูรณ์ (ขนาด: 125 MB)</small>
                                    </td>
                                    <td><code class="small">127.0.0.1</code></td>
                                    <td><span class="badge bg-info">Info</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">ก่อนหน้า</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">63</a></li>
                            <li class="page-item"><a class="page-link" href="#">ถัดไป</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Security Alerts -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-shield-exclamation text-danger me-2"></i>การแจ้งเตือนความปลอดภัย
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item list-group-item-danger">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        การพยายามเข้าสู่ระบบผิดพลาดหลายครั้ง
                                    </h6>
                                    <p class="mb-1 small">IP: 185.220.101.45 พยายามเข้าสู่ระบบผิดพลาด 5 ครั้ง</p>
                                    <small class="text-muted">25 ธ.ค. 67 12:58:33</small>
                                </div>
                                <button class="btn btn-sm btn-outline-danger">บล็อค IP</button>
                            </div>
                        </div>
                        <div class="list-group-item list-group-item-warning">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-key me-1"></i>
                                        การเปลี่ยนแปลงสิทธิ์ผู้ใช้
                                    </h6>
                                    <p class="mb-1 small">ผู้ใช้ "วรรณา สุขใจ" ได้รับสิทธิ์ Manager</p>
                                    <small class="text-muted">24 ธ.ค. 67 16:25:10</small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary">ดูรายละเอียด</button>
                            </div>
                        </div>
                        <div class="list-group-item list-group-item-warning">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        การเข้าสู่ระบบจากตำแหน่งใหม่
                                    </h6>
                                    <p class="mb-1 small">admin เข้าสู่ระบบจาก IP ที่ไม่เคยใช้มาก่อน</p>
                                    <small class="text-muted">24 ธ.ค. 67 09:15:32</small>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary">ตรวจสอบ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Active Users -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-badge text-info me-2"></i>ผู้ใช้ที่ใช้งานมากที่สุด
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ผู้ใช้</th>
                                    <th class="text-center">กิจกรรม</th>
                                    <th class="text-center">เข้าสู่ระบบ</th>
                                    <th>เข้าสู่ระบบล่าสุด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:11px;">AD</div>
                                            <div>
                                                <strong>admin</strong>
                                                <br><small class="text-muted">ผู้ดูแลระบบ</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary">245</span></td>
                                    <td class="text-center"><span class="badge bg-success">38</span></td>
                                    <td><small>25 ธ.ค. 67 08:45</small></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:11px;">สม</div>
                                            <div>
                                                <strong>สมชาย ใจดี</strong>
                                                <br><small class="text-muted">ฝ่ายบริหาร</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary">128</span></td>
                                    <td class="text-center"><span class="badge bg-success">22</span></td>
                                    <td><small>25 ธ.ค. 67 14:32</small></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:11px;">วร</div>
                                            <div>
                                                <strong>วรรณา สุขใจ</strong>
                                                <br><small class="text-muted">ฝ่ายวิชาการ</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary">95</span></td>
                                    <td class="text-center"><span class="badge bg-success">18</span></td>
                                    <td><small>25 ธ.ค. 67 13:45</small></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:11px;">ปร</div>
                                            <div>
                                                <strong>ประยุทธ์ มั่นคง</strong>
                                                <br><small class="text-muted">ฝ่ายบริการ</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-primary">72</span></td>
                                    <td class="text-center"><span class="badge bg-success">15</span></td>
                                    <td><small>25 ธ.ค. 67 10:25</small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
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

// Activity Timeline Chart
new Chart(document.getElementById('activityTimelineChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
        datasets: [{
            label: 'กิจกรรม',
            data: [5, 2, 45, 68, 52, 35, 12],
            borderColor: chartColors.info,
            backgroundColor: 'rgba(13, 202, 240, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

// Activity Type Chart
new Chart(document.getElementById('activityTypeChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['สร้าง', 'แก้ไข', 'ลบ', 'เข้าสู่ระบบ', 'อนุมัติ', 'อื่นๆ'],
        datasets: [{
            data: [320, 280, 45, 380, 125, 98],
            backgroundColor: [
                chartColors.success,
                chartColors.warning,
                chartColors.danger,
                chartColors.secondary,
                chartColors.info,
                chartColors.primary
            ]
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
        },
        cutout: '50%'
    }
});
JS;
$this->registerJs($js);
?>

<style>
code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
@media print {
    .btn-group, form, .pagination { display: none !important; }
    .card { break-inside: avoid; }
}
</style>
