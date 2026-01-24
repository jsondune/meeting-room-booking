<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $buildings */
/** @var array $roomTypes */
/** @var array $filters */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\MeetingRoom;

$this->title = 'ห้องประชุม';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="room-index">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 90px;">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-filter text-primary me-2"></i> กรองผลลัพธ์
                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?= Url::to(['/room/index']) ?>" method="get" id="filter-form">
                        <!-- Date & Time -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">วันที่</label>
                            <input type="date" name="date" class="form-control" 
                                   value="<?= Html::encode($filters['date']) ?>" 
                                   min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">เริ่ม</label>
                                <select name="start_time" class="form-select form-select-sm">
                                    <option value="">- เลือก -</option>
                                    <?php for ($h = 7; $h <= 20; $h++): ?>
                                        <?php for ($m = 0; $m < 60; $m += 30): ?>
                                            <?php $time = sprintf('%02d:%02d', $h, $m); ?>
                                            <option value="<?= $time ?>" 
                                                <?= $filters['start_time'] === $time ? 'selected' : '' ?>>
                                                <?= $time ?>
                                            </option>
                                        <?php endfor; ?>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label">สิ้นสุด</label>
                                <select name="end_time" class="form-select form-select-sm">
                                    <option value="">- เลือก -</option>
                                    <?php for ($h = 8; $h <= 21; $h++): ?>
                                        <?php for ($m = 0; $m < 60; $m += 30): ?>
                                            <?php $time = sprintf('%02d:%02d', $h, $m); ?>
                                            <option value="<?= $time ?>" 
                                                <?= $filters['end_time'] === $time ? 'selected' : '' ?>>
                                                <?= $time ?>
                                            </option>
                                        <?php endfor; ?>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Building -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">อาคาร</label>
                            <select name="building_id" class="form-select">
                                <option value="">ทุกอาคาร</option>
                                <?php foreach ($buildings as $id => $name): ?>
                                    <option value="<?= $id ?>" 
                                        <?= $filters['building_id'] == $id ? 'selected' : '' ?>>
                                        <?= Html::encode($name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Room Type -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">ประเภทห้อง</label>
                            <select name="room_type" class="form-select">
                                <option value="">ทุกประเภท</option>
                                <?php foreach ($roomTypes as $type => $label): ?>
                                    <option value="<?= $type ?>" 
                                        <?= $filters['room_type'] === $type ? 'selected' : '' ?>>
                                        <?= Html::encode($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Capacity -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">ความจุ (คน)</label>
                            <select name="capacity" class="form-select">
                                <option value="">ไม่จำกัด</option>
                                <option value="5" <?= $filters['capacity'] == 5 ? 'selected' : '' ?>>5+ คน</option>
                                <option value="10" <?= $filters['capacity'] == 10 ? 'selected' : '' ?>>10+ คน</option>
                                <option value="20" <?= $filters['capacity'] == 20 ? 'selected' : '' ?>>20+ คน</option>
                                <option value="30" <?= $filters['capacity'] == 30 ? 'selected' : '' ?>>30+ คน</option>
                                <option value="50" <?= $filters['capacity'] == 50 ? 'selected' : '' ?>>50+ คน</option>
                                <option value="100" <?= $filters['capacity'] == 100 ? 'selected' : '' ?>>100+ คน</option>
                            </select>
                        </div>

                        <!-- Features -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">สิ่งอำนวยความสะดวก</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_projector" value="1" 
                                       id="has_projector" <?= $filters['has_projector'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_projector">
                                    <i class="fas fa-projector text-muted me-1"></i> โปรเจคเตอร์
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_video_conference" value="1" 
                                       id="has_video_conference" <?= $filters['has_video_conference'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_video_conference">
                                    <i class="fas fa-video text-muted me-1"></i> Video Conference
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_whiteboard" value="1" 
                                       id="has_whiteboard" <?= $filters['has_whiteboard'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_whiteboard">
                                    <i class="fas fa-chalkboard text-muted me-1"></i> ไวท์บอร์ด
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="has_wifi" value="1" 
                                       id="has_wifi" <?= $filters['has_wifi'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_wifi">
                                    <i class="fas fa-wifi text-muted me-1"></i> WiFi
                                </label>
                            </div>
                        </div>

                        <!-- Search Keyword -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">ค้นหา</label>
                            <input type="text" name="keyword" class="form-control" 
                                   placeholder="ชื่อห้อง, รหัส..." 
                                   value="<?= Html::encode($filters['keyword']) ?>">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> ค้นหา
                            </button>
                            <a href="<?= Url::to(['/room/index']) ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> ล้างตัวกรอง
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Room Listing -->
        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">ห้องประชุมทั้งหมด</h4>
                    <p class="text-muted mb-0">
                        พบ <?= $dataProvider->getTotalCount() ?> ห้อง
                    </p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active" id="grid-view-btn">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="list-view-btn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Room Cards -->
            <?php if ($dataProvider->getCount() > 0): ?>
                <div class="row g-4" id="room-grid">
                    <?php foreach ($dataProvider->getModels() as $room): ?>
                        <div class="col-md-6 col-xl-4 room-item">
                            <div class="card h-100 border-0 shadow-sm room-card">
                                <div class="room-image position-relative">
                                    <?php 
                                    $primaryImage = $room->getPrimaryImage();
                                    if ($primaryImage): 
                                    ?>
                                        <img src="<?= Html::encode($primaryImage->getUrl()) ?>" 
                                             class="card-img-top" 
                                             alt="<?= Html::encode($room->name_th) ?>"
                                             style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="room-placeholder d-flex flex-column align-items-center justify-content-center text-white"
                                             style="height: 180px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bi bi-door-open" style="font-size: 3rem; opacity: 0.8;"></i>
                                            <span class="mt-2 fw-medium" style="opacity: 0.9;"><?= Html::encode($room->name_th) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <span class="badge bg-primary position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                                        <?= Html::encode($room->room_code) ?>
                                    </span>
                                    
                                    <?php if ($room->requires_approval): ?>
                                        <span class="badge bg-warning text-dark position-absolute" style="top: 10px; right: 10px; z-index: 10;" title="ต้องได้รับอนุมัติ">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-2">
                                        <?= Html::encode($room->name_th) ?>
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-building me-1"></i> 
                                        <?= Html::encode($room->building->name_th ?? '-') ?>
                                        <?php if ($room->floor): ?>
                                            <span class="mx-1">•</span> ชั้น <?= $room->floor ?>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-3">
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-users me-1"></i> <?= $room->capacity ?> คน
                                        </span>
                                        <?php if ($room->has_projector): ?>
                                            <span class="badge bg-light text-dark" title="โปรเจคเตอร์">
                                                <i class="fas fa-projector"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($room->has_video_conference): ?>
                                            <span class="badge bg-light text-dark" title="Video Conference">
                                                <i class="fas fa-video"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($room->has_wifi): ?>
                                            <span class="badge bg-light text-dark" title="WiFi">
                                                <i class="fas fa-wifi"></i>
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($room->has_whiteboard): ?>
                                            <span class="badge bg-light text-dark" title="ไวท์บอร์ด">
                                                <i class="fas fa-chalkboard"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="room-price">
                                            <?php if ($room->hourly_rate > 0): ?>
                                                <span class="fw-bold text-primary">
                                                    <?= number_format($room->hourly_rate) ?>
                                                </span>
                                                <span class="text-muted small">บาท/ชม.</span>
                                            <?php else: ?>
                                                <span class="text-success fw-bold">ฟรี</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-white border-top-0 pt-0">
                                    <div class="d-grid gap-2">
                                        <a href="<?= Url::to(['/room/view', 'id' => $room->id]) ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                        </a>
                                        <?php if (!Yii::$app->user->isGuest): ?>
                                            <a href="<?= Url::to(['/booking/create', 'room_id' => $room->id]) ?>" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-calendar-plus me-1"></i> จอง
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    <?= LinkPager::widget([
                        'pagination' => $dataProvider->getPagination(),
                        'options' => ['class' => 'pagination'],
                        'linkContainerOptions' => ['class' => 'page-item'],
                        'linkOptions' => ['class' => 'page-link'],
                        'disabledListItemSubTagOptions' => ['class' => 'page-link'],
                    ]) ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-4"></i>
                    <h5 class="text-muted">ไม่พบห้องประชุมที่ตรงกับเงื่อนไข</h5>
                    <p class="text-muted mb-4">ลองเปลี่ยนเงื่อนไขการค้นหาใหม่</p>
                    <a href="<?= Url::to(['/room/index']) ?>" class="btn btn-outline-primary">
                        <i class="fas fa-redo me-1"></i> ดูห้องทั้งหมด
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.room-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.room-image img {
    transition: transform 0.3s;
}

.room-card:hover .room-image img {
    transform: scale(1.05);
}

.room-image {
    overflow: hidden;
}

/* List view styles */
.list-view .room-item {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
}

.list-view .room-card {
    flex-direction: row;
}

.list-view .room-image {
    width: 200px;
    flex-shrink: 0;
}

.list-view .room-image img {
    height: 100% !important;
    min-height: 150px;
    border-radius: 0.5rem 0 0 0.5rem;
}

.list-view .card-body {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.list-view .card-footer {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-radius: 0 0.5rem 0.5rem 0;
}

.list-view .card-footer .d-grid {
    display: flex !important;
    flex-direction: row !important;
    gap: 0.5rem !important;
}

@media (max-width: 767.98px) {
    .list-view .room-card {
        flex-direction: column;
    }
    
    .list-view .room-image {
        width: 100%;
    }
    
    .list-view .room-image img {
        border-radius: 0.5rem 0.5rem 0 0;
        height: 180px !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridBtn = document.getElementById('grid-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    const roomGrid = document.getElementById('room-grid');
    
    gridBtn.addEventListener('click', function() {
        roomGrid.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('room-view', 'grid');
    });
    
    listBtn.addEventListener('click', function() {
        roomGrid.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('room-view', 'list');
    });
    
    // Restore view preference
    const savedView = localStorage.getItem('room-view');
    if (savedView === 'list') {
        listBtn.click();
    }
});
</script>
