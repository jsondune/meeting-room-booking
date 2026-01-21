<?php

/** @var yii\web\View $this */
/** @var common\models\Building $model */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\ActiveForm;
use common\models\BuildingImage;

$this->title = $model->isNewRecord ? 'เพิ่มอาคาร' : 'แก้ไขอาคาร: ' . $model->name_th;
?>

<div class="page-header">
    <h1 class="page-title"><?= Html::encode($this->title) ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
            <li class="breadcrumb-item"><a href="<?= Url::to(['building/index']) ?>">อาคาร</a></li>
            <li class="breadcrumb-item active"><?= $model->isNewRecord ? 'เพิ่มใหม่' : 'แก้ไข' ?></li>
        </ol>
    </nav>
</div>

<?php
// Display flash messages (filter out debug messages)
$allowedTypes = ['success', 'error', 'danger', 'warning', 'info'];
foreach (Yii::$app->session->getAllFlashes() as $type => $message):
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
    'id' => 'building-form',
    'options' => ['enctype' => 'multipart/form-data', 'class' => 'needs-validation'],
    'fieldConfig' => [
        'template' => "{label}\n{input}\n{error}",
        'labelOptions' => ['class' => 'form-label'],
        'inputOptions' => ['class' => 'form-control'],
        'errorOptions' => ['class' => 'invalid-feedback'],
    ],
]); ?>

