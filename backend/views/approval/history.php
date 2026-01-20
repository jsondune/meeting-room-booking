<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $approvers array */
/* @var $rooms array */

$this->title = 'ประวัติการอนุมัติ';
$this->params['breadcrumbs'][] = ['label' => 'ศูนย์อนุมัติ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="approval-history">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-clock-history text-secondary me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">ดูประวัติการพิจารณาคำขอจองห้องประชุม</p>
        </div>
        <div>
            <a href="<?= Url::to(['statistics']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-bar-chart me-1"></i> ดูสถิติ
            </a>
            <a href="<?= Url::to(['pending']) ?>" class="btn btn-warning ms-2">
                <i class="bi bi-hourglass-split me-1"></i> รายการรออนุมัติ
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="true">
                <i class="bi bi-funnel me-2"></i>ตัวกรอง
                <i class="bi bi-chevron-down float-end"></i>
            </a>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['history'],
                    'method' => 'get',
                    'options' => ['class' => 'row g-3']
                ]); ?>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'status')->dropDownList([
                        'approved' => 'อนุมัติแล้ว',
                        'rejected' => 'ปฏิเสธ',
                    ], ['prompt' => '-- ทุกสถานะ --', 'class' => 'form-select'])->label('สถานะ') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'room_id')->dropDownList(
                        $rooms,
                        ['prompt' => '-- ทุกห้อง --', 'class' => 'form-select']
                    )->label('ห้องประชุม') ?>
                </div>

                <div class="col-md-2">
                    <?= Html::dropDownList('approver_id', Yii::$app->request->get('approver_id'), $approvers, [
                        'prompt' => '-- ทุกผู้อนุมัติ --',
                        'class' => 'form-select',
                        'id' => 'approver_id'
                    ]) ?>
                    <label class="form-label small text-muted">ผู้อนุมัติ</label>
                </div>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'date_from')->input('date', ['class' => 'form-control'])->label('ตั้งแต่วันที่') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'date_to')->input('date', ['class' => 'form-control'])->label('ถึงวันที่') ?>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <?= Html::submitButton('<i class="bi bi-search me-1"></i> ค้นหา', ['class' => 'btn btn-primary me-2']) ?>
                    <?= Html::a('<i class="bi bi-arrow-clockwise"></i>', ['history'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover mb-0'],
                'layout' => "{items}\n<div class='card-footer bg-white d-flex justify-content-between align-items-center'><div class='text-muted'>{summary}</div>{pager}</div>",
                'pager' => [
                    'class' => 'yii\bootstrap5\LinkPager',
                    'options' => ['class' => 'pagination mb-0'],
                ],
                'emptyText' => '<div class="text-center py-5"><i class="bi bi-inbox text-muted fs-1"></i><p class="text-muted mt-3">ไม่มีประวัติการอนุมัติ</p></div>',
                'emptyTextOptions' => ['class' => ''],
                'columns' => [
                    [
                        'attribute' => 'booking_code',
                        'label' => 'รหัสการจอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a(Html::encode($model->booking_code), ['view', 'id' => $model->id], ['class' => 'fw-bold']);
                        },
                        'headerOptions' => ['style' => 'width: 130px;'],
                    ],
                    [
                        'attribute' => 'title',
                        'label' => 'หัวข้อการประชุม',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $title = Html::encode($model->title);
                            if (mb_strlen($model->title) > 35) {
                                $title = '<span title="' . Html::encode($model->title) . '">' . Html::encode(mb_substr($model->title, 0, 35)) . '...</span>';
                            }
                            return $title;
                        },
                    ],
                    [
                        'attribute' => 'room_id',
                        'label' => 'ห้องประชุม',
                        'value' => function ($model) {
                            return $model->room->name ?? '-';
                        },
                        'headerOptions' => ['style' => 'width: 140px;'],
                    ],
                    [
                        'attribute' => 'booking_date',
                        'label' => 'วันที่จอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $date = Yii::$app->formatter->asDate($model->booking_date);
                            $time = substr($model->start_time, 0, 5) . ' - ' . substr($model->end_time, 0, 5);
                            return $date . '<br><small class="text-muted">' . $time . '</small>';
                        },
                        'headerOptions' => ['style' => 'width: 130px;'],
                    ],
                    [
                        'attribute' => 'user_id',
                        'label' => 'ผู้ขอจอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $user = $model->user;
                            if (!$user) return '-';
                            return Html::encode($user->full_name ?? $user->username);
                        },
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'สถานะ',
                        'format' => 'raw',
                        'value' => function ($model) {
                            switch ($model->status) {
                                case 'approved':
                                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>อนุมัติ</span>';
                                case 'rejected':
                                    return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>ปฏิเสธ</span>';
                                case 'completed':
                                    return '<span class="badge bg-info"><i class="bi bi-check-all me-1"></i>เสร็จสิ้น</span>';
                                case 'cancelled':
                                    return '<span class="badge bg-secondary"><i class="bi bi-slash-circle me-1"></i>ยกเลิก</span>';
                                default:
                                    return '<span class="badge bg-warning text-dark">' . $model->status . '</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'approved_by',
                        'label' => 'ผู้อนุมัติ',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $approver = $model->approver;
                            if (!$approver) return '-';
                            return Html::encode($approver->full_name ?? $approver->username);
                        },
                    ],
                    [
                        'attribute' => 'approved_at',
                        'label' => 'วันที่พิจารณา',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (!$model->approved_at) return '-';
                            return Yii::$app->formatter->asDatetime($model->approved_at);
                        },
                        'headerOptions' => ['style' => 'width: 130px;'],
                    ],
                    [
                        'label' => 'เวลาตอบกลับ',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (!$model->approved_at || !$model->created_at) return '-';
                            
                            $created = strtotime($model->created_at);
                            $approved = strtotime($model->approved_at);
                            $diff = $approved - $created;
                            
                            if ($diff < 3600) {
                                $mins = floor($diff / 60);
                                return '<span class="text-success">' . $mins . ' นาที</span>';
                            } elseif ($diff < 86400) {
                                $hours = floor($diff / 3600);
                                return '<span class="text-warning">' . $hours . ' ชม.</span>';
                            } else {
                                $days = floor($diff / 86400);
                                return '<span class="text-danger">' . $days . ' วัน</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '',
                        'headerOptions' => ['style' => 'width: 50px;'],
                        'template' => '{view}',
                        'buttons' => [
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'ดูรายละเอียด',
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

    <!-- Rejection Reasons Summary -->
    <?php if (Yii::$app->request->get('status') === 'rejected'): ?>
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="bi bi-chat-text me-2"></i>สรุปเหตุผลการปฏิเสธ
            </h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-0">เหตุผลการปฏิเสธจะแสดงในรายละเอียดของแต่ละรายการ</p>
        </div>
    </div>
    <?php endif; ?>
</div>
