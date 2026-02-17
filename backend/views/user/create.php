<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = 'เพิ่มผู้ใช้งาน';
$this->params['breadcrumbs'][] = ['label' => 'จัดการผู้ใช้งาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-create">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">สร้างบัญชีผู้ใช้งานใหม่</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'departments' => $departments,
        'roles' => $roles,
    ]) ?>
</div>
