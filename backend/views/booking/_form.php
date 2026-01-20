<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */
/* @var $form yii\widgets\ActiveForm */
/* @var $rooms array */
/* @var $users array */
/* @var $bookingTypes array */

$isNewRecord = $model->isNewRecord;
?>

<div class="booking-form">
    <?php $form = ActiveForm::begin([
        'id' => 'booking-form',
        'options' => ['class' => 'needs-validation'],
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>ข้อมูลการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'booking_code')->textInput([
                                'maxlength' => true,
                                'readonly' => true,
                                'class' => 'form-control',
                                'placeholder' => 'จะถูกสร้างอัตโนมัติ'
                            ])->hint('รหัสการจองจะถูกสร้างอัตโนมัติ') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'booking_type')->dropDownList([
                                'meeting' => 'ประชุม',
                                'training' => 'อบรม/สัมมนา',
                                'workshop' => 'เวิร์คช็อป',
                                'presentation' => 'นำเสนอ',
                                'interview' => 'สัมภาษณ์',
                                'other' => 'อื่นๆ',
                            ], ['class' => 'form-select', 'prompt' => '-- เลือกประเภท --']) ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'title')->textInput([
                        'maxlength' => true,
                        'class' => 'form-control',
                        'placeholder' => 'หัวข้อการประชุม/กิจกรรม'
                    ]) ?>

                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'รายละเอียดเพิ่มเติม (ถ้ามี)'
                    ]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'user_id')->dropDownList(
                                ArrayHelper::map($users ?? [], 'id', function($user) {
                                    return $user['full_name'] . ' (' . ($user['department_name'] ?? '-') . ')';
                                }),
                                [
                                    'class' => 'form-select select2',
                                    'prompt' => '-- เลือกผู้จอง --',
                                    'data-placeholder' => 'ค้นหาผู้จอง...'
                                ]
                            )->label('ผู้จอง') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'attendee_count')->input('number', [
                                'class' => 'form-control',
                                'min' => 1,
                                'placeholder' => 'จำนวนผู้เข้าร่วม'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-door-open me-2"></i>เลือกห้องประชุม
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Room Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">อาคาร</label>
                            <select id="filter-building" class="form-select">
                                <option value="">ทั้งหมด</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ความจุ (ขั้นต่ำ)</label>
                            <input type="number" id="filter-capacity" class="form-control" min="1" placeholder="จำนวนคน">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ประเภทห้อง</label>
                            <select id="filter-room-type" class="form-select">
                                <option value="">ทั้งหมด</option>
                                <option value="meeting_room">ห้องประชุม</option>
                                <option value="conference_room">ห้องประชุมใหญ่</option>
                                <option value="training_room">ห้องอบรม</option>
                                <option value="auditorium">หอประชุม</option>
                            </select>
                        </div>
                    </div>

                    <!-- Room Cards -->
                    <div class="row" id="room-selection">
                        <?php foreach ($rooms ?? [] as $room): ?>
                        <div class="col-md-6 col-lg-4 mb-3 room-item" 
                             data-building="<?= Html::encode($room['building_id'] ?? '') ?>"
                             data-capacity="<?= Html::encode($room['capacity'] ?? 0) ?>"
                             data-type="<?= Html::encode($room['room_type'] ?? '') ?>">
                            <div class="card room-card h-100 <?= $model->room_id == $room['id'] ? 'selected' : '' ?>" 
                                 data-room-id="<?= $room['id'] ?>">
                                <div class="position-relative">
                                    <?php if (!empty($room['images'])): ?>
                                    <img src="<?= Html::encode($room['images'][0]) ?>" class="card-img-top" 
                                         alt="<?= Html::encode($room['name_th']) ?>" style="height: 120px; object-fit: cover;">
                                    <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                    </div>
                                    <?php endif; ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-primary">
                                        <i class="bi bi-people me-1"></i><?= $room['capacity'] ?>
                                    </span>
                                </div>
                                <div class="card-body p-2">
                                    <h6 class="card-title mb-1"><?= Html::encode($room['name_th']) ?></h6>
                                    <p class="card-text small text-muted mb-1">
                                        <i class="bi bi-building me-1"></i><?= Html::encode($room['building_name'] ?? '-') ?>
                                        <span class="mx-1">|</span>
                                        <i class="bi bi-geo-alt me-1"></i>ชั้น <?= Html::encode($room['floor'] ?? '-') ?>
                                    </p>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if (!empty($room['has_projector'])): ?>
                                        <span class="badge bg-light text-dark" title="โปรเจคเตอร์"><i class="bi bi-projector"></i></span>
                                        <?php endif; ?>
                                        <?php if (!empty($room['has_video_conference'])): ?>
                                        <span class="badge bg-light text-dark" title="Video Conference"><i class="bi bi-camera-video"></i></span>
                                        <?php endif; ?>
                                        <?php if (!empty($room['has_whiteboard'])): ?>
                                        <span class="badge bg-light text-dark" title="ไวท์บอร์ด"><i class="bi bi-easel"></i></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer p-2 bg-transparent">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input room-radio" type="radio" 
                                               name="Booking[room_id]" id="room-<?= $room['id'] ?>" 
                                               value="<?= $room['id'] ?>"
                                               <?= $model->room_id == $room['id'] ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="room-<?= $room['id'] ?>">
                                            เลือกห้องนี้
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($rooms)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>ไม่พบห้องประชุมที่พร้อมใช้งาน
                    </div>
                    <?php endif; ?>

                    <!-- Selected Room Info -->
                    <div id="selected-room-info" class="alert alert-primary d-none mt-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                <strong>ห้องที่เลือก:</strong> <span id="selected-room-name">-</span>
                                <span class="ms-3"><strong>ความจุ:</strong> <span id="selected-room-capacity">-</span> คน</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i>วันที่และเวลา
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'booking_date')->input('date', [
                                'class' => 'form-control',
                                'min' => date('Y-m-d'),
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'start_time')->input('time', [
                                'class' => 'form-control',
                                'step' => 900, // 15 minutes
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'end_time')->input('time', [
                                'class' => 'form-control',
                                'step' => 900,
                            ]) ?>
                        </div>
                    </div>

                    <!-- Duration Info -->
                    <div class="alert alert-light border mt-3">
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">ระยะเวลา</small>
                                <strong id="booking-duration">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">เริ่ม</small>
                                <strong id="booking-start-display">-</strong>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">สิ้นสุด</small>
                                <strong id="booking-end-display">-</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Recurring Booking -->
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="is-recurring" name="is_recurring">
                        <label class="form-check-label" for="is-recurring">จองซ้ำ (Recurring)</label>
                    </div>

                    <div id="recurring-options" class="mt-3 d-none">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">รูปแบบการจองซ้ำ</label>
                                <select name="recurring_pattern" class="form-select">
                                    <option value="daily">ทุกวัน</option>
                                    <option value="weekly" selected>ทุกสัปดาห์</option>
                                    <option value="biweekly">ทุก 2 สัปดาห์</option>
                                    <option value="monthly">ทุกเดือน</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จำนวนครั้ง</label>
                                <input type="number" name="recurring_count" class="form-control" min="2" max="52" value="4">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">สิ้นสุดวันที่</label>
                                <input type="date" name="recurring_end_date" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Availability Check -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-primary" id="check-availability">
                            <i class="bi bi-search me-2"></i>ตรวจสอบห้องว่าง
                        </button>
                    </div>

                    <div id="availability-result" class="mt-3 d-none"></div>
                </div>
            </div>

            <!-- Equipment Request -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-tools me-2"></i>อุปกรณ์เพิ่มเติม
                    </h5>
                    <span class="badge bg-secondary">ไม่บังคับ</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        เลือกอุปกรณ์ที่ต้องการใช้เพิ่มเติม (นอกเหนือจากที่มีในห้องอยู่แล้ว)
                    </p>

                    <div class="table-responsive">
                        <table class="table table-sm" id="equipment-table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;"></th>
                                    <th>อุปกรณ์</th>
                                    <th style="width: 120px;">จำนวน</th>
                                    <th style="width: 200px;">หมายเหตุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[laptop][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-laptop me-2"></i>โน๊ตบุ๊ค
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[laptop][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[laptop][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="หมายเหตุ" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[microphone][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-mic me-2"></i>ไมโครโฟน
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[microphone][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[microphone][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="หมายเหตุ" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[pointer][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-cursor me-2"></i>Laser Pointer
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[pointer][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[pointer][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="หมายเหตุ" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[flipchart][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-easel2 me-2"></i>Flip Chart
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[flipchart][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[flipchart][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="หมายเหตุ" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[extension][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-plug me-2"></i>ปลั๊กไฟ/สายพ่วง
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[extension][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[extension][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="หมายเหตุ" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input class="form-check-input equip-check" type="checkbox" 
                                               name="equipment[other][selected]" value="1">
                                    </td>
                                    <td>
                                        <i class="bi bi-three-dots me-2"></i>อื่นๆ
                                    </td>
                                    <td>
                                        <input type="number" name="equipment[other][quantity]" 
                                               class="form-control form-control-sm equip-qty" min="1" value="1" disabled>
                                    </td>
                                    <td>
                                        <input type="text" name="equipment[other][notes]" 
                                               class="form-control form-control-sm equip-notes" placeholder="ระบุอุปกรณ์" disabled>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Special Requests -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-text me-2"></i>คำขอพิเศษ
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'special_requests')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'เช่น ต้องการจัดห้องแบบ U-Shape, ต้องการน้ำดื่ม, ฯลฯ'
                    ])->label(false) ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card mb-4 sticky-lg-top" style="top: 80px;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-sliders me-2"></i>สถานะและการดำเนินการ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!$isNewRecord): ?>
                    <?= $form->field($model, 'status')->dropDownList([
                        'pending' => 'รออนุมัติ',
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ไม่อนุมัติ',
                        'cancelled' => 'ยกเลิก',
                        'completed' => 'เสร็จสิ้น',
                    ], ['class' => 'form-select']) ?>
                    <?php endif; ?>

                    <div class="d-grid gap-2 mt-3">
                        <?= Html::submitButton(
                            $isNewRecord 
                                ? '<i class="bi bi-calendar-plus me-2"></i>สร้างการจอง' 
                                : '<i class="bi bi-save me-2"></i>บันทึกการเปลี่ยนแปลง',
                            ['class' => $isNewRecord ? 'btn btn-primary btn-lg' : 'btn btn-success btn-lg']
                        ) ?>
                        <?= Html::a(
                            '<i class="bi bi-x-lg me-2"></i>ยกเลิก',
                            ['index'],
                            ['class' => 'btn btn-outline-secondary']
                        ) ?>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>สรุปการจอง
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">ห้อง:</td>
                            <td class="text-end fw-semibold" id="summary-room">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted">วันที่:</td>
                            <td class="text-end fw-semibold" id="summary-date">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted">เวลา:</td>
                            <td class="text-end fw-semibold" id="summary-time">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted">ระยะเวลา:</td>
                            <td class="text-end fw-semibold" id="summary-duration">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted">ผู้เข้าร่วม:</td>
                            <td class="text-end fw-semibold" id="summary-attendees">-</td>
                        </tr>
                    </table>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">ค่าใช้จ่าย (โดยประมาณ):</span>
                        <span class="h5 mb-0 text-primary" id="summary-cost">฿0</span>
                    </div>
                    <small class="text-muted d-block mt-2">
                        * ค่าใช้จ่ายจริงอาจแตกต่างกันขึ้นอยู่กับอุปกรณ์ที่ใช้
                    </small>
                </div>
            </div>

            <!-- Contact Person -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-lines-fill me-2"></i>ผู้ประสานงาน
                    </h5>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'contact_name')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'ชื่อผู้ประสานงาน'
                    ]) ?>

                    <?= $form->field($model, 'contact_phone')->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'เบอร์โทรศัพท์'
                    ]) ?>

                    <?= $form->field($model, 'contact_email')->input('email', [
                        'class' => 'form-control',
                        'placeholder' => 'อีเมล'
                    ]) ?>
                </div>
            </div>

            <!-- Quick Info -->
            <?php if (!$isNewRecord): ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ
                    </h5>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <div class="mb-2">
                            <i class="bi bi-calendar-plus me-2"></i>สร้างเมื่อ: 
                            <?= Yii::$app->formatter->asDatetime($model->created_at) ?>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-calendar-check me-2"></i>แก้ไขล่าสุด: 
                            <?= Yii::$app->formatter->asDatetime($model->updated_at) ?>
                        </div>
                        <?php if ($model->approved_by): ?>
                        <div class="mb-2">
                            <i class="bi bi-person-check me-2"></i>อนุมัติโดย: 
                            <?= Html::encode($model->approver->full_name ?? '-') ?>
                        </div>
                        <div>
                            <i class="bi bi-clock me-2"></i>อนุมัติเมื่อ: 
                            <?= Yii::$app->formatter->asDatetime($model->approved_at) ?>
                        </div>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<style>
.room-card {
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}
.room-card:hover {
    border-color: var(--bs-primary);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.room-card.selected {
    border-color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}
.room-card.selected .card-footer {
    background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
}
</style>

<?php
$js = <<<JS
// Room selection
$('.room-card').on('click', function() {
    var roomId = $(this).data('room-id');
    $('.room-card').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('.room-radio').prop('checked', true).trigger('change');
});

$('.room-radio').on('change', function() {
    if ($(this).is(':checked')) {
        var card = $(this).closest('.room-card');
        var roomName = card.find('.card-title').text();
        var capacity = card.find('.badge').text();
        
        $('#selected-room-info').removeClass('d-none');
        $('#selected-room-name').text(roomName);
        $('#selected-room-capacity').text(capacity);
        $('#summary-room').text(roomName);
    }
});

// Room filtering
function filterRooms() {
    var building = $('#filter-building').val();
    var capacity = parseInt($('#filter-capacity').val()) || 0;
    var roomType = $('#filter-room-type').val();
    
    $('.room-item').each(function() {
        var show = true;
        if (building && $(this).data('building') != building) show = false;
        if (capacity && $(this).data('capacity') < capacity) show = false;
        if (roomType && $(this).data('type') != roomType) show = false;
        $(this).toggle(show);
    });
}

$('#filter-building, #filter-room-type').on('change', filterRooms);
$('#filter-capacity').on('input', filterRooms);

// Equipment checkboxes
$('.equip-check').on('change', function() {
    var row = $(this).closest('tr');
    var qty = row.find('.equip-qty');
    var notes = row.find('.equip-notes');
    
    if ($(this).is(':checked')) {
        qty.prop('disabled', false);
        notes.prop('disabled', false);
    } else {
        qty.prop('disabled', true);
        notes.prop('disabled', true);
    }
});

// Date/time calculations
function updateBookingSummary() {
    var date = $('#booking-booking_date').val();
    var startTime = $('#booking-start_time').val();
    var endTime = $('#booking-end_time').val();
    var attendees = $('#booking-attendee_count').val();
    
    if (date) {
        // Use ThaiDate helper if available, otherwise fallback
        if (typeof ThaiDate !== 'undefined') {
            $('#summary-date').text(ThaiDate.format(date, 'long'));
        } else {
            var dateObj = new Date(date);
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            $('#summary-date').text(dateObj.toLocaleDateString('th-TH', options));
        }
    }
    
    if (startTime && endTime) {
        $('#summary-time').text(startTime + ' - ' + endTime);
        $('#booking-start-display').text(startTime);
        $('#booking-end-display').text(endTime);
        
        // Calculate duration
        var start = new Date('2000-01-01 ' + startTime);
        var end = new Date('2000-01-01 ' + endTime);
        var diff = (end - start) / 1000 / 60; // minutes
        
        if (diff > 0) {
            var hours = Math.floor(diff / 60);
            var mins = diff % 60;
            var durationText = hours > 0 ? hours + ' ชั่วโมง' : '';
            if (mins > 0) durationText += (hours > 0 ? ' ' : '') + mins + ' นาที';
            $('#booking-duration').text(durationText);
            $('#summary-duration').text(durationText);
        }
    }
    
    if (attendees) {
        $('#summary-attendees').text(attendees + ' คน');
    }
}

$('#booking-booking_date, #booking-start_time, #booking-end_time, #booking-attendee_count').on('change input', updateBookingSummary);

// Recurring toggle
$('#is-recurring').on('change', function() {
    $('#recurring-options').toggleClass('d-none', !$(this).is(':checked'));
});

// Check availability
$('#check-availability').on('click', function() {
    var roomId = $('input[name="Booking[room_id]"]:checked').val();
    var date = $('#booking-booking_date').val();
    var startTime = $('#booking-start_time').val();
    var endTime = $('#booking-end_time').val();
    
    if (!roomId || !date || !startTime || !endTime) {
        alert('กรุณาเลือกห้อง วันที่ และเวลาก่อน');
        return;
    }
    
    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>กำลังตรวจสอบ...');
    
    $.ajax({
        url: 'check-availability',
        method: 'POST',
        data: {
            room_id: roomId,
            date: date,
            start_time: startTime,
            end_time: endTime,
            _csrf: yii.getCsrfToken()
        },
        success: function(response) {
            var resultDiv = $('#availability-result');
            resultDiv.removeClass('d-none');
            
            if (response.available) {
                resultDiv.html('<div class="alert alert-success mb-0"><i class="bi bi-check-circle me-2"></i>ห้องว่างในช่วงเวลาที่เลือก</div>');
            } else {
                var html = '<div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-2"></i>ห้องไม่ว่างในช่วงเวลาที่เลือก';
                if (response.conflicts && response.conflicts.length > 0) {
                    html += '<ul class="mb-0 mt-2">';
                    response.conflicts.forEach(function(c) {
                        html += '<li>' + c.title + ' (' + c.start_time + ' - ' + c.end_time + ')</li>';
                    });
                    html += '</ul>';
                }
                html += '</div>';
                resultDiv.html(html);
            }
        },
        error: function() {
            alert('เกิดข้อผิดพลาดในการตรวจสอบ');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="bi bi-search me-2"></i>ตรวจสอบห้องว่าง');
        }
    });
});

// Initialize
updateBookingSummary();
if ($('.room-radio:checked').length) {
    $('.room-radio:checked').trigger('change');
}
JS;

$this->registerJs($js);
?>
