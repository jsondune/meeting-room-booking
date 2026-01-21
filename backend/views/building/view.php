<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name_th;
$this->params['breadcrumbs'][] = ['label' => 'จัดการอาคาร', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
// Display flash messages
$allowedTypes = ['success', 'error', 'danger', 'warning', 'info'];
foreach (Yii::$app->session->getAllFlashes() as $type => $message):
    if (strpos($type, 'debug') !== false) continue;
    if (!in_array($type, $allowedTypes)) continue;
    
    $alertClass = match($type) {
        'success' => 'alert-success',
        'error', 'danger' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
        default => 'alert-secondary'
    };
?>
<div class="alert <?= $alertClass ?> alert-dismissible fade show" role="alert">
    <?= $message ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endforeach; ?>

<div class="building-view">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-building me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div class="btn-group">
            <?= Html::a('<i class="bi bi-pencil me-1"></i>แก้ไข', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="bi bi-trash me-1"></i>ลบ', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'ยืนยันการลบอาคารนี้?',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">ข้อมูลอาคาร</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table table-striped mb-0'],
                        'attributes' => [
                            [
                                'attribute' => 'code',
                                'format' => 'raw',
                                'value' => '<span class="badge bg-secondary">' . Html::encode($model->code) . '</span>',
                            ],
                            'name_th',
                            'name_en',
                            'address:ntext',
                            'floor_count',
                            [
                                'attribute' => 'is_active',
                                'format' => 'raw',
                                'value' => $model->is_active
                                    ? '<span class="badge bg-success">ใช้งาน</span>'
                                    : '<span class="badge bg-danger">ปิดใช้งาน</span>',
                            ],
                            'description:ntext',
                            'created_at:datetime',
                            'updated_at:datetime',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">ห้องประชุมในอาคาร</h5>
                    <?= Html::a('<i class="bi bi-plus"></i> เพิ่มห้อง', ['/room/create', 'building_id' => $model->id], ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php $rooms = $model->getMeetingRooms()->all(); ?>
                    <?php if (empty($rooms)): ?>
                        <p class="text-muted mb-0">ยังไม่มีห้องประชุมในอาคารนี้</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($rooms as $room): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= Html::encode($room->name_th) ?></strong>
                                        <br><small class="text-muted">ชั้น <?= $room->floor ?> | <?= $room->capacity ?> คน</small>
                                    </div>
                                    <?= Html::a('<i class="bi bi-eye"></i>', ['/room/view', 'id' => $room->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
