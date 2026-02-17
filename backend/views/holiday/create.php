<?php

use yii\helpers\Html;

$this->title = 'เพิ่มวันหยุด';
$this->params['breadcrumbs'][] = ['label' => 'จัดการวันหยุด', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="holiday-create">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-plus-circle me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= $this->render('_form', ['model' => $model]) ?>
        </div>
    </div>
</div>
