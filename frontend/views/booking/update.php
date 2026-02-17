<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\Booking;

/** @var yii\web\View $this */
/** @var common\models\Booking $model */
/** @var array $rooms */
/** @var array $equipment */
/** @var array $attendees */
/** @var array $equipmentRequests */

$this->title = 'แก้ไขการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'การจองของฉัน', 'url' => ['my-bookings']];
$this->params['breadcrumbs'][] = ['label' => $model->booking_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';

// Thai date helpers
$thaiMonths = [1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiMonthsShort = [1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

// Meeting types
$meetingTypes = [
    'internal' => 'ประชุมภายใน',
    'external' => 'ประชุมภายนอก',
    'training' => 'อบรม/สัมมนา',
    'interview' => 'สัมภาษณ์',
    'seminar' => 'สัมมนา/Workshop',
    'other' => 'อื่นๆ',
];

// Existing equipment IDs
$existingEquipmentIds = [];
foreach ($equipmentRequests as $eq) {
    $existingEquipmentIds[$eq->equipment_id] = $eq->quantity;
}
?>

<div class="booking-update">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?>
                        </h1>
                        <p class="text-muted mb-0">แก้ไขรายละเอียดการจอง</p>
                    </div>
                    <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>กลับ
                    </a>
                </div>

                <!-- Alert -->
                <?php if ($model->status === Booking::STATUS_APPROVED): ?>
                <div class="alert alert-warning d-flex align-items-center mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>
                        <strong>หมายเหตุ:</strong> หากมีการเปลี่ยนแปลงห้อง วันที่ หรือเวลา การจองจะถูกส่งไปอนุมัติใหม่อีกครั้ง
                    </div>
                </div>
                <?php endif; ?>

                <?php $form = ActiveForm::begin(['id' => 'booking-update-form']); ?>

                <!-- Current Booking Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>ข้อมูลการจองปัจจุบัน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-muted small">รหัสการจอง</div>
                                <div class="fw-bold"><?= Html::encode($model->booking_code) ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">สถานะ</div>
                                <?php
                                $statusClass = [
                                    'pending' => 'warning',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'cancelled' => 'secondary',
                                    'completed' => 'info',
                                ];
                                $statusLabel = [
                                    'pending' => 'รออนุมัติ',
                                    'approved' => 'อนุมัติแล้ว',
                                    'rejected' => 'ไม่อนุมัติ',
                                    'cancelled' => 'ยกเลิก',
                                    'completed' => 'เสร็จสิ้น',
                                ];
                                ?>
                                <span class="badge bg-<?= $statusClass[$model->status] ?? 'secondary' ?>">
                                    <?= $statusLabel[$model->status] ?? $model->status ?>
                                </span>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">ห้องประชุม</div>
                                <div class="fw-bold"><?= Html::encode($model->room->name_th ?? '-') ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Room Selection -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-door-open me-2"></i>ห้องประชุม
                        </h5>
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'room_id')->dropDownList($rooms, [
                            'class' => 'form-select',
                            'prompt' => '-- เลือกห้องประชุม --'
                        ])->label('เลือกห้องประชุม <span class="text-danger">*</span>') ?>
                    </div>
                </div>

                <!-- Date & Time -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i>วันที่และเวลา
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">วันที่ประชุม <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="text" id="thaiDateDisplay" class="form-control" 
                                           readonly placeholder="เลือกวันที่" style="background-color: #fff; cursor: pointer;" required>
                                    <?= $form->field($model, 'booking_date', ['template' => '{input}'])->hiddenInput(['id' => 'bookingDate']) ?>
                                    <i class="bi bi-calendar3 position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'start_time')->dropDownList(
                                    array_combine(
                                        array_map(function($h, $m) { return sprintf('%02d:%02d', $h, $m); }, 
                                            array_merge(...array_map(function($h) { return [$h, $h]; }, range(8, 17))),
                                            array_merge(...array_map(function($h) { return [0, 30]; }, range(8, 17)))
                                        ),
                                        array_map(function($h, $m) { return sprintf('%02d:%02d น.', $h, $m); }, 
                                            array_merge(...array_map(function($h) { return [$h, $h]; }, range(8, 17))),
                                            array_merge(...array_map(function($h) { return [0, 30]; }, range(8, 17)))
                                        )
                                    ),
                                    ['class' => 'form-select', 'prompt' => '-- เลือก --']
                                )->label('เวลาเริ่ม <span class="text-danger">*</span>') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'end_time')->dropDownList(
                                    array_combine(
                                        array_map(function($h, $m) { return sprintf('%02d:%02d', $h, $m); }, 
                                            array_merge(...array_map(function($h) { return [$h, $h]; }, range(9, 18))),
                                            array_merge(...array_map(function($h) { return [0, 30]; }, range(9, 18)))
                                        ),
                                        array_map(function($h, $m) { return sprintf('%02d:%02d น.', $h, $m); }, 
                                            array_merge(...array_map(function($h) { return [$h, $h]; }, range(9, 18))),
                                            array_merge(...array_map(function($h) { return [0, 30]; }, range(9, 18)))
                                        )
                                    ),
                                    ['class' => 'form-select', 'prompt' => '-- เลือก --']
                                )->label('เวลาสิ้นสุด <span class="text-danger">*</span>') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meeting Details -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-card-text me-2"></i>รายละเอียดการประชุม
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <?= $form->field($model, 'meeting_title')->textInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'เช่น ประชุมทีมพัฒนาระบบ ครั้งที่ 1/2569'
                                ])->label('หัวข้อการประชุม <span class="text-danger">*</span>') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'meeting_type')->dropDownList($meetingTypes, [
                                    'class' => 'form-select',
                                    'prompt' => '-- เลือกประเภท --'
                                ])->label('ประเภทการประชุม') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'attendees_count')->input('number', [
                                    'class' => 'form-control',
                                    'min' => 1,
                                    'placeholder' => 'จำนวนคน'
                                ])->label('จำนวนผู้เข้าร่วม <span class="text-danger">*</span>') ?>
                            </div>
                            <div class="col-12">
                                <?= $form->field($model, 'meeting_description')->textarea([
                                    'class' => 'form-control',
                                    'rows' => 3,
                                    'placeholder' => 'รายละเอียดเพิ่มเติมเกี่ยวกับการประชุม (ถ้ามี)'
                                ])->label('รายละเอียด/วาระการประชุม') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-lines-fill me-2"></i>ข้อมูลผู้ติดต่อ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <?= $form->field($model, 'contact_person')->textInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'ชื่อผู้ติดต่อ'
                                ])->label('ผู้ติดต่อ') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'contact_phone')->textInput([
                                    'class' => 'form-control',
                                    'placeholder' => 'เบอร์โทรศัพท์'
                                ])->label('เบอร์โทรศัพท์') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'contact_email')->textInput([
                                    'class' => 'form-control',
                                    'type' => 'email',
                                    'placeholder' => 'อีเมล'
                                ])->label('อีเมล') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Requests -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-chat-dots me-2"></i>ความต้องการพิเศษ
                        </h5>
                    </div>
                    <div class="card-body">
                        <?= $form->field($model, 'special_requests')->textarea([
                            'class' => 'form-control',
                            'rows' => 3,
                            'placeholder' => 'เช่น ต้องการจัดห้องแบบ U-Shape, ต้องการน้ำดื่ม 20 ขวด ฯลฯ'
                        ])->label('ความต้องการเพิ่มเติม') ?>
                    </div>
                </div>

                <!-- Equipment (if available) -->
                <?php if (!empty($equipment)): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-display me-2"></i>อุปกรณ์เพิ่มเติม
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($equipment as $item): ?>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="equipment[<?= $item->id ?>]" 
                                           id="equipment_<?= $item->id ?>"
                                           value="1"
                                           <?= isset($existingEquipmentIds[$item->id]) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="equipment_<?= $item->id ?>">
                                        <?= Html::encode($item->name_th) ?>
                                        <?php if ($item->hourly_rate > 0): ?>
                                            <span class="text-muted">(฿<?= number_format($item->hourly_rate) ?>/ชม.)</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between">
                    <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>บันทึกการแก้ไข
                    </button>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.75rem;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.05);
    padding: 1rem 1.25rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thai Date Picker
    const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                       'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    const thaiMonthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                             'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
    
    const displayInput = document.getElementById('thaiDateDisplay');
    const hiddenInput = document.getElementById('bookingDate');
    
    if (!displayInput || !hiddenInput) return;
    
    let selectedDate = hiddenInput.value ? new Date(hiddenInput.value) : new Date();
    let viewDate = new Date(selectedDate);
    const minDate = new Date();
    minDate.setHours(0, 0, 0, 0);
    
    function formatThaiDateShort(date) {
        const day = date.getDate();
        const month = thaiMonthsShort[date.getMonth()];
        const year = date.getFullYear() + 543;
        return `${day} ${month} ${year}`;
    }
    
    function updateDisplay() {
        displayInput.value = formatThaiDateShort(selectedDate);
        hiddenInput.value = selectedDate.toISOString().split('T')[0];
    }
    
    // Create picker container
    const pickerContainer = document.createElement('div');
    pickerContainer.style.cssText = `
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        padding: 1rem;
        min-width: 280px;
        display: none;
    `;
    displayInput.parentElement.appendChild(pickerContainer);
    
    function renderCalendar() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const thaiYear = year + 543;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        let html = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="prevMonthBtn">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <span style="font-weight: 600;">${thaiMonths[month]} ${thaiYear}</span>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="nextMonthBtn">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center;">
        `;
        
        thaiDaysShort.forEach(day => {
            html += `<div style="font-size: 0.75rem; font-weight: 600; color: #6c757d; padding: 0.25rem;">${day}</div>`;
        });
        
        for (let i = firstDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            html += `<div style="padding: 0.5rem; color: #adb5bd; font-size: 0.875rem;">${day}</div>`;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            let style = 'padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;';
            let classes = '';
            
            if (date < minDate) {
                style += ' color: #dee2e6; cursor: not-allowed;';
            } else {
                style += ' cursor: pointer;';
                classes = 'date-selectable';
            }
            
            if (date.toDateString() === today.toDateString()) {
                style += ' border: 1px solid #0d6efd;';
            }
            
            if (date.toDateString() === selectedDate.toDateString()) {
                style += ' background-color: #0d6efd; color: #fff;';
            }
            
            html += `<div class="${classes}" data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}" style="${style}">${day}</div>`;
        }
        
        html += '</div>';
        pickerContainer.innerHTML = html;
        
        document.getElementById('prevMonthBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            viewDate.setMonth(viewDate.getMonth() - 1);
            renderCalendar();
        });
        
        document.getElementById('nextMonthBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            viewDate.setMonth(viewDate.getMonth() + 1);
            renderCalendar();
        });
        
        pickerContainer.querySelectorAll('.date-selectable').forEach(el => {
            el.addEventListener('click', function() {
                const dateStr = this.dataset.date;
                selectedDate = new Date(dateStr);
                updateDisplay();
                pickerContainer.style.display = 'none';
            });
            
            el.addEventListener('mouseenter', function() {
                if (!this.style.backgroundColor.includes('13, 110, 253')) {
                    this.style.backgroundColor = '#e9ecef';
                }
            });
            
            el.addEventListener('mouseleave', function() {
                if (!this.style.backgroundColor.includes('13, 110, 253')) {
                    this.style.backgroundColor = '';
                }
            });
        });
    }
    
    displayInput.addEventListener('click', function(e) {
        e.stopPropagation();
        viewDate = new Date(selectedDate);
        renderCalendar();
        pickerContainer.style.display = pickerContainer.style.display === 'none' ? 'block' : 'none';
    });
    
    document.addEventListener('click', function(e) {
        if (!pickerContainer.contains(e.target) && e.target !== displayInput) {
            pickerContainer.style.display = 'none';
        }
    });
    
    // Initial display
    updateDisplay();
});
</script>
