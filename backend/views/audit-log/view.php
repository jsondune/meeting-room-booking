<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = 'Audit Log #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Audit Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="audit-log-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-journal-text me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i>กลับ', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-striped mb-0'],
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'user_id',
                        'value' => $model->user ? $model->user->username : '-',
                    ],
                    'action',
                    'model_type',
                    'model_id',
                    [
                        'attribute' => 'old_values',
                        'format' => 'raw',
                        'value' => '<pre class="bg-light p-2">' . Html::encode(json_encode(json_decode($model->old_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>',
                    ],
                    [
                        'attribute' => 'new_values',
                        'format' => 'raw',
                        'value' => '<pre class="bg-light p-2">' . Html::encode(json_encode(json_decode($model->new_values), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>',
                    ],
                    'ip_address',
                    'user_agent',
                    'created_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
</div>
