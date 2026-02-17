<?php
/**
 * Department Index View
 * backend/views/department/index.php
 * 
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var array $parentDepartments
 * @var array $stats
 * @var array $filters
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\User;
use common\models\Booking;

$this->title = 'จัดการหน่วยงาน';
$this->params['breadcrumbs'][] = $this->title;

// Use real stats from controller
$totalDepartments = $stats['total'] ?? 0;
$activeDepartments = $stats['active'] ?? 0;
$inactiveDepartments = $stats['inactive'] ?? 0;
$totalUsers = $stats['total_users'] ?? 0;
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
            <?= Html::a('<i class="fas fa-download me-1"></i> ส่งออก', ['export'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
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
                            <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-pause-circle text-secondary fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="text-muted small">ไม่ใช้งาน</div>
                            <div class="h4 mb-0"><?= number_format($inactiveDepartments) ?></div>
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
    </div>

    <!-- Filter & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control" placeholder="ค้นหาหน่วยงาน..." 
                               value="<?= Html::encode($filters['keyword'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- สถานะทั้งหมด --</option>
                        <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>ใช้งานอยู่</option>
                        <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>ไม่ใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="parent_id" class="form-select">
                        <option value="">-- หน่วยงานหลักทั้งหมด --</option>
                        <option value="0" <?= ($filters['parent_id'] ?? '') === '0' ? 'selected' : '' ?>>หน่วยงานระดับบน</option>
                        <?php foreach ($parentDepartments as $parent): ?>
                        <option value="<?= $parent->id ?>" <?= ($filters['parent_id'] ?? '') == $parent->id ? 'selected' : '' ?>>
                            <?= Html::encode($parent->name_th) ?>
                        </option>
                        <?php endforeach; ?>
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
                <span class="badge bg-secondary"><?= $dataProvider->getTotalCount() ?> รายการ</span>
            </div>
        </div>

        <!-- Table View -->
        <div class="table-responsive">
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
                        <th class="text-center">สถานะ</th>
                        <th style="width: 150px">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataProvider->getCount() > 0): ?>
                        <?php foreach ($dataProvider->getModels() as $dept): ?>
                        <?php 
                        // Count users in this department
                        $userCount = User::find()->where(['department_id' => $dept->id, 'status' => User::STATUS_ACTIVE])->count();
                        ?>
                        <tr>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input row-checkbox" type="checkbox" value="<?= $dept->id ?>">
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= Html::encode($dept->name_th) ?></div>
                                        <?php if ($dept->name_en): ?>
                                        <small class="text-muted"><?= Html::encode($dept->name_en) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark"><?= Html::encode($dept->code ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if ($dept->parent): ?>
                                    <span class="text-muted">
                                        <i class="fas fa-level-up-alt fa-rotate-90 me-1"></i>
                                        <?= Html::encode($dept->parent->name_th) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-info">หน่วยงานหลัก</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary"><?= number_format($userCount) ?> คน</span>
                            </td>
                            <td class="text-center">
                                <?php if ($dept->is_active): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>ใช้งาน
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-pause-circle me-1"></i>ไม่ใช้งาน
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $dept->id], [
                                        'class' => 'btn btn-outline-primary',
                                        'title' => 'ดูรายละเอียด',
                                    ]) ?>
                                    <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $dept->id], [
                                        'class' => 'btn btn-outline-secondary',
                                        'title' => 'แก้ไข',
                                    ]) ?>
                                    <button type="button" class="btn btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal"
                                            data-id="<?= $dept->id ?>"
                                            data-name="<?= Html::encode($dept->name_th) ?>"
                                            title="ลบ">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-0">ไม่พบข้อมูลหน่วยงาน</p>
                                    <?php if (!empty($filters['keyword']) || !empty($filters['status']) || !empty($filters['parent_id'])): ?>
                                    <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-primary mt-3">
                                        <i class="fas fa-times me-1"></i> ล้างตัวกรอง
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($dataProvider->getTotalCount() > $dataProvider->getPagination()->pageSize): ?>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= $dataProvider->getTotalCount() ?> รายการ
                </div>
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                    'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    'prevPageLabel' => '<i class="fas fa-chevron-left"></i>',
                    'nextPageLabel' => '<i class="fas fa-chevron-right"></i>',
                    'firstPageLabel' => '<i class="fas fa-angle-double-left"></i>',
                    'lastPageLabel' => '<i class="fas fa-angle-double-right"></i>',
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
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
                    <strong>คำเตือน:</strong> หากหน่วยงานนี้มีผู้ใช้หรือหน่วยงานย่อย จะไม่สามารถลบได้
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> ลบหน่วยงาน
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Delete modal
document.getElementById('deleteModal').addEventListener('show.bs.modal', function(event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-id');
    var name = button.getAttribute('data-name');
    
    document.getElementById('deleteDeptName').textContent = name;
    document.getElementById('deleteForm').action = 'delete?id=' + id;
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
