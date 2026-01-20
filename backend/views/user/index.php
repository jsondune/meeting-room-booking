<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\User $searchModel */
/** @var array $departments */
/** @var array $roles */

$this->title = 'จัดการผู้ใช้งาน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">จัดการบัญชีผู้ใช้งานและสิทธิ์การเข้าถึง</p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-person-plus me-1"></i> เพิ่มผู้ใช้', ['create'], ['class' => 'btn btn-primary']) ?>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-download me-1"></i> ส่งออก
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><?= Html::a('<i class="bi bi-file-earmark-excel me-1"></i> Excel', ['export', 'format' => 'excel'], ['class' => 'dropdown-item']) ?></li>
                    <li><?= Html::a('<i class="bi bi-file-earmark-pdf me-1"></i> PDF', ['export', 'format' => 'pdf'], ['class' => 'dropdown-item']) ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['total'] ?? 0 ?></h3>
                            <small class="text-muted">ผู้ใช้ทั้งหมด</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-person-check text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['active'] ?? 0 ?></h3>
                            <small class="text-muted">ใช้งานปกติ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-calendar-check text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['logged_in_today'] ?? 0 ?></h3>
                            <small class="text-muted">เข้าใช้งานวันนี้</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-person-x text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['inactive'] ?? 0 ?></h3>
                            <small class="text-muted">ถูกระงับ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <a class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="collapse" href="#filterCollapse">
                <i class="bi bi-funnel me-2"></i>
                <span class="fw-medium">ตัวกรอง</span>
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form method="get" action="<?= Url::to(['index']) ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">ค้นหา</label>
                            <input type="text" name="search" class="form-control" placeholder="ชื่อ, อีเมล, username..." value="<?= Html::encode($searchModel->search ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">หน่วยงาน</label>
                            <select name="department_id" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <?php foreach ($departments as $id => $name): ?>
                                    <option value="<?= $id ?>" <?= ($searchModel->department_id ?? '') == $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">บทบาท</label>
                            <select name="role" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <?php foreach ($roles as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($searchModel->role ?? '') == $key ? 'selected' : '' ?>><?= Html::encode($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="10" <?= ($searchModel->status ?? '') == '10' ? 'selected' : '' ?>>ใช้งานปกติ</option>
                                <option value="9" <?= ($searchModel->status ?? '') == '9' ? 'selected' : '' ?>>รอยืนยันอีเมล</option>
                                <option value="0" <?= ($searchModel->status ?? '') == '0' ? 'selected' : '' ?>>ถูกระงับ</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-search me-1"></i> ค้นหา
                            </button>
                            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> ล้าง
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th>ผู้ใช้งาน</th>
                            <th>หน่วยงาน</th>
                            <th>บทบาท</th>
                            <th>เข้าใช้ล่าสุด</th>
                            <th>สถานะ</th>
                            <th style="width: 140px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataProvider->models)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        ไม่พบข้อมูลผู้ใช้งาน
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->models as $model): ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" value="<?= $model->id ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($model->avatar): ?>
                                                <img src="<?= $model->avatar ?>" alt="" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <?php else: ?>
                                                <?php
                                                $colors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger', 'bg-secondary'];
                                                $colorIndex = crc32($model->username) % count($colors);
                                                $initials = mb_substr($model->full_name ?? $model->username, 0, 1);
                                                ?>
                                                <div class="rounded-circle <?= $colors[$colorIndex] ?> text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    <span class="fw-medium"><?= mb_strtoupper($initials) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-medium">
                                                    <?= Html::encode($model->fullname ?? $model->username) ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="bi bi-envelope me-1"></i><?= Html::encode($model->email) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($model->department): ?>
                                            <span class="badge bg-light text-dark"><?= Html::encode($model->department->name_th) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $roleClasses = [
                                            'admin' => 'bg-danger',
                                            'manager' => 'bg-warning text-dark',
                                            'staff' => 'bg-info',
                                            'user' => 'bg-secondary',
                                        ];
                                        $roleLabels = [
                                            'admin' => 'ผู้ดูแลระบบ',
                                            'manager' => 'ผู้จัดการ',
                                            'staff' => 'เจ้าหน้าที่',
                                            'user' => 'ผู้ใช้งาน',
                                        ];
                                        $userRole = $model->role ?? 'user';
                                        ?>
                                        <span class="badge <?= $roleClasses[$userRole] ?? 'bg-secondary' ?>">
                                            <?= $roleLabels[$userRole] ?? $userRole ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($model->last_login_at): ?>
                                            <div><?= Yii::$app->formatter->asRelativeTime($model->last_login_at) ?></div>
                                            <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->last_login_at, 'short') ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">ยังไม่เคยเข้าใช้งาน</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            10 => 'bg-success',
                                            9 => 'bg-warning text-dark',
                                            0 => 'bg-secondary',
                                        ];
                                        $statusLabels = [
                                            10 => 'ใช้งานปกติ',
                                            9 => 'รอยืนยัน',
                                            0 => 'ถูกระงับ',
                                        ];
                                        ?>
                                        <span class="badge <?= $statusClasses[$model->status] ?? 'bg-secondary' ?>">
                                            <?= $statusLabels[$model->status] ?? $model->status ?>
                                        </span>
                                        <?php if ($model->two_factor_enabled): ?>
                                            <span class="badge bg-info" title="เปิดใช้งาน 2FA">
                                                <i class="bi bi-shield-check"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?= Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                                'class' => 'btn btn-outline-primary',
                                                'title' => 'ดูรายละเอียด'
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $model->id], [
                                                'class' => 'btn btn-outline-secondary',
                                                'title' => 'แก้ไข'
                                            ]) ?>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    onclick="confirmAction('<?= $model->status == 10 ? 'suspend' : 'activate' ?>', <?= $model->id ?>, '<?= Html::encode($model->username) ?>')"
                                                    title="<?= $model->status == 10 ? 'ระงับ' : 'เปิดใช้งาน' ?>">
                                                <i class="bi bi-<?= $model->status == 10 ? 'person-x' : 'person-check' ?>"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($dataProvider->totalCount > 0): ?>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        แสดง <?= $dataProvider->pagination->offset + 1 ?>-<?= min($dataProvider->pagination->offset + $dataProvider->pagination->pageSize, $dataProvider->totalCount) ?> 
                        จากทั้งหมด <?= $dataProvider->totalCount ?> รายการ
                    </div>
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->pagination,
                        'options' => ['class' => 'pagination pagination-sm mb-0'],
                        'linkContainerOptions' => ['class' => 'page-item'],
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    ]) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bulk Actions -->
    <div id="bulk-actions" class="card border-0 shadow-sm mt-3" style="display: none;">
        <div class="card-body d-flex align-items-center">
            <span class="me-3">เลือก <span id="selected-count">0</span> รายการ</span>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-success" onclick="bulkAction('activate')">
                    <i class="bi bi-person-check me-1"></i> เปิดใช้งาน
                </button>
                <button type="button" class="btn btn-outline-warning" onclick="bulkAction('suspend')">
                    <i class="bi bi-person-x me-1"></i> ระงับ
                </button>
                <button type="button" class="btn btn-outline-info" onclick="bulkAction('reset-password')">
                    <i class="bi bi-key me-1"></i> รีเซ็ตรหัสผ่าน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Action Confirmation Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalTitle">ยืนยันการดำเนินการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="actionModalBody"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="action-form" method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-primary" id="actionModalBtn">ยืนยัน</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
