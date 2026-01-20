<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Equipment $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $categories */
/** @var array $rooms */

$isNewRecord = $model->isNewRecord;
?>

<div class="equipment-form">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>ข้อมูลพื้นฐาน</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <?= $form->field($model, 'equipment_code')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'EQ-XXXX',
                                'readonly' => !$isNewRecord
                            ])->label('รหัสอุปกรณ์ <span class="text-danger">*</span>') ?>
                            <?php if ($isNewRecord): ?>
                                <small class="text-muted">ระบบจะสร้างให้อัตโนมัติหากเว้นว่าง</small>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <?= $form->field($model, 'name_th')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'ชื่ออุปกรณ์ภาษาไทย'
                            ])->label('ชื่ออุปกรณ์ (ไทย) <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'name_en')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'Equipment name in English'
                            ])->label('ชื่ออุปกรณ์ (อังกฤษ)') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'category_id')->dropDownList($categories, [
                                'class' => 'form-select',
                                'prompt' => '-- เลือกหมวดหมู่ --'
                            ])->label('หมวดหมู่ <span class="text-danger">*</span>') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Brand & Model -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-tag me-2"></i>ยี่ห้อและรุ่น</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <?= $form->field($model, 'brand')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'เช่น Epson, LG, Samsung'
                            ])->label('ยี่ห้อ') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'model')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'รุ่น/Model'
                            ])->label('รุ่น') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'serial_number')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'หมายเลขเครื่อง'
                            ])->label('Serial Number') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quantity & Pricing -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-calculator me-2"></i>จำนวนและราคา</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <?= $form->field($model, 'total_quantity')->textInput([
                                'type' => 'number',
                                'min' => 1,
                                'class' => 'form-control',
                                'value' => $model->total_quantity ?: 1
                            ])->label('จำนวน <span class="text-danger">*</span>') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'unit')->textInput([
                                'maxlength' => true,
                                'class' => 'form-control',
                                'placeholder' => 'เช่น ชิ้น, เครื่อง',
                                'value' => $model->unit ?: 'ชิ้น'
                            ])->label('หน่วยนับ') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'purchase_price')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => 0,
                                'class' => 'form-control',
                                'placeholder' => '0.00'
                            ])->label('ราคาซื้อ (บาท)') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'rental_rate')->textInput([
                                'type' => 'number',
                                'step' => '0.01',
                                'min' => 0,
                                'class' => 'form-control',
                                'placeholder' => '0.00'
                            ])->label('ค่าเช่า/ชม. (บาท)') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'purchase_date')->textInput([
                                'type' => 'date',
                                'class' => 'form-control'
                            ])->label('วันที่ซื้อ') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'warranty_expiry')->textInput([
                                'type' => 'date',
                                'class' => 'form-control'
                            ])->label('วันหมดประกัน') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-text-paragraph me-2"></i>รายละเอียดเพิ่มเติม</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 3,
                        'class' => 'form-control',
                        'placeholder' => 'รายละเอียดอุปกรณ์...'
                    ])->label('รายละเอียด') ?>

                    <?= $form->field($model, 'specifications')->textarea([
                        'rows' => 4,
                        'class' => 'form-control',
                        'placeholder' => 'ข้อมูลจำเพาะทางเทคนิค เช่น ความละเอียด, ขนาด, กำลังไฟ...'
                    ])->label('ข้อมูลจำเพาะ (Specifications)') ?>

                    <?= $form->field($model, 'notes')->textarea([
                        'rows' => 2,
                        'class' => 'form-control',
                        'placeholder' => 'หมายเหตุ...'
                    ])->label('หมายเหตุ') ?>
                </div>
            </div>

            <!-- Room Assignment -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-door-open me-2"></i>การติดตั้งในห้องประชุม</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-info-circle me-2"></i>
                        เลือกห้องประชุมที่ต้องการติดตั้งอุปกรณ์นี้
                    </div>
                    
                    <div class="row g-3">
                        <?php if (!empty($rooms) && is_array($rooms)): ?>
                            <?php foreach ($rooms as $roomId => $roomName): ?>
                                <?php if (is_scalar($roomId)): ?>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="Equipment[rooms][]" 
                                               value="<?= Html::encode($roomId) ?>" 
                                               id="room-<?= Html::encode($roomId) ?>"
                                               <?= in_array($roomId, $model->roomIds ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="room-<?= Html::encode($roomId) ?>">
                                            <?= Html::encode(is_string($roomName) ? $roomName : ($roomName['name_th'] ?? 'ห้องประชุม')) ?>
                                        </label>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($rooms)): ?>
                        <p class="text-muted mb-0">ยังไม่มีห้องประชุมในระบบ</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>สถานะและการดำเนินการ</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'status')->dropDownList(
                        \common\models\Equipment::getStatusOptions(),
                        ['class' => 'form-select']
                    )->label('สถานะ') ?>

                    <div class="d-grid gap-2 mt-4">
                        <?= Html::submitButton(
                            $isNewRecord ? '<i class="bi bi-plus-lg me-1"></i> บันทึกอุปกรณ์' : '<i class="bi bi-check-lg me-1"></i> บันทึกการเปลี่ยนแปลง',
                            ['class' => 'btn btn-primary btn-lg']
                        ) ?>
                        <?= Html::a('<i class="bi bi-x-lg me-1"></i> ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>

            <!-- Image Upload -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-image me-2"></i>รูปภาพ</h6>
                </div>
                <div class="card-body">
                    <!-- Current Image Preview -->
                    <div id="current-image" class="mb-3 <?= $model->hasImage() ? '' : 'd-none' ?>">
                        <label class="form-label">รูปภาพปัจจุบัน</label>
                        <div class="position-relative d-inline-block">
                            <img id="current-img" 
                                src="<?= $model->hasImage() ? Html::encode($model->imageUrl) : '' ?>" 
                                class="img-thumbnail" 
                                style="max-height: 200px; max-width: 100%;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                                onclick="deleteCurrentImage()" title="ลบรูปภาพ">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Image Preview (hidden by default) -->
                    <div id="new-image-preview" class="mb-3 d-none">
                        <label class="form-label">ตัวอย่างรูปภาพใหม่</label>
                        <div class="position-relative d-inline-block">
                            <img id="preview-img" src="" class="img-thumbnail" style="max-height: 200px; max-width: 100%;">
                            <button type="button" class="btn btn-sm btn-secondary position-absolute top-0 end-0 m-1" 
                                onclick="cancelNewImage()" title="ยกเลิก">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Upload Input -->
                    <div class="mb-2">
                        <label class="form-label">อัปโหลดรูปภาพ <?= $model->hasImage() ? '(เลือกไฟล์ใหม่เพื่อเปลี่ยน)' : '' ?></label>
                        <input type="file" name="Equipment[imageFile]" class="form-control" accept="image/*" id="image-input">
                        <small class="text-muted">รองรับ JPG, PNG, GIF ขนาดไม่เกิน 2MB</small>
                    </div>
                    
                    <!-- Hidden field to track image removal -->
                    <input type="hidden" name="Equipment[removeImage]" id="remove-image" value="0">
                </div>
            </div>

            <!-- Maintenance Log -->
            <?php if (!$isNewRecord): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-tools me-2"></i>ประวัติการซ่อมบำรุง</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#maintenanceModal">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($model->maintenanceLogs)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach (array_slice($model->maintenanceLogs, 0, 5) as $log): ?>
                                    <li class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted"><?= Yii::$app->formatter->asDate($log->date) ?></small>
                                            <span class="badge bg-<?= $log->type == 'repair' ? 'danger' : 'info' ?>"><?= $log->type == 'repair' ? 'ซ่อม' : 'บำรุงรักษา' ?></span>
                                        </div>
                                        <p class="mb-0 small"><?= Html::encode($log->description) ?></p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clipboard-check fs-3 d-block mb-2"></i>
                                <small>ยังไม่มีประวัติการซ่อมบำรุง</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>ข้อมูลระบบ</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted d-block">สร้างเมื่อ</small>
                            <span><?= Yii::$app->formatter->asDatetime($model->created_at) ?></span>
                        </div>
                        <div>
                            <small class="text-muted d-block">แก้ไขล่าสุด</small>
                            <span><?= Yii::$app->formatter->asDatetime($model->updated_at) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Maintenance Log Modal -->
<?php if (!$isNewRecord): ?>
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มประวัติการซ่อมบำรุง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= Url::to(['add-maintenance', 'id' => $model->id]) ?>" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ประเภท</label>
                        <select name="MaintenanceLog[type]" class="form-select" required>
                            <option value="maintenance">บำรุงรักษาตามกำหนด</option>
                            <option value="repair">ซ่อมแซม</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">วันที่</label>
                        <input type="date" name="MaintenanceLog[date]" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea name="MaintenanceLog[description]" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ค่าใช้จ่าย (บาท)</label>
                        <input type="number" name="MaintenanceLog[cost]" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ผู้ดำเนินการ</label>
                        <input type="text" name="MaintenanceLog[performed_by]" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$js = <<<JS
// Image preview for new uploads
document.getElementById('image-input').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('ไฟล์มีขนาดใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 2MB');
            this.value = '';
            return;
        }
        
        // Validate file type
        if (!file.type.match('image.*')) {
            alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น');
            this.value = '';
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('new-image-preview').classList.remove('d-none');
            // Hide current image when new one is selected
            document.getElementById('current-image').classList.add('d-none');
        };
        reader.readAsDataURL(file);
        document.getElementById('remove-image').value = '0';
    }
});

// Delete current image (mark for deletion on save)
function deleteCurrentImage() {
    if (confirm('คุณต้องการลบรูปภาพนี้หรือไม่?')) {
        document.getElementById('current-image').classList.add('d-none');
        document.getElementById('remove-image').value = '1';
    }
}

// Cancel new image selection (revert to current)
function cancelNewImage() {
    document.getElementById('new-image-preview').classList.add('d-none');
    document.getElementById('image-input').value = '';
    // Show current image again if it exists and wasn't marked for deletion
    if (document.getElementById('current-img').src && document.getElementById('remove-image').value !== '1') {
        document.getElementById('current-image').classList.remove('d-none');
    }
}

// Restore current image display
function restoreCurrentImage() {
    document.getElementById('current-image').classList.remove('d-none');
    document.getElementById('remove-image').value = '0';
}
JS;
$this->registerJs($js);
?>
