<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Audit Log';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="audit-log-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-journal-text me-2"></i><?= Html::encode($this->title) ?>
        </h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'headerOptions' => ['style' => 'width: 150px'],
                    ],
                    [
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->user ? Html::encode($model->user->username) : '-';
                        },
                    ],
                    'action',
                    'model_type',
                    'model_id',
                    [
                        'attribute' => 'ip_address',
                        'headerOptions' => ['style' => 'width: 120px'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-info',
                                    'title' => 'ดูรายละเอียด',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