<div class="row">
    <!-- Left Column - Basic Info -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>ข้อมูลอาคาร</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'placeholder' => 'เช่น BLD-001']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'name_th')->textInput(['maxlength' => true, 'placeholder' => 'ชื่ออาคาร (ภาษาไทย)']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'name_en')->textInput(['maxlength' => true, 'placeholder' => 'Building Name (English)']) ?>
                    </div>
                </div>

                <?= $form->field($model, 'address')->textarea(['rows' => 2, 'placeholder' => 'ที่อยู่อาคาร']) ?>

                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'floor_count')->textInput(['type' => 'number', 'min' => 1, 'value' => $model->floor_count ?: 1]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'latitude')->textInput(['placeholder' => 'เช่น 13.7563']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'longitude')->textInput(['placeholder' => 'เช่น 100.5018']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'is_active')->checkbox(['label' => 'เปิดใช้งาน']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column - Images -->
    <div class="col-lg-4">
        <?php if (!$model->isNewRecord): ?>
        <?php
            $existingImages = BuildingImage::find()->where(['building_id' => $model->id])->orderBy(['is_primary' => SORT_DESC, 'sort_order' => SORT_ASC])->all();
            $imageCount = count($existingImages);
            $maxImages = 5;
            $remainingSlots = $maxImages - $imageCount;
        ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-images me-2"></i>รูปภาพอาคาร</h5>
                <span class="badge <?= $imageCount >= $maxImages ? 'bg-warning' : 'bg-info' ?>"><?= $imageCount ?>/<?= $maxImages ?> รูป</span>
            </div>
            <div class="card-body">
                <!-- Existing Images -->
                <?php if ($imageCount > 0): ?>
                <div class="mb-3">
                    <label class="form-label text-muted small">รูปภาพปัจจุบัน <span class="text-primary">(คลิกที่รูปเพื่อตั้งเป็นรูปหลัก)</span></label>
                    <div class="row g-2">
                        <?php foreach ($existingImages as $image): ?>
                        <div class="col-6" id="image-container-<?= $image->id ?>">
                            <div class="position-relative image-item <?= $image->is_primary ? 'border-primary border-2' : 'border' ?>" 
                                 style="border-radius: 8px; overflow: hidden; cursor: pointer;"
                                 onclick="setPrimaryImage(<?= $image->id ?>)">
                                <img src="<?= $image->getUrl() ?>" 
                                     class="w-100" 
                                     style="height: 100px; object-fit: cover;"
                                     alt="<?= Html::encode($image->original_name) ?>">
                                
                                <?php if ($image->is_primary): ?>
                                <span class="position-absolute top-0 start-0 badge bg-primary m-1">
                                    <i class="bi bi-star-fill"></i> รูปหลัก
                                </span>
                                <?php endif; ?>
                                
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                        style="padding: 2px 6px; font-size: 0.7rem;"
                                        onclick="event.stopPropagation(); deleteImage(<?= $image->id ?>);"
                                        title="ลบรูปนี้">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="text-center mt-1">
                                <input type="radio" name="primaryImage" value="<?= $image->id ?>" 
                                       id="primary-<?= $image->id ?>" <?= $image->is_primary ? 'checked' : '' ?>
                                       class="form-check-input">
                                <label for="primary-<?= $image->id ?>" class="form-check-label small">รูปหลัก</label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Hidden inputs for delete images -->
                <div id="deleteImagesContainer"></div>

                <!-- Upload New Images -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="bi bi-cloud-upload me-1"></i>อัปโหลดรูปภาพเพิ่มเติม 
                        <?php if ($remainingSlots > 0): ?>
                            <span class="text-muted">(เหลืออีก <?= $remainingSlots ?> รูป)</span>
                        <?php endif; ?>
                    </label>
                    
                    <?php if ($remainingSlots > 0): ?>
                    <input type="file" 
                           class="form-control" 
                           name="Building[imageFiles][]" 
                           multiple 
                           accept="image/jpeg,image/png,image/gif,image/webp"
                           id="imageUpload">
                    <div class="form-text">
                        <i class="bi bi-info-circle me-1"></i>รองรับ JPG, PNG, GIF, WEBP ขนาดไม่เกิน 2MB/รูป (สูงสุด 5 รูป)
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        ครบ 5 รูปแล้ว กรุณาลบรูปเดิมก่อนเพิ่มรูปใหม่
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Preview New Uploads -->
                <div id="imagePreviewContainer" class="row g-2" style="display: none;"></div>
            </div>
        </div>
        <?php else: ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-images me-2"></i>รูปภาพอาคาร</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    กรุณาบันทึกข้อมูลอาคารก่อน จากนั้นจึงอัปโหลดรูปภาพได้
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Form Actions -->
<div class="card border-0 shadow-sm">
    <div class="card-body d-flex justify-content-between">
        <a href="<?= Url::to(['building/index']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i>ยกเลิก
        </a>
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-1"></i>บันทึก
        </button>
    </div>
</div>

<?php ActiveForm::end(); ?>

<script>
// Set primary image
function setPrimaryImage(imageId) {
    document.getElementById('primary-' + imageId).checked = true;
    
    // Update visual
    document.querySelectorAll('.image-item').forEach(item => {
        item.classList.remove('border-primary', 'border-2');
        item.classList.add('border');
    });
    
    const container = document.getElementById('image-container-' + imageId);
    if (container) {
        const imageItem = container.querySelector('.image-item');
        imageItem.classList.remove('border');
        imageItem.classList.add('border-primary', 'border-2');
    }
}

// Delete image
function deleteImage(imageId) {
    if (!confirm('ต้องการลบรูปภาพนี้?')) return;
    
    // Add hidden input for delete
    const container = document.getElementById('deleteImagesContainer');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'deleteImages[]';
    input.value = imageId;
    container.appendChild(input);
    
    // Hide the image container with fade effect
    const imageContainer = document.getElementById('image-container-' + imageId);
    if (imageContainer) {
        imageContainer.style.transition = 'opacity 0.3s';
        imageContainer.style.opacity = '0';
        setTimeout(() => {
            imageContainer.style.display = 'none';
        }, 300);
    }
}

// Preview new uploads
const imageUpload = document.getElementById('imageUpload');
if (imageUpload) {
    imageUpload.addEventListener('change', function(e) {
        const previewContainer = document.getElementById('imagePreviewContainer');
        previewContainer.innerHTML = '';
        previewContainer.style.display = 'flex';
        
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        Array.from(this.files).forEach((file, index) => {
            // Validate file
            if (!allowedTypes.includes(file.type)) {
                alert('ไฟล์ ' + file.name + ' ไม่ใช่รูปภาพที่รองรับ');
                return;
            }
            if (file.size > maxSize) {
                alert('ไฟล์ ' + file.name + ' มีขนาดเกิน 2MB');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6';
                col.innerHTML = `
                    <div class="position-relative border rounded" style="overflow: hidden;">
                        <img src="${e.target.result}" class="w-100" style="height: 100px; object-fit: cover;">
                        <span class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white text-center small py-1">
                            ${file.name.substring(0, 15)}...
                        </span>
                    </div>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    });
}
</script>

<style>
.image-item {
    transition: all 0.2s ease;
}
.image-item:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
