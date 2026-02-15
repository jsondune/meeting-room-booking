<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $searchModel */

$this->title = 'Audit Log';
$this->params['breadcrumbs'][] = $this->title;

// Get filter values from request
$filterAction = Yii::$app->request->get('action', '');
$filterUserId = Yii::$app->request->get('user_id', '');
$filterDateFrom = Yii::$app->request->get('date_from', '');
$filterDateTo = Yii::$app->request->get('date_to', '');
$filterModelClass = Yii::$app->request->get('model_class', '');
$filterIp = Yii::$app->request->get('ip_address', '');

// Get unique actions for dropdown
$actions = \common\models\AuditLog::find()
    ->select('action')
    ->distinct()
    ->orderBy('action')
    ->column();
$actionOptions = array_combine($actions, $actions);

// Get users for dropdown
$users = \common\models\User::find()
    ->select(['id', 'username', 'full_name'])
    ->where(['status' => 10])
    ->orderBy('username')
    ->all();
$userOptions = [];
foreach ($users as $user) {
    $userOptions[$user->id] = $user->username . ' (' . $user->full_name . ')';
}
?>

<div class="audit-log-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-journal-text me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#filterPanel">
                <i class="bi bi-funnel me-1"></i>ตัวกรอง
            </button>
            <?= Html::a('<i class="bi bi-arrow-counterclockwise me-1"></i>รีเซ็ต', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="collapse <?= ($filterAction || $filterUserId || $filterDateFrom || $filterDateTo || $filterModelClass || $filterIp) ? 'show' : '' ?>" id="filterPanel">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <i class="bi bi-funnel me-1"></i>ค้นหา / กรองข้อมูล
            </div>
            <div class="card-body">
                <form method="get" action="<?= \yii\helpers\Url::to(['index']) ?>">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" name="date_from" class="form-control" value="<?= Html::encode($filterDateFrom) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" name="date_to" class="form-control" value="<?= Html::encode($filterDateTo) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">การกระทำ</label>
                            <select name="action" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                <?php foreach ($actionOptions as $key => $label): ?>
                                    <option value="<?= Html::encode($key) ?>" <?= $filterAction === $key ? 'selected' : '' ?>><?= Html::encode($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">ผู้ใช้</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- ทั้งหมด --</option>
                                <?php foreach ($userOptions as $id => $name): ?>
                                    <option value="<?= $id ?>" <?= $filterUserId == $id ? 'selected' : '' ?>><?= Html::encode($name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Model</label>
                            <input type="text" name="model_class" class="form-control" placeholder="เช่น User, Booking" value="<?= Html::encode($filterModelClass) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">IP Address</label>
                            <input type="text" name="ip_address" class="form-control" placeholder="เช่น 192.168.1.1" value="<?= Html::encode($filterIp) ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>ค้นหา
                            </button>
                            <?= Html::a('<i class="bi bi-x-lg me-1"></i>ล้างตัวกรอง', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ทั้งหมด</h6>
                            <h3 class="mb-0"><?= number_format($dataProvider->totalCount) ?></h3>
                        </div>
                        <i class="bi bi-journal-text fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">วันนี้</h6>
                            <h3 class="mb-0"><?= number_format(\common\models\AuditLog::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00')])->count()) ?></h3>
                        </div>
                        <i class="bi bi-calendar-day fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">สัปดาห์นี้</h6>
                            <h3 class="mb-0"><?= number_format(\common\models\AuditLog::find()->where(['>=', 'created_at', date('Y-m-d 00:00:00', strtotime('monday this week'))])->count()) ?></h3>
                        </div>
                        <i class="bi bi-calendar-week fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">เดือนนี้</h6>
                            <h3 class="mb-0"><?= number_format(\common\models\AuditLog::find()->where(['>=', 'created_at', date('Y-m-01 00:00:00')])->count()) ?></h3>
                        </div>
                        <i class="bi bi-calendar-month fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-1"></i>รายการ Audit Log</span>
            <span class="badge bg-secondary"><?= number_format($dataProvider->totalCount) ?> รายการ</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'tableOptions' => ['class' => 'table table-striped table-hover mb-0'],
                    'pager' => [
                        'class' => 'yii\bootstrap5\LinkPager',
                        'options' => ['class' => 'pagination justify-content-center my-3'],
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['style' => 'width: 50px'],
                        ],
                        [
                            'attribute' => 'created_at',
                            'label' => 'วันที่/เวลา',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 160px'],
                            'value' => function ($model) {
                                // Format as CE (ค.ศ.) not BE (พ.ศ.)
                                $timestamp = strtotime($model->created_at);
                                return '<span class="text-nowrap">' . date('d/m/Y', $timestamp) . '</span><br>'
                                     . '<small class="text-muted">' . date('H:i:s', $timestamp) . '</small>';
                            },
                        ],
                        [
                            'attribute' => 'username',
                            'label' => 'ผู้ใช้',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 150px'],
                            'value' => function ($model) {
                                if ($model->user) {
                                    return Html::a(Html::encode($model->username ?: $model->user->username), 
                                        ['/user/view', 'id' => $model->user_id], 
                                        ['class' => 'text-decoration-none']);
                                }
                                return $model->username ?: '<span class="text-muted">-</span>';
                            },
                        ],
                        [
                            'attribute' => 'action',
                            'label' => 'การกระทำ',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 120px'],
                            'value' => function ($model) {
                                $badges = [
                                    'create' => 'bg-success',
                                    'update' => 'bg-info',
                                    'delete' => 'bg-danger',
                                    'login' => 'bg-primary',
                                    'logout' => 'bg-secondary',
                                    'approve' => 'bg-success',
                                    'reject' => 'bg-warning text-dark',
                                    'cancel' => 'bg-danger',
                                ];
                                $class = $badges[$model->action] ?? 'bg-secondary';
                                return '<span class="badge ' . $class . '">' . Html::encode($model->action) . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'model_class',
                            'label' => 'Model',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 120px'],
                            'value' => function ($model) {
                                if (empty($model->model_class)) {
                                    return '<span class="text-muted">-</span>';
                                }
                                // Get short class name
                                $parts = explode('\\', $model->model_class);
                                $shortName = end($parts);
                                return '<code>' . Html::encode($shortName) . '</code>';
                            },
                        ],
                        [
                            'attribute' => 'model_id',
                            'label' => 'ID',
                            'headerOptions' => ['style' => 'width: 80px'],
                            'value' => function ($model) {
                                return $model->model_id ?: '-';
                            },
                        ],
                        [
                            'attribute' => 'description',
                            'label' => 'รายละเอียด',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $desc = $model->description ?: '';
                                if (strlen($desc) > 50) {
                                    return '<span title="' . Html::encode($desc) . '">' . Html::encode(mb_substr($desc, 0, 50)) . '...</span>';
                                }
                                return Html::encode($desc) ?: '<span class="text-muted">-</span>';
                            },
                        ],
                        [
                            'attribute' => 'ip_address',
                            'label' => 'IP',
                            'headerOptions' => ['style' => 'width: 120px'],
                            'value' => function ($model) {
                                return $model->ip_address ?: '-';
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view}',
                            'headerOptions' => ['style' => 'width: 60px'],
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a('<i class="bi bi-eye"></i>', $url, [
                                        'class' => 'btn btn-sm btn-outline-info',
                                        'title' => 'ดูรายละเอียด',
                                        'data-bs-toggle' => 'modal',
                                        'data-bs-target' => '#viewModal',
                                        'onclick' => 'loadAuditDetail(' . $model->id . '); return false;',
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>รายละเอียด Audit Log</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="auditDetailContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?php
$viewUrl = \yii\helpers\Url::to(['view']);
$js = <<<JS
function loadAuditDetail(id) {
    $('#auditDetailContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    $.get('{$viewUrl}', {id: id, ajax: 1}, function(data) {
        $('#auditDetailContent').html(data);
    }).fail(function() {
        $('#auditDetailContent').html('<div class="alert alert-danger">ไม่สามารถโหลดข้อมูลได้</div>');
    });
}
JS;
$this->registerJs($js);
?>
