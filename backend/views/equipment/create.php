<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Equipment $model */

$this->title = 'เพิ่มอุปกรณ์';
$this->params['breadcrumbs'][] = ['label' => 'จัดการอุปกรณ์', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="equipment-create">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">เพิ่มอุปกรณ์และสิ่งอำนวยความสะดวกใหม่</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'categories' => $categories,
        'rooms' => $rooms,
    ]) ?>
</div>
