<?php
/**
 * Booking Reject View
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var common\models\Booking $model
 */

use yii\helpers\Html;

$this->title = 'ปฏิเสธการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->booking_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ปฏิเสธการจอง';
?>

<div class="booking-reject">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>ปฏิเสธการจอง
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
                            </div>
                        </div>
                    </div>

                    <!-- Warning -->
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>คำเตือน:</strong> การปฏิเสธการจองจะส่งการแจ้งเตือนไปยังผู้จองทันที
                    </div>

                    <!-- Reject Form -->
                    <?= Html::beginForm(['reject', 'id' => $model->id], 'post', ['id' => 'reject-form']) ?>
                    
                    <div class="mb-4">
                        <label for="reason" class="form-label">
                            <strong>เหตุผลในการปฏิเสธ</strong> <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" 
                                  placeholder="กรุณาระบุเหตุผลในการปฏิเสธการจอง..." required></textarea>
                        <div class="form-text">กรุณาระบุเหตุผลอย่างชัดเจนเพื่อให้ผู้จองทราบ</div>
                    </div>

                    <!-- Quick Reasons -->
                    <div class="mb-4">
                        <label class="form-label text-muted">เหตุผลที่ใช้บ่อย:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ห้องประชุมไม่ว่างในเวลาดังกล่าว">
                                ห้องไม่ว่าง
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="จำนวนผู้เข้าร่วมเกินความจุของห้องประชุม">
                                เกินความจุ
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ห้องประชุมอยู่ระหว่างการซ่อมบำรุง">
                                ซ่อมบำรุง
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ข้อมูลการจองไม่ครบถ้วน กรุณาจองใหม่">
                                ข้อมูลไม่ครบ
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm quick-reason" 
                                    data-reason="ไม่ได้รับอนุมัติจากผู้บังคับบัญชา">
                                ไม่ได้รับอนุมัติ
                            </button>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <?= Html::a(
                            '<i class="fas fa-arrow-left me-1"></i> กลับ',
                            ['view', 'id' => $model->id],
                            ['class' => 'btn btn-outline-secondary']
                        ) ?>
                        
                        <button type="submit" class="btn btn-danger" id="submit-btn">
                            <i class="fas fa-times-circle me-1"></i> ยืนยันการปฏิเสธ
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
$('#reject-form').on('submit', function(e) {
    if ($('#reason').val().trim() === '') {
        e.preventDefault();
        alert('กรุณาระบุเหตุผลในการปฏิเสธ');
        return false;
    }
    
    if (!confirm('คุณต้องการปฏิเสธการจองนี้ใช่หรือไม่?')) {
        e.preventDefault();
        return false;
    }
    
    $('#submit-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> กำลังดำเนินการ...');
});
JS
);
?>
