<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $bookings */
/** @var array $stats */

$this->title = Yii::t('app', 'การจองของฉัน');
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .booking-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .booking-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1) !important;
    }
    .booking-card.status-pending { border-left-color: #ffc107; }
    .booking-card.status-approved { border-left-color: #198754; }
    .booking-card.status-rejected { border-left-color: #dc3545; }
    .booking-card.status-cancelled { border-left-color: #6c757d; }
    .booking-card.status-completed { border-left-color: #0dcaf0; }
    
    .stat-card {
        border-radius: 1rem;
        overflow: hidden;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .room-thumb {
        width: 120px;
        height: 90px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    .filter-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .filter-tabs .nav-link:hover {
        color: #0d6efd;
    }
    .filter-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }
    .empty-state {
        padding: 4rem 2rem;
    }
    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
    }
");
?>

<div class="booking-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><?= Html::encode($this->title) ?></h2>
            <p class="text-muted mb-0">จัดการและติดตามการจองห้องประชุมของคุณ</p>
        </div>
        <?= Html::a('<i class="bi bi-plus-lg me-2"></i>จองห้องใหม่', ['room/index'], ['class' => 'btn btn-primary btn-lg']) ?>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $stats['total'] ?? 0 ?></div>
                            <div class="text-muted">การจองทั้งหมด</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $stats['pending'] ?? 0 ?></div>
                            <div class="text-muted">รออนุมัติ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $stats['upcoming'] ?? 0 ?></div>
                            <div class="text-muted">กำลังจะมาถึง</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div>
                            <div class="fs-3 fw-bold"><?= $stats['completed'] ?? 0 ?></div>
                            <div class="text-muted">เสร็จสิ้น</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-0">
            <ul class="nav filter-tabs" id="bookingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                        ทั้งหมด <span class="badge bg-secondary ms-1"><?= $stats['total'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                        กำลังจะมาถึง <span class="badge bg-success ms-1"><?= $stats['upcoming'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button">
                        รออนุมัติ <span class="badge bg-warning ms-1"><?= $stats['pending'] ?? 0 ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button">
                        ที่ผ่านมา
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <!-- Search & Sort -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="searchBookings" placeholder="ค้นหาการจอง...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortBookings">
                        <option value="date_desc">วันที่ล่าสุด</option>
                        <option value="date_asc">วันที่เก่าสุด</option>
                        <option value="room">ชื่อห้อง</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterRoom">
                        <option value="">ห้องทั้งหมด</option>
                        <!-- Populated dynamically -->
                    </select>
                </div>
            </div>

            <!-- Booking List -->
            <div class="tab-content" id="bookingTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel">
                    <?php if (!empty($bookings)): ?>
                        <div class="booking-list">
                            <?php foreach ($bookings as $booking): ?>
                            <div class="card booking-card status-<?= $booking['status'] ?> shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <?php if (!empty($booking['room_image'])): ?>
                                                <img src="<?= Html::encode($booking['room_image']) ?>" 
                                                     class="room-thumb" alt="">
                                            <?php else: ?>
                                                <div class="room-thumb d-flex flex-column align-items-center justify-content-center text-white"
                                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem;">
                                                    <i class="bi bi-door-open" style="font-size: 1.5rem; opacity: 0.8;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="mb-1"><?= Html::encode($booking['title']) ?></h5>
                                                    <p class="text-muted mb-0">
                                                        <i class="bi bi-door-open me-1"></i><?= Html::encode($booking['room_name']) ?>
                                                        <span class="mx-2">|</span>
                                                        <i class="bi bi-geo-alt me-1"></i><?= Html::encode($booking['room_location']) ?>
                                                    </p>
                                                </div>
                                                <?php
                                                $statusLabels = [
                                                    'pending' => ['class' => 'warning', 'text' => 'รออนุมัติ'],
                                                    'approved' => ['class' => 'success', 'text' => 'อนุมัติแล้ว'],
                                                    'rejected' => ['class' => 'danger', 'text' => 'ไม่อนุมัติ'],
                                                    'cancelled' => ['class' => 'secondary', 'text' => 'ยกเลิก'],
                                                    'completed' => ['class' => 'info', 'text' => 'เสร็จสิ้น'],
                                                ];
                                                $status = $statusLabels[$booking['status']] ?? ['class' => 'secondary', 'text' => $booking['status']];
                                                ?>
                                                <span class="badge bg-<?= $status['class'] ?> fs-6"><?= $status['text'] ?></span>
                                            </div>
                                            
                                            <div class="row g-3 mt-2">
                                                <div class="col-auto">
                                                    <div class="d-flex align-items-center text-muted">
                                                        <i class="bi bi-calendar-event me-2"></i>
                                                        <span><?= Yii::$app->formatter->asDate($booking['booking_date'], 'php:d M Y') ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="d-flex align-items-center text-muted">
                                                        <i class="bi bi-clock me-2"></i>
                                                        <span><?= substr($booking['start_time'], 0, 5) ?> - <?= substr($booking['end_time'], 0, 5) ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="d-flex align-items-center text-muted">
                                                        <i class="bi bi-people me-2"></i>
                                                        <span><?= $booking['attendees_count'] ?> คน</span>
                                                    </div>
                                                </div>
                                                <?php if ($booking['total_price'] > 0): ?>
                                                <div class="col-auto">
                                                    <div class="d-flex align-items-center text-primary fw-bold">
                                                        <i class="bi bi-currency-dollar me-2"></i>
                                                        <span>฿<?= number_format($booking['total_price']) ?></span>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group-vertical">
                                                <?= Html::a('<i class="bi bi-eye"></i>', ['booking/view', 'id' => $booking['id']], [
                                                    'class' => 'btn btn-outline-primary btn-sm',
                                                    'title' => 'ดูรายละเอียด'
                                                ]) ?>
                                                <?php if (in_array($booking['status'], ['pending', 'approved']) && strtotime($booking['booking_date']) > time()): ?>
                                                    <button type="button" class="btn btn-outline-danger btn-sm" 
                                                            onclick="cancelBooking(<?= $booking['id'] ?>)" title="ยกเลิกการจอง">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($booking['status'] === 'completed' && !$booking['has_review']): ?>
                                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                                            onclick="reviewBooking(<?= $booking['id'] ?>)" title="ให้คะแนน">
                                                        <i class="bi bi-star"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state text-center">
                            <i class="bi bi-calendar-x d-block mb-3"></i>
                            <h5>ไม่พบการจอง</h5>
                            <p class="text-muted mb-4">คุณยังไม่มีการจองห้องประชุม เริ่มต้นจองห้องแรกของคุณได้เลย!</p>
                            <?= Html::a('<i class="bi bi-plus-lg me-2"></i>ค้นหาและจองห้อง', ['room/index'], ['class' => 'btn btn-primary']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="tab-pane fade" id="upcoming" role="tabpanel">
                    <!-- Content loaded dynamically or filtered from main list -->
                </div>
                
                <div class="tab-pane fade" id="pending" role="tabpanel">
                    <!-- Content loaded dynamically or filtered from main list -->
                </div>
                
                <div class="tab-pane fade" id="past" role="tabpanel">
                    <!-- Content loaded dynamically or filtered from main list -->
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar View Toggle -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>มุมมองปฏิทิน</h5>
            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#calendarView">
                <i class="bi bi-arrows-expand me-1"></i>แสดง/ซ่อน
            </button>
        </div>
        <div class="collapse" id="calendarView">
            <div class="card-body">
                <div id="bookingCalendar" style="min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-warning me-2"></i>ยืนยันการยกเลิก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ว่าต้องการยกเลิกการจองนี้?</p>
                <div class="mb-3">
                    <label class="form-label">เหตุผลในการยกเลิก</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="ระบุเหตุผล (ไม่บังคับ)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">ยืนยันยกเลิก</button>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-star text-warning me-2"></i>ให้คะแนนการใช้งาน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="rating-input fs-1">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="bi bi-star rating-star" data-rating="<?= $i ?>" style="cursor: pointer;"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="ratingValue" value="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">ความคิดเห็น</label>
                    <textarea class="form-control" id="reviewComment" rows="4" placeholder="แบ่งปันประสบการณ์ของคุณ..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="submitReview">ส่งรีวิว</button>
            </div>
        </div>
    </div>
</div>

<?php
$cancelUrl = Url::to(['booking/cancel']);
$reviewUrl = Url::to(['booking/review']);
$csrfToken = Yii::$app->request->csrfToken;

$this->registerJs(<<<JS
let currentBookingId = null;

// Cancel booking
window.cancelBooking = function(id) {
    currentBookingId = id;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
};

document.getElementById('confirmCancel').addEventListener('click', function() {
    const reason = document.getElementById('cancelReason').value;
    
    fetch('{$cancelUrl}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '{$csrfToken}'
        },
        body: JSON.stringify({
            id: currentBookingId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'เกิดข้อผิดพลาด');
        }
    });
});

// Review booking
window.reviewBooking = function(id) {
    currentBookingId = id;
    document.getElementById('ratingValue').value = 0;
    document.querySelectorAll('.rating-star').forEach(s => s.classList.replace('bi-star-fill', 'bi-star'));
    document.getElementById('reviewComment').value = '';
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
};

// Rating stars interaction
document.querySelectorAll('.rating-star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        document.getElementById('ratingValue').value = rating;
        
        document.querySelectorAll('.rating-star').forEach((s, i) => {
            if (i < rating) {
                s.classList.replace('bi-star', 'bi-star-fill');
                s.classList.add('text-warning');
            } else {
                s.classList.replace('bi-star-fill', 'bi-star');
                s.classList.remove('text-warning');
            }
        });
    });
    
    star.addEventListener('mouseenter', function() {
        const rating = this.dataset.rating;
        document.querySelectorAll('.rating-star').forEach((s, i) => {
            if (i < rating) {
                s.classList.add('text-warning');
            }
        });
    });
    
    star.addEventListener('mouseleave', function() {
        const currentRating = document.getElementById('ratingValue').value;
        document.querySelectorAll('.rating-star').forEach((s, i) => {
            if (i >= currentRating) {
                s.classList.remove('text-warning');
            }
        });
    });
});

document.getElementById('submitReview').addEventListener('click', function() {
    const rating = document.getElementById('ratingValue').value;
    const comment = document.getElementById('reviewComment').value;
    
    if (rating < 1) {
        alert('โปรดให้คะแนน');
        return;
    }
    
    fetch('{$reviewUrl}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '{$csrfToken}'
        },
        body: JSON.stringify({
            booking_id: currentBookingId,
            rating: rating,
            comment: comment
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'เกิดข้อผิดพลาด');
        }
    });
});

// Search functionality
document.getElementById('searchBookings').addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.booking-card').forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(query) ? '' : 'none';
    });
});
JS);
?>
