<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BookingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $rooms array */
/* @var $departments array */

$this->title = 'รายการรออนุมัติ';
$this->params['breadcrumbs'][] = ['label' => 'ศูนย์อนุมัติ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="approval-pending">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-hourglass-split text-warning me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">พิจารณาและอนุมัติคำขอจองห้องประชุม</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" id="btn-bulk-approve" disabled>
                <i class="bi bi-check-all me-1"></i> อนุมัติที่เลือก
            </button>
            <button type="button" class="btn btn-danger ms-2" id="btn-bulk-reject" disabled data-bs-toggle="modal" data-bs-target="#bulkRejectModal">
                <i class="bi bi-x-lg me-1"></i> ปฏิเสธที่เลือก
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#filterCollapse" role="button" aria-expanded="false">
                <i class="bi bi-funnel me-2"></i>ตัวกรอง
                <i class="bi bi-chevron-down float-end"></i>
            </a>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['pending'],
                    'method' => 'get',
                    'options' => ['class' => 'row g-3']
                ]); ?>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'room_id')->dropDownList(
                        $rooms,
                        ['prompt' => '-- ทุกห้อง --', 'class' => 'form-select']
                    )->label('ห้องประชุม') ?>
                </div>

                <div class="col-md-3">
                    <?= $form->field($searchModel, 'department_id')->dropDownList(
                        $departments,
                        ['prompt' => '-- ทุกหน่วยงาน --', 'class' => 'form-select']
                    )->label('หน่วยงาน') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'date_from')->input('date', ['class' => 'form-control'])->label('ตั้งแต่วันที่') ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($searchModel, 'date_to')->input('date', ['class' => 'form-control'])->label('ถึงวันที่') ?>
                </div>

                <div class="col-md-2">
                    <div class="form-check mt-4 pt-3">
                        <?= Html::checkbox('urgent_only', Yii::$app->request->get('urgent_only'), [
                            'class' => 'form-check-input',
                            'id' => 'urgent_only'
                        ]) ?>
                        <label class="form-check-label" for="urgent_only">
                            <i class="bi bi-exclamation-triangle text-danger"></i> เร่งด่วนเท่านั้น
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <?= Html::submitButton('<i class="bi bi-search me-1"></i> ค้นหา', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="bi bi-arrow-clockwise me-1"></i> รีเซ็ต', ['pending'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <!-- Pending Bookings Table -->
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
                'emptyText' => '<div class="text-center py-5"><i class="bi bi-check-circle text-success fs-1"></i><p class="text-muted mt-3">ไม่มีรายการรออนุมัติ</p></div>',
                'emptyTextOptions' => ['class' => ''],
                'columns' => [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'checkboxOptions' => function ($model) {
                            return ['value' => $model->id, 'class' => 'booking-checkbox'];
                        },
                        'headerOptions' => ['style' => 'width: 40px;'],
                    ],
                    [
                        'attribute' => 'booking_code',
                        'label' => 'รหัสการจอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $hoursUntil = (strtotime($model->booking_date . ' ' . $model->start_time) - time()) / 3600;
                            $badge = '';
                            if ($hoursUntil <= 24 && $hoursUntil > 0) {
                                $badge = '<span class="badge bg-danger ms-1"><i class="bi bi-alarm"></i></span>';
                            } elseif ($hoursUntil <= 48 && $hoursUntil > 0) {
                                $badge = '<span class="badge bg-warning text-dark ms-1"><i class="bi bi-clock"></i></span>';
                            }
                            return Html::a(Html::encode($model->booking_code), ['view', 'id' => $model->id], ['class' => 'fw-bold']) . $badge;
                        },
                        'headerOptions' => ['style' => 'width: 150px;'],
                    ],
                    [
                        'attribute' => 'title',
                        'label' => 'หัวข้อการประชุม',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $title = Html::encode($model->title);
                            if (strlen($model->title) > 40) {
                                $title = '<span title="' . Html::encode($model->title) . '">' . Html::encode(mb_substr($model->title, 0, 40)) . '...</span>';
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
                    ],
                    [
                        'attribute' => 'booking_date',
                        'label' => 'วัน-เวลา',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $date = Yii::$app->formatter->asDate($model->booking_date, 'medium');
                            $time = substr($model->start_time, 0, 5) . ' - ' . substr($model->end_time, 0, 5);
                            return $date . '<br><small class="text-muted">' . $time . '</small>';
                        },
                    ],
                    [
                        'attribute' => 'user_id',
                        'label' => 'ผู้ขอจอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $user = $model->user;
                            if (!$user) return '-';
                            
                            $avatar = $user->avatar 
                                ? Html::img($user->avatar, ['class' => 'rounded-circle me-2', 'style' => 'width: 32px; height: 32px; object-fit: cover;'])
                                : '<span class="avatar-placeholder rounded-circle me-2 d-inline-flex align-items-center justify-content-center bg-secondary text-white" style="width: 32px; height: 32px; font-size: 14px;">' . strtoupper(substr($user->username, 0, 1)) . '</span>';
                            
                            $name = Html::encode($user->full_name ?? $user->username);
                            $dept = $user->department ? '<br><small class="text-muted">' . Html::encode($user->department->name_th) . '</small>' : '';
                            
                            return '<div class="d-flex align-items-center">' . $avatar . '<div>' . $name . $dept . '</div></div>';
                        },
                    ],
                    [
                        'attribute' => 'attendee_count',
                        'label' => 'จำนวนคน',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $room = $model->room;
                            $attendees = $model->attendee_count ?? 0;
                            $capacity = $room ? $room->capacity : 0;
                            $class = $attendees > $capacity ? 'text-danger' : '';
                            return '<span class="' . $class . '">' . $attendees . '</span>' . ($capacity ? ' / ' . $capacity : '');
                        },
                        'headerOptions' => ['style' => 'width: 100px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'label' => 'รอมา',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $created = strtotime($model->created_at);
                            $now = time();
                            $diff = $now - $created;
                            
                            if ($diff < 3600) {
                                $mins = floor($diff / 60);
                                return '<span class="badge bg-success">' . $mins . ' นาที</span>';
                            } elseif ($diff < 86400) {
                                $hours = floor($diff / 3600);
                                return '<span class="badge bg-warning text-dark">' . $hours . ' ชม.</span>';
                            } else {
                                $days = floor($diff / 86400);
                                return '<span class="badge bg-danger">' . $days . ' วัน</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 80px;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'ดำเนินการ',
                        'headerOptions' => ['style' => 'width: 140px;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'template' => '{approve} {reject} {view}',
                        'buttons' => [
                            'approve' => function ($url, $model) {
                                return Html::a('<i class="bi bi-check-lg"></i>', ['approve', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-success',
                                    'title' => 'อนุมัติ',
                                    'data-confirm' => 'คุณต้องการอนุมัติการจองนี้หรือไม่?',
                                    'data-method' => 'post',
                                ]);
                            },
                            'reject' => function ($url, $model) {
                                return Html::a('<i class="bi bi-x-lg"></i>', ['view', 'id' => $model->id, 'action' => 'reject'], [
                                    'class' => 'btn btn-sm btn-danger ms-1',
                                    'title' => 'ปฏิเสธ',
                                ]);
                            },
                            'view' => function ($url, $model) {
                                return Html::a('<i class="bi bi-eye"></i>', ['view', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-secondary ms-1',
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

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>ปฏิเสธการจองที่เลือก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulk-reject-form" action="<?= Url::to(['bulk-reject']) ?>" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                <input type="hidden" name="ids" id="bulk-reject-ids" value="">
                <div class="modal-body">
                    <p class="text-muted mb-3">คุณกำลังจะปฏิเสธ <strong id="reject-count">0</strong> รายการ</p>
                    <div class="mb-3">
                        <label for="bulk-reject-reason" class="form-label">เหตุผลในการปฏิเสธ <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="bulk-reject-reason" name="reason" rows="3" required placeholder="โปรดระบุเหตุผลในการปฏิเสธ..."></textarea>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="ห้องประชุมไม่ว่าง">ห้องไม่ว่าง</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="จำนวนผู้เข้าร่วมเกินความจุ">เกินความจุ</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="ห้องประชุมอยู่ระหว่างการซ่อมบำรุง">ซ่อมบำรุง</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="ข้อมูลการจองไม่ครบถ้วน">ข้อมูลไม่ครบ</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-lg me-1"></i> ปฏิเสธที่เลือก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$bulkApproveUrl = Url::to(['bulk-approve']);
$csrfToken = Yii::$app->request->csrfToken;
$csrfParam = Yii::$app->request->csrfParam;

$js = <<<JS
// Handle checkbox selection
$(document).on('change', '.booking-checkbox, #w0_all', function() {
    var selected = $('.booking-checkbox:checked').length;
    $('#btn-bulk-approve, #btn-bulk-reject').prop('disabled', selected === 0);
    
    var ids = [];
    $('.booking-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    $('#bulk-reject-ids').val(ids.join(','));
    $('#reject-count').text(selected);
});

// Bulk approve
$('#btn-bulk-approve').on('click', function() {
    var ids = [];
    $('.booking-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    
    if (ids.length === 0) {
        alert('โปรดเลือกรายการที่ต้องการอนุมัติ');
        return;
    }
    
    if (!confirm('คุณต้องการอนุมัติ ' + ids.length + ' รายการที่เลือกหรือไม่?')) {
        return;
    }
    
    $.ajax({
        url: '{$bulkApproveUrl}',
        type: 'POST',
        data: {
            ids: ids,
            {$csrfParam}: '{$csrfToken}'
        },
        success: function(response) {
            location.reload();
        },
        error: function() {
            alert('เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง');
        }
    });
});

// Quick reason buttons
$('.quick-reason').on('click', function() {
    $('#bulk-reject-reason').val($(this).data('reason'));
});
JS;

$this->registerJs($js);
?>
