<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Department $model */

$isUpdate = !$model->isNewRecord;
?>

<div class="department-form">
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>ข้อมูลพื้นฐาน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">ชื่อหน่วยงาน <span class="text-danger">*</span></label>
                                <input type="text" name="Department[name]" 
                                    class="form-control" 
                                    value="<?= Html::encode($model->name ?? '') ?>"
                                    placeholder="เช่น กองกลาง, คณะวิทยาศาสตร์"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">รหัสหน่วยงาน <span class="text-danger">*</span></label>
                                <input type="text" name="Department[code]" 
                                    class="form-control text-uppercase" 
                                    value="<?= Html::encode($model->code ?? '') ?>"
                                    placeholder="เช่น ADMIN, SCI"
                                    maxlength="20"
                                    required>
                                <div class="form-text">ใช้ตัวอักษรภาษาอังกฤษและตัวเลข</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ชื่อภาษาอังกฤษ</label>
                                <input type="text" name="Department[name_en]" 
                                    class="form-control" 
                                    value="<?= Html::encode($model->name_en ?? '') ?>"
                                    placeholder="English name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ชื่อย่อ</label>
                                <input type="text" name="Department[short_name]" 
                                    class="form-control" 
                                    value="<?= Html::encode($model->short_name ?? '') ?>"
                                    placeholder="ชื่อย่อหน่วยงาน">
                            </div>
                            <div class="col-12">
                                <label class="form-label">คำอธิบาย</label>
                                <textarea name="Department[description]" 
                                    class="form-control" 
                                    rows="3"
                                    placeholder="อธิบายเกี่ยวกับหน่วยงาน หน้าที่ความรับผิดชอบ"><?= Html::encode($model->description ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hierarchy -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-sitemap text-info me-2"></i>โครงสร้างลำดับชั้น
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">หน่วยงานหลัก</label>
                                <select name="Department[parent_id]" class="form-select">
                                    <option value="">-- ไม่มี (เป็นหน่วยงานระดับสูงสุด) --</option>
                                    <option value="1" <?= ($model->parent_id ?? '') == 1 ? 'selected' : '' ?>>สำนักงานเลขานุการกรม</option>
                                    <option value="2" <?= ($model->parent_id ?? '') == 2 ? 'selected' : '' ?>>คณะวิทยาศาสตร์</option>
                                    <option value="3" <?= ($model->parent_id ?? '') == 3 ? 'selected' : '' ?>>คณะวิศวกรรมศาสตร์</option>
                                    <option value="4" <?= ($model->parent_id ?? '') == 4 ? 'selected' : '' ?>>คณะมนุษยศาสตร์</option>
                                    <option value="5" <?= ($model->parent_id ?? '') == 5 ? 'selected' : '' ?>>สำนักวิทยบริการ</option>
                                </select>
                                <div class="form-text">เลือกหน่วยงานที่อยู่เหนือหน่วยงานนี้ในโครงสร้างองค์กร</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ระดับ</label>
                                <select name="Department[level]" class="form-select">
                                    <option value="1" <?= ($model->level ?? 1) == 1 ? 'selected' : '' ?>>ระดับ 1 (สูงสุด)</option>
                                    <option value="2" <?= ($model->level ?? 1) == 2 ? 'selected' : '' ?>>ระดับ 2</option>
                                    <option value="3" <?= ($model->level ?? 1) == 3 ? 'selected' : '' ?>>ระดับ 3</option>
                                    <option value="4" <?= ($model->level ?? 1) == 4 ? 'selected' : '' ?>>ระดับ 4</option>
                                    <option value="5" <?= ($model->level ?? 1) == 5 ? 'selected' : '' ?>>ระดับ 5</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ลำดับการแสดง</label>
                                <input type="number" name="Department[sort_order]" 
                                    class="form-control" 
                                    value="<?= Html::encode($model->sort_order ?? 0) ?>"
                                    min="0">
                            </div>
                        </div>

                        <?php if ($isUpdate): ?>
                        <!-- Child departments -->
                        <div class="mt-4">
                            <label class="form-label">หน่วยงานย่อย</label>
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-folder me-1"></i>กองกลาง
                                    </span>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-folder me-1"></i>กองคลัง
                                    </span>
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-folder me-1"></i>กองบริหารงานบุคคล
                                    </span>
                                    <a href="<?= Url::to(['create']) ?>" class="badge bg-secondary px-3 py-2 text-decoration-none">
                                        <i class="fas fa-plus me-1"></i>เพิ่มหน่วยงานย่อย
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-address-book text-success me-2"></i>ข้อมูลติดต่อ
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">โทรศัพท์</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" name="Department[phone]" 
                                        class="form-control" 
                                        value="<?= Html::encode($model->phone ?? '') ?>"
                                        placeholder="02-XXX-XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">โทรสาร</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-fax"></i></span>
                                    <input type="tel" name="Department[fax]" 
                                        class="form-control" 
                                        value="<?= Html::encode($model->fax ?? '') ?>"
                                        placeholder="02-XXX-XXXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="Department[email]" 
                                        class="form-control" 
                                        value="<?= Html::encode($model->email ?? '') ?>"
                                        placeholder="department@gmail.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">เว็บไซต์</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-globe"></i></span>
                                    <input type="url" name="Department[website]" 
                                        class="form-control" 
                                        value="<?= Html::encode($model->website ?? '') ?>"
                                        placeholder="https://">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">ที่อยู่/สถานที่ตั้ง</label>
                                <textarea name="Department[address]" 
                                    class="form-control" 
                                    rows="2"
                                    placeholder="อาคาร ชั้น ห้อง"><?= Html::encode($model->address ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Head of Department -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie text-warning me-2"></i>หัวหน้าหน่วยงาน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">หัวหน้าหน่วยงาน</label>
                                <select name="Department[head_user_id]" class="form-select">
                                    <option value="">-- เลือกหัวหน้าหน่วยงาน --</option>
                                    <option value="1">รศ.ดร.สมชาย ใจดี</option>
                                    <option value="2">ผศ.ดร.สมหญิง รักงาน</option>
                                    <option value="3">นายสมศักดิ์ มานะ</option>
                                    <option value="4">นางสาวสมใจ ขยัน</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">รองหัวหน้าหน่วยงาน</label>
                                <select name="Department[deputy_user_id]" class="form-select">
                                    <option value="">-- เลือกรองหัวหน้าหน่วยงาน --</option>
                                    <option value="1">รศ.ดร.สมชาย ใจดี</option>
                                    <option value="2">ผศ.ดร.สมหญิง รักงาน</option>
                                    <option value="3">นายสมศักดิ์ มานะ</option>
                                    <option value="4">นางสาวสมใจ ขยัน</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status & Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-cog text-secondary me-2"></i>ตั้งค่า
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="Department[status]" class="form-select">
                                <option value="active" <?= ($model->status ?? 'active') === 'active' ? 'selected' : '' ?>>
                                    ใช้งานอยู่
                                </option>
                                <option value="inactive" <?= ($model->status ?? '') === 'inactive' ? 'selected' : '' ?>>
                                    ไม่ใช้งาน
                                </option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">สีประจำหน่วยงาน</label>
                            <div class="input-group">
                                <input type="color" name="Department[color]" 
                                    class="form-control form-control-color" 
                                    value="<?= Html::encode($model->color ?? '#3b82f6') ?>">
                                <input type="text" class="form-control" 
                                    id="colorHex" 
                                    value="<?= Html::encode($model->color ?? '#3b82f6') ?>"
                                    pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <div class="form-text">ใช้แสดงในปฏิทินและแผนภูมิ</div>
                        </div>

                        <hr>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?= $isUpdate ? 'บันทึกการแก้ไข' : 'สร้างหน่วยงาน' ?>
                            </button>
                            <?= Html::a('<i class="fas fa-times me-1"></i> ยกเลิก', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                        </div>
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-image text-info me-2"></i>โลโก้หน่วยงาน
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="border rounded bg-light d-flex align-items-center justify-content-center mx-auto" 
                                style="width: 150px; height: 150px;" id="logoPreview">
                                <?php if ($isUpdate && !empty($model->logo)): ?>
                                    <img src="<?= Html::encode($model->logo) ?>" 
                                        class="img-fluid" 
                                        style="max-height: 140px;">
                                <?php else: ?>
                                    <div class="text-muted">
                                        <i class="fas fa-building fa-3x mb-2"></i>
                                        <div class="small">ยังไม่มีโลโก้</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-2">
                            <input type="file" name="logo" 
                                class="form-control" 
                                id="logoInput"
                                accept="image/*">
                        </div>
                        <div class="form-text text-center">
                            PNG, JPG ขนาดไม่เกิน 2MB<br>
                            แนะนำขนาด 200x200 พิกเซล
                        </div>
                    </div>
                </div>

                <!-- Booking Settings -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt text-danger me-2"></i>การตั้งค่าการจอง
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                name="Department[can_book_external]" 
                                id="canBookExternal"
                                value="1"
                                <?= ($model->can_book_external ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="canBookExternal">
                                จองห้องประชุมหน่วยงานอื่นได้
                            </label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                name="Department[require_approval]" 
                                id="requireApproval"
                                value="1"
                                <?= ($model->require_approval ?? true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="requireApproval">
                                ต้องขออนุมัติก่อนจอง
                            </label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">จำนวนการจองสูงสุด/เดือน</label>
                            <div class="input-group">
                                <input type="number" name="Department[max_bookings_per_month]" 
                                    class="form-control" 
                                    value="<?= Html::encode($model->max_bookings_per_month ?? '') ?>"
                                    min="0"
                                    placeholder="ไม่จำกัด">
                                <span class="input-group-text">ครั้ง</span>
                            </div>
                            <div class="form-text">เว้นว่างหากไม่จำกัด</div>
                        </div>
                    </div>
                </div>

                <?php if ($isUpdate): ?>
                <!-- Statistics -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar text-primary me-2"></i>สถิติ
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">บุคลากร</span>
                                <strong>25 คน</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">การจองทั้งหมด</span>
                                <strong>156 ครั้ง</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted">การจองเดือนนี้</span>
                                <strong>12 ครั้ง</strong>
                            </li>
                            <li class="d-flex justify-content-between py-2">
                                <span class="text-muted">สร้างเมื่อ</span>
                                <strong>1 ม.ค. 2567</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php
$js = <<<JS
// Logo preview
document.getElementById('logoInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').innerHTML = 
                '<img src="' + e.target.result + '" class="img-fluid" style="max-height: 140px;">';
        };
        reader.readAsDataURL(file);
    }
});

// Color picker sync
document.querySelector('input[type="color"]').addEventListener('input', function() {
    document.getElementById('colorHex').value = this.value;
});

document.getElementById('colorHex').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.querySelector('input[type="color"]').value = this.value;
    }
});

// Code field uppercase
document.querySelector('input[name="Department[code]"]').addEventListener('input', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '');
});
JS;
$this->registerJs($js);
?>
