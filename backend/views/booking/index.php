<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var common\models\Booking $searchModel */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\MeetingRoom;
use common\models\Department;

$this->title = 'จัดการการจอง';
?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1 class="page-title">จัดการการจอง</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">การจอง</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to(['booking/calendar']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-calendar3 me-1"></i>ปฏิทิน
        </a>
        <a href="<?= Url::to(['booking/create']) ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>สร้างการจอง
        </a>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-warning"><?= $stats['pending'] ?? 0 ?></div>
                <div class="text-muted small">รออนุมัติ</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-success"><?= $stats['approved'] ?? 0 ?></div>
                <div class="text-muted small">อนุมัติแล้ว</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-primary"><?= $stats['today'] ?? 0 ?></div>
                <div class="text-muted small">วันนี้</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 bg-light">
            <div class="card-body text-center py-3">
                <div class="fs-3 fw-bold text-secondary"><?= $stats['thisMonth'] ?? 0 ?></div>
                <div class="text-muted small">เดือนนี้</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <i class="bi bi-calendar-event me-2"></i>รายการจอง
            </div>
            <div class="col-md-6 text-md-end">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                    <i class="bi bi-funnel me-1"></i>ตัวกรอง
                </button>
            </div>
        </div>
    </div>
    
    <!-- Filter Panel -->
    <div class="collapse border-bottom" id="filterPanel">
        <div class="card-body bg-light">
            <?= Html::beginForm(['booking/index'], 'get', ['class' => 'row g-3']) ?>
                <div class="col-md-2">
                    <label class="form-label">ค้นหา</label>
                    <input type="text" name="BookingSearch[keyword]" class="form-control form-control-sm" 
                           placeholder="รหัส, หัวข้อ..." value="<?= Html::encode($searchModel->keyword ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">ห้องประชุม</label>
                    <?= Html::dropDownList('BookingSearch[room_id]', $searchModel->room_id ?? '', 
                        ['' => '-- ทั้งหมด --'] + MeetingRoom::getDropdownList(),
                        ['class' => 'form-select form-select-sm']) ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">สถานะ</label>
                    <?= Html::dropDownList('BookingSearch[status]', $searchModel->status ?? '', [
                        '' => '-- ทั้งหมด --',
                        'pending' => 'รออนุมัติ',
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ปฏิเสธ',
                        'cancelled' => 'ยกเลิก',
                        'completed' => 'เสร็จสิ้น'
                    ], ['class' => 'form-select form-select-sm']) ?>
                </div>
                <div class="col-md-2">
                    <label class="form-label">วันที่เริ่ม</label>
                    <input type="date" name="BookingSearch[date_from]" class="form-control form-control-sm" 
                           value="<?= Html::encode($searchModel->date_from ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">วันที่สิ้นสุด</label>
                    <input type="date" name="BookingSearch[date_to]" class="form-control form-control-sm" 
                           value="<?= Html::encode($searchModel->date_to ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            <?= Html::endForm() ?>
        </div>
    </div>
    
    <div class="card-body p-0">
        <?php Pjax::begin(['id' => 'booking-grid']); ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>รหัส/หัวข้อ</th>
                        <th>ห้องประชุม</th>
                        <th>วันที่/เวลา</th>
                        <th>ผู้จอง</th>
                        <th class="text-center">ผู้เข้าร่วม</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center" style="width: 140px;">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider->getModels() as $booking): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold">
                                    <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" class="text-decoration-none">
                                        <?= Html::encode($booking->booking_code) ?>
                                    </a>
                                </div>
                                <div class="text-muted small text-truncate" style="max-width: 200px;">
                                    <?= Html::encode($booking->title) ?>
                                </div>
                                <?php if ($booking->booking_type != 'internal'): ?>
                                    <span class="badge bg-info"><?= ucfirst($booking->booking_type) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= Html::encode($booking->room->name_th ?? '-') ?></div>
                                <small class="text-muted"><?= Html::encode($booking->room->room_code ?? '') ?></small>
                            </td>
                            <td>
                                <div>
                                    <i class="bi bi-calendar3 me-1 text-muted"></i>
                                    <?= Yii::$app->formatter->asDate($booking->booking_date) ?>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-2" style="width:32px;height:32px;font-size:0.8rem;">
                                        <?= strtoupper(substr($booking->user->username ?? 'U', 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="small fw-semibold"><?= Html::encode($booking->user->display_name ?? $booking->user->displayName ?? '-') ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= Html::encode($booking->department->name_th ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-people me-1"></i><?= $booking->attendee_count ?: 0 ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                $statusClass = match($booking->status) {
                                    'pending' => 'pending',
                                    'approved' => 'approved',
                                    'rejected' => 'rejected',
                                    'cancelled' => 'cancelled',
                                    'completed' => 'completed',
                                    default => 'secondary'
                                };
                                $statusText = match($booking->status) {
                                    'pending' => 'รออนุมัติ',
                                    'approved' => 'อนุมัติแล้ว',
                                    'rejected' => 'ปฏิเสธ',
                                    'cancelled' => 'ยกเลิก',
                                    'completed' => 'เสร็จสิ้น',
                                    default => $booking->status
                                };
                                ?>
                                <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" 
                                       class="btn btn-outline-secondary" title="ดูรายละเอียด">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($booking->status === 'pending'): ?>
                                        <button type="button" class="btn btn-outline-success btn-approve" 
                                                data-id="<?= $booking->id ?>" title="อนุมัติ">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-reject" 
                                                data-id="<?= $booking->id ?>" title="ปฏิเสธ">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    <?php elseif ($booking->status === 'approved'): ?>
                                        <button type="button" class="btn btn-outline-warning btn-cancel" 
                                                data-id="<?= $booking->id ?>" title="ยกเลิก">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dataProvider->getModels())): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่พบข้อมูลการจอง
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
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
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>อนุมัติการจอง</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <p class="mt-3 mb-0">ต้องการอนุมัติการจองนี้หรือไม่?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="approveForm" method="post">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>อนุมัติ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>ปฏิเสธการจอง</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <div class="mb-3">
                        <label class="form-label">เหตุผลในการปฏิเสธ <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="กรุณาระบุเหตุผล..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i>ปฏิเสธ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>ยกเลิกการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    <div class="mb-3">
                        <label class="form-label">เหตุผลในการยกเลิก <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="กรุณาระบุเหตุผล..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-x-circle me-1"></i>ยกเลิกการจอง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveModal = new bootstrap.Modal(document.getElementById('approveModal'));
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
    
    // Approve
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            document.getElementById('approveForm').action = '<?= Url::to(['booking/approve']) ?>?id=' + bookingId;
            approveModal.show();
        });
    });
    
    // Reject
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            document.getElementById('rejectForm').action = '<?= Url::to(['booking/reject']) ?>?id=' + bookingId;
            rejectModal.show();
        });
    });
    
    // Cancel
    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', function() {
            const bookingId = this.dataset.id;
            document.getElementById('cancelForm').action = '<?= Url::to(['booking/cancel']) ?>?id=' + bookingId;
            cancelModal.show();
        });
    });
});
</script>
