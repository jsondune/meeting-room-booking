<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Department $model */

$this->title = 'แก้ไขหน่วยงาน: ' . $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการหน่วยงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_th, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="department-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">แก้ไขข้อมูลหน่วยงาน <?= Html::encode($model->code) ?></p>
        </div>
        <div>
            <?= Html::a('<i class="fas fa-eye me-1"></i> ดูรายละเอียด', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info me-2']) ?>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= $this->render('_form', ['model' => $model]) ?>
</div>
