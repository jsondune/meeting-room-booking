<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\Equipment $searchModel */
/** @var array $categories */

$this->title = 'จัดการอุปกรณ์';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="equipment-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">จัดการอุปกรณ์และสิ่งอำนวยความสะดวกในห้องประชุม</p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i> เพิ่มอุปกรณ์', ['create'], ['class' => 'btn btn-primary']) ?>
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
                                <i class="bi bi-box-seam text-primary fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['total'] ?? 0 ?></h3>
                            <small class="text-muted">อุปกรณ์ทั้งหมด</small>
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
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['available'] ?? 0 ?></h3>
                            <small class="text-muted">พร้อมใช้งาน</small>
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
                                <i class="bi bi-tools text-warning fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['maintenance'] ?? 0 ?></h3>
                            <small class="text-muted">ซ่อมบำรุง</small>
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
                                <i class="bi bi-door-open text-info fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $stats['in_rooms'] ?? 0 ?></h3>
                            <small class="text-muted">ติดตั้งในห้อง</small>
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
                            <input type="text" name="search" class="form-control" placeholder="ชื่อ, รหัส, รุ่น..." value="<?= Html::encode($searchModel->search ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">หมวดหมู่</label>
                            <select name="category" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <?php foreach ($categories as $key => $label): ?>
                                    <option value="<?= $key ?>" <?= ($searchModel->category ?? '') == $key ? 'selected' : '' ?>><?= Html::encode($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="available" <?= ($searchModel->status ?? '') == 'available' ? 'selected' : '' ?>>พร้อมใช้งาน</option>
                                <option value="in_use" <?= ($searchModel->status ?? '') == 'in_use' ? 'selected' : '' ?>>กำลังใช้งาน</option>
                                <option value="maintenance" <?= ($searchModel->status ?? '') == 'maintenance' ? 'selected' : '' ?>>ซ่อมบำรุง</option>
                                <option value="retired" <?= ($searchModel->status ?? '') == 'retired' ? 'selected' : '' ?>>ปลดระวาง</option>
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

    <!-- Equipment Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 80px;">รูปภาพ</th>
                            <th>รหัส / ชื่ออุปกรณ์</th>
                            <th>หมวดหมู่</th>
                            <th>ยี่ห้อ / รุ่น</th>
                            <th class="text-center">จำนวน</th>
                            <th>ห้องที่ติดตั้ง</th>
                            <th>สถานะ</th>
                            <th style="width: 120px;">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataProvider->models)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        ไม่พบข้อมูลอุปกรณ์
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataProvider->models as $model): ?>
                                <tr>
                                    <td>
                                        <?php if ($model->image): ?>
                                            <img src="<?= $model->image ?>" alt="<?= Html::encode($model->name_th) ?>" 
                                                 class="rounded" style="width: 60px; height: 45px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 60px; height: 45px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-medium"><?= Html::encode($model->name_th) ?></div>
                                        <small class="text-muted"><?= Html::encode($model->equipment_code) ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        $categoryIcons = [
                                            'audio_visual' => 'bi-display',
                                            'furniture' => 'bi-lamp',
                                            'it_equipment' => 'bi-pc-display',
                                            'office_supplies' => 'bi-paperclip',
                                            'communication' => 'bi-telephone',
                                            'other' => 'bi-box',
                                        ];
                                        $icon = $categoryIcons[$model->category] ?? 'bi-box';
                                        ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi <?= $icon ?> me-1"></i>
                                            <?= Html::encode($categories[$model->category] ?? $model->category) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($model->brand || $model->model): ?>
                                            <div><?= Html::encode($model->brand) ?></div>
                                            <small class="text-muted"><?= Html::encode($model->model) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium"><?= $model->quantity ?></span>
                                        <small class="text-muted">ชิ้น</small>
                                    </td>
                                    <td>
                                        <?php if (!empty($model->rooms)): ?>
                                            <?php foreach (array_slice($model->rooms, 0, 2) as $room): ?>
                                                <span class="badge bg-info bg-opacity-10 text-info mb-1">
                                                    <?= Html::encode($room->name_th) ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($model->rooms) > 2): ?>
                                                <span class="badge bg-secondary">+<?= count($model->rooms) - 2 ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClasses = [
                                            'available' => 'bg-success',
                                            'in_use' => 'bg-primary',
                                            'maintenance' => 'bg-warning text-dark',
                                            'retired' => 'bg-secondary',
                                        ];
                                        $statusLabels = [
                                            'available' => 'พร้อมใช้งาน',
                                            'in_use' => 'กำลังใช้งาน',
                                            'maintenance' => 'ซ่อมบำรุง',
                                            'retired' => 'ปลดระวาง',
                                        ];
                                        ?>
                                        <span class="badge <?= $statusClasses[$model->status] ?? 'bg-secondary' ?>">
                                            <?= $statusLabels[$model->status] ?? $model->status ?>
                                        </span>
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
                                                    onclick="confirmDelete(<?= $model->id ?>, '<?= Html::encode($model->name_th) ?>')"
                                                    title="ลบ">
                                                <i class="bi bi-trash"></i>
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
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบอุปกรณ์ "<span id="delete-name" class="fw-medium"></span>" ใช่หรือไม่?</p>
                <p class="text-danger mb-0"><small>การดำเนินการนี้ไม่สามารถย้อนกลับได้</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="delete-form" method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> ยืนยันการลบ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
function confirmDelete(id, name) {
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-form').action = 'delete?id=' + id;
    var modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
JS;
$this->registerJs($js);
?>
