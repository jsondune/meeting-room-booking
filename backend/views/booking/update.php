<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = 'แก้ไขการจอง: ' . $model->booking_code;
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->booking_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="booking-update">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-pencil-square me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">
                <span class="badge bg-<?= $model->status === 'approved' ? 'success' : ($model->status === 'pending' ? 'warning' : 'secondary') ?>">
                    <?= Html::encode($model->getStatusLabel()) ?>
                </span>
                <span class="ms-2"><?= Html::encode($model->title) ?></span>
            </p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-eye me-2"></i>ดูรายละเอียด', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-info me-2']) ?>
            <?= Html::a('<i class="bi bi-arrow-left me-2"></i>กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'rooms' => $rooms ?? [],
        'users' => $users ?? [],
    ]) ?>
</div>