// Select all
document.getElementById('select-all').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = this.checked;
    }.bind(this));
    updateBulkActions();
});

// Individual checkboxes
document.querySelectorAll('.user-checkbox').forEach(function(cb) {
    cb.addEventListener('change', function() {
        updateBulkActions();
    });
});

function updateBulkActions() {
    var checked = document.querySelectorAll('.user-checkbox:checked').length;
    var bulkActions = document.getElementById('bulk-actions');
    var selectedCount = document.getElementById('selected-count');
    
    if (checked > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = checked;
    } else {
        bulkActions.style.display = 'none';
    }
}

function confirmAction(action, id, username) {
    var modal = document.getElementById('actionModal');
    var title = document.getElementById('actionModalTitle');
    var body = document.getElementById('actionModalBody');
    var btn = document.getElementById('actionModalBtn');
    var form = document.getElementById('action-form');
    
    if (action === 'suspend') {
        title.textContent = 'ยืนยันการระงับผู้ใช้';
        body.textContent = 'คุณต้องการระงับผู้ใช้ "' + username + '" ใช่หรือไม่?';
        btn.className = 'btn btn-warning';
        btn.textContent = 'ระงับผู้ใช้';
        form.action = 'suspend?id=' + id;
    } else if (action === 'activate') {
        title.textContent = 'ยืนยันการเปิดใช้งาน';
        body.textContent = 'คุณต้องการเปิดใช้งานผู้ใช้ "' + username + '" ใช่หรือไม่?';
        btn.className = 'btn btn-success';
        btn.textContent = 'เปิดใช้งาน';
        form.action = 'activate?id=' + id;
    }
    
    var bsModal = new bootstrap.Modal(modal);
    bsModal.show();
}

function bulkAction(action) {
    var ids = [];
    document.querySelectorAll('.user-checkbox:checked').forEach(function(cb) {
        ids.push(cb.value);
    });
    
    if (ids.length === 0) {
        alert('กรุณาเลือกผู้ใช้อย่างน้อย 1 คน');
        return;
    }
    
    if (confirm('คุณต้องการดำเนินการกับผู้ใช้ที่เลือก ' + ids.length + ' คน ใช่หรือไม่?')) {
        window.location.href = 'bulk-action?action=' + action + '&ids=' + ids.join(',');
    }
}
JS;
$this->registerJs($js);
?>
