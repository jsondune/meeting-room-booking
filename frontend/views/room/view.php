<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Room $model */
/** @var array $equipment */
/** @var array $upcomingBookings */
/** @var array $reviews */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ห้องประชุม'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .room-hero {
        position: relative;
        height: 400px;
        overflow: hidden;
        border-radius: 1rem;
    }
    .room-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        padding: 2rem;
        color: white;
    }
    .gallery-thumb {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 0.5rem;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s;
    }
    .gallery-thumb:hover, .gallery-thumb.active {
        opacity: 1;
        transform: scale(1.05);
    }
    .feature-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .time-slot {
        padding: 0.5rem 1rem;
        border: 2px solid #dee2e6;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
    }
    .time-slot:hover {
        border-color: #0d6efd;
        background: #f8f9ff;
    }
    .time-slot.selected {
        border-color: #0d6efd;
        background: #0d6efd;
        color: white;
    }
    .time-slot.unavailable {
        background: #f8f9fa;
        color: #adb5bd;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .booking-summary {
        position: sticky;
        top: 100px;
    }
    .review-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .rating-stars {
        color: #ffc107;
    }
    .amenity-badge {
        padding: 0.5rem 1rem;
        background: #f8f9fa;
        border-radius: 2rem;
        font-size: 0.875rem;
    }
");
?>

