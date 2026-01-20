<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="building-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'placeholder' => 'เช่น BLD-A']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'name_th')->textInput(['maxlength' => true, 'placeholder' => 'ชื่ออาคาร (ภาษาไทย)']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'name_en')->textInput(['maxlength' => true, 'placeholder' => 'Building Name (English)']) ?>
        </div>
    </div>

    <?= $form->field($model, 'address')->textarea(['rows' => 2, 'placeholder' => 'ที่อยู่']) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'floor_count')->textInput(['type' => 'number', 'min' => 1]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sort_order')->textInput(['type' => 'number', 'min' => 0]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'is_active')->checkbox(['label' => 'เปิดใช้งาน']) ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => 3, 'placeholder' => 'รายละเอียดเพิ่มเติม (ถ้ามี)']) ?>

    <div class="form-group mt-4">
        <?= Html::submitButton('<i class="bi bi-check-lg me-1"></i>บันทึก', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="bi bi-x-lg me-1"></i>ยกเลิก', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
