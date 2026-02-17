<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'จัดการวันหยุด';
$this->params['breadcrumbs'][] = ['label' => 'ตั้งค่าระบบ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Sample holidays data
$holidays = [
    ['id' => 1, 'name' => 'วันขึ้นปีใหม่', 'name_en' => 'New Year\'s Day', 'date' => '2025-01-01', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 2, 'name' => 'วันมาฆบูชา', 'name_en' => 'Makha Bucha Day', 'date' => '2025-02-12', 'type' => 'religious', 'recurring' => false, 'status' => 'active'],
    ['id' => 3, 'name' => 'วันจักรี', 'name_en' => 'Chakri Memorial Day', 'date' => '2025-04-06', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 4, 'name' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'date' => '2025-04-13', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 5, 'name' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'date' => '2025-04-14', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 6, 'name' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'date' => '2025-04-15', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 7, 'name' => 'วันแรงงานแห่งชาติ', 'name_en' => 'National Labour Day', 'date' => '2025-05-01', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 8, 'name' => 'วันฉัตรมงคล', 'name_en' => 'Coronation Day', 'date' => '2025-05-04', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 9, 'name' => 'วันวิสาขบูชา', 'name_en' => 'Visakha Bucha Day', 'date' => '2025-05-11', 'type' => 'religious', 'recurring' => false, 'status' => 'active'],
    ['id' => 10, 'name' => 'วันเฉลิมพระชนมพรรษา ร.10', 'name_en' => 'H.M. King\'s Birthday', 'date' => '2025-07-28', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 11, 'name' => 'วันอาสาฬหบูชา', 'name_en' => 'Asanha Bucha Day', 'date' => '2025-07-10', 'type' => 'religious', 'recurring' => false, 'status' => 'active'],
    ['id' => 12, 'name' => 'วันเข้าพรรษา', 'name_en' => 'Buddhist Lent Day', 'date' => '2025-07-11', 'type' => 'religious', 'recurring' => false, 'status' => 'active'],
    ['id' => 13, 'name' => 'วันแม่แห่งชาติ', 'name_en' => 'H.M. Queen\'s Birthday', 'date' => '2025-08-12', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 14, 'name' => 'วันคล้ายวันสวรรคต ร.9', 'name_en' => 'King Bhumibol Memorial Day', 'date' => '2025-10-13', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 15, 'name' => 'วันปิยมหาราช', 'name_en' => 'King Chulalongkorn Day', 'date' => '2025-10-23', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 16, 'name' => 'วันพ่อแห่งชาติ', 'name_en' => 'King Bhumibol\'s Birthday', 'date' => '2025-12-05', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 17, 'name' => 'วันรัฐธรรมนูญ', 'name_en' => 'Constitution Day', 'date' => '2025-12-10', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 18, 'name' => 'วันสิ้นปี', 'name_en' => 'New Year\'s Eve', 'date' => '2025-12-31', 'type' => 'national', 'recurring' => true, 'status' => 'active'],
    ['id' => 19, 'name' => 'วันหยุดพิเศษ (ประชุมผู้บริหาร)', 'name_en' => 'Special Holiday (Executive Meeting)', 'date' => '2025-03-15', 'type' => 'special', 'recurring' => false, 'status' => 'active'],
];

$typeLabels = [
    'national' => ['label' => 'วันหยุดราชการ', 'class' => 'bg-danger'],
    'religious' => ['label' => 'วันสำคัญทางศาสนา', 'class' => 'bg-warning'],
    'special' => ['label' => 'วันหยุดพิเศษ', 'class' => 'bg-info'],
];
?>

<div class="setting-holidays">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">จัดการวันหยุดของหน่วยงาน สำหรับปิดการจองห้องประชุม</p>
        </div>
        <div>
            <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-upload me-1"></i> นำเข้า
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มวันหยุด
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Sidebar - Calendar -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar3 me-2"></i>ปฏิทินวันหยุด
                    </h5>
                </div>
                <div class="card-body">
                    <div id="holidayCalendar"></div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bar-chart me-2"></i>สถิติวันหยุด
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-danger"><?= count(array_filter($holidays, fn($h) => $h['type'] === 'national')) ?></div>
                                <small class="text-muted">วันหยุดราชการ</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-warning"><?= count(array_filter($holidays, fn($h) => $h['type'] === 'religious')) ?></div>
                                <small class="text-muted">วันสำคัญศาสนา</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-info"><?= count(array_filter($holidays, fn($h) => $h['type'] === 'special')) ?></div>
                                <small class="text-muted">วันหยุดพิเศษ</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3 text-center">
                                <div class="h4 mb-1 text-primary"><?= count($holidays) ?></div>
                                <small class="text-muted">รวมทั้งหมด</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>คำอธิบาย
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-danger me-2">&nbsp;&nbsp;&nbsp;</span>
                        <span>วันหยุดราชการ</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-warning me-2">&nbsp;&nbsp;&nbsp;</span>
                        <span>วันสำคัญทางศาสนา</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-info me-2">&nbsp;&nbsp;&nbsp;</span>
                        <span>วันหยุดพิเศษ</span>
                    </div>
                    <hr>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-arrow-repeat text-success me-2"></i>
                        <span>เกิดซ้ำทุกปี</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-event text-secondary me-2"></i>
                        <span>เฉพาะปีนี้</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Holiday List -->
        <div class="col-lg-8">
            <!-- Filters -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">ปี</label>
                            <select class="form-select" id="filterYear">
                                <option value="2024">2024</option>
                                <option value="2025" selected>2025</option>
                                <option value="2026">2026</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ประเภท</label>
                            <select class="form-select" id="filterType">
                                <option value="">ทั้งหมด</option>
                                <option value="national">วันหยุดราชการ</option>
                                <option value="religious">วันสำคัญทางศาสนา</option>
                                <option value="special">วันหยุดพิเศษ</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">เกิดซ้ำ</label>
                            <select class="form-select" id="filterRecurring">
                                <option value="">ทั้งหมด</option>
                                <option value="1">เกิดซ้ำทุกปี</option>
                                <option value="0">เฉพาะปีนี้</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">ค้นหา</label>
                            <input type="text" class="form-control" id="searchHoliday" placeholder="ชื่อวันหยุด...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Holiday Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>รายการวันหยุด
                    </h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="deleteSelected" disabled>
                            <i class="bi bi-trash me-1"></i> ลบที่เลือก
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </div>
                                    </th>
                                    <th>วันที่</th>
                                    <th>ชื่อวันหยุด</th>
                                    <th>ประเภท</th>
                                    <th class="text-center">เกิดซ้ำ</th>
                                    <th class="text-center">สถานะ</th>
                                    <th width="120" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($holidays as $holiday): ?>
                                    <?php
                                    $date = new DateTime($holiday['date']);
                                    $isPast = $date < new DateTime('today');
                                    ?>
                                    <tr class="<?= $isPast ? 'table-secondary' : '' ?>">
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input holiday-checkbox" value="<?= $holiday['id'] ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 text-center me-3" style="min-width: 50px;">
                                                    <div class="h5 mb-0"><?= $date->format('d') ?></div>
                                                    <small class="text-muted"><?= $date->format('M') ?></small>
                                                </div>
                                                <div>
                                                    <div class="text-muted small"><?= $date->format('l') ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-medium"><?= Html::encode($holiday['name']) ?></div>
                                            <small class="text-muted"><?= Html::encode($holiday['name_en']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge <?= $typeLabels[$holiday['type']]['class'] ?>">
                                                <?= $typeLabels[$holiday['type']]['label'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($holiday['recurring']): ?>
                                                <i class="bi bi-arrow-repeat text-success" title="เกิดซ้ำทุกปี"></i>
                                            <?php else: ?>
                                                <i class="bi bi-calendar-event text-secondary" title="เฉพาะปีนี้"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" <?= $holiday['status'] === 'active' ? 'checked' : '' ?>>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" title="แก้ไข" data-bs-toggle="modal" data-bs-target="#editHolidayModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" title="ลบ" onclick="deleteHoliday(<?= $holiday['id'] ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            แสดง <?= count($holidays) ?> รายการ
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled"><a class="page-link" href="#">ก่อนหน้า</a></li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#">ถัดไป</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>เพิ่มวันหยุด
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addHolidayForm">
                    <div class="mb-3">
                        <label class="form-label">ชื่อวันหยุด (ภาษาไทย) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อวันหยุด (English)</label>
                        <input type="text" class="form-control" name="name_en">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ถึงวันที่</label>
                            <input type="date" class="form-control" name="date_end">
                            <small class="text-muted">เว้นว่างถ้าเป็นวันเดียว</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="">เลือกประเภท</option>
                            <option value="national">วันหยุดราชการ</option>
                            <option value="religious">วันสำคัญทางศาสนา</option>
                            <option value="special">วันหยุดพิเศษ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="recurring" id="addRecurring">
                            <label class="form-check-label" for="addRecurring">
                                เกิดซ้ำทุกปี
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveHoliday()">
                    <i class="bi bi-check-lg me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Holiday Modal -->
<div class="modal fade" id="editHolidayModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil me-2"></i>แก้ไขวันหยุด
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editHolidayForm">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">ชื่อวันหยุด (ภาษาไทย) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="วันขึ้นปีใหม่" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ชื่อวันหยุด (English)</label>
                        <input type="text" class="form-control" name="name_en" value="New Year's Day">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" value="2025-01-01" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ถึงวันที่</label>
                            <input type="date" class="form-control" name="date_end">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" required>
                            <option value="national" selected>วันหยุดราชการ</option>
                            <option value="religious">วันสำคัญทางศาสนา</option>
                            <option value="special">วันหยุดพิเศษ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="recurring" id="editRecurring" checked>
                            <label class="form-check-label" for="editRecurring">
                                เกิดซ้ำทุกปี
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="updateHoliday()">
                    <i class="bi bi-check-lg me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-upload me-2"></i>นำเข้าวันหยุด
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <h6>นำเข้าจากแหล่งข้อมูล</h6>
                    <div class="list-group">
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-calendar-check text-primary me-3 fs-4"></i>
                            <div>
                                <div class="fw-medium">วันหยุดราชการไทย 2025</div>
                                <small class="text-muted">นำเข้าวันหยุดราชการประจำปี 2568 อัตโนมัติ</small>
                            </div>
                        </button>
                        <button type="button" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-google text-danger me-3 fs-4"></i>
                            <div>
                                <div class="fw-medium">Google Calendar - Thai Holidays</div>
                                <small class="text-muted">ซิงค์จาก Google Calendar</small>
                            </div>
                        </button>
                    </div>
                </div>
                <hr>
                <div>
                    <h6>นำเข้าจากไฟล์</h6>
                    <div class="border rounded p-4 text-center bg-light">
                        <i class="bi bi-file-earmark-spreadsheet fs-1 text-success mb-2"></i>
                        <p class="mb-2">ลากไฟล์มาวางที่นี่ หรือ</p>
                        <input type="file" class="d-none" id="importFile" accept=".csv,.xlsx,.xls">
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('importFile').click()">
                            เลือกไฟล์
                        </button>
                        <p class="text-muted small mt-2 mb-0">รองรับไฟล์ .csv, .xlsx, .xls</p>
                    </div>
                    <div class="mt-3">
                        <a href="#" class="small">
                            <i class="bi bi-download me-1"></i>ดาวน์โหลดไฟล์ตัวอย่าง
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<?php
$holidayJson = json_encode(array_map(function($h) use ($typeLabels) {
    $colors = [
        'national' => '#dc3545',
        'religious' => '#ffc107',
        'special' => '#0dcaf0',
    ];
    return [
        'title' => $h['name'],
        'start' => $h['date'],
        'color' => $colors[$h['type']] ?? '#6c757d',
    ];
}, $holidays));

$js = <<<JS
// Initialize calendar
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('holidayCalendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        locale: 'th',
        height: 'auto',
        events: {$holidayJson},
        eventClick: function(info) {
            alert(info.event.title);
        }
    });
    calendar.render();
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.holiday-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateDeleteButton();
});

