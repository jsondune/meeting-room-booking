<?php

use yii\helpers\Html;

$this->title = 'แก้ไขอาคาร: ' . $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการอาคาร', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="building-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-pencil me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
