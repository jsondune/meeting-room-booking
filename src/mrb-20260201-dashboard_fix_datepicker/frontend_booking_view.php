<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Booking $model */
/** @var array $equipment */

$this->title = Yii::t('app', 'รายละเอียดการจอง') . ' #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'การจองของฉัน'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusLabels = [
    'pending' => ['class' => 'warning', 'text' => 'รออนุมัติ', 'icon' => 'bi-hourglass-split'],
    'approved' => ['class' => 'success', 'text' => 'อนุมัติแล้ว', 'icon' => 'bi-check-circle'],
    'rejected' => ['class' => 'danger', 'text' => 'ไม่อนุมัติ', 'icon' => 'bi-x-circle'],
    'cancelled' => ['class' => 'secondary', 'text' => 'ยกเลิกแล้ว', 'icon' => 'bi-slash-circle'],
    'completed' => ['class' => 'info', 'text' => 'เสร็จสิ้น', 'icon' => 'bi-check2-all'],
];
$status = $statusLabels[$model->status] ?? ['class' => 'secondary', 'text' => $model->status, 'icon' => 'bi-question-circle'];

$this->registerCss("
    .booking-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        color: white;
        padding: 2rem;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 2rem;
        font-size: 1.1rem;
    }
    .info-card {
        border-radius: 1rem;
        transition: transform 0.3s;
    }
    .info-card:hover {
        transform: translateY(-5px);
    }
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 0.25rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #0d6efd;
        border: 2px solid white;
        box-shadow: 0 0 0 2px #0d6efd;
    }
    .timeline-item.completed::before {
        background: #198754;
        box-shadow: 0 0 0 2px #198754;
    }
    .room-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 0.75rem;
    }
    .qr-code {
        width: 150px;
        height: 150px;
        padding: 1rem;
        background: white;
        border-radius: 0.5rem;
    }
    .equipment-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
        margin-bottom: 0.5rem;
    }
");
?>

