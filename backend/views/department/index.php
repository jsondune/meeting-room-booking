<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $searchKeyword */

$this->title = 'จัดการหน่วยงาน';
$this->params['breadcrumbs'][] = $this->title;

// Sample stats
$totalDepartments = 12;
$activeDepartments = 10;
$totalUsers = 156;
$totalBookings = 342;
?>

<div class="department-index">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">จัดการหน่วยงานและโครงสร้างองค์กร</p>
        </div>
        <div>
            <?= Html::a('<i class="fas fa-plus me-1"></i> เพิ่มหน่วยงาน', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-building text-primary fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">หน่วยงานทั้งหมด</div>
                            <div class="h4 mb-0"><?= number_format($totalDepartments) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-check-circle text-success fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">ใช้งานอยู่</div>
                            <div class="h4 mb-0"><?= number_format($activeDepartments) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-users text-info fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">บุคลากรทั้งหมด</div>
                            <div class="h4 mb-0"><?= number_format($totalUsers) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-calendar-check text-warning fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">การจองทั้งหมด</div>
                            <div class="h4 mb-0"><?= number_format($totalBookings) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control" placeholder="ค้นหาหน่วยงาน..." value="<?= Html::encode($searchKeyword ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- สถานะทั้งหมด --</option>
                        <option value="active">ใช้งานอยู่</option>
                        <option value="inactive">ไม่ใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="name_asc">ชื่อ (ก-ฮ)</option>
                        <option value="name_desc">ชื่อ (ฮ-ก)</option>
                        <option value="users_desc">จำนวนบุคลากร (มาก-น้อย)</option>
                        <option value="created_desc">ล่าสุด</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> กรอง
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Department List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">รายการหน่วยงาน</h5>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active" data-view="table" title="มุมมองตาราง">
                        <i class="fas fa-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-view="tree" title="มุมมองแผนผัง">
                        <i class="fas fa-sitemap"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="table-responsive" id="tableView">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th>หน่วยงาน</th>
                        <th>รหัส</th>
                        <th>หน่วยงานหลัก</th>
                        <th class="text-center">บุคลากร</th>
                        <th class="text-center">การจอง</th>
                        <th class="text-center">สถานะ</th>
                        <th style="width: 120px">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $departments = [
                        ['id' => 1, 'name' => 'สำนักงานอธิการบดี', 'code' => 'PRES', 'parent' => null, 'users' => 15, 'bookings' => 45, 'status' => 'active', 'color' => '#3b82f6'],
                        ['id' => 2, 'name' => 'กองกลาง', 'code' => 'ADMIN', 'parent' => 'สำนักงานอธิการบดี', 'users' => 25, 'bookings' => 67, 'status' => 'active', 'color' => '#10b981'],
                        ['id' => 3, 'name' => 'กองคลัง', 'code' => 'FIN', 'parent' => 'สำนักงานอธิการบดี', 'users' => 18, 'bookings' => 32, 'status' => 'active', 'color' => '#f59e0b'],
                        ['id' => 4, 'name' => 'กองบริหารงานบุคคล', 'code' => 'HR', 'parent' => 'สำนักงานอธิการบดี', 'users' => 12, 'bookings' => 28, 'status' => 'active', 'color' => '#ef4444'],
                        ['id' => 5, 'name' => 'คณะวิทยาศาสตร์', 'code' => 'SCI', 'parent' => null, 'users' => 45, 'bookings' => 89, 'status' => 'active', 'color' => '#8b5cf6'],
                        ['id' => 6, 'name' => 'ภาควิชาคอมพิวเตอร์', 'code' => 'CS', 'parent' => 'คณะวิทยาศาสตร์', 'users' => 22, 'bookings' => 56, 'status' => 'active', 'color' => '#06b6d4'],
                        ['id' => 7, 'name' => 'คณะวิศวกรรมศาสตร์', 'code' => 'ENG', 'parent' => null, 'users' => 38, 'bookings' => 72, 'status' => 'active', 'color' => '#ec4899'],
                        ['id' => 8, 'name' => 'ศูนย์คอมพิวเตอร์', 'code' => 'ICC', 'parent' => null, 'users' => 8, 'bookings' => 15, 'status' => 'inactive', 'color' => '#6b7280'],
                    ];
                    foreach ($departments as $dept):
                    ?>
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input row-checkbox" type="checkbox" value="<?= $dept['id'] ?>">
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: <?= $dept['color'] ?>20;">
                                    <i class="fas fa-building" style="color: <?= $dept['color'] ?>"></i>
                                </div>
                                <div>
                                    <div class="fw-medium"><?= Html::encode($dept['name']) ?></div>
                                    <?php if ($dept['parent']): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i>
                                        <?= Html::encode($dept['parent']) ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded"><?= Html::encode($dept['code']) ?></code>
                        </td>
                        <td>
                            <?php if ($dept['parent']): ?>
                                <span class="text-muted"><?= Html::encode($dept['parent']) ?></span>
                            <?php else: ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary">หน่วยงานหลัก</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info">
                                <i class="fas fa-users me-1"></i><?= number_format($dept['users']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                <?= number_format($dept['bookings']) ?> ครั้ง
                            </span>
                        </td>
                        <td class="text-center">
                            <?php if ($dept['status'] === 'active'): ?>
                                <span class="badge bg-success">ใช้งานอยู่</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">ไม่ใช้งาน</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $dept['id']], [
                                    'class' => 'btn btn-outline-info',
                                    'title' => 'ดูรายละเอียด'
                                ]) ?>
                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $dept['id']], [
                                    'class' => 'btn btn-outline-warning',
                                    'title' => 'แก้ไข'
                                ]) ?>
                                <button type="button" class="btn btn-outline-danger" title="ลบ" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                    data-id="<?= $dept['id'] ?>" data-name="<?= Html::encode($dept['name']) ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tree View (Hidden by default) -->
        <div class="card-body d-none" id="treeView">
            <div class="org-tree">
                <ul class="tree-root">
                    <li>
                        <div class="tree-node">
                            <i class="fas fa-building text-primary me-2"></i>
                            <strong>สำนักงานอธิการบดี</strong>
                            <span class="badge bg-info ms-2">15 คน</span>
                        </div>
                        <ul>
                            <li>
                                <div class="tree-node">
                                    <i class="fas fa-folder text-success me-2"></i>
                                    กองกลาง
                                    <span class="badge bg-info ms-2">25 คน</span>
                                </div>
                            </li>
                            <li>
                                <div class="tree-node">
                                    <i class="fas fa-folder text-warning me-2"></i>
                                    กองคลัง
                                    <span class="badge bg-info ms-2">18 คน</span>
                                </div>
                            </li>
                            <li>
                                <div class="tree-node">
                                    <i class="fas fa-folder text-danger me-2"></i>
                                    กองบริหารงานบุคคล
                                    <span class="badge bg-info ms-2">12 คน</span>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="tree-node">
                            <i class="fas fa-building text-purple me-2"></i>
                            <strong>คณะวิทยาศาสตร์</strong>
                            <span class="badge bg-info ms-2">45 คน</span>
                        </div>
                        <ul>
                            <li>
                                <div class="tree-node">
                                    <i class="fas fa-folder text-cyan me-2"></i>
                                    ภาควิชาคอมพิวเตอร์
                                    <span class="badge bg-info ms-2">22 คน</span>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <div class="tree-node">
                            <i class="fas fa-building text-pink me-2"></i>
                            <strong>คณะวิศวกรรมศาสตร์</strong>
                            <span class="badge bg-info ms-2">38 คน</span>
                        </div>
                    </li>
                    <li>
                        <div class="tree-node text-muted">
                            <i class="fas fa-building me-2"></i>
                            ศูนย์คอมพิวเตอร์ <small>(ไม่ใช้งาน)</small>
                            <span class="badge bg-secondary ms-2">8 คน</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง 1-8 จาก 12 รายการ
                </div>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">ก่อนหน้า</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">ถัดไป</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบหน่วยงาน <strong id="deleteDeptName"></strong> หรือไม่?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>คำเตือน:</strong> หากหน่วยงานนี้มีหน่วยงานย่อย จะต้องย้ายหรือลบหน่วยงานย่อยก่อน
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <input type="hidden" name="id" id="deleteId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> ลบหน่วยงาน
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Tree View Styles */
.org-tree {
    padding: 20px;
}

