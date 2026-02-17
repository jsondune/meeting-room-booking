<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\EmailTemplate $model */

$this->title = 'สร้างเทมเพลตอีเมลใหม่';
$this->params['breadcrumbs'][] = ['label' => 'จัดการเทมเพลตอีเมล', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="email-template-create">
    <div class="mb-4">
        <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
        <p class="text-muted mb-0">สร้างเทมเพลตอีเมลสำหรับการแจ้งเตือนต่างๆ</p>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
