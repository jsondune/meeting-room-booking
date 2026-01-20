<?php
/**
 * Booking Cancel View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var common\models\Booking $model
 */

use yii\helpers\Html;

$this->title = 'ยกเลิกการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->booking_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ยกเลิกการจอง';
?>

<div class="booking-cancel">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-ban me-2"></i>ยกเลิกการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Booking Summary -->
                    <div class="alert alert-light border mb-4">
                        <h6 class="alert-heading mb-3">
                            <i class="fas fa-info-circle me-2"></i>รายละเอียดการจอง
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>รหัสการจอง:</strong> 
                                    <span class="badge bg-secondary"><?= Html::encode($model->booking_code) ?></span>
                                </p>
                                <p class="mb-2">
                                    <strong>หัวข้อ:</strong> <?= Html::encode($model->title) ?>
                                </p>
                                <p class="mb-2">
                                    <strong>ห้องประชุม:</strong> <?= Html::encode($model->room->name_th ?? '-') ?>
                                </p>
                                <p class="mb-2">
                                    <strong>สถานะปัจจุบัน:</strong>
                                    <?php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                    ];
                                    $statusLabels = [
                                        'pending' => 'รออนุมัติ',
                                        'approved' => 'อนุมัติแล้ว',
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $statusColors[$model->status] ?? 'secondary' ?>">
                                        <?= $statusLabels[$model->status] ?? $model->status ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>วันที่:</strong> 
                                    <?= Yii::$app->formatter->asDate($model->booking_date) ?>
                                </p>
                                <p class="mb-2">
                                    <strong>เวลา:</strong> 
                                    <?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?> น.
                                </p>
                                <p class="mb-2">
                                    <strong>ผู้จอง:</strong> <?= Html::encode($model->user->full_name ?? '-') ?>
                                </p>
                                <p class="mb-2">
                                    <strong>จำนวนผู้เข้าร่วม:</strong> <?= $model->attendee_count ?> คน
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <?php
                    $bookingDateTime = strtotime($model->booking_date . ' ' . $model->start_time);
                    $hoursUntilBooking = ($bookingDateTime - time()) / 3600;
                    ?>
                    
                    <?php if ($hoursUntilBooking < 24): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>การยกเลิกล่วงหน้าน้อยกว่า 24 ชั่วโมง:</strong> 
                        อาจมีการบันทึกประวัติการยกเลิกที่ส่งผลต่อการจองในอนาคต
                    </div>
                    <?php elseif ($hoursUntilBooking < 48): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>หมายเหตุ:</strong> การยกเลิกล่วงหน้าน้อยกว่า 48 ชั่วโมง
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        การยกเลิกล่วงหน้า <?= round($hoursUntilBooking) ?> ชั่วโมง อยู่ในระยะเวลาที่อนุญาต
                    </div>
                    <?php endif; ?>

                    <!-- Cancel Form -->
                    <?= Html::beginForm(['cancel', 'id' => $model->id], 'post', ['id' => 'cancel-form']) ?>
                    
                    <div class="mb-4">
                        <label for="reason" class="form-label">
                            <strong>เหตุผลในการยกเลิก</strong>
                        </label>
                        <textarea name="reason" id="reason" class="form-control" rows="3" 
                                  placeholder="ระบุเหตุผลในการยกเลิก (ถ้ามี)"></textarea>
                    </div>

                    <!-- Quick Reasons -->
                    <div class="mb-4">
                        <label class="form-label text-muted">เหตุผลที่ใช้บ่อย:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="มีการประชุมอื่นที่สำคัญกว่า">
                                มีประชุมอื่น
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="เปลี่ยนแปลงวันที่/เวลา">
                                เปลี่ยนวันเวลา
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ไม่จำเป็นต้องใช้ห้องประชุมแล้ว">
                                ไม่จำเป็นแล้ว
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="เปลี่ยนเป็นประชุมออนไลน์">
                                ประชุมออนไลน์
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ผู้เข้าร่วมไม่สามารถมาได้">
                                ผู้เข้าร่วมติดภารกิจ
                            </button>
                        </div>
                    </div>

                    <!-- Refund Info (if applicable) -->
                    <?php if ($model->total_cost > 0): ?>
                    <div class="alert alert-secondary">
                        <h6 class="alert-heading">
                            <i class="fas fa-money-bill-wave me-2"></i>ข้อมูลค่าใช้จ่าย
                        </h6>
                        <p class="mb-1">ค่าใช้จ่ายรวม: <strong><?= Yii::$app->formatter->asCurrency($model->total_cost, 'THB') ?></strong></p>
                        <?php if ($hoursUntilBooking >= 48): ?>
                        <p class="mb-0 text-success">
                            <i class="fas fa-check-circle me-1"></i>
                            สามารถขอคืนค่าใช้จ่ายได้เต็มจำนวน
                        </p>
                        <?php elseif ($hoursUntilBooking >= 24): ?>
                        <p class="mb-0 text-warning">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            สามารถขอคืนค่าใช้จ่ายได้ 50%
                        </p>
                        <?php else: ?>
                        <p class="mb-0 text-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            ไม่สามารถขอคืนค่าใช้จ่ายได้
                        </p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <?= Html::a(
                            '<i class="fas fa-arrow-left me-1"></i> กลับ',
                            ['view', 'id' => $model->id],
                            ['class' => 'btn btn-outline-secondary']
                        ) ?>
                        
                        <button type="submit" class="btn btn-warning" id="submit-btn">
                            <i class="fas fa-ban me-1"></i> ยืนยันการยกเลิก
                        </button>
                    </div>

                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
// Quick reason buttons
$('.quick-reason').on('click', function() {
    $('#reason').val($(this).data('reason'));
});

// Form submission confirmation
$('#cancel-form').on('submit', function(e) {
    if (!confirm('คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?')) {
        e.preventDefault();
        return false;
    }
    
    $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังดำเนินการ...');
});
JS
);
?>
