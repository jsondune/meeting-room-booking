<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\User;
use common\models\Department;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $searchModel */

$this->title = 'จัดการผู้ใช้';
$this->params['breadcrumbs'][] = $this->title;

// Get filter values
$filterStatus = Yii::$app->request->get('status', '');
$filterRole = Yii::$app->request->get('role', '');
$filterDepartment = Yii::$app->request->get('department_id', '');
$filterSearch = Yii::$app->request->get('search', '');
?>

<div class="user-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-people me-2"></i><?= Html::encode($this->title) ?>
        </h1>
        <div>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i>เพิ่มผู้ใช้', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <i class="bi bi-funnel me-1"></i>ค้นหา / กรองข้อมูล
        </div>
        <div class="card-body">
            <form method="get" action="<?= Url::to(['index']) ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">ค้นหา</label>
                        <input type="text" name="search" class="form-control" placeholder="ชื่อ, อีเมล, username" value="<?= Html::encode($filterSearch) ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <option value="10" <?= $filterStatus === '10' ? 'selected' : '' ?>>ใช้งาน</option>
                            <option value="9" <?= $filterStatus === '9' ? 'selected' : '' ?>>รอยืนยัน</option>
                            <option value="0" <?= $filterStatus === '0' ? 'selected' : '' ?>>ถูกระงับ</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">บทบาท</label>
                        <select name="role" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <?php foreach (User::getRoleOptions() as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $filterRole === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">หน่วยงาน</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- ทั้งหมด --</option>
                            <?php foreach (Department::getDropdownList() as $id => $name_th): ?>
                                <option value="<?= $id ?>" <?= $filterDepartment == $id ? 'selected' : '' ?>><?= Html::encode($name_th) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i>ค้นหา
                        </button>
                        <?= Html::a('<i class="bi bi-x-lg"></i>', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ผู้ใช้ทั้งหมด</h6>
                            <h3 class="mb-0"><?= number_format(User::find()->count()) ?></h3>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ใช้งานปกติ</h6>
                            <h3 class="mb-0"><?= number_format(User::find()->where(['status' => User::STATUS_ACTIVE])->count()) ?></h3>
                        </div>
                        <i class="bi bi-person-check fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">รอยืนยัน</h6>
                            <h3 class="mb-0"><?= number_format(User::find()->where(['status' => User::STATUS_INACTIVE])->count()) ?></h3>
                        </div>
                        <i class="bi bi-person-exclamation fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-subtitle mb-1 opacity-75">ถูกระงับ</h6>
                            <h3 class="mb-0"><?= number_format(User::find()->where(['status' => User::STATUS_DELETED])->count()) ?></h3>
                        </div>
                        <i class="bi bi-person-x fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-1"></i>รายการผู้ใช้</span>
            <span class="badge bg-secondary"><?= number_format($dataProvider->totalCount) ?> รายการ</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <?php Pjax::begin(['id' => 'user-grid']); ?>
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
                            'attribute' => 'full_name',
                            'label' => 'ผู้ใช้',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // Use avatarUrl method for proper URL
                                $avatarUrl = $model->avatarUrl;
                                $avatar = '<img src="' . Html::encode($avatarUrl) . '" alt="" class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">';
                                
                                $name = Html::encode($model->full_name);
                                $username = '<small class="text-muted">@' . Html::encode($model->username) . '</small>';
                                
                                return '<div class="d-flex align-items-center">' . $avatar . '<div>' . $name . '<br>' . $username . '</div></div>';
                            },
                        ],
                        [
                            'attribute' => 'email',
                            'format' => 'email',
                        ],
                        [
                            'attribute' => 'department_id',
                            'label' => 'หน่วยงาน',
                            'value' => function ($model) {
                                return $model->department ? $model->department->name_th : '-';
                            },
                        ],
                        [
                            'attribute' => 'role',
                            'label' => 'บทบาท',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 120px'],
                            'value' => function ($model) {
                                return $model->roleLabel;
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'label' => 'สถานะ',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 100px'],
                            'value' => function ($model) {
                                return $model->statusLabel;
                            },
                        ],
                        [
                            'attribute' => 'last_login_at',
                            'label' => 'เข้าสู่ระบบล่าสุด',
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 140px'],
                            'value' => function ($model) {
                                if ($model->last_login_at) {
                                    return '<small>' . Yii::$app->formatter->asDatetime($model->last_login_at, 'short') . '</small>';
                                }
                                return '<small class="text-muted">ยังไม่เคยเข้าสู่ระบบ</small>';
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view} {update} {delete}',
                            'headerOptions' => ['style' => 'width: 120px'],
                            'buttons' => [
                                'view' => function ($url, $model) {
                                    return Html::a('<i class="bi bi-eye"></i>', $url, [
                                        'class' => 'btn btn-sm btn-outline-info me-1',
                                        'title' => 'ดูรายละเอียด',
                                    ]);
                                },
                                'update' => function ($url, $model) {
                                    return Html::a('<i class="bi bi-pencil"></i>', $url, [
                                        'class' => 'btn btn-sm btn-outline-primary me-1',
                                        'title' => 'แก้ไข',
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    // Don't allow deleting yourself
                                    if ($model->id == Yii::$app->user->id) {
                                        return '';
                                    }
                                    return Html::a('<i class="bi bi-trash"></i>', $url, [
                                        'class' => 'btn btn-sm btn-outline-danger',
                                        'title' => 'ลบ',
                                        'data' => [
                                            'confirm' => 'คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
