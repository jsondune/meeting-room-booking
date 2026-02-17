<?php
/**
 * Approval View - Detailed booking review for approval decision
 * Meeting Room Booking System - Backend
 * 
 * @var yii\web\View $this
 * @var common\models\Booking $model
 * @var array $auditLogs
 * @var array $conflicts
 * @var array $userBookings
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'พิจารณาการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'อนุมัติการจอง', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'รอพิจารณา', 'url' => ['pending']];
$this->params['breadcrumbs'][] = $model->booking_code;

// Thai date helpers
$thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

$formatThaiDate = function($date, $format = 'medium') use ($thaiMonths, $thaiMonthsShort) {
    if (empty($date)) return '-';
    $dt = new DateTime($date);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    
    switch ($format) {
        case 'short':
            return $day . '/' . $month . '/' . ($year % 100);
        case 'medium':
            return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
        case 'long':
            return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
        default:
            return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year;
    }
};

$formatThaiDateTime = function($datetime, $format = 'medium') use ($thaiMonths, $thaiMonthsShort) {
    if (empty($datetime)) return '-';
    $dt = new DateTime($datetime);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    $time = $dt->format('H:i');
    
    if ($format === 'short') {
        return $day . '/' . $month . '/' . ($year % 100) . ' ' . $time;
    }
    return $day . ' ' . $thaiMonthsShort[$month] . ' ' . $year . ' ' . $time . ' น.';
};

$statusLabels = [
    'pending' => ['label' => 'รออนุมัติ', 'class' => 'warning'],
    'approved' => ['label' => 'อนุมัติแล้ว', 'class' => 'success'],
    'rejected' => ['label' => 'ปฏิเสธ', 'class' => 'danger'],
    'cancelled' => ['label' => 'ยกเลิก', 'class' => 'secondary'],
    'completed' => ['label' => 'เสร็จสิ้น', 'class' => 'info'],
];

$status = $statusLabels[$model->status] ?? ['label' => $model->status, 'class' => 'secondary'];

// Check if booking is urgent (within 24 hours)
$isUrgent = strtotime($model->booking_date . ' ' . $model->start_time) <= strtotime('+24 hours');
$isTomorrow = $model->booking_date == date('Y-m-d', strtotime('+1 day'));
$isToday = $model->booking_date == date('Y-m-d');

// Calculate waiting time
$createdAt = strtotime($model->created_at);
$waitingHours = round((time() - $createdAt) / 3600, 1);
$waitingDays = floor($waitingHours / 24);
?>

<div class="approval-view">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-file-earmark-check me-2"></i>
                พิจารณาการจอง
            </h1>
            <p class="text-muted mb-0">
                รหัสการจอง: <strong><?= Html::encode($model->booking_code) ?></strong>
                <span class="badge bg-<?= $status['class'] ?> ms-2"><?= $status['label'] ?></span>
                <?php if ($isUrgent && $model->status === 'pending'): ?>
                    <span class="badge bg-danger ms-1">
                        <i class="bi bi-exclamation-triangle me-1"></i>เร่งด่วน
                    </span>
                <?php endif; ?>
            </p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['pending'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?php if (!empty($conflicts)): ?>
        <div class="alert alert-danger d-flex align-items-start mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <h6 class="alert-heading mb-1">พบการจองที่ทับซ้อน!</h6>
                <p class="mb-2">มีการจอง <?= count($conflicts) ?> รายการที่ใช้ห้องเดียวกันในช่วงเวลาใกล้เคียง:</p>
                <ul class="mb-0 small">
                    <?php foreach ($conflicts as $conflict): ?>
                        <li>
                            <strong><?= Html::encode($conflict->booking_code) ?></strong>
                            - <?= $formatThaiDate($conflict->booking_date, 'medium') ?>
                            เวลา <?= substr($conflict->start_time, 0, 5) ?> - <?= substr($conflict->end_time, 0, 5) ?>
                            (<?= Html::encode($conflict->user->full_name ?? '-') ?>)
                            <span class="badge bg-<?= $statusLabels[$conflict->status]['class'] ?? 'secondary' ?>">
                                <?= $statusLabels[$conflict->status]['label'] ?? $conflict->status ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($isToday && $model->status === 'pending'): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="bi bi-alarm fs-4 me-3"></i>
            <div>
                <strong>การจองวันนี้!</strong>
                กรุณาพิจารณาโดยเร็ว เนื่องจากการจองนี้ต้องใช้ในวันนี้
            </div>
        </div>
    <?php elseif ($isTomorrow && $model->status === 'pending'): ?>
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-calendar-event fs-4 me-3"></i>
            <div>
                <strong>การจองวันพรุ่งนี้</strong>
                กรุณาพิจารณาภายในวันนี้
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Left Column: Booking Details -->
        <div class="col-lg-8">
            <!-- Booking Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        ข้อมูลการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-calendar3 me-2"></i>วันที่และเวลา
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">วันที่:</td>
                                    <td>
                                        <strong>
                                            <?= $formatThaiDate($model->booking_date, 'long') ?>
                                        </strong>
                                        <?php if ($isToday): ?>
                                            <span class="badge bg-warning text-dark">วันนี้</span>
                                        <?php elseif ($isTomorrow): ?>
                                            <span class="badge bg-info">พรุ่งนี้</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">เวลา:</td>
                                    <td>
                                        <strong>
                                            <?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?> น.
                                        </strong>
                                        <?php
                                        $start = strtotime($model->start_time);
                                        $end = strtotime($model->end_time);
                                        $hours = ($end - $start) / 3600;
                                        ?>
                                        <span class="text-muted">(<?= $hours ?> ชั่วโมง)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">รหัสการจอง:</td>
                                    <td><code><?= Html::encode($model->booking_code) ?></code></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-door-open me-2"></i>ห้องประชุม
                            </h6>
                            <?php if ($model->room): ?>
                                <div class="d-flex align-items-center">
                                    <?php if ($model->room->image): ?>
                                        <img src="<?= $model->room->getImageUrl() ?>" 
                                             class="rounded me-3" 
                                             style="width: 80px; height: 60px; object-fit: cover;"
                                             alt="<?= Html::encode($model->room->name) ?>">
                                    <?php else: ?>
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                             style="width: 80px; height: 60px;">
                                            <i class="bi bi-door-open text-muted fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <strong><?= Html::encode($model->room->name) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-geo-alt me-1"></i><?= Html::encode($model->room->location ?? '-') ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>ความจุ <?= $model->room->capacity ?> คน
                                        </small>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="bi bi-card-heading me-2"></i>รายละเอียดการประชุม
                            </h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted" style="width: 150px;">หัวข้อการประชุม:</td>
                                    <td><strong><?= Html::encode($model->title) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ประเภท:</td>
                                    <td>
                                        <?php
                                        $typeLabels = [
                                            'meeting' => ['label' => 'ประชุม', 'icon' => 'people'],
                                            'training' => ['label' => 'อบรม', 'icon' => 'book'],
                                            'seminar' => ['label' => 'สัมมนา', 'icon' => 'mic'],
                                            'workshop' => ['label' => 'เวิร์คช็อป', 'icon' => 'tools'],
                                            'other' => ['label' => 'อื่นๆ', 'icon' => 'three-dots'],
                                        ];
                                        $type = $typeLabels[$model->booking_type] ?? ['label' => $model->booking_type, 'icon' => 'question'];
                                        ?>
                                        <i class="bi bi-<?= $type['icon'] ?> me-1"></i>
                                        <?= $type['label'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">จำนวนผู้เข้าร่วม:</td>
                                    <td>
                                        <i class="bi bi-people me-1"></i>
                                        <?= $model->attendee_count ?> คน
                                        <?php if ($model->room && $model->attendee_count > $model->room->capacity): ?>
                                            <span class="badge bg-danger ms-2">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                เกินความจุ
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($model->description): ?>
                                    <tr>
                                        <td class="text-muted">รายละเอียด:</td>
                                        <td><?= nl2br(Html::encode($model->description)) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <?php if (!empty($model->bookingEquipment)): ?>
                        <hr>
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-pc-display me-2"></i>อุปกรณ์ที่ขอใช้
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($model->bookingEquipment as $bookingEquip): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-check-circle me-1 text-success"></i>
                                    <?= Html::encode($bookingEquip->equipment->name ?? 'อุปกรณ์ #' . $bookingEquip->equipment_id) ?>
                                    <?php if ($bookingEquip->quantity > 1): ?>
                                        x<?= $bookingEquip->quantity ?>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Requester Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person me-2"></i>
                        ข้อมูลผู้จอง
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <?php if ($model->user && $model->user->avatar): ?>
                            <img src="<?= $model->user->getAvatarUrl() ?>" 
                                 class="rounded-circle me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;"
                                 alt="<?= Html::encode($model->user->full_name) ?>">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                                <?= mb_substr($model->user->full_name ?? 'U', 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h6 class="mb-1"><?= Html::encode($model->user->full_name ?? '-') ?></h6>
                            <p class="text-muted mb-0 small">
                                <i class="bi bi-envelope me-1"></i><?= Html::encode($model->user->email ?? '-') ?>
                            </p>
                            <?php if ($model->user->phone): ?>
                                <p class="text-muted mb-0 small">
                                    <i class="bi bi-telephone me-1"></i><?= Html::encode($model->user->phone) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <small class="text-muted">หน่วยงาน:</small>
                            <p class="mb-0"><?= Html::encode($model->department->name_th ?? '-') ?></p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">ตำแหน่ง:</small>
                            <p class="mb-0"><?= Html::encode($model->user->position ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User's Other Bookings -->
            <?php if (!empty($userBookings)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-range me-2"></i>
                            การจองอื่นของผู้ใช้คนนี้
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>รหัส</th>
                                    <th>หัวข้อ</th>
                                    <th>ห้อง</th>
                                    <th>วันที่/เวลา</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= Url::to(['view', 'id' => $booking->id]) ?>">
                                                <?= Html::encode($booking->booking_code) ?>
                                            </a>
                                        </td>
                                        <td><?= Html::encode($booking->title) ?></td>
                                        <td><?= Html::encode($booking->room->name ?? '-') ?></td>
                                        <td>
                                            <?= $formatThaiDate($booking->booking_date, 'short') ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php $s = $statusLabels[$booking->status] ?? ['label' => $booking->status, 'class' => 'secondary']; ?>
                                            <span class="badge bg-<?= $s['class'] ?>"><?= $s['label'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Audit Log -->
            <?php if (!empty($auditLogs)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            ประวัติการดำเนินการ
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="timeline p-3">
                            <?php foreach ($auditLogs as $log): ?>
                                <div class="d-flex mb-3">
                                    <?php
                                    $actionIcons = [
                                        'approve' => ['icon' => 'check-circle-fill', 'color' => 'success'],
                                        'reject' => ['icon' => 'x-circle-fill', 'color' => 'danger'],
                                        'status_change' => ['icon' => 'arrow-repeat', 'color' => 'info'],
                                        'create' => ['icon' => 'plus-circle-fill', 'color' => 'primary'],
                                    ];
                                    $actionInfo = $actionIcons[$log->action] ?? ['icon' => 'circle', 'color' => 'secondary'];
                                    ?>
                                    <div class="me-3">
                                        <i class="bi bi-<?= $actionInfo['icon'] ?> text-<?= $actionInfo['color'] ?> fs-5"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <strong>
                                                <?php
                                                $actionLabels = [
                                                    'approve' => 'อนุมัติการจอง',
                                                    'reject' => 'ปฏิเสธการจอง',
                                                    'status_change' => 'เปลี่ยนสถานะ',
                                                    'create' => 'สร้างการจอง',
                                                ];
                                                echo $actionLabels[$log->action] ?? $log->action;
                                                ?>
                                            </strong>
                                            <small class="text-muted">
                                                <?= $formatThaiDateTime($log->created_at, 'medium') ?>
                                            </small>
                                        </div>
                                        <p class="text-muted mb-0 small">
                                            โดย <?= Html::encode($log->user->full_name ?? 'ระบบ') ?>
                                        </p>
                                        <?php if (!empty($log->new_values)): ?>
                                            <?php $values = is_array($log->new_values) ? $log->new_values : json_decode($log->new_values, true); ?>
                                            <?php if (!empty($values['reason'])): ?>
                                                <p class="mb-0 small mt-1">
                                                    <i class="bi bi-chat-left-text me-1"></i>
                                                    เหตุผล: <?= Html::encode($values['reason']) ?>
                                                </p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Action Panel -->
        <div class="col-lg-4">
            <!-- Waiting Time Card -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="bi bi-hourglass-split text-warning fs-1 mb-2"></i>
                    <h6 class="text-muted">รอการพิจารณา</h6>
                    <h4 class="mb-0">
                        <?php if ($waitingDays > 0): ?>
                            <?= $waitingDays ?> วัน <?= round($waitingHours - ($waitingDays * 24)) ?> ชม.
                        <?php else: ?>
                            <?= $waitingHours ?> ชั่วโมง
                        <?php endif; ?>
                    </h4>
                    <small class="text-muted">
                        จองเมื่อ <?= $formatThaiDateTime($model->created_at, 'medium') ?>
                    </small>
                </div>
            </div>

            <?php if ($model->status === 'pending'): ?>
                <!-- Approval Actions Card -->
                <div class="card mb-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check2-square me-2"></i>
                            ดำเนินการ
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($conflicts)): ?>
                            <div class="alert alert-warning small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                พบการจองทับซ้อน กรุณาตรวจสอบก่อนอนุมัติ
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?= Html::a(
                                '<i class="bi bi-check-lg me-2"></i>อนุมัติการจอง',
                                ['approve', 'id' => $model->id],
                                [
                                    'class' => 'btn btn-success btn-lg',
                                    'data' => [
                                        'confirm' => 'ยืนยันการอนุมัติการจองนี้?',
                                        'method' => 'post',
                                    ],
                                ]
                            ) ?>

                            <?= Html::a(
                                '<i class="bi bi-x-lg me-2"></i>ปฏิเสธการจอง',
                                ['#'],
                                [
                                    'class' => 'btn btn-outline-danger btn-lg',
                                    'data-bs-toggle' => 'modal',
                                    'data-bs-target' => '#rejectModal',
                                ]
                            ) ?>

                            <hr class="my-2">

                            <?= Html::a(
                                '<i class="bi bi-arrow-repeat me-2"></i>ส่งต่อผู้อนุมัติอื่น',
                                ['reassign', 'id' => $model->id],
                                ['class' => 'btn btn-outline-secondary']
                            ) ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Status Card for non-pending bookings -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <?php if ($model->status === 'approved'): ?>
                            <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                            <h5 class="text-success">อนุมัติแล้ว</h5>
                            <?php if ($model->approver): ?>
                                <p class="text-muted mb-0">
                                    โดย <?= Html::encode($model->approver->full_name) ?>
                                </p>
                                <small class="text-muted">
                                    <?= $formatThaiDateTime($model->approved_at, 'medium') ?>
                                </small>
                            <?php endif; ?>
                        <?php elseif ($model->status === 'rejected'): ?>
                            <i class="bi bi-x-circle-fill text-danger fs-1 mb-2"></i>
                            <h5 class="text-danger">ปฏิเสธแล้ว</h5>
                            <?php if ($model->approver): ?>
                                <p class="text-muted mb-0">
                                    โดย <?= Html::encode($model->approver->full_name) ?>
                                </p>
                                <small class="text-muted">
                                    <?= $formatThaiDateTime($model->approved_at, 'medium') ?>
                                </small>
                            <?php endif; ?>
                            <?php if ($model->rejection_reason): ?>
                                <div class="alert alert-light mt-3 text-start small">
                                    <strong>เหตุผล:</strong><br>
                                    <?= Html::encode($model->rejection_reason) ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <i class="bi bi-info-circle-fill text-<?= $status['class'] ?> fs-1 mb-2"></i>
                            <h5 class="text-<?= $status['class'] ?>"><?= $status['label'] ?></h5>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Info Card -->
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>
                        ข้อมูลด่วน
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-calendar-check text-muted me-2"></i>
                            <strong><?= $formatThaiDate($model->booking_date, 'long') ?></strong>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-clock text-muted me-2"></i>
                            <?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?> น.
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-door-open text-muted me-2"></i>
                            <?= Html::encode($model->room->name ?? '-') ?>
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-people text-muted me-2"></i>
                            <?= $model->attendee_count ?> คน
                        </li>
                        <li>
                            <i class="bi bi-person text-muted me-2"></i>
                            <?= Html::encode($model->user->full_name ?? '-') ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['reject', 'id' => $model->id], 'post') ?>
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2"></i>
                    ปฏิเสธการจอง
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    การปฏิเสธจะส่งการแจ้งเตือนไปยังผู้จอง
                </div>

                <div class="mb-3">
                    <label class="form-label">เหตุผลในการปฏิเสธ <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" 
                              placeholder="กรุณาระบุเหตุผล..." required></textarea>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" 
                            data-reason="ห้องประชุมไม่ว่าง">ห้องไม่ว่าง</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" 
                            data-reason="จำนวนผู้เข้าร่วมเกินความจุของห้อง">เกินความจุ</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" 
                            data-reason="ห้องอยู่ระหว่างการซ่อมบำรุง">ซ่อมบำรุง</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" 
                            data-reason="ข้อมูลการจองไม่ครบถ้วน">ข้อมูลไม่ครบ</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-lg me-1"></i>
                    ยืนยันการปฏิเสธ
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
// Quick reason buttons
document.querySelectorAll('.quick-reason').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelector('textarea[name="reason"]').value = this.dataset.reason;
    });
});
JS);
?>
