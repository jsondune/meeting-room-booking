<?php

/** @var yii\web\View $this */
/** @var common\models\MeetingRoom $model */
/** @var bool $isNewRecord */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use common\models\Building;
use common\models\Equipment;

$this->title = $isNewRecord ? 'เพิ่มห้องประชุม' : 'แก้ไขห้องประชุม: ' . $model->name_th;

$buildings = Building::getDropdownList();
$equipmentList = Equipment::find()->where(['status' => 1])->all();
?>

<div class="page-header">
    <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="<?= Url::to(['room/index']) ?>">ห้องประชุม</a></li>
            <li class="breadcrumb-item active"><?= $isNewRecord ? 'เพิ่มใหม่' : 'แก้ไข' ?></li>
        </ol>
    </nav>
</div>

<?php
// Display flash messages (filter out debug messages)
$allowedTypes = ['success', 'error', 'danger', 'warning', 'info'];
foreach (Yii::$app->session->getAllFlashes() as $type => $message):
    // Skip debug messages
    if (strpos($type, 'debug') !== false) continue;
    if (!in_array($type, $allowedTypes)) continue;
    
    $alertClass = match($type) {
        'success' => 'alert-success',
        'error', 'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-secondary'
    };
?>
<div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endforeach; ?>

<?php $form = ActiveForm::begin([
    'id' => 'room-form',
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'needs-validation'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]); ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-basic">
                            <i class="bi bi-info-circle me-1"></i>ข้อมูลทั่วไป
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-features">
                            <i class="bi bi-star me-1"></i>สิ่งอำนวยความสะดวก
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-booking">
                            <i class="bi bi-calendar-check me-1"></i>ตั้งค่าการจอง
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pricing">
                            <i class="bi bi-cash me-1"></i>ราคา
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Basic Information Tab -->
                    <div class="tab-pane fade show active" id="tab-basic">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <?= $form->field($model, 'room_code')->textInput([
                                    'maxlength' => true, 
                                    'readonly' => !$isNewRecord,
                                    'placeholder' => 'ระบบสร้างอัตโนมัติ'
                                ])->hint('ปล่อยว่างเพื่อสร้างอัตโนมัติ') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'building_id')->dropDownList(
                                    $buildings,
                                    ['prompt' => '-- เลือกอาคาร --', 'class' => 'form-select']
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'floor')->textInput([
                                    'type' => 'number', 
                                    'min' => 0,
                                    'placeholder' => 'ชั้นที่'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'name_th')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'ชื่อห้องประชุม (ภาษาไทย)'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'name_en')->textInput([
                                    'maxlength' => true,
                                    'placeholder' => 'Room Name (English)'
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'room_type')->dropDownList([
                                    'conference' => 'ห้องประชุม (Conference)',
                                    'training' => 'ห้องฝึกอบรม (Training)',
                                    'boardroom' => 'ห้องคณะกรรมการ (Boardroom)',
                                    'huddle' => 'ห้องประชุมขนาดเล็ก (Huddle)',
                                    'auditorium' => 'หอประชุม (Auditorium)',
                                ], ['prompt' => '-- เลือกประเภท --', 'class' => 'form-select']) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'capacity')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'placeholder' => 'จำนวนที่นั่ง'
                                ]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'room_layout')->dropDownList([
                                    'theater' => 'โรงละคร (Theater)',
                                    'classroom' => 'ห้องเรียน (Classroom)',
                                    'u_shape' => 'รูปตัว U (U-Shape)',
                                    'boardroom' => 'คณะกรรมการ (Boardroom)',
                                    'banquet' => 'จัดเลี้ยง (Banquet)',
                                ], ['prompt' => '-- เลือกรูปแบบ --', 'class' => 'form-select']) ?>
                            </div>
                            <div class="col-12">
                                <?= $form->field($model, 'description')->textarea([
                                    'rows' => 4,
                                    'placeholder' => 'รายละเอียดเพิ่มเติมเกี่ยวกับห้องประชุม'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Features Tab -->
                    <div class="tab-pane fade" id="tab-features">
                        <h6 class="mb-3">สิ่งอำนวยความสะดวกในห้อง</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_projector', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_projector'
                                    ]) ?>
                                    <label class="form-check-label" for="has_projector">
                                        <i class="bi bi-projector me-1"></i>โปรเจคเตอร์
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_video_conference', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_video_conference'
                                    ]) ?>
                                    <label class="form-check-label" for="has_video_conference">
                                        <i class="bi bi-camera-video me-1"></i>Video Conference
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_whiteboard', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_whiteboard'
                                    ]) ?>
                                    <label class="form-check-label" for="has_whiteboard">
                                        <i class="bi bi-easel me-1"></i>ไวท์บอร์ด
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_air_conditioning', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_air_conditioning'
                                    ]) ?>
                                    <label class="form-check-label" for="has_air_conditioning">
                                        <i class="bi bi-snow me-1"></i>เครื่องปรับอากาศ
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_wifi', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_wifi'
                                    ]) ?>
                                    <label class="form-check-label" for="has_wifi">
                                        <i class="bi bi-wifi me-1"></i>WiFi
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_audio_system', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_audio_system'
                                    ]) ?>
                                    <label class="form-check-label" for="has_audio_system">
                                        <i class="bi bi-speaker me-1"></i>ระบบเสียง
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <?= Html::activeCheckbox($model, 'has_recording', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'has_recording'
                                    ]) ?>
                                    <label class="form-check-label" for="has_recording">
                                        <i class="bi bi-record-circle me-1"></i>ระบบบันทึก
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3">อุปกรณ์เพิ่มเติม (ที่ติดตั้งประจำห้อง)</h6>
                        <div class="table-responsive">
                            <table class="table table-sm" id="equipmentTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>อุปกรณ์</th>
                                        <th style="width: 100px;">จำนวน</th>
                                        <th style="width: 120px;">รวมในราคาห้อง</th>
                                        <th>หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($equipmentList as $equipment): ?>
                                    <?php 
                                        $roomEquipment = null;
                                        if (!$isNewRecord) {
                                            $roomEquipment = $model->getRoomEquipment()
                                                ->where(['equipment_id' => $equipment->id])
                                                ->one();
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input equipment-check" 
                                                       name="Equipment[<?= $equipment->id ?>][selected]" 
                                                       value="1" <?= $roomEquipment ? 'checked' : '' ?>
                                                       data-equipment-id="<?= $equipment->id ?>">
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?= Html::encode($equipment->name_th) ?></strong>
                                            <small class="text-muted d-block"><?= Html::encode($equipment->brand . ' ' . $equipment->model) ?></small>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm equipment-qty" 
                                                   name="Equipment[<?= $equipment->id ?>][quantity]"
                                                   min="1" value="<?= $roomEquipment ? $roomEquipment->quantity : 1 ?>"
                                                   <?= $roomEquipment ? '' : 'disabled' ?>>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input type="checkbox" class="form-check-input equipment-included" 
                                                       name="Equipment[<?= $equipment->id ?>][is_included]"
                                                       value="1" <?= ($roomEquipment && $roomEquipment->is_included) ? 'checked' : '' ?>
                                                       <?= $roomEquipment ? '' : 'disabled' ?>>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm equipment-notes" 
                                                   name="Equipment[<?= $equipment->id ?>][notes]"
                                                   value="<?= $roomEquipment ? Html::encode($roomEquipment->notes) : '' ?>"
                                                   placeholder="หมายเหตุ"
                                                   <?= $roomEquipment ? '' : 'disabled' ?>>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Booking Settings Tab -->
                    <div class="tab-pane fade" id="tab-booking">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <?= $form->field($model, 'operating_start_time')->textInput([
                                    'type' => 'time',
                                    'value' => $model->operating_start_time ?: '08:00'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'operating_end_time')->textInput([
                                    'type' => 'time',
                                    'value' => $model->operating_end_time ?: '17:00'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'min_booking_duration')->textInput([
                                    'type' => 'number',
                                    'min' => 15,
                                    'step' => 15,
                                    'value' => $model->min_booking_duration ?: 30
                                ])->hint('หน่วยเป็นนาที') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'max_booking_duration')->textInput([
                                    'type' => 'number',
                                    'min' => 30,
                                    'step' => 30,
                                    'value' => $model->max_booking_duration ?: 480
                                ])->hint('หน่วยเป็นนาที (480 = 8 ชม.)') ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'advance_booking_days')->textInput([
                                    'type' => 'number',
                                    'min' => 1,
                                    'value' => $model->advance_booking_days ?: 30
                                ])->hint('จองล่วงหน้าได้กี่วัน') ?>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">วันที่เปิดให้จอง</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php 
                                    $days = ['0' => 'อา.', '1' => 'จ.', '2' => 'อ.', '3' => 'พ.', '4' => 'พฤ.', '5' => 'ศ.', '6' => 'ส.'];
                                    // Handle both string (from DB) and array (from form submission)
                                    $rawDays = $model->available_days;
                                    if (is_array($rawDays)) {
                                        $availableDays = $rawDays;
                                    } elseif (is_string($rawDays) && !empty($rawDays)) {
                                        $availableDays = explode(',', $rawDays);
                                    } else {
                                        $availableDays = ['1', '2', '3', '4', '5']; // Default Mon-Fri
                                    }
                                    foreach ($days as $value => $label): 
                                    ?>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" 
                                                   name="MeetingRoom[available_days][]" 
                                                   value="<?= $value ?>"
                                                   id="day_<?= $value ?>"
                                                   <?= in_array($value, $availableDays) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="day_<?= $value ?>"><?= $label ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <?= Html::activeCheckbox($model, 'requires_approval', [
                                        'class' => 'form-check-input',
                                        'label' => false,
                                        'id' => 'requires_approval'
                                    ]) ?>
                                    <label class="form-check-label" for="requires_approval">
                                        ต้องมีการอนุมัติก่อนใช้งาน
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pricing Tab -->
                    <div class="tab-pane fade" id="tab-pricing">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <?= $form->field($model, 'hourly_rate')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'step' => 0.01,
                                    'value' => $model->hourly_rate ?: 0,
                                    'class' => 'form-control'
                                ])->hint('บาท/ชั่วโมง') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'half_day_rate')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'step' => 0.01,
                                    'value' => $model->half_day_rate ?: 0
                                ])->hint('บาท/ครึ่งวัน (4 ชม.)') ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'full_day_rate')->textInput([
                                    'type' => 'number',
                                    'min' => 0,
                                    'step' => 0.01,
                                    'value' => $model->full_day_rate ?: 0
                                ])->hint('บาท/วัน (8 ชม.)') ?>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <strong>การคำนวณค่าใช้จ่าย:</strong> ระบบจะเลือกราคาที่คุ้มค่าที่สุดให้ผู้จองอัตโนมัติ
                                    <br>• จอง 8 ชม.ขึ้นไป = ใช้ราคาเต็มวัน
                                    <br>• จอง 4-7 ชม. = ใช้ราคาครึ่งวัน
                                    <br>• จอง 1-3 ชม. = ใช้ราคารายชั่วโมง
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Status & Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>สถานะและการดำเนินการ
            </div>
            <div class="card-body">
                <?= $form->field($model, 'status')->dropDownList([
                    1 => 'เปิดใช้งาน',
                    0 => 'ปิดใช้งาน',
                    2 => 'อยู่ระหว่างซ่อมบำรุง'
                ], ['class' => 'form-select']) ?>
                
                <div class="d-grid gap-2 mt-4">
                    <?= Html::submitButton(
                        $isNewRecord ? '<i class="bi bi-plus-lg me-1"></i>บันทึกห้องประชุม' : '<i class="bi bi-check-lg me-1"></i>บันทึกการแก้ไข',
                        ['class' => 'btn btn-primary btn-lg']
                    ) ?>
                    <a href="<?= Url::to(['room/index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>ยกเลิก
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Images -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-images me-2"></i>รูปภาพห้องประชุม</span>
                <?php 
                $existingCount = $isNewRecord ? 0 : count($model->images);
                $remainingSlots = 5 - $existingCount;
                ?>
                <span class="badge bg-<?= $remainingSlots > 0 ? 'info' : 'warning' ?>">
                    <?= $existingCount ?>/5 รูป
                </span>
            </div>
            <div class="card-body">
                <?php if (!$isNewRecord && $model->images): ?>
                    <label class="form-label mb-2">รูปภาพปัจจุบัน <small class="text-muted">(คลิกที่รูปเพื่อตั้งเป็นรูปหลัก)</small></label>
                    <div class="row g-3 mb-3" id="existingImages">
                        <?php foreach ($model->images as $index => $image): ?>
                            <div class="col-md-4 col-6" data-image-id="<?= $image->id ?>">
                                <div class="card h-100 <?= $image->is_primary ? 'border-primary border-2' : '' ?>">
                                    <div class="position-relative">
                                        <img src="<?= $image->getUrl() ?>" alt="" 
                                             class="card-img-top" 
                                             style="height: 120px; object-fit: cover; cursor: pointer;"
                                             onclick="setPrimaryImage(<?= $image->id ?>)"
                                             title="คลิกเพื่อตั้งเป็นรูปหลัก">
                                        <?php if ($image->is_primary): ?>
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                                <i class="bi bi-star-fill me-1"></i>รูปหลัก
                                            </span>
                                        <?php endif; ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 btn-remove-image" 
                                                data-image-id="<?= $image->id ?>" 
                                                title="ลบรูปภาพ">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <div class="form-check d-inline-block">
                                            <input class="form-check-input" type="radio" 
                                                   name="primaryImage" 
                                                   value="<?= $image->id ?>" 
                                                   id="primary_<?= $image->id ?>"
                                                   <?= $image->is_primary ? 'checked' : '' ?>>
                                            <label class="form-check-label small" for="primary_<?= $image->id ?>">
                                                รูปหลัก
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($remainingSlots > 0 || $isNewRecord): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-cloud-arrow-up me-1"></i>
                        อัปโหลดรูปภาพ<?= $isNewRecord ? '' : 'เพิ่มเติม' ?>
                        <small class="text-muted">(เหลืออีก <?= $isNewRecord ? 5 : $remainingSlots ?> รูป)</small>
                    </label>
                    <input type="file" 
                           name="MeetingRoom[imageFiles][]" 
                           class="form-control" 
                           multiple 
                           accept="image/jpeg,image/png,image/gif,image/webp" 
                           id="imageUpload"
                           data-max-files="<?= $isNewRecord ? 5 : $remainingSlots ?>">
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>
                        รองรับ JPG, PNG, GIF, WEBP ขนาดไม่เกิน 2MB/รูป (สูงสุด 5 รูป)
                    </div>
                </div>
                
                <!-- Preview new images -->
                <div id="imagePreview" class="row g-3"></div>
                <?php else: ?>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ครบ 5 รูปแล้ว โปรดลบรูปเดิมก่อนเพิ่มรูปใหม่
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Info -->
        <?php if (!$isNewRecord): ?>
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>ข้อมูลเพิ่มเติม
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">สร้างเมื่อ:</td>
                        <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">แก้ไขล่าสุด:</td>
                        <td><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">จำนวนการจอง:</td>
                        <td><?= $model->getBookings()->count() ?> ครั้ง</td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Equipment checkbox toggle
    document.querySelectorAll('.equipment-check').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            const inputs = row.querySelectorAll('.equipment-qty, .equipment-included, .equipment-notes');
            inputs.forEach(input => {
                input.disabled = !this.checked;
                if (!this.checked) {
                    if (input.type === 'checkbox') input.checked = false;
                    else if (input.type === 'number') input.value = 1;
                    else input.value = '';
                }
            });
        });
    });
    
    // Image preview with file limit
    const imageUpload = document.getElementById('imageUpload');
    if (imageUpload) {
        imageUpload.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const maxFiles = parseInt(this.dataset.maxFiles) || 5;
            const files = Array.from(e.target.files);
            
            // Check file count
            if (files.length > maxFiles) {
                alert(`สามารถอัปโหลดได้สูงสุด ${maxFiles} รูปเท่านั้น`);
                // Keep only allowed files
                const dt = new DataTransfer();
                files.slice(0, maxFiles).forEach(file => dt.items.add(file));
                this.files = dt.files;
            }
            
            preview.innerHTML = '';
            
            const filesToPreview = Array.from(this.files);
            filesToPreview.forEach((file, index) => {
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert(`ไฟล์ "${file.name}" มีขนาดเกิน 2MB`);
                    return;
                }
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 col-6';
                        col.innerHTML = `
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 120px; object-fit: cover;">
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2">
                                        <i class="bi bi-plus-circle me-1"></i>ใหม่
                                    </span>
                                    <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-2 btn-cancel-upload" data-index="${index}" title="ยกเลิก">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                                <div class="card-body p-2">
                                    <small class="text-muted text-truncate d-block">${file.name}</small>
                                </div>
                            </div>
                        `;
                        preview.appendChild(col);
                        
                        // Add cancel button handler
                        col.querySelector('.btn-cancel-upload').addEventListener('click', function() {
                            col.remove();
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }
    
    // Set primary image
    window.setPrimaryImage = function(imageId) {
        document.querySelectorAll('input[name="primaryImage"]').forEach(radio => {
            radio.checked = (radio.value == imageId);
            const card = radio.closest('.card');
            if (card) {
                if (radio.checked) {
                    card.classList.add('border-primary', 'border-2');
                } else {
                    card.classList.remove('border-primary', 'border-2');
                }
            }
        });
    };
    
    // Remove existing image
    document.querySelectorAll('.btn-remove-image').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('ต้องการลบรูปภาพนี้หรือไม่?')) {
                const imageId = this.dataset.imageId;
                const container = this.closest('[data-image-id]');
                
                // Add hidden input to mark for deletion
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleteImages[]';
                input.value = imageId;
                document.getElementById('room-form').appendChild(input);
                
                // Remove from UI with fade effect
                container.style.transition = 'opacity 0.3s';
                container.style.opacity = '0';
                setTimeout(() => {
                    container.remove();
                    // Update remaining count
                    updateRemainingCount();
                }, 300);
            }
        });
    });
    
    // Update remaining upload slots
    function updateRemainingCount() {
        const existingImages = document.querySelectorAll('#existingImages [data-image-id]').length;
        const remaining = 5 - existingImages;
        const uploadInput = document.getElementById('imageUpload');
        if (uploadInput) {
            uploadInput.dataset.maxFiles = remaining;
            const label = uploadInput.previousElementSibling;
            if (label) {
                const small = label.querySelector('small');
                if (small) {
                    small.textContent = `(เหลืออีก ${remaining} รูป)`;
                }
            }
        }
    }
});
</script>
