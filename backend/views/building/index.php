<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'จัดการอาคาร';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="building-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-building me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <?= Html::a('<i class="bi bi-plus-lg me-1"></i>เพิ่มอาคาร', ['create'], ['class' => 'btn btn-primary']) ?>
    </div>

    <!-- Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="keyword" class="form-control" placeholder="ค้นหา..." 
                           value="<?= Html::encode(Yii::$app->request->get('keyword')) ?>">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- สถานะทั้งหมด --</option>
                        <option value="1" <?= Yii::$app->request->get('status') === '1' ? 'selected' : '' ?>>ใช้งาน</option>
                        <option value="0" <?= Yii::$app->request->get('status') === '0' ? 'selected' : '' ?>>ปิดใช้งาน</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-striped table-hover mb-0 align-middle'],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'label' => 'รูปภาพ',
                        'format' => 'raw',
                        'contentOptions' => ['style' => 'width: 80px;'],
                        'value' => function ($model) {
                            $primaryImage = $model->getPrimaryImage();
                            if ($primaryImage) {
                                return '<img src="' . $primaryImage->getUrl() . '" 
                                        class="rounded" 
                                        style="width: 60px; height: 45px; object-fit: cover;"
                                        alt="' . Html::encode($model->name_th) . '">';
                            }
                            return '<div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                    style="width: 60px; height: 45px;">
                                    <i class="bi bi-building text-muted"></i>
                                </div>';
                        },
                    ],
                    [
                        'attribute' => 'code',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span class="badge bg-secondary">' . Html::encode($model->code) . '</span>';
                        },
                    ],
                    [
                        'attribute' => 'name_th',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $html = '<strong>' . Html::encode($model->name_th) . '</strong>';
                            if ($model->name_en) {
                                $html .= '<br><small class="text-muted">' . Html::encode($model->name_en) . '</small>';
                            }
                            return $html;
                        },
                    ],
                    [
                        'attribute' => 'address',
                        'value' => function ($model) {
                            return $model->address ? mb_substr($model->address, 0, 50) . '...' : '-';
                        },
                    ],
                    [
                        'label' => 'จำนวนห้อง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $count = $model->getMeetingRooms()->count();
                            return '<span class="badge bg-info">' . $count . ' ห้อง</span>';
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
                                        'confirm' => 'ยืนยันการลบอาคารนี้?',
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