// Individual checkboxes
document.querySelectorAll('.holiday-checkbox').forEach(cb => {
    cb.addEventListener('change', updateDeleteButton);
});

function updateDeleteButton() {
    const checked = document.querySelectorAll('.holiday-checkbox:checked').length;
    document.getElementById('deleteSelected').disabled = checked === 0;
}

// Delete holiday
function deleteHoliday(id) {
    if (confirm('คุณต้องการลบวันหยุดนี้หรือไม่?')) {
        // AJAX delete
        console.log('Delete holiday:', id);
    }
}

// Save holiday
function saveHoliday() {
    const form = document.getElementById('addHolidayForm');
    if (form.checkValidity()) {
        // AJAX save
        console.log('Save holiday');
        bootstrap.Modal.getInstance(document.getElementById('addHolidayModal')).hide();
    } else {
        form.reportValidity();
    }
}

// Update holiday
function updateHoliday() {
    const form = document.getElementById('editHolidayForm');
    if (form.checkValidity()) {
        // AJAX update
        console.log('Update holiday');
        bootstrap.Modal.getInstance(document.getElementById('editHolidayModal')).hide();
    } else {
        form.reportValidity();
    }
}
JS;
$this->registerJs($js);

// Register FullCalendar CSS
$this->registerCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js', ['position' => \yii\web\View::POS_HEAD]);
?>
