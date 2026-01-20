<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$statusColors = [
    'pending' => 'warning',
    'approved' => 'success',
    'rejected' => 'danger',
    'cancelled' => 'secondary',
    'completed' => 'info',
];
$statusLabels = [
    'pending' => 'รออนุมัติ',
    'approved' => 'อนุมัติแล้ว',
    'rejected' => 'ไม่อนุมัติ',
    'cancelled' => 'ยกเลิก',
    'completed' => 'เสร็จสิ้น',
];
$typeLabels = [
    'meeting' => 'ประชุม',
    'training' => 'อบรม/สัมมนา',
    'workshop' => 'เวิร์คช็อป',
    'presentation' => 'นำเสนอ',
    'interview' => 'สัมภาษณ์',
    'other' => 'อื่นๆ',
];
?>

<div class="booking-view">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-2">
                <i class="bi bi-calendar-check me-2"></i><?= Html::encode($model->title) ?>
            </h1>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-<?= $statusColors[$model->status] ?? 'secondary' ?> fs-6">
                    <?= $statusLabels[$model->status] ?? $model->status ?>
                </span>
                <span class="text-muted">
                    <i class="bi bi-hash me-1"></i><?= Html::encode($model->booking_code) ?>
                </span>
                <span class="badge bg-light text-dark">
                    <?= $typeLabels[$model->meeting_type] ?? $model->meeting_type ?>
                </span>
            </div>
        </div>
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-pencil me-1"></i>แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary']) ?>
            <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><?= Html::a('<i class="bi bi-printer me-2"></i>พิมพ์', ['print', 'id' => $model->id], ['class' => 'dropdown-item', 'target' => '_blank']) ?></li>
                <li><?= Html::a('<i class="bi bi-envelope me-2"></i>ส่งอีเมล', ['send-email', 'id' => $model->id], ['class' => 'dropdown-item']) ?></li>
                <li><hr class="dropdown-divider"></li>
                <?php if ($model->status === 'pending'): ?>
                <li><a href="#" class="dropdown-item text-success" data-bs-toggle="modal" data-bs-target="#approveModal"><i class="bi bi-check-circle me-2"></i>อนุมัติ</a></li>
                <li><a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#rejectModal"><i class="bi bi-x-circle me-2"></i>ไม่อนุมัติ</a></li>
                <?php endif; ?>
                <?php if (in_array($model->status, ['pending', 'approved'])): ?>
                <li><a href="#" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="bi bi-slash-circle me-2"></i>ยกเลิก</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><?= Html::a('<i class="bi bi-trash me-2"></i>ลบ', ['delete', 'id' => $model->id], [
                    'class' => 'dropdown-item text-danger',
                    'data' => [
                        'confirm' => 'คุณแน่ใจหรือไม่ที่จะลบการจองนี้?',
                        'method' => 'post',
                    ],
                ]) ?></li>
            </ul>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Booking Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>รายละเอียดการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 140px;">หัวข้อ:</td>
                                    <td class="fw-semibold"><?= Html::encode($model->title) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ประเภท:</td>
                                    <td><?= $typeLabels[$model->meeting_type] ?? $model->meeting_type ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ผู้จอง:</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                <?= strtoupper(substr($model->user->first_name ?? 'U', 0, 1) . substr($model->user->last_name ?? '', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div><?= Html::encode($model->user->full_name ?? '-') ?></div>
                                                <small class="text-muted"><?= Html::encode($model->user->department->name_th ?? '-') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">จำนวนผู้เข้าร่วม:</td>
                                    <td><i class="bi bi-people me-1"></i><?= Html::encode($model->attendees_count) ?> คน</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 140px;">วันที่:</td>
                                    <td class="fw-semibold">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?= Yii::$app->formatter->asDate($model->booking_date) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">เวลา:</td>
                                    <td>
                                        <i class="bi bi-clock me-1"></i>
                                        <?= Html::encode($model->start_time) ?> - <?= Html::encode($model->end_time) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ระยะเวลา:</td>
                                    <td>
                                        <?php
                                        $start = strtotime($model->start_time);
                                        $end = strtotime($model->end_time);
                                        $diff = ($end - $start) / 60;
                                        $hours = floor($diff / 60);
                                        $mins = $diff % 60;
                                        echo $hours > 0 ? $hours . ' ชั่วโมง ' : '';
                                        echo $mins > 0 ? $mins . ' นาที' : '';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">สถานะ:</td>
                                    <td>
                                        <span class="badge bg-<?= $statusColors[$model->status] ?? 'secondary' ?>">
                                            <?= $statusLabels[$model->status] ?? $model->status ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <?php if ($model->description): ?>
                    <hr>
                    <h6 class="text-muted mb-2">รายละเอียด</h6>
                    <p class="mb-0"><?= nl2br(Html::encode($model->description)) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Room Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-door-open me-2"></i>ห้องประชุม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?php if (!empty($model->room->images)): ?>
                            <img src="<?= Html::encode($model->room->images[0]) ?>" 
                                 class="img-fluid rounded" alt="<?= Html::encode($model->room->name_th) ?>">
                            <?php else: ?>
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h5 class="mb-2"><?= Html::encode($model->room->name_th ?? '-') ?></h5>
                            <p class="text-muted mb-2">
                                <i class="bi bi-building me-1"></i><?= Html::encode($model->room->building->name_th ?? '-') ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-geo-alt me-1"></i>ชั้น <?= Html::encode($model->room->floor ?? '-') ?>
                            </p>
                            <p class="mb-2">
                                <i class="bi bi-people me-1"></i>ความจุ: <?= Html::encode($model->room->capacity ?? '-') ?> คน
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <?php if (!empty($model->room->has_projector)): ?>
                                <span class="badge bg-light text-dark"><i class="bi bi-projector me-1"></i>โปรเจคเตอร์</span>
                                <?php endif; ?>
                                <?php if (!empty($model->room->has_video_conference)): ?>
                                <span class="badge bg-light text-dark"><i class="bi bi-camera-video me-1"></i>Video Conference</span>
                                <?php endif; ?>
                                <?php if (!empty($model->room->has_whiteboard)): ?>
                                <span class="badge bg-light text-dark"><i class="bi bi-easel me-1"></i>ไวท์บอร์ด</span>
                                <?php endif; ?>
                                <?php if (!empty($model->room->has_wifi)): ?>
                                <span class="badge bg-light text-dark"><i class="bi bi-wifi me-1"></i>WiFi</span>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3">
                                <?= Html::a('<i class="bi bi-eye me-1"></i>ดูรายละเอียดห้อง', ['/room/view', 'id' => $model->room_id], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Equipment Requested -->
            <?php if (!empty($model->bookingEquipment)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>อุปกรณ์ที่ขอใช้
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>อุปกรณ์</th>
                                    <th style="width: 100px;">จำนวน</th>
                                    <th>หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($model->bookingEquipment as $equip): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <?= Html::encode($equip->equipment->name_th ?? '-') ?>
                                    </td>
                                    <td><?= Html::encode($equip->quantity) ?></td>
                                    <td class="text-muted"><?= Html::encode($equip->notes ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Special Requests -->
            <?php if ($model->special_requests): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>คำขอพิเศษ
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(Html::encode($model->special_requests)) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Activity Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-clock-history me-2"></i>ประวัติกิจกรรม
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Created -->
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">สร้างการจอง</span>
                                    <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->created_at) ?></small>
                                </div>
                                <p class="text-muted mb-0 small">
                                    โดย <?= Html::encode($model->user->full_name ?? '-') ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($model->status === 'approved' && $model->approved_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold text-success">อนุมัติแล้ว</span>
                                    <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->approved_at) ?></small>
                                </div>
                                <p class="text-muted mb-0 small">
                                    อนุมัติโดย <?= Html::encode($model->approver->full_name ?? '-') ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($model->status === 'rejected' && $model->approved_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold text-danger">ไม่อนุมัติ</span>
                                    <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->approved_at) ?></small>
                                </div>
                                <p class="text-muted mb-0 small">
                                    โดย <?= Html::encode($model->approver->full_name ?? '-') ?>
                                    <?php if ($model->rejection_reason): ?>
                                    <br>เหตุผล: <?= Html::encode($model->rejection_reason) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($model->status === 'cancelled' && $model->cancelled_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold text-secondary">ยกเลิก</span>
                                    <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->cancelled_at) ?></small>
                                </div>
                                <?php if ($model->cancellation_reason): ?>
                                <p class="text-muted mb-0 small">
                                    เหตุผล: <?= Html::encode($model->cancellation_reason) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($model->updated_at && $model->updated_at != $model->created_at): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-semibold">แก้ไขล่าสุด</span>
                                    <small class="text-muted"><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <?php if ($model->status === 'pending'): ?>
            <div class="card mb-4 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="card-title mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>รออนุมัติ
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">การจองนี้รอการอนุมัติจากผู้ดูแลระบบ</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="bi bi-check-circle me-2"></i>อนุมัติ
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-2"></i>ไม่อนุมัติ
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Contact Person -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>ผู้ประสานงาน
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($model->contact_name): ?>
                    <p class="mb-2">
                        <i class="bi bi-person me-2 text-muted"></i>
                        <?= Html::encode($model->contact_name) ?>
                    </p>
                    <?php endif; ?>
                    <?php if ($model->contact_phone): ?>
                    <p class="mb-2">
                        <i class="bi bi-telephone me-2 text-muted"></i>
                        <a href="tel:<?= Html::encode($model->contact_phone) ?>"><?= Html::encode($model->contact_phone) ?></a>
                    </p>
                    <?php endif; ?>
                    <?php if ($model->contact_email): ?>
                    <p class="mb-0">
                        <i class="bi bi-envelope me-2 text-muted"></i>
                        <a href="mailto:<?= Html::encode($model->contact_email) ?>"><?= Html::encode($model->contact_email) ?></a>
                    </p>
                    <?php endif; ?>
                    <?php if (!$model->contact_name && !$model->contact_phone && !$model->contact_email): ?>
                    <p class="text-muted mb-0">ไม่ได้ระบุข้อมูลผู้ประสานงาน</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cost Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>ค่าใช้จ่าย
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ค่าห้อง:</td>
                            <td class="text-end">฿<?= number_format($model->room_cost ?? 0, 2) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">ค่าอุปกรณ์:</td>
                            <td class="text-end">฿<?= number_format($model->equipment_cost ?? 0, 2) ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="fw-semibold">รวมทั้งสิ้น:</td>
                            <td class="text-end fw-bold text-primary h5 mb-0">
                                ฿<?= number_format($model->total_cost ?? 0, 2) ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- System Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <i class="bi bi-hash me-2"></i>รหัส: <?= Html::encode($model->booking_code) ?>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-calendar-plus me-2"></i>สร้างเมื่อ: <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-calendar-check me-2"></i>แก้ไขล่าสุด: <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
                        </div>
                        <div>
                            <i class="bi bi-key me-2"></i>ID: <?= $model->id ?>
                        </div>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['approve', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle text-success me-2"></i>อนุมัติการจอง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการอนุมัติการจอง <strong><?= Html::encode($model->booking_code) ?></strong> หรือไม่?</p>
                <div class="alert alert-light">
                    <strong><?= Html::encode($model->title) ?></strong><br>
                    <small class="text-muted">
                        <?= Html::encode($model->room->name_th ?? '-') ?><br>
                        <?= Yii::$app->formatter->asDate($model->booking_date) ?> <?= $model->start_time ?> - <?= $model->end_time ?>
                    </small>
                </div>
                <div class="mb-3">
                    <label class="form-label">หมายเหตุ (ถ้ามี)</label>
                    <textarea name="approval_notes" class="form-control" rows="2" placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-2"></i>อนุมัติ
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['reject', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle text-danger me-2"></i>ไม่อนุมัติการจอง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการไม่อนุมัติการจอง <strong><?= Html::encode($model->booking_code) ?></strong> หรือไม่?</p>
                <div class="mb-3">
                    <label class="form-label">เหตุผลที่ไม่อนุมัติ <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="โปรดระบุเหตุผล..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle me-2"></i>ไม่อนุมัติ
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= Html::beginForm(['cancel', 'id' => $model->id], 'post') ?>
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-slash-circle text-warning me-2"></i>ยกเลิกการจอง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการยกเลิกการจอง <strong><?= Html::encode($model->booking_code) ?></strong> หรือไม่?</p>
                <div class="mb-3">
                    <label class="form-label">เหตุผลที่ยกเลิก <span class="text-danger">*</span></label>
                    <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="โปรดระบุเหตุผล..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-slash-circle me-2"></i>ยกเลิกการจอง
                </button>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--bs-primary) 0%, #6366f1 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-item:last-child {
    padding-bottom: 0;
}
.timeline-marker {
    position: absolute;
    left: -26px;
    top: 4px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px currentColor;
}
.timeline-content {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 8px;
}
</style>
