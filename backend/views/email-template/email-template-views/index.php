<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'เทมเพลตอีเมล';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="email-template-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-envelope-paper me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>เพิ่มเทมเพลต', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'template_key',
                    'name',
                    'subject',
                    [
                        'attribute' => 'is_active',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->is_active
                                ? '<span class="badge bg-success">ใช้งาน</span>'
                                : '<span class="badge bg-danger">ปิดใช้งาน</span>';
                        },
                    ],
                    'updated_at:datetime',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-info me-1',
                                    'title' => 'ดู',
                                ]);
                            },
                            'update' => function ($url, $model) {
                                return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary me-1',
                                    'title' => 'แก้ไข',
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<i class="bi bi-trash"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => 'ลบ',
                                    'data' => [
                                        'confirm' => 'ยืนยันการลบเทมเพลตนี้?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
