<?php
/**
 * Pending Bookings View - Approval Queue
 * Meeting Room Booking System
 * 
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = 'การจองรออนุมัติ';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="booking-pending">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-clock text-warning me-2"></i><?= Html::encode($this->title) ?></h4>
            <p class="text-muted mb-0">รายการจองที่รอการพิจารณาอนุมัติ</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success" id="bulk-approve-btn" disabled>
                <i class="fas fa-check-double me-1"></i> อนุมัติที่เลือก
            </button>
            <button type="button" class="btn btn-danger" id="bulk-reject-btn" disabled>
                <i class="fas fa-times me-1"></i> ปฏิเสธที่เลือก
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <?php
        $models = $dataProvider->getModels();
        $totalPending = $dataProvider->totalCount;
        $todayCount = count(array_filter($models, fn($m) => $m->booking_date == date('Y-m-d')));
        $tomorrowCount = count(array_filter($models, fn($m) => $m->booking_date == date('Y-m-d', strtotime('+1 day'))));
        $urgentCount = count(array_filter($models, fn($m) => strtotime($m->booking_date) <= strtotime('+2 days')));
        ?>
        <div class="col-md-3">
            <div class="card border-0 bg-warning bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h2 class="mb-1 text-warning"><?= $totalPending ?></h2>
                    <small class="text-muted">รอดำเนินการทั้งหมด</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-danger bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h2 class="mb-1 text-danger"><?= $urgentCount ?></h2>
                    <small class="text-muted">เร่งด่วน (2 วัน)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h2 class="mb-1 text-info"><?= $todayCount ?></h2>
                    <small class="text-muted">วันนี้</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-primary bg-opacity-10">
                <div class="card-body text-center py-3">
                    <h2 class="mb-1 text-primary"><?= $tomorrowCount ?></h2>
                    <small class="text-muted">พรุ่งนี้</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Bookings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?= Html::beginForm(['bulk-reject'], 'post', ['id' => 'bulk-form']) ?>
            
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-hover align-middle mb-0'],
                'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center mt-3'>{summary}{pager}</div>",
                'pager' => [
                    'class' => 'yii\bootstrap5\LinkPager',
                    'options' => ['class' => 'pagination pagination-sm mb-0'],
                ],
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
                        'format' => 'raw',
                        'value' => function ($model) {
                            $isUrgent = strtotime($model->booking_date) <= strtotime('+2 days');
                            $urgent = $isUrgent ? '<span class="badge bg-danger ms-1">เร่งด่วน</span>' : '';
                            return Html::a(
                                '<strong>' . Html::encode($model->booking_code) . '</strong>' . $urgent,
                                ['view', 'id' => $model->id],
                                ['class' => 'text-decoration-none']
                            );
                        },
                        'headerOptions' => ['style' => 'width: 160px;'],
                    ],
                    [
                        'attribute' => 'title',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<div class="text-truncate" style="max-width: 200px;" title="' . Html::encode($model->title) . '">' 
                                . Html::encode($model->title) . '</div>';
                        },
                    ],
                    [
                        'attribute' => 'room_id',
                        'label' => 'ห้องประชุม',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<i class="fas fa-door-open text-muted me-1"></i>' . Html::encode($model->room->name_th ?? '-');
                        },
                    ],
                    [
                        'attribute' => 'booking_date',
                        'label' => 'วันที่/เวลา',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $date = Yii::$app->formatter->asDate($model->booking_date);
                            $time = substr($model->start_time, 0, 5) . '-' . substr($model->end_time, 0, 5);
                            $dayDiff = (strtotime($model->booking_date) - strtotime(date('Y-m-d'))) / 86400;
                            
                            $dateClass = '';
                            if ($dayDiff == 0) $dateClass = 'text-danger fw-bold';
                            elseif ($dayDiff == 1) $dateClass = 'text-warning fw-bold';
                            elseif ($dayDiff <= 2) $dateClass = 'text-info';
                            
                            return '<span class="' . $dateClass . '">' . $date . '</span><br>'
                                . '<small class="text-muted">' . $time . ' น.</small>';
                        },
                    ],
                    [
                        'attribute' => 'user_id',
                        'label' => 'ผู้จอง',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $avatar = $model->user->avatar_url ?? null;
                            if ($avatar) {
                                $img = Html::img($avatar, ['class' => 'rounded-circle me-2', 'style' => 'width: 32px; height: 32px; object-fit: cover;']);
                            } else {
                                $img = '<span class="avatar-circle bg-primary text-white me-2" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 12px;">'
                                    . strtoupper(substr($model->user->first_name ?? 'U', 0, 1))
                                    . '</span>';
                            }
                            return $img . '<span>' . Html::encode($model->user->full_name ?? '-') . '</span>'
                                . '<br><small class="text-muted">' . Html::encode($model->department->name_th ?? '-') . '</small>';
                        },
                    ],
                    [
                        'attribute' => 'attendee_count',
                        'label' => 'ผู้เข้าร่วม',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span class="badge bg-secondary">' . $model->attendee_count . ' คน</span>';
                        },
                        'headerOptions' => ['style' => 'width: 90px; text-align: center;'],
                        'contentOptions' => ['class' => 'text-center'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'รอมา',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $hours = round((time() - strtotime($model->created_at)) / 3600);
                            if ($hours < 1) {
                                return '<span class="text-success">เมื่อสักครู่</span>';
                            } elseif ($hours < 24) {
                                return '<span class="text-info">' . $hours . ' ชม.</span>';
                            } else {
                                $days = floor($hours / 24);
                                $color = $days > 2 ? 'text-danger' : 'text-warning';
                                return '<span class="' . $color . '">' . $days . ' วัน</span>';
                            }
                        },
                        'headerOptions' => ['style' => 'width: 80px;'],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{approve} {reject} {view}',
                        'headerOptions' => ['style' => 'width: 140px; text-align: center;'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'approve' => function ($url, $model) {
                                return Html::a(
                                    '<i class="fas fa-check"></i>',
                                    ['approve', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-success btn-sm me-1',
                                        'title' => 'อนุมัติ',
                                        'data-confirm' => 'ต้องการอนุมัติการจองนี้ใช่หรือไม่?',
                                        'data-method' => 'post',
                                    ]
                                );
                            },
                            'reject' => function ($url, $model) {
                                return Html::a(
                                    '<i class="fas fa-times"></i>',
                                    ['reject', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-danger btn-sm me-1',
                                        'title' => 'ปฏิเสธ',
                                    ]
                                );
                            },
                            'view' => function ($url, $model) {
                                return Html::a(
                                    '<i class="fas fa-eye"></i>',
                                    ['view', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-outline-secondary btn-sm',
                                        'title' => 'ดูรายละเอียด',
                                    ]
                                );
                            },
                        ],
                    ],
                ],
            ]) ?>
            
            <?= Html::endForm() ?>
        </div>
    </div>

    <?php if ($dataProvider->totalCount == 0): ?>
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
        </div>
        <h5>ไม่มีการจองที่รออนุมัติ</h5>
        <p class="text-muted">การจองทั้งหมดได้รับการพิจารณาแล้ว</p>
        <?= Html::a(
            '<i class="fas fa-list me-1"></i> ดูการจองทั้งหมด',
            ['index'],
            ['class' => 'btn btn-primary']
        ) ?>
    </div>
    <?php endif; ?>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>ปฏิเสธการจองที่เลือก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>กรุณาระบุเหตุผลในการปฏิเสธการจอง <span id="selected-count">0</span> รายการ</p>
                <div class="mb-3">
                    <label for="bulk-reason" class="form-label">เหตุผล</label>
                    <textarea class="form-control" id="bulk-reason" rows="3" placeholder="ระบุเหตุผลในการปฏิเสธ..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirm-bulk-reject">
                    <i class="fas fa-times me-1"></i>ปฏิเสธ
                </button>
            </div>
        </div>
    </div>
</div>

<?php
$bulkApproveUrl = Url::to(['bulk-approve']);
$bulkRejectUrl = Url::to(['bulk-reject']);

$this->registerJs(<<<JS
// Checkbox selection handling
function updateBulkButtons() {
    var checked = $('.booking-checkbox:checked').length;
    $('#bulk-approve-btn, #bulk-reject-btn').prop('disabled', checked === 0);
    $('#selected-count').text(checked);
}

$(document).on('change', '.booking-checkbox, #selection_all', function() {
    updateBulkButtons();
});

// Bulk Approve
$('#bulk-approve-btn').on('click', function() {
    var ids = [];
    $('.booking-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    
    if (ids.length === 0) {
        alert('กรุณาเลือกการจองที่ต้องการอนุมัติ');
        return;
    }
    
    if (!confirm('ต้องการอนุมัติการจอง ' + ids.length + ' รายการใช่หรือไม่?')) {
        return;
    }
    
    $.post('{$bulkApproveUrl}', { ids: ids }, function() {
        location.reload();
    });
});

// Bulk Reject - Show Modal
$('#bulk-reject-btn').on('click', function() {
    var checked = $('.booking-checkbox:checked').length;
    if (checked === 0) {
        alert('กรุณาเลือกการจองที่ต้องการปฏิเสธ');
        return;
    }
    $('#bulkRejectModal').modal('show');
});

// Confirm Bulk Reject
$('#confirm-bulk-reject').on('click', function() {
    var ids = [];
    $('.booking-checkbox:checked').each(function() {
        ids.push($(this).val());
    });
    
    var reason = $('#bulk-reason').val() || 'ปฏิเสธโดยผู้ดูแลระบบ';
    
    $.post('{$bulkRejectUrl}', { ids: ids, reason: reason }, function() {
        location.reload();
    });
});
JS
);
?>
