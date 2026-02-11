<?php
/**
 * Reassign View - Delegate booking approval to another approver
 * Meeting Room Booking System - Backend
 * 
 * @var yii\web\View $this
 * @var common\models\Booking $model
 * @var array $approvers
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'ส่งต่อการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'อนุมัติการจอง', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'รอพิจารณา', 'url' => ['pending']];
$this->params['breadcrumbs'][] = ['label' => $model->booking_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'ส่งต่อ';

// Thai date helpers
$thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

$formatThaiDate = function($date, $format = 'long') use ($thaiMonths, $thaiMonthsShort) {
    if (empty($date)) return '-';
    $dt = new DateTime($date);
    $day = $dt->format('j');
    $month = (int)$dt->format('n');
    $year = $dt->format('Y') + 543;
    if ($format === 'short') {
        return $day . '/' . $month . '/' . ($year % 100);
    }
    return $day . ' ' . $thaiMonths[$month] . ' ' . $year;
};

$formatThaiDateTime = function($datetime, $format = 'medium') use ($thaiMonthsShort) {
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
?>

<div class="approval-reassign">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-arrow-repeat me-2"></i>
                ส่งต่อการจอง
            </h1>
            <p class="text-muted mb-0">
                ส่งต่อการจอง <strong><?= Html::encode($model->booking_code) ?></strong> ให้ผู้อนุมัติท่านอื่นพิจารณา
            </p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Booking Summary Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        ข้อมูลการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">รหัสการจอง:</td>
                                    <td><code><?= Html::encode($model->booking_code) ?></code></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">หัวข้อ:</td>
                                    <td><strong><?= Html::encode($model->title) ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ห้องประชุม:</td>
                                    <td><?= Html::encode($model->room->name ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">ผู้จอง:</td>
                                    <td>
                                        <?= Html::encode($model->user->full_name ?? '-') ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= Html::encode($model->department->name_th ?? '-') ?>
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 120px;">วันที่:</td>
                                    <td>
                                        <strong><?= $formatThaiDate($model->booking_date, 'long') ?></strong>
                                        <?php 
                                        $bookingDate = strtotime($model->booking_date);
                                        $today = strtotime('today');
                                        $tomorrow = strtotime('tomorrow');
                                        ?>
                                        <?php if ($bookingDate == $today): ?>
                                            <span class="badge bg-warning text-dark">วันนี้</span>
                                        <?php elseif ($bookingDate == $tomorrow): ?>
                                            <span class="badge bg-info">พรุ่งนี้</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">เวลา:</td>
                                    <td><?= substr($model->start_time, 0, 5) ?> - <?= substr($model->end_time, 0, 5) ?> น.</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">จำนวนผู้เข้าร่วม:</td>
                                    <td><?= $model->attendee_count ?> คน</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">สร้างเมื่อ:</td>
                                    <td>
                                        <?= $formatThaiDateTime($model->created_at, 'short') ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php
                                            $waitingHours = round((time() - strtotime($model->created_at)) / 3600, 1);
                                            echo "รอการพิจารณา {$waitingHours} ชั่วโมง";
                                            ?>
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reassign Form Card -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        เลือกผู้อนุมัติ
                    </h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(['options' => ['class' => 'needs-validation']]); ?>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        ผู้อนุมัติที่ถูกเลือกจะได้รับการแจ้งเตือนและสามารถพิจารณาการจองนี้ได้
                    </div>

                    <div class="mb-4">
                        <label class="form-label">
                            เลือกผู้อนุมัติ <span class="text-danger">*</span>
                        </label>
                        <?php if (empty($approvers)): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                ไม่พบผู้อนุมัติที่สามารถส่งต่อได้
                            </div>
                        <?php else: ?>
                            <div class="approver-list">
                                <?php foreach ($approvers as $id => $name): ?>
                                    <?php
                                    $approver = \common\models\User::findOne($id);
                                    ?>
                                    <div class="form-check approver-option p-3 mb-2 border rounded">
                                        <input class="form-check-input" type="radio" 
                                               name="approver_id" id="approver-<?= $id ?>" 
                                               value="<?= $id ?>" required>
                                        <label class="form-check-label d-flex align-items-center w-100" 
                                               for="approver-<?= $id ?>">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <?php if ($approver && $approver->avatar): ?>
                                                    <img src="<?= $approver->getAvatarUrl() ?>" 
                                                         class="rounded-circle me-3" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                                         style="width: 40px; height: 40px;">
                                                        <?= mb_substr($name, 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= Html::encode($name) ?></strong>
                                                    <?php if ($approver): ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?= Html::encode($approver->department->name_th ?? '-') ?>
                                                            • 
                                                            <?php
                                                            $roleLabels = [
                                                                'superadmin' => 'ผู้ดูแลระบบสูงสุด',
                                                                'admin' => 'ผู้ดูแลระบบ',
                                                                'manager' => 'ผู้จัดการ',
                                                            ];
                                                            echo $roleLabels[$approver->role] ?? $approver->role;
                                                            ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">หมายเหตุ (ไม่บังคับ)</label>
                        <textarea name="note" class="form-control" rows="3" 
                                  placeholder="เหตุผลในการส่งต่อ หรือข้อมูลเพิ่มเติมสำหรับผู้อนุมัติ..."></textarea>
                        <div class="form-text">
                            ข้อความนี้จะถูกส่งไปพร้อมกับการแจ้งเตือน
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <?= Html::a('ยกเลิก', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::submitButton(
                            '<i class="bi bi-arrow-repeat me-1"></i>ส่งต่อการจอง',
                            ['class' => 'btn btn-primary', 'disabled' => empty($approvers)]
                        ) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-question-circle me-2"></i>
                        เกี่ยวกับการส่งต่อ
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-3">
                        การส่งต่อการจองหมายถึงการมอบหมายให้ผู้อนุมัติท่านอื่นพิจารณาแทน
                    </p>
                    <h6 class="small fw-bold">เหตุผลที่อาจส่งต่อ:</h6>
                    <ul class="small mb-3">
                        <li>ไม่อยู่ในช่วงเวลาทำงาน</li>
                        <li>การจองอยู่นอกเหนือความรับผิดชอบ</li>
                        <li>ต้องการความเห็นจากผู้เชี่ยวชาญ</li>
                        <li>ปริมาณงานมากเกินไป</li>
                    </ul>
                    <div class="alert alert-light small mb-0">
                        <i class="bi bi-lightbulb me-1"></i>
                        ประวัติการส่งต่อจะถูกบันทึกไว้ในระบบ
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="card">
                <div class="card-body text-center">
                    <span class="badge bg-warning text-dark p-2 mb-2">
                        <i class="bi bi-hourglass-split me-1"></i>
                        รออนุมัติ
                    </span>
                    <h6 class="mb-0"><?= Html::encode($model->booking_code) ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.approver-option {
    cursor: pointer;
    transition: all 0.2s ease;
}
.approver-option:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd !important;
}
.approver-option:has(input:checked) {
    background-color: #e7f1ff;
    border-color: #0d6efd !important;
}
</style>

<?php
$this->registerJs(<<<JS
// Make entire option clickable
document.querySelectorAll('.approver-option').forEach(function(option) {
    option.addEventListener('click', function(e) {
        if (e.target.type !== 'radio') {
            this.querySelector('input[type="radio"]').click();
        }
    });
});
JS);
?>
