<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Holiday;
?>

<div class="holiday-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'holiday_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'holiday_type')->dropDownList(Holiday::getTypeOptions(), ['prompt' => '-- เลือกประเภท --']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name_th')->textInput(['maxlength' => true, 'placeholder' => 'ชื่อวันหยุด (ภาษาไทย)']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true, 'placeholder' => 'Holiday Name (English)']) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'รายละเอียดเพิ่มเติม (ถ้ามี)']) ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'is_recurring')->checkbox(['label' => 'เกิดซ้ำทุกปี']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'is_active')->checkbox(['label' => 'เปิดใช้งาน']) ?>
        </div>
    </div>

    <div class="form-group mt-4">
        <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i>บันทึก', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="bi bi-x-lg me-1"></i>ยกเลิก', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
