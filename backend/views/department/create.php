<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Department $model */

$this->title = 'เพิ่มหน่วยงานใหม่';
$this->params['breadcrumbs'][] = ['label' => 'จัดการหน่วยงาน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="department-create">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">กรอกข้อมูลเพื่อสร้างหน่วยงานใหม่ในระบบ</p>
        </div>
        <div>
            <?= Html::a('<i class="fas fa-arrow-left me-1"></i> กลับ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= $this->render('_form', ['model' => $model]) ?>
</div>
