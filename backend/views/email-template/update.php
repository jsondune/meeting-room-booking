<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */

$this->title = 'แก้ไขเทมเพลต: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'จัดการเทมเพลตอีเมล', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'แก้ไข';
?>

<div class="email-template-update">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">แก้ไขเนื้อหาและการตั้งค่าเทมเพลตอีเมล</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
