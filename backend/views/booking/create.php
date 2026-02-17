<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Booking */

$this->title = 'สร้างการจองใหม่';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="booking-create">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-calendar-plus me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">กรอกข้อมูลเพื่อจองห้องประชุม</p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left me-2"></i>กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'rooms' => $rooms ?? [],
        'users' => $users ?? [],
    ]) ?>
</div>
