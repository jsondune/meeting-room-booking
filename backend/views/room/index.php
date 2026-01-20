<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\MeetingRoom $searchModel */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Building;

$this->title = 'จัดการห้องประชุม';
?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1 class="page-title">จัดการห้องประชุม</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">ห้องประชุม</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= Url::to(['room/create']) ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มห้องประชุม
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <i class="bi bi-door-open me-2"></i>รายการห้องประชุม
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-md-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                        <i class="bi bi-funnel me-1"></i>ตัวกรอง
                    </button>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary active" data-view="table" title="มุมมองตาราง">
                            <i class="bi bi-list"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" data-view="grid" title="มุมมองการ์ด">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Panel -->
    <div class="collapse border-bottom" id="filterPanel">
        <div class="card-body bg-light">
            <?= Html::beginForm(['room/index'], 'get', ['class' => 'row g-3']) ?>
                <div class="col-md-3">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" name="RoomSearch[keyword]" class="form-control form-control-sm" 
                           placeholder="รหัส, ชื่อห้อง..." value="<?= Html::encode($searchModel->keyword ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">อาคาร</label>
                    <?= Html::dropDownList('RoomSearch[building_id]', $searchModel->building_id ?? '', 
                        ['' => '-- ทั้งหมด --'] + Building::getDropdownList(),
                        ['class' => 'form-select form-select-sm']) ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">ประเภท</label>
                    <?= Html::dropDownList('RoomSearch[room_type]', $searchModel->room_type ?? '', [
                        '' => '-- ทั้งหมด --',
                        'conference' => 'ห้องประชุม',
                        'training' => 'ห้องฝึกอบรม',
                        'boardroom' => 'ห้องประชุมคณะกรรมการ',
                        'huddle' => 'ห้องประชุมขนาดเล็ก',
                        'auditorium' => 'หอประชุม'
                    ], ['class' => 'form-select form-select-sm']) ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">สถานะ</label>
                    <?= Html::dropDownList('RoomSearch[status]', $searchModel->status ?? '', [
                        '' => '-- ทั้งหมด --',
                        '1' => 'เปิดใช้งาน',
                        '0' => 'ปิดใช้งาน',
                        '2' => 'อยู่ระหว่างซ่อมบำรุง'
                    ], ['class' => 'form-select form-select-sm']) ?>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="<?= Url::to(['room/index']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg me-1"></i>ล้าง
                    </a>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
    
    <div class="card-body p-0">
        <!-- Table View -->
        <div id="tableView">
            <?php Pjax::begin(['id' => 'room-grid']); ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 80px;">รูปภาพ</th>
                            <th>รหัส/ชื่อห้อง</th>
                            <th>อาคาร/ชั้น</th>
                            <th>ประเภท</th>
                            <th class="text-center">ความจุ</th>
                            <th>สิ่งอำนวยความสะดวก</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center" style="width: 120px;">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataProvider->getModels() as $room): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $primaryImage = $room->getPrimaryImage();
                                    if ($primaryImage): 
                                    ?>
                                        <img src="<?= $primaryImage->getUrl() ?>" alt="<?= Html::encode($room->name_th) ?>" 
                                             class="rounded" style="width: 60px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 45px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold">
                                        <a href="<?= Url::to(['room/view', 'id' => $room->id]) ?>" class="text-decoration-none">
                                            <?= Html::encode($room->room_code) ?>
                                        </a>
                                    </div>
                                    <div class="text-muted small"><?= Html::encode($room->name_th) ?></div>
                                    <?php if ($room->name_en): ?>
                                        <div class="text-muted small fst-italic"><?= Html::encode($room->name_en) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div><?= Html::encode($room->building->name_th ?? '-') ?></div>
                                    <small class="text-muted">ชั้น <?= Html::encode($room->floor) ?></small>
                                </td>
                                <td>
                                    <?php
                                    $typeLabels = [
                                        'conference' => ['text' => 'ห้องประชุม', 'class' => 'bg-primary'],
                                        'training' => ['text' => 'ห้องฝึกอบรม', 'class' => 'bg-success'],
                                        'boardroom' => ['text' => 'ห้องคณะกรรมการ', 'class' => 'bg-info'],
                                        'huddle' => ['text' => 'ห้องประชุมเล็ก', 'class' => 'bg-secondary'],
                                        'auditorium' => ['text' => 'หอประชุม', 'class' => 'bg-warning'],
                                    ];
                                    $type = $typeLabels[$room->room_type] ?? ['text' => $room->room_type, 'class' => 'bg-secondary'];
                                    ?>
                                    <span class="badge <?= $type['class'] ?>"><?= $type['text'] ?></span>
                                </td>
                                <td class="text-center">
                                    <i class="bi bi-people me-1"></i><?= $room->capacity ?> คน
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if ($room->has_projector): ?>
                                            <span class="badge bg-light text-dark" title="โปรเจคเตอร์"><i class="bi bi-projector"></i></span>
                                        <?php endif; ?>
                                        <?php if ($room->has_video_conference): ?>
                                            <span class="badge bg-light text-dark" title="Video Conference"><i class="bi bi-camera-video"></i></span>
                                        <?php endif; ?>
                                        <?php if ($room->has_whiteboard): ?>
                                            <span class="badge bg-light text-dark" title="ไวท์บอร์ด"><i class="bi bi-easel"></i></span>
                                        <?php endif; ?>
                                        <?php if ($room->has_wifi): ?>
                                            <span class="badge bg-light text-dark" title="WiFi"><i class="bi bi-wifi"></i></span>
                                        <?php endif; ?>
                                        <?php if ($room->has_air_conditioning): ?>
                                            <span class="badge bg-light text-dark" title="แอร์"><i class="bi bi-snow"></i></span>
                                        <?php endif; ?>
                                        <?php if ($room->has_audio_system): ?>
                                            <span class="badge bg-light text-dark" title="ระบบเสียง"><i class="bi bi-speaker"></i></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $statusLabels = [
                                        0 => ['text' => 'ปิดใช้งาน', 'class' => 'cancelled'],
                                        1 => ['text' => 'เปิดใช้งาน', 'class' => 'approved'],
                                        2 => ['text' => 'ซ่อมบำรุง', 'class' => 'pending'],
                                    ];
                                    $status = $statusLabels[$room->status] ?? ['text' => '-', 'class' => 'secondary'];
                                    ?>
                                    <span class="status-badge <?= $status['class'] ?>"><?= $status['text'] ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= Url::to(['room/view', 'id' => $room->id]) ?>" 
                                           class="btn btn-outline-secondary" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= Url::to(['room/update', 'id' => $room->id]) ?>" 
                                           class="btn btn-outline-primary" title="แก้ไข">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-delete" 
                                                data-id="<?= $room->id ?>" data-name="<?= Html::encode($room->name_th) ?>" title="ลบ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($dataProvider->getModels())): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    ไม่พบข้อมูลห้องประชุม
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($dataProvider->getTotalCount() > 0): ?>
            <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="text-muted small">
                    แสดง <?= $dataProvider->getCount() ?> จาก <?= $dataProvider->getTotalCount() ?> รายการ
                </div>
                <nav>
                    <?= \yii\widgets\LinkPager::widget([
                        'pagination' => $dataProvider->getPagination(),
                        'options' => ['class' => 'pagination pagination-sm mb-0'],
                        'linkContainerOptions' => ['class' => 'page-item'],
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    ]) ?>
                </nav>
            </div>
            <?php endif; ?>
            <?php Pjax::end(); ?>
        </div>
        
        <!-- Grid View -->
        <div id="gridView" class="d-none">
            <div class="row g-3 p-3">
                <?php foreach ($dataProvider->getModels() as $room): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 shadow-sm">
                            <?php 
                            $primaryImage = $room->getPrimaryImage();
                            if ($primaryImage): 
                            ?>
                                <img src="<?= $primaryImage->getUrl() ?>" alt="<?= Html::encode($room->name_th) ?>" 
                                     class="card-img-top" style="height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 180px;">
                                    <i class="bi bi-image text-muted fs-1"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">
                                        <a href="<?= Url::to(['room/view', 'id' => $room->id]) ?>" class="text-decoration-none">
                                            <?= Html::encode($room->name_th) ?>
                                        </a>
                                    </h6>
                                    <?php
                                    $statusLabels = [
                                        0 => ['text' => 'ปิด', 'class' => 'cancelled'],
                                        1 => ['text' => 'เปิด', 'class' => 'approved'],
                                        2 => ['text' => 'ซ่อม', 'class' => 'pending'],
                                    ];
                                    $status = $statusLabels[$room->status] ?? ['text' => '-', 'class' => 'secondary'];
                                    ?>
                                    <span class="status-badge <?= $status['class'] ?>"><?= $status['text'] ?></span>
                                </div>
                                <p class="card-text small text-muted mb-2">
                                    <i class="bi bi-geo-alt me-1"></i><?= Html::encode($room->building->name_th ?? '-') ?> ชั้น <?= $room->floor ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small"><i class="bi bi-people me-1"></i><?= $room->capacity ?> คน</span>
                                    <span class="badge bg-light text-dark"><?= Html::encode($room->room_code) ?></span>
                                </div>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php if ($room->has_projector): ?>
                                        <span class="badge bg-light text-dark"><i class="bi bi-projector"></i></span>
                                    <?php endif; ?>
                                    <?php if ($room->has_video_conference): ?>
                                        <span class="badge bg-light text-dark"><i class="bi bi-camera-video"></i></span>
                                    <?php endif; ?>
                                    <?php if ($room->has_wifi): ?>
                                        <span class="badge bg-light text-dark"><i class="bi bi-wifi"></i></span>
                                    <?php endif; ?>
                                    <?php if ($room->has_air_conditioning): ?>
                                        <span class="badge bg-light text-dark"><i class="bi bi-snow"></i></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-footer bg-white d-flex gap-2">
                                <a href="<?= Url::to(['room/view', 'id' => $room->id]) ?>" class="btn btn-sm btn-outline-secondary flex-fill">
                                    <i class="bi bi-eye"></i> ดู
                                </a>
                                <a href="<?= Url::to(['room/update', 'id' => $room->id]) ?>" class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-pencil"></i> แก้ไข
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                <p class="mt-3 mb-0">คุณต้องการลบห้องประชุม <strong id="deleteRoomName"></strong> หรือไม่?</p>
                <p class="text-muted small">การดำเนินการนี้ไม่สามารถยกเลิกได้</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="post" style="display: inline;">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>ลบห้องประชุม
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    
    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('[data-view]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (this.dataset.view === 'grid') {
                tableView.classList.add('d-none');
                gridView.classList.remove('d-none');
            } else {
                tableView.classList.remove('d-none');
                gridView.classList.add('d-none');
            }
        });
    });
    
    // Delete confirmation
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            document.getElementById('deleteRoomName').textContent = name;
            document.getElementById('deleteForm').action = '<?= Url::to(['room/delete']) ?>?id=' + id;
            
            deleteModal.show();
        });
    });
});
</script>