<div class="booking-view">
    <!-- Booking Header -->
    <div class="booking-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <span class="status-badge bg-<?= $status['class'] ?>">
                        <i class="bi <?= $status['icon'] ?> me-2"></i><?= $status['text'] ?>
                    </span>
                </div>
                <h2 class="mb-2"><?= Html::encode($model->title) ?></h2>
                <p class="mb-0 opacity-75">
                    <i class="bi bi-hash me-1"></i>หมายเลขการจอง: <strong><?= $model->booking_code ?? 'BK-' . str_pad($model->id, 6, '0', STR_PAD_LEFT) ?></strong>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <?php if (in_array($model->status, ['pending', 'approved']) && strtotime($model->booking_date) > time()): ?>
                    <button type="button" class="btn btn-light btn-lg" onclick="cancelBooking()">
                        <i class="bi bi-x-lg me-2"></i>ยกเลิกการจอง
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Room Information -->
            <div class="card info-card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php 
                            $roomImage = $model->room ? $model->room->primaryImage : null;
                            if ($roomImage): 
                            ?>
                                <img src="<?= Html::encode($roomImage->url) ?>" 
                                     class="room-image" alt="">
                            <?php else: ?>
                                <div class="room-image d-flex flex-column align-items-center justify-content-center text-white"
                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 0.5rem;">
                                    <i class="bi bi-door-open" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                    <span class="mt-2 small text-center px-2" style="opacity: 0.9;"><?= Html::encode($model->room->name ?? 'ห้องประชุม') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-3">
                                <i class="bi bi-door-open text-primary me-2"></i>
                                <?= Html::encode($model->room->name) ?>
                            </h4>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-muted small">สถานที่</div>
                                    <div><i class="bi bi-geo-alt me-1"></i><?= Html::encode($model->room->location) ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">อาคาร / ชั้น</div>
                                    <div><i class="bi bi-building me-1"></i><?= Html::encode($model->room->building) ?> / ชั้น <?= $model->room->floor ?></div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">ความจุ</div>
                                    <div><i class="bi bi-people me-1"></i><?= $model->room->capacity ?> คน</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">ประเภทห้อง</div>
                                    <div><i class="bi bi-tag me-1"></i><?= Html::encode($model->room->room_type) ?></div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <?= Html::a('<i class="bi bi-eye me-1"></i>ดูรายละเอียดห้อง', ['room/view', 'id' => $model->room_id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>รายละเอียดการจอง</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-calendar-date text-primary fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">วันที่</div>
                                    <?php
                                    // Thai date formatting
                                    $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
                                    $thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                                                   'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                                    $bookingDate = new DateTime($model->booking_date);
                                    $dayOfWeek = $thaiDays[$bookingDate->format('w')];
                                    $day = $bookingDate->format('j');
                                    $month = $thaiMonths[(int)$bookingDate->format('n')];
                                    $year = $bookingDate->format('Y') + 543;
                                    ?>
                                    <div class="fw-bold">วัน<?= $dayOfWeek ?>ที่ <?= $day ?> <?= $month ?> พ.ศ. <?= $year ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-clock text-success fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">เวลา</div>
                                    <div class="fw-bold"><?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?> น.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-people text-info fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">จำนวนผู้เข้าร่วม</div>
                                    <div class="fw-bold"><?= $model->attendees_count ?> คน</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted small">ระยะเวลา</div>
                                    <?php
                                    $start = strtotime($model->start_time);
                                    $end = strtotime($model->end_time);
                                    $duration = ($end - $start) / 3600;
                                    ?>
                                    <div class="fw-bold"><?= $duration ?> ชั่วโมง</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($model->purpose): ?>
                    <hr class="my-4">
                    <h6><i class="bi bi-chat-left-text me-2"></i>วัตถุประสงค์</h6>
                    <p class="mb-0"><?= nl2br(Html::encode($model->purpose)) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($model->notes): ?>
                    <hr class="my-4">
                    <h6><i class="bi bi-sticky me-2"></i>หมายเหตุ</h6>
                    <p class="mb-0"><?= nl2br(Html::encode($model->notes)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Equipment Requested -->
            <?php if (!empty($equipment)): ?>
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>อุปกรณ์ที่ขอใช้</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($equipment as $item): ?>
                    <div class="equipment-item">
                        <i class="bi bi-box text-primary fs-4 me-3"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold"><?= Html::encode($item['name']) ?></div>
                            <small class="text-muted">จำนวน: <?= $item['quantity'] ?> ชิ้น</small>
                        </div>
                        <?php if ($item['price'] > 0): ?>
                        <div class="text-primary fw-bold">฿<?= number_format($item['price'] * $item['quantity']) ?></div>
                        <?php else: ?>
                        <span class="badge bg-success">ฟรี</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Activity Timeline -->
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>ประวัติกิจกรรม</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="fw-bold">สร้างการจอง</div>
                            <div class="text-muted small"><?= Yii::$app->formatter->asDatetime($model->created_at, 'medium') ?></div>
                        </div>
                        <?php if ($model->status !== 'pending'): ?>
                        <div class="timeline-item completed">
                            <div class="fw-bold">
                                <?php if ($model->status === 'approved'): ?>
                                    ได้รับการอนุมัติ
                                <?php elseif ($model->status === 'rejected'): ?>
                                    ไม่ได้รับการอนุมัติ
                                <?php elseif ($model->status === 'cancelled'): ?>
                                    ยกเลิกการจอง
                                <?php elseif ($model->status === 'completed'): ?>
                                    การใช้งานเสร็จสิ้น
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small"><?= Yii::$app->formatter->asDatetime($model->updated_at, 'medium') ?></div>
                            <?php if ($model->status === 'rejected' && $model->rejection_reason): ?>
                                <div class="alert alert-danger mt-2 mb-0 py-2">
                                    <small><strong>เหตุผล:</strong> <?= Html::encode($model->rejection_reason) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="timeline-item">
                            <div class="fw-bold text-muted">รอการอนุมัติ</div>
                            <div class="text-muted small">รอดำเนินการ</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Price Summary -->
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>สรุปค่าใช้จ่าย</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าห้องประชุม</span>
                        <span>฿<?= number_format($model->room_price ?? 0) ?></span>
                    </div>
                    <?php if (($model->equipment_price ?? 0) > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าอุปกรณ์</span>
                        <span>฿<?= number_format($model->equipment_price) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (($model->service_price ?? 0) > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ค่าบริการเพิ่มเติม</span>
                        <span>฿<?= number_format($model->service_price) ?></span>
                    </div>
                    <?php endif; ?>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>รวมทั้งหมด</span>
                        <span class="text-primary">฿<?= number_format($model->total_price ?? 0) ?></span>
                    </div>
                    
                    <?php if ($model->payment_status): ?>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>สถานะการชำระ</span>
                        <?php
                        $paymentStatuses = [
                            'pending' => ['class' => 'warning', 'text' => 'รอชำระ'],
                            'paid' => ['class' => 'success', 'text' => 'ชำระแล้ว'],
                            'refunded' => ['class' => 'info', 'text' => 'คืนเงินแล้ว'],
                        ];
                        $pStatus = $paymentStatuses[$model->payment_status] ?? ['class' => 'secondary', 'text' => $model->payment_status];
                        ?>
                        <span class="badge bg-<?= $pStatus['class'] ?>"><?= $pStatus['text'] ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- QR Code for Check-in -->
            <?php if ($model->status === 'approved'): ?>
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i>QR Code สำหรับเช็คอิน</h5>
                </div>
                <div class="card-body text-center">
                    <div class="qr-code mx-auto mb-3 border">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($model->booking_code ?? 'BK-' . $model->id) ?>" 
                             alt="QR Code" class="img-fluid">
                    </div>
                    <p class="text-muted small mb-2">สแกน QR Code เพื่อเช็คอินเมื่อถึงห้องประชุม</p>
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i>พิมพ์
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contact Information -->
            <div class="card info-card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>ข้อมูลผู้จอง</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-3" style="width: 50px; height: 50px;">
                            <?= strtoupper(substr($model->user->fullname ?? $model->user->username, 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?= Html::encode($model->user->fullname ?? $model->user->username) ?></div>
                            <small class="text-muted"><?= Html::encode($model->user->department->name_th ?? '-') ?></small>
                        </div>
                    </div>
                    <div class="mb-2">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <?= Html::encode($model->user->email) ?>
                    </div>
                    <?php if ($model->user->phone): ?>
                    <div class="mb-0">
                        <i class="bi bi-telephone me-2 text-muted"></i>
                        <?= Html::encode($model->user->phone) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card info-card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>การดำเนินการ</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <?= Html::a('<i class="bi bi-arrow-left me-2"></i>กลับไปรายการจอง', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    
                    <?php if ($model->status === 'approved'): ?>
                        <button class="btn btn-outline-primary" onclick="addToCalendar()">
                            <i class="bi bi-calendar-plus me-2"></i>เพิ่มในปฏิทิน
                        </button>
                    <?php endif; ?>
                    
                    <?php if (in_array($model->status, ['pending', 'approved']) && strtotime($model->booking_date) > time()): ?>
                        <button class="btn btn-outline-danger" onclick="cancelBooking()">
                            <i class="bi bi-x-lg me-2"></i>ยกเลิกการจอง
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($model->status === 'completed' && !$model->has_review): ?>
                        <button class="btn btn-warning" onclick="openReviewModal()">
                            <i class="bi bi-star me-2"></i>ให้คะแนน
                        </button>
                    <?php endif; ?>
                </div>
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
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    การยกเลิกจะไม่สามารถย้อนกลับได้
                </div>
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

<?php
$cancelUrl = Url::to(['booking/cancel', 'id' => $model->id]);
$csrfToken = Yii::$app->request->csrfToken;

// Calendar event data
$calendarTitle = addslashes($model->title);
$calendarLocation = addslashes($model->room->name . ' - ' . $model->room->location);
$calendarStart = date('Ymd', strtotime($model->booking_date)) . 'T' . str_replace(':', '', substr($model->start_time, 0, 5)) . '00';
$calendarEnd = date('Ymd', strtotime($model->booking_date)) . 'T' . str_replace(':', '', substr($model->end_time, 0, 5)) . '00';

$this->registerJs(<<<JS
// Cancel booking
window.cancelBooking = function() {
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
        body: JSON.stringify({ reason: reason })
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

// Add to calendar
window.addToCalendar = function() {
    const url = 'https://calendar.google.com/calendar/render?action=TEMPLATE' +
        '&text={$calendarTitle}' +
        '&dates={$calendarStart}/{$calendarEnd}' +
        '&location={$calendarLocation}' +
        '&sf=true&output=xml';
    window.open(url, '_blank');
};
JS);
?>