.tree-root {
    list-style: none;
    padding-left: 0;
}

.tree-root ul {
    list-style: none;
    padding-left: 30px;
    position: relative;
}

.tree-root ul::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 20px;
    border-left: 2px dashed #dee2e6;
}

.tree-root li {
    position: relative;
    padding: 10px 0;
}

.tree-root ul li::before {
    content: '';
    position: absolute;
    left: -20px;
    top: 20px;
    width: 20px;
    border-bottom: 2px dashed #dee2e6;
}

.tree-node {
    display: inline-flex;
    align-items: center;
    padding: 8px 15px;
    background: #f8f9fa;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.tree-node:hover {
    background: #e9ecef;
    cursor: pointer;
}

/* Custom colors */
.text-purple { color: #8b5cf6 !important; }
.text-pink { color: #ec4899 !important; }
.text-cyan { color: #06b6d4 !important; }
</style>

<?php
$js = <<<JS
// Delete modal
document.getElementById('deleteModal').addEventListener('show.bs.modal', function(event) {
    var button = event.relatedTarget;
    document.getElementById('deleteDeptName').textContent = button.getAttribute('data-name');
    document.getElementById('deleteId').value = button.getAttribute('data-id');
});

// View toggle
document.querySelectorAll('[data-view]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        var view = this.getAttribute('data-view');
        if (view === 'tree') {
            document.getElementById('tableView').classList.add('d-none');
            document.getElementById('treeView').classList.remove('d-none');
        } else {
            document.getElementById('tableView').classList.remove('d-none');
            document.getElementById('treeView').classList.add('d-none');
        }
    });
});

// Select all checkbox
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(function(checkbox) {
        checkbox.checked = this.checked;
    }.bind(this));
});
JS;
$this->registerJs($js);
?>
