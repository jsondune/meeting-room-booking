<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\Holiday;

$this->title = 'จัดการวันหยุด';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="holiday-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-calendar-heart me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-download me-1"></i>นำเข้าวันหยุด', ['import'], ['class' => 'btn btn-outline-success']) ?>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i>เพิ่มวันหยุด', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'date',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $thaiDate = Yii::$app->formatter->asDate($model->date, 'php:d M Y');
                            $dayOfWeek = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'][date('w', strtotime($model->date))];
                            return "<strong>{$thaiDate}</strong> <small class='text-muted'>({$dayOfWeek})</small>";
                        },
                    ],
                    [
                        'attribute' => 'name_th',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $html = Html::encode($model->name_th);
                            if ($model->name_en) {
                                $html .= '<br><small class="text-muted">' . Html::encode($model->name_en) . '</small>';
                            }
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'holiday_type',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $types = Holiday::getTypeOptions();
                            $colors = [
                                Holiday::TYPE_NATIONAL => 'danger',
                                Holiday::TYPE_REGIONAL => 'warning',
                                Holiday::TYPE_ORGANIZATION => 'info',
                                Holiday::TYPE_SPECIAL => 'secondary',
                            ];
                            $label = $types[$model->holiday_type] ?? $model->holiday_type;
                            $color = $colors[$model->holiday_type] ?? 'secondary';
                            return "<span class='badge bg-{$color}'>{$label}</span>";
                        },
                    ],
                    [
                        'attribute' => 'is_recurring',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->is_recurring
                                ? '<span class="badge bg-success"><i class="bi bi-arrow-repeat me-1"></i>ทุกปี</span>'
                                : '<span class="badge bg-secondary">ครั้งเดียว</span>';
                        },
                    ],
                    [
                        'attribute' => 'is_active',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->is_active
                                ? '<span class="badge bg-success">ใช้งาน</span>'
                                : '<span class="badge bg-danger">ปิดใช้งาน</span>';
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update} {toggle} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary me-1',
                                    'title' => 'แก้ไข',
                                ]);
                            },
                            'toggle' => function ($url, $model) {
                                $icon = $model->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off text-danger';
                                return Html::a("<i class='bi {$icon}'></i>", ['toggle-status', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-secondary me-1',
                                    'title' => $model->is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน',
                                    'data' => ['method' => 'post'],
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                return Html::a('<i class="bi bi-trash"></i>', $url, [
                                    'class' => 'btn btn-sm btn-outline-danger',
                                    'title' => 'ลบ',
                                    'data' => [
                                        'confirm' => 'ยืนยันการลบวันหยุดนี้?',
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
