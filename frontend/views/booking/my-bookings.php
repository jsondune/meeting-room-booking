<?php
/**
 * My Bookings Page
 * Frontend view for user's booking history
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\Booking;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $status string */
/* @var $dateRange string */
/* @var $stats array */

$this->title = 'การจองของฉัน';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="booking-my-bookings">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><i class="bi bi-calendar-check me-2"></i><?= Html::encode($this->title) ?></h2>
                <p class="text-muted mb-0">จัดการและติดตามการจองห้องประชุมของคุณ</p>
            </div>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>จองห้องใหม่
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-primary"><?= $stats['total'] ?></div>
                        <small class="text-muted">การจองทั้งหมด</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-warning"><?= $stats['pending'] ?></div>
                        <small class="text-muted">รอการอนุมัติ</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-success"><?= $stats['approved'] ?></div>
                        <small class="text-muted">ที่จะมาถึง</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-6 fw-bold text-info"><?= $stats['completed'] ?></div>
                        <small class="text-muted">เสร็จสิ้น</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'upcoming', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'upcoming' ? 'active' : '' ?>">
                                ที่จะมาถึง
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'today', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'today' ? 'active' : '' ?>">
                                วันนี้
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'this_week', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'this_week' ? 'active' : '' ?>">
                                สัปดาห์นี้
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'this_month', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'this_month' ? 'active' : '' ?>">
                                เดือนนี้
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'past', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'past' ? 'active' : '' ?>">
                                ที่ผ่านมา
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => 'all', 'status' => $status]) ?>" 
                               class="btn btn-outline-primary <?= $dateRange == 'all' ? 'active' : '' ?>">
                                ทั้งหมด
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                            <a href="<?= Url::to(['my-bookings', 'date_range' => $dateRange]) ?>" 
                               class="btn btn-sm <?= empty($status) ? 'btn-secondary' : 'btn-outline-secondary' ?>">
                                ทุกสถานะ
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => $dateRange, 'status' => Booking::STATUS_PENDING]) ?>" 
                               class="btn btn-sm <?= $status == Booking::STATUS_PENDING ? 'btn-warning' : 'btn-outline-warning' ?>">
                                รออนุมัติ
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => $dateRange, 'status' => Booking::STATUS_APPROVED]) ?>" 
                               class="btn btn-sm <?= $status == Booking::STATUS_APPROVED ? 'btn-success' : 'btn-outline-success' ?>">
                                อนุมัติแล้ว
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => $dateRange, 'status' => Booking::STATUS_REJECTED]) ?>" 
                               class="btn btn-sm <?= $status == Booking::STATUS_REJECTED ? 'btn-danger' : 'btn-outline-danger' ?>">
                                ปฏิเสธ
                            </a>
                            <a href="<?= Url::to(['my-bookings', 'date_range' => $dateRange, 'status' => Booking::STATUS_CANCELLED]) ?>" 
                               class="btn btn-sm <?= $status == Booking::STATUS_CANCELLED ? 'btn-dark' : 'btn-outline-dark' ?>">
                                ยกเลิก
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <?php if ($dataProvider->count > 0): ?>
            <div class="row g-3">
                <?php foreach ($dataProvider->models as $booking): ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm booking-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <!-- Date Badge -->
                                    <div class="col-auto">
                                        <div class="date-badge text-center bg-light rounded p-3">
                                            <div class="fw-bold text-primary" style="font-size: 1.5rem;">
                                                <?= date('d', strtotime($booking->booking_date)) ?>
                                            </div>
                                            <div class="text-muted small">
                                                <?= Yii::$app->formatter->asDate($booking->booking_date) ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Booking Info -->
                                    <div class="col">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h5 class="mb-1">
                                                    <a href="<?= Url::to(['view', 'id' => $booking->id]) ?>" class="text-decoration-none">
                                                        <?= Html::encode($booking->title) ?>
                                                    </a>
                                                </h5>
                                                <p class="mb-0 text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    <?= Html::encode($booking->room->name ?? '-') ?>
                                                    <?php if ($booking->room && $booking->room->building): ?>
                                                        <span class="mx-1">•</span>
                                                        <?= Html::encode($booking->room->building->name ?? '') ?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                            <?php
                                            $statusClass = match($booking->status) {
                                                Booking::STATUS_PENDING => 'bg-warning text-dark',
                                                Booking::STATUS_APPROVED => 'bg-success',
                                                Booking::STATUS_REJECTED => 'bg-danger',
                                                Booking::STATUS_CANCELLED => 'bg-secondary',
                                                Booking::STATUS_COMPLETED => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                            $statusLabel = match($booking->status) {
                                                Booking::STATUS_PENDING => 'รออนุมัติ',
                                                Booking::STATUS_APPROVED => 'อนุมัติแล้ว',
                                                Booking::STATUS_REJECTED => 'ปฏิเสธ',
                                                Booking::STATUS_CANCELLED => 'ยกเลิก',
                                                Booking::STATUS_COMPLETED => 'เสร็จสิ้น',
                                                default => 'ไม่ทราบ'
                                            };
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-3 text-muted small">
                                            <span>
                                                <i class="bi bi-clock me-1"></i>
                                                <?= Html::encode(substr($booking->start_time, 0, 5)) ?> - <?= Html::encode(substr($booking->end_time, 0, 5)) ?> น.
                                            </span>
                                            <span>
                                                <i class="bi bi-people me-1"></i>
                                                <?= $booking->attendees_count ?? '-' ?> คน
                                            </span>
                                            <span>
                                                <i class="bi bi-calendar-plus me-1"></i>
                                                จองเมื่อ <?= Yii::$app->formatter->asDate($booking->created_at) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="col-auto">
                                        <div class="btn-group">
                                            <a href="<?= Url::to(['view', 'id' => $booking->id]) ?>" 
                                               class="btn btn-outline-primary btn-sm" title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($booking->status == Booking::STATUS_PENDING): ?>
                                                <a href="<?= Url::to(['update', 'id' => $booking->id]) ?>" 
                                                   class="btn btn-outline-warning btn-sm" title="แก้ไข">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= Url::to(['cancel', 'id' => $booking->id]) ?>" 
                                                   class="btn btn-outline-danger btn-sm" title="ยกเลิก"
                                                   data-method="post" data-confirm="คุณต้องการยกเลิกการจองนี้หรือไม่?">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            <?php elseif ($booking->status == Booking::STATUS_APPROVED && strtotime($booking->booking_date) >= strtotime('today')): ?>
                                                <a href="<?= Url::to(['cancel', 'id' => $booking->id]) ?>" 
                                                   class="btn btn-outline-danger btn-sm" title="ยกเลิก"
                                                   data-method="post" data-confirm="คุณต้องการยกเลิกการจองนี้หรือไม่?">
                                                    <i class="bi bi-x-lg"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->pagination,
                    'options' => ['class' => 'pagination'],
                    'linkOptions' => ['class' => 'page-link'],
                    'activePageCssClass' => 'active',
                    'disabledPageCssClass' => 'disabled',
                    'prevPageLabel' => '<i class="bi bi-chevron-left"></i>',
                    'nextPageLabel' => '<i class="bi bi-chevron-right"></i>',
                    'firstPageLabel' => '<i class="bi bi-chevron-double-left"></i>',
                    'lastPageLabel' => '<i class="bi bi-chevron-double-right"></i>',
                ]) ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
                    <h4>ไม่พบการจอง</h4>
                    <p class="text-muted mb-4">คุณยังไม่มีการจองห้องประชุมในช่วงเวลาที่เลือก</p>
                    <a href="<?= Url::to(['create']) ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>จองห้องประชุมใหม่
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.booking-card {
    transition: all 0.2s ease;
}
.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.date-badge {
    min-width: 80px;
}
.btn-group .btn {
    padding: 0.25rem 0.5rem;
}
</style>
