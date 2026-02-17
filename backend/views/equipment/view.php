<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Equipment $model */
/** @var array $categories */

$this->title = $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการอุปกรณ์', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

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

$categoryIcons = [
    'audio_visual' => 'bi-display',
    'furniture' => 'bi-lamp',
    'it_equipment' => 'bi-pc-display',
    'office_supplies' => 'bi-paperclip',
    'communication' => 'bi-telephone',
    'other' => 'bi-box',
];

// Get category info safely
$categoryCode = $model->category ? $model->category->code : null;
$categoryName = $model->category ? $model->category->name_th : '-';
?>

<div class="equipment-view">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= Url::to(['index']) ?>">จัดการอุปกรณ์</a></li>
                    <li class="breadcrumb-item active"><?= Html::encode($model->equipment_code) ?></li>
                </ol>
            </nav>
            <h1 class="h3 mb-1"><?= Html::encode($model->name_th) ?></h1>
            <?php if ($model->name_en): ?>
                <p class="text-muted mb-0"><?= Html::encode($model->name_en) ?></p>
            <?php endif; ?>
        </div>
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-pencil me-1"></i> แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"></button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><?= Html::a('<i class="bi bi-printer me-1"></i> พิมพ์', '#', ['class' => 'dropdown-item', 'onclick' => 'window.print(); return false;']) ?></li>
                <li><?= Html::a('<i class="bi bi-file-earmark-pdf me-1"></i> ส่งออก PDF', ['export-pdf', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <?= Html::a('<i class="bi bi-trash me-1"></i> ลบอุปกรณ์', ['delete', 'id' => $model->id], [
                        'class' => 'dropdown-item text-danger',
                        'data' => [
                            'confirm' => 'คุณต้องการลบอุปกรณ์นี้?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Image & Basic Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if ($model->hasImage()): ?>
                                <img src="<?= Html::encode($model->imageUrl) ?>" alt="<?= Html::encode($model->name_th) ?>" 
                                     class="img-fluid rounded" style="max-height: 250px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <div class="text-center text-muted">
                                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                                        <span>ไม่มีรูปภาพ</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 140px;">รหัสอุปกรณ์</td>
                                    <td class="fw-medium"><?= Html::encode($model->equipment_code) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">หมวดหมู่</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="bi <?= $categoryIcons[$categoryCode] ?? 'bi-box' ?> me-1"></i>
                                            <?= Html::encode($categoryName) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($model->brand || $model->model): ?>
                                    <tr>
                                        <td class="text-muted">ยี่ห้อ / รุ่น</td>
                                        <td>
                                            <?= Html::encode($model->brand) ?>
                                            <?php if ($model->model): ?>
                                                <span class="text-muted">(<?= Html::encode($model->model) ?>)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($model->serial_number): ?>
                                    <tr>
                                        <td class="text-muted">Serial Number</td>
                                        <td><code><?= Html::encode($model->serial_number) ?></code></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="text-muted">จำนวน</td>
                                    <td>
                                        <span class="fw-medium"><?= $model->quantity ?></span>
                                        <span class="text-muted"><?= Html::encode($model->unit ?: 'ชิ้น') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">สถานะ</td>
                                    <td>
                                        <span class="badge <?= $statusClasses[$model->status] ?? 'bg-secondary' ?> fs-6">
                                            <?= $statusLabels[$model->status] ?? $model->status ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if ($model->description): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-text-paragraph me-2"></i>รายละเอียด</h6>
                    </div>
                    <div class="card-body">
                        <?= nl2br(Html::encode($model->description)) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Specifications -->
            <?php if ($model->specifications): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-list-check me-2"></i>ข้อมูลจำเพาะ</h6>
                    </div>
                    <div class="card-body">
                        <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;"><?= Html::encode($model->specifications) ?></pre>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Installed Rooms -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-door-open me-2"></i>ห้องที่ติดตั้ง</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($model->rooms)): ?>
                        <div class="row g-3">
                            <?php foreach ($model->rooms as $room): ?>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="bi bi-building text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0"><?= Html::encode($room->name_th) ?></h6>
                                            <small class="text-muted">
                                                <?= Html::encode($room->building->name_th ?? '') ?>
                                                <?php if ($room->floor): ?>
                                                    - ชั้น <?= $room->floor ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div>
                                            <?= Html::a('<i class="bi bi-arrow-right"></i>', ['/room/view', 'id' => $room->id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            <p class="mb-0">ยังไม่ได้ติดตั้งในห้องประชุมใดๆ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Maintenance History -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-tools me-2"></i>ประวัติการซ่อมบำรุง</h6>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                        <i class="bi bi-plus-lg me-1"></i> เพิ่ม
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($model->maintenanceLogs)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ประเภท</th>
                                        <th>รายละเอียด</th>
                                        <th>ค่าใช้จ่าย</th>
                                        <th>ผู้ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($model->maintenanceLogs as $log): ?>
                                        <tr>
                                            <td><?= Yii::$app->formatter->asDate($log->date) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $log->type == 'repair' ? 'danger' : 'info' ?>">
                                                    <?= $log->type == 'repair' ? 'ซ่อมแซม' : 'บำรุงรักษา' ?>
                                                </span>
                                            </td>
                                            <td><?= Html::encode($log->description) ?></td>
                                            <td>
                                                <?php if ($log->cost): ?>
                                                    <?= Yii::$app->formatter->asCurrency($log->cost, 'THB') ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= Html::encode($log->performed_by) ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                            <p class="mb-0">ยังไม่มีประวัติการซ่อมบำรุง</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Pricing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-currency-exchange me-2"></i>ข้อมูลราคา</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">ราคาซื้อ</span>
                        <span class="fw-medium">
                            <?php if ($model->purchase_price): ?>
                                <?= Yii::$app->formatter->asCurrency($model->purchase_price, 'THB') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">ค่าเช่า/ชม.</span>
                        <span class="fw-medium">
                            <?php if ($model->rental_rate): ?>
                                <?= Yii::$app->formatter->asCurrency($model->rental_rate, 'THB') ?>
                            <?php else: ?>
                                <span class="text-success">ฟรี</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Purchase & Warranty -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>วันที่สำคัญ</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">วันที่ซื้อ</small>
                        <span><?= $model->purchase_date ? Yii::$app->formatter->asDate($model->purchase_date) : '-' ?></span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">วันหมดประกัน</small>
                        <?php if ($model->warranty_expiry): ?>
                            <?php
                            $warrantyDate = new DateTime($model->warranty_expiry);
                            $today = new DateTime();
                            $isExpired = $warrantyDate < $today;
                            ?>
                            <span class="<?= $isExpired ? 'text-danger' : 'text-success' ?>">
                                <?= Yii::$app->formatter->asDate($model->warranty_expiry) ?>
                                <?php if ($isExpired): ?>
                                    <small>(หมดประกันแล้ว)</small>
                                <?php else: ?>
                                    <small>(ยังมีผล)</small>
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Total Maintenance Cost -->
            <?php if (!empty($model->maintenanceLogs)): ?>
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body text-center">
                        <small class="text-muted d-block mb-1">ค่าซ่อมบำรุงรวม</small>
                        <h4 class="mb-0 text-danger">
                            <?php
                            $totalCost = array_sum(array_column($model->maintenanceLogs, 'cost'));
                            echo Yii::$app->formatter->asCurrency($totalCost, 'THB');
                            ?>
                        </h4>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Notes -->
            <?php if ($model->notes): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-sticky me-2"></i>หมายเหตุ</h6>
                    </div>
                    <div class="card-body">
                        <?= nl2br(Html::encode($model->notes)) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- System Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>ข้อมูลระบบ</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted d-block">สร้างเมื่อ</small>
                        <span><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted d-block">แก้ไขล่าสุด</small>
                        <span><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></span>
                    </div>
                    <?php if ($model->createdBy): ?>
                        <div>
                            <small class="text-muted d-block">สร้างโดย</small>
                            <span><?= Html::encode($model->createdBy->fullname ?? $model->createdBy->username) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มประวัติการซ่อมบำรุง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= Url::to(['add-maintenance', 'id' => $model->id]) ?>" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ประเภท <span class="text-danger">*</span></label>
                        <select name="MaintenanceLog[type]" class="form-select" required>
                            <option value="maintenance">บำรุงรักษาตามกำหนด</option>
                            <option value="repair">ซ่อมแซม</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">วันที่ <span class="text-danger">*</span></label>
                        <input type="date" name="MaintenanceLog[date]" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รายละเอียด <span class="text-danger">*</span></label>
                        <textarea name="MaintenanceLog[description]" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ค่าใช้จ่าย (บาท)</label>
                        <input type="number" name="MaintenanceLog[cost]" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ผู้ดำเนินการ</label>
                        <input type="text" name="MaintenanceLog[performed_by]" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
