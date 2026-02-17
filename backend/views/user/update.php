<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = 'แก้ไขผู้ใช้: ' . ($model->fullname ?? $model->username);
$this->params['breadcrumbs'][] = ['label' => 'จัดการผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullname ?? $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="user-update">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">แก้ไขข้อมูลบัญชีผู้ใช้งาน</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'departments' => $departments,
        'roles' => $roles,
    ]) ?>
</div>