<div class="room-view">
    <!-- Room Hero Section -->
    <div class="room-hero mb-4">
        <?php 
        $primaryImage = $model->primaryImage;
        if ($primaryImage): 
        ?>
            <img src="<?= Html::encode($primaryImage->url) ?>" 
                 alt="<?= Html::encode($model->name) ?>" id="mainImage">
        <?php else: ?>
            <div class="room-placeholder-hero d-flex flex-column align-items-center justify-content-center text-white"
                 style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" id="mainImage">
                <i class="bi bi-door-open" style="font-size: 5rem; opacity: 0.6;"></i>
                <span class="mt-3 fs-4 fw-medium" style="opacity: 0.8;"><?= Html::encode($model->name) ?></span>
            </div>
        <?php endif; ?>
        <div class="room-hero-overlay">
            <div class="d-flex justify-content-between align-items-end">
                <div>
                    <h1 class="mb-2"><?= Html::encode($model->name) ?></h1>
                    <p class="mb-0">
                        <i class="bi bi-geo-alt me-1"></i> <?= Html::encode($model->location) ?>
                        <span class="mx-2">|</span>
                        <i class="bi bi-building me-1"></i> <?= Html::encode($model->building) ?> ชั้น <?= $model->floor ?>
                    </p>
                </div>
                <div class="text-end">
                    <div class="fs-3 fw-bold">
                        <?php if ($model->hourly_rate > 0): ?>
                            ฿<?= number_format($model->hourly_rate) ?><small class="fs-6 fw-normal">/ชั่วโมง</small>
                        <?php else: ?>
                            <span class="badge bg-success fs-5">ฟรี</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Thumbnails -->
    <?php 
    $roomImages = $model->roomImages;
    if (!empty($roomImages)): 
    ?>
    <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
        <?php foreach ($roomImages as $index => $roomImage): ?>
        <img src="<?= Html::encode($roomImage->url) ?>" 
             class="gallery-thumb <?= $index === 0 ? 'active' : '' ?>" onclick="changeMainImage(this)">
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Room Details -->
        <div class="col-lg-8">
            <!-- Quick Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto mb-2">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="fw-bold"><?= $model->capacity ?></div>
                            <small class="text-muted">ความจุ (คน)</small>
                        </div>
                        <div class="col-3">
                            <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto mb-2">
                                <i class="bi bi-aspect-ratio"></i>
                            </div>
                            <div class="fw-bold"><?= $model->area_sqm ?? '-' ?></div>
                            <small class="text-muted">ตร.ม.</small>
                        </div>
                        <div class="col-3">
                            <div class="feature-icon bg-info bg-opacity-10 text-info mx-auto mb-2">
                                <i class="bi bi-star"></i>
                            </div>
                            <div class="fw-bold"><?= number_format($model->average_rating ?? 0, 1) ?></div>
                            <small class="text-muted">คะแนน</small>
                        </div>
                        <div class="col-3">
                            <div class="feature-icon bg-warning bg-opacity-10 text-warning mx-auto mb-2">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="fw-bold"><?= $model->total_bookings ?? 0 ?></div>
                            <small class="text-muted">การจอง</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>รายละเอียดห้อง</h5>
                </div>
                <div class="card-body">
                    <p><?= nl2br(Html::encode($model->description)) ?></p>
                    
                    <hr>
                    
                    <h6 class="mb-3">ประเภทห้อง</h6>
                    <span class="badge bg-primary fs-6 mb-3"><?= Html::encode($model->room_type) ?></span>
                    
                    <h6 class="mb-3">สิ่งอำนวยความสะดวก</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <?php 
                        $amenities = json_decode($model->amenities, true) ?? [];
                        $amenityIcons = [
                            'projector' => ['icon' => 'bi-projector', 'label' => 'โปรเจคเตอร์'],
                            'whiteboard' => ['icon' => 'bi-easel', 'label' => 'ไวท์บอร์ด'],
                            'video_conference' => ['icon' => 'bi-camera-video', 'label' => 'ระบบประชุมทางไกล'],
                            'air_conditioning' => ['icon' => 'bi-snow', 'label' => 'เครื่องปรับอากาศ'],
                            'wifi' => ['icon' => 'bi-wifi', 'label' => 'Wi-Fi'],
                            'sound_system' => ['icon' => 'bi-speaker', 'label' => 'ระบบเสียง'],
                            'tv' => ['icon' => 'bi-tv', 'label' => 'จอทีวี'],
                            'telephone' => ['icon' => 'bi-telephone', 'label' => 'โทรศัพท์'],
                        ];
                        foreach ($amenities as $amenity):
                            $info = $amenityIcons[$amenity] ?? ['icon' => 'bi-check-circle', 'label' => $amenity];
                        ?>
                        <span class="amenity-badge">
                            <i class="bi <?= $info['icon'] ?> me-1"></i><?= $info['label'] ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Equipment Available -->
            <?php if (!empty($equipment)): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>อุปกรณ์ที่สามารถขอใช้เพิ่มเติม</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php foreach ($equipment as $item): ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 border rounded">
                                <div class="me-3">
                                    <i class="bi bi-box text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= Html::encode($item['name']) ?></div>
                                    <small class="text-muted">คงเหลือ: <?= $item['available_quantity'] ?> ชิ้น</small>
                                </div>
                                <div class="text-end">
                                    <?php if ($item['rental_price'] > 0): ?>
                                        <div class="text-primary fw-bold">฿<?= number_format($item['rental_price']) ?></div>
                                        <small class="text-muted">/ชิ้น/ครั้ง</small>
                                    <?php else: ?>
                                        <span class="badge bg-success">ฟรี</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Rules & Regulations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>กฎระเบียบการใช้ห้อง</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>กรุณารักษาความสะอาดของห้องประชุม</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>ห้ามนำอาหารและเครื่องดื่มเข้ามาในห้อง</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>กรุณาปิดไฟและเครื่องปรับอากาศเมื่อใช้งานเสร็จ</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>หากต้องการยกเลิกการจอง กรุณาแจ้งล่วงหน้าอย่างน้อย 24 ชั่วโมง</li>
                        <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>หากอุปกรณ์ชำรุด กรุณาแจ้งเจ้าหน้าที่ทันที</li>
                    </ul>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-chat-left-quote me-2"></i>รีวิวจากผู้ใช้งาน</h5>
                    <span class="badge bg-primary"><?= count($reviews ?? []) ?> รีวิว</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="d-flex mb-4">
                            <div class="me-3">
                                <?php if ($review['user_avatar']): ?>
                                    <img src="<?= $review['user_avatar'] ?>" class="review-avatar" alt="">
                                <?php else: ?>
                                    <div class="review-avatar bg-secondary d-flex align-items-center justify-content-center text-white">
                                        <?= strtoupper(substr($review['user_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <strong><?= Html::encode($review['user_name']) ?></strong>
                                        <div class="rating-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi <?= $i <= $review['rating'] ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <small class="text-muted"><?= Yii::$app->formatter->asRelativeTime($review['created_at']) ?></small>
                                </div>
                                <p class="mb-0"><?= Html::encode($review['comment']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-chat-left fs-1 d-block mb-2"></i>
                            <p class="mb-0">ยังไม่มีรีวิว</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="booking-summary">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-calendar-plus me-2"></i>จองห้องประชุม</h5>
                    </div>
                    <div class="card-body">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-person-lock fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-3">กรุณาเข้าสู่ระบบเพื่อจองห้องประชุม</p>
                                <?= Html::a('<i class="bi bi-box-arrow-in-right me-2"></i>เข้าสู่ระบบ', ['site/login'], ['class' => 'btn btn-primary']) ?>
                            </div>
                        <?php else: ?>
                            <form id="bookingForm">
                                <input type="hidden" name="room_id" value="<?= $model->id ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">วันที่จอง</label>
                                    <input type="date" class="form-control form-control-lg" name="booking_date" 
                                           id="bookingDate" min="<?= date('Y-m-d') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ช่วงเวลา</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">เริ่มต้น</label>
                                            <select class="form-select" name="start_time" id="startTime" required>
                                                <option value="">เลือกเวลา</option>
                                                <?php for ($h = 8; $h <= 17; $h++): ?>
                                                    <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                                                    <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">สิ้นสุด</label>
                                            <select class="form-select" name="end_time" id="endTime" required>
                                                <option value="">เลือกเวลา</option>
                                                <?php for ($h = 8; $h <= 18; $h++): ?>
                                                    <?php if ($h < 18): ?>
                                                    <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
                                                    <?php endif; ?>
                                                    <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">หัวข้อการประชุม</label>
                                    <input type="text" class="form-control" name="title" 
                                           placeholder="ระบุหัวข้อการประชุม" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">จำนวนผู้เข้าร่วม</label>
                                    <input type="number" class="form-control" name="attendees_count" 
                                           min="1" max="<?= $model->capacity ?>" placeholder="จำนวนคน" required>
                                    <small class="text-muted">สูงสุด <?= $model->capacity ?> คน</small>
                                </div>
                                
                                <hr>
                                
                                <!-- Price Summary -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>ค่าห้อง (<span id="durationText">0</span> ชม.)</span>
                                        <span id="roomPrice">฿0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>ค่าอุปกรณ์</span>
                                        <span id="equipmentPrice">฿0</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>รวมทั้งหมด</span>
                                        <span class="text-primary" id="totalPrice">฿0</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-calendar-check me-2"></i>ดำเนินการจอง
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>การจองที่กำลังจะมาถึง</h6>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($upcomingBookings)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach (array_slice($upcomingBookings, 0, 5) as $booking): ?>
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-bold"><?= Yii::$app->formatter->asDate($booking['booking_date'], 'php:d M Y') ?></div>
                                            <small class="text-muted">
                                                <?= substr($booking['start_time'], 0, 5) ?> - <?= substr($booking['end_time'], 0, 5) ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-danger">จองแล้ว</span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-calendar-x d-block mb-2"></i>
                                <small>ไม่มีการจองในขณะนี้</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$hourlyRate = $model->hourly_rate ?? 0;
$csrfToken = Yii::$app->request->csrfToken;
$checkAvailabilityUrl = Url::to(['booking/check-availability']);
$createBookingUrl = Url::to(['booking/create']);

$this->registerJs(<<<JS
// Change main image
function changeMainImage(thumb) {
    document.getElementById('mainImage').src = thumb.src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}
window.changeMainImage = changeMainImage;

// Calculate price
const hourlyRate = {$hourlyRate};

function calculatePrice() {
    const startTime = document.getElementById('startTime').value;
    const endTime = document.getElementById('endTime').value;
    
    if (startTime && endTime) {
        const start = new Date('2000-01-01 ' + startTime);
        const end = new Date('2000-01-01 ' + endTime);
        const hours = (end - start) / (1000 * 60 * 60);
        
        if (hours > 0) {
            document.getElementById('durationText').textContent = hours;
            const roomTotal = hours * hourlyRate;
            document.getElementById('roomPrice').textContent = '฿' + roomTotal.toLocaleString();
            document.getElementById('totalPrice').textContent = '฿' + roomTotal.toLocaleString();
        }
    }
}

document.getElementById('startTime')?.addEventListener('change', calculatePrice);
document.getElementById('endTime')?.addEventListener('change', calculatePrice);

// Check availability on date change
document.getElementById('bookingDate')?.addEventListener('change', function() {
    const date = this.value;
    if (date) {
        // Could add AJAX call to check availability for the selected date
    }
});

// Submit booking form
document.getElementById('bookingForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('_csrf', '{$csrfToken}');
    
    fetch('{$createBookingUrl}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
        }
    })
    .catch(error => {
        alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง');
    });
});
JS);
?>
