<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $pendingCount int */
/* @var $urgentBookings array */
/* @var $todayPending array */
/* @var $recentApprovals array */
/* @var $statistics array */

$this->title = 'ศูนย์อนุมัติการจอง';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="approval-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-check2-square text-primary me-2"></i><?= Html::encode($this->title) ?>
            </h1>
            <p class="text-muted mb-0">จัดการและอนุมัติคำขอจองห้องประชุม</p>
        </div>
        <div>
            <a href="<?= Url::to(['pending']) ?>" class="btn btn-primary">
                <i class="bi bi-hourglass-split me-1"></i> รายการรออนุมัติ
                <?php if ($pendingCount > 0): ?>
                    <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= Url::to(['history']) ?>" class="btn btn-outline-secondary ms-2">
                <i class="bi bi-clock-history me-1"></i> ประวัติการอนุมัติ
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">รออนุมัติ</p>
                            <h2 class="mb-0"><?= number_format($pendingCount) ?></h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                    </div>
                    <?php if ($pendingCount > 0): ?>
                        <a href="<?= Url::to(['pending']) ?>" class="btn btn-sm btn-warning mt-3">
                            ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">อนุมัติแล้ว</p>
                            <h2 class="mb-0"><?= number_format($statistics['approved'] ?? 0) ?></h2>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">เดือนนี้</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">ปฏิเสธ</p>
                            <h2 class="mb-0"><?= number_format($statistics['rejected'] ?? 0) ?></h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-x-circle text-danger fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">เดือนนี้</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1">อัตราอนุมัติ</p>
                            <h2 class="mb-0"><?= number_format($statistics['approval_rate'] ?? 0, 1) ?>%</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-graph-up text-primary fs-4"></i>
                        </div>
                    </div>
                    <small class="text-muted">เดือนนี้</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Urgent Bookings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        การจองเร่งด่วน
                        <?php if (count($urgentBookings) > 0): ?>
                            <span class="badge bg-white text-danger ms-2"><?= count($urgentBookings) ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($urgentBookings)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <p class="text-muted mt-2 mb-0">ไม่มีการจองเร่งด่วน</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($urgentBookings as $booking): ?>
                                <?php
                                $hoursUntil = (strtotime($booking->booking_date . ' ' . $booking->start_time) - time()) / 3600;
                                $urgencyClass = $hoursUntil <= 24 ? 'danger' : 'warning';
                                ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-<?= $urgencyClass ?> me-2">
                                                    <?php if ($hoursUntil <= 24): ?>
                                                        <i class="bi bi-alarm me-1"></i>ภายใน 24 ชม.
                                                    <?php else: ?>
                                                        <i class="bi bi-clock me-1"></i>ภายใน 48 ชม.
                                                    <?php endif; ?>
                                                </span>
                                                <strong><?= Html::encode($booking->booking_code) ?></strong>
                                            </div>
                                            <p class="mb-1"><?= Html::encode($booking->title) ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i><?= Html::encode($booking->room->name ?? '-') ?>
                                                <span class="mx-2">|</span>
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-clock me-1"></i><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <a href="<?= Url::to(['view', 'id' => $booking->id]) ?>" class="btn btn-sm btn-outline-primary me-1" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="<?= Url::to(['approve', 'id' => $booking->id]) ?>" class="btn btn-sm btn-success" title="อนุมัติ" data-confirm="คุณต้องการอนุมัติการจองนี้หรือไม่?">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Today's Pending -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-calendar-event me-2"></i>
                        รอพิจารณาวันนี้
                        <?php if (count($todayPending) > 0): ?>
                            <span class="badge bg-dark ms-2"><?= count($todayPending) ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($todayPending)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-check text-success fs-1"></i>
                            <p class="text-muted mt-2 mb-0">ไม่มีการจองรอพิจารณาสำหรับวันนี้</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($todayPending as $booking): ?>
                                <div class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-warning text-dark me-2">
                                                    <?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?>
                                                </span>
                                                <strong><?= Html::encode($booking->title) ?></strong>
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i><?= Html::encode($booking->room->name ?? '-') ?>
                                                <span class="mx-2">|</span>
                                                <i class="bi bi-person me-1"></i><?= Html::encode($booking->user->full_name ?? $booking->user->username ?? '-') ?>
                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <a href="<?= Url::to(['approve', 'id' => $booking->id]) ?>" class="btn btn-sm btn-success me-1" title="อนุมัติ">
                                                <i class="bi bi-check-lg"></i>
                                            </a>
                                            <a href="<?= Url::to(['view', 'id' => $booking->id, 'action' => 'reject']) ?>" class="btn btn-sm btn-danger" title="ปฏิเสธ">
                                                <i class="bi bi-x-lg"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Approvals -->
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            การอนุมัติล่าสุดของคุณ
                        </h5>
                        <a href="<?= Url::to(['history']) ?>" class="btn btn-sm btn-outline-primary">
                            ดูทั้งหมด <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentApprovals)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted fs-1"></i>
                            <p class="text-muted mt-2 mb-0">ยังไม่มีประวัติการอนุมัติ</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัสการจอง</th>
                                        <th>หัวข้อ</th>
                                        <th>ห้อง</th>
                                        <th>วันที่จอง</th>
                                        <th>ผู้ขอจอง</th>
                                        <th>สถานะ</th>
                                        <th>วันที่พิจารณา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentApprovals as $booking): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= Url::to(['view', 'id' => $booking->id]) ?>">
                                                    <?= Html::encode($booking->booking_code) ?>
                                                </a>
                                            </td>
                                            <td><?= Html::encode($booking->title) ?></td>
                                            <td><?= Html::encode($booking->room->name ?? '-') ?></td>
                                            <td>
                                                <?= Yii::$app->formatter->asDate($booking->booking_date) ?>
                                                <br>
                                                <small class="text-muted"><?= substr($booking->start_time, 0, 5) ?> - <?= substr($booking->end_time, 0, 5) ?></small>
                                            </td>
                                            <td>
                                                <?= Html::encode($booking->user->full_name ?? $booking->user->username ?? '-') ?>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = $booking->status === 'approved' ? 'success' : 'danger';
                                                $statusText = $booking->status === 'approved' ? 'อนุมัติ' : 'ปฏิเสธ';
                                                ?>
                                                <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                            </td>
                                            <td>
                                                <?= Yii::$app->formatter->asDatetime($booking->approved_at) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="card border-0 bg-primary bg-gradient text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($statistics['total'] ?? 0) ?></h3>
                    <p class="mb-0">การจองทั้งหมดเดือนนี้</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-info bg-gradient text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($statistics['my_approvals'] ?? 0) ?></h3>
                    <p class="mb-0">ที่คุณอนุมัติเดือนนี้</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 bg-secondary bg-gradient text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1"><?= number_format($statistics['avg_response_time'] ?? 0, 1) ?> ชม.</h3>
                    <p class="mb-0">เวลาตอบกลับเฉลี่ย</p>
                </div>
            </div>
        </div>
    </div>
</div>
