<?php

/** @var yii\web\View $this */
/** @var common\models\MeetingRoom $model */

$isNewRecord = true;

echo $this->render('_form', [
    'model' => $model,
    'isNewRecord' => $isNewRecord,
]);
