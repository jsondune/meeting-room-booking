<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Equipment $model */

$this->title = 'แก้ไขอุปกรณ์: ' . $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการอุปกรณ์', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name_th, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="equipment-update">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">รหัส: <?= Html::encode($model->equipment_code) ?></p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'rooms' => $rooms,
    ]) ?>
</div>
