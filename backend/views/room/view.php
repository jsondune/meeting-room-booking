<?php

/** @var yii\web\View $this */
/** @var common\models\MeetingRoom $model */
/** @var array $recentBookings */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->name_th;
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

<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2">
    <div>
        <h1 class="page-title"><?= Html::encode($model->name_th) ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= Url::to(['site/index']) ?>">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="<?= Url::to(['room/index']) ?>">ห้องประชุม</a></li>
                <li class="breadcrumb-item active"><?= Html::encode($model->room_code) ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to(['room/update', 'id' => $model->id]) ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i>แก้ไข
        </a>
        <a href="<?= Url::to(['booking/create', 'room_id' => $model->id]) ?>" class="btn btn-success">
            <i class="bi bi-calendar-plus me-1"></i>จองห้องนี้
        </a>
        <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= Url::to(['room/calendar', 'id' => $model->id]) ?>"><i class="bi bi-calendar3 me-2"></i>ดูปฏิทิน</a></li>
                <li><a class="dropdown-item" href="<?= Url::to(['room/report', 'id' => $model->id]) ?>"><i class="bi bi-bar-chart me-2"></i>รายงานการใช้งาน</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash me-2"></i>ลบห้องประชุม</a></li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Image Gallery -->
        <div class="card mb-4">
            <div class="card-body p-0">
                <?php if ($model->images && count($model->images) > 0): ?>
                    <div id="roomGallery" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach ($model->images as $index => $image): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= $image->getUrl() ?>" class="d-block w-100" 
                                         alt="<?= Html::encode($model->name_th) ?>"
                                         style="height: 400px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($model->images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#roomGallery" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#roomGallery" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                            <div class="carousel-indicators">
                                <?php foreach ($model->images as $index => $image): ?>
                                    <button type="button" data-bs-target="#roomGallery" data-bs-slide-to="<?= $index ?>" 
                                            class="<?= $index === 0 ? 'active' : '' ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <div class="text-center text-muted">
                            <i class="bi bi-image fs-1 d-block mb-2"></i>
                            ไม่มีรูปภาพ
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Room Details -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>ข้อมูลห้องประชุม
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">ข้อมูลทั่วไป</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">รหัสห้อง:</td>
                                <td><strong><?= Html::encode($model->room_code) ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ชื่อห้อง (TH):</td>
                                <td><?= Html::encode($model->name_th) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ชื่อห้อง (EN):</td>
                                <td><?= Html::encode($model->name_en ?: '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">อาคาร/ชั้น:</td>
                                <td><?= Html::encode($model->building->name_th ?? '-') ?> ชั้น <?= $model->floor ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ประเภทห้อง:</td>
                                <td>
                                    <?php
                                    $types = [
                                        'conference' => 'ห้องประชุม',
                                        'training' => 'ห้องฝึกอบรม',
                                        'boardroom' => 'ห้องคณะกรรมการ',
                                        'huddle' => 'ห้องประชุมขนาดเล็ก',
                                        'auditorium' => 'หอประชุม'
                                    ];
                                    echo $types[$model->room_type] ?? $model->room_type;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">ความจุ:</td>
                                <td><i class="bi bi-people me-1"></i><?= $model->capacity ?> คน</td>
                            </tr>
                            <tr>
                                <td class="text-muted">รูปแบบการจัด:</td>
                                <td>
                                    <?php
                                    $layouts = [
                                        'theater' => 'โรงละคร',
                                        'classroom' => 'ห้องเรียน',
                                        'u_shape' => 'รูปตัว U',
                                        'boardroom' => 'คณะกรรมการ',
                                        'banquet' => 'จัดเลี้ยง'
                                    ];
                                    echo $layouts[$model->default_layout] ?? $model->default_layout;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">การตั้งค่าการจอง</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">เวลาเปิดใช้:</td>
                                <td><?= $model->operating_start_time ?> - <?= $model->operating_end_time ?> น.</td>
                            </tr>
                            <tr>
                                <td class="text-muted">จองขั้นต่ำ:</td>
                                <td><?= $model->min_booking_duration ?> นาที</td>
                            </tr>
                            <tr>
                                <td class="text-muted">จองสูงสุด:</td>
                                <td><?= $model->max_booking_duration ?> นาที (<?= round($model->max_booking_duration / 60, 1) ?> ชม.)</td>
                            </tr>
                            <tr>
                                <td class="text-muted">จองล่วงหน้า:</td>
                                <td>ไม่เกิน <?= $model->advance_booking_days ?> วัน</td>
                            </tr>
                            <tr>
                                <td class="text-muted">ต้องอนุมัติ:</td>
                                <td>
                                    <?php if ($model->requires_approval): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-check-circle me-1"></i>ใช่</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><i class="bi bi-lightning me-1"></i>อนุมัติอัตโนมัติ</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">วันที่เปิดจอง:</td>
                                <td>
                                    <?php
                                    $dayNames = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];
                                    // Handle both string and array
                                    $rawDays = $model->available_days;
                                    if (is_array($rawDays)) {
                                        $availableDays = $rawDays;
                                    } elseif (is_string($rawDays) && !empty($rawDays)) {
                                        $availableDays = explode(',', $rawDays);
                                    } else {
                                        $availableDays = ['1', '2', '3', '4', '5'];
                                    }
                                    $dayList = array_map(fn($d) => $dayNames[$d] ?? '', $availableDays);
                                    echo implode(' ', $dayList);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($model->description): ?>
                    <hr>
                    <h6 class="text-muted mb-2">รายละเอียดเพิ่มเติม</h6>
                    <p class="mb-0"><?= nl2br(Html::encode($model->description)) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Features & Equipment -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-star me-2"></i>สิ่งอำนวยความสะดวก
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">สิ่งอำนวยความสะดวกในห้อง</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($model->has_projector): ?>
                                <span class="badge bg-primary"><i class="bi bi-projector me-1"></i>โปรเจคเตอร์</span>
                            <?php endif; ?>
                            <?php if ($model->has_video_conference): ?>
                                <span class="badge bg-primary"><i class="bi bi-camera-video me-1"></i>Video Conference</span>
                            <?php endif; ?>
                            <?php if ($model->has_whiteboard): ?>
                                <span class="badge bg-primary"><i class="bi bi-easel me-1"></i>ไวท์บอร์ด</span>
                            <?php endif; ?>
                            <?php if ($model->has_air_conditioning): ?>
                                <span class="badge bg-primary"><i class="bi bi-snow me-1"></i>เครื่องปรับอากาศ</span>
                            <?php endif; ?>
                            <?php if ($model->has_wifi): ?>
                                <span class="badge bg-primary"><i class="bi bi-wifi me-1"></i>WiFi</span>
                            <?php endif; ?>
                            <?php if ($model->has_audio_system): ?>
                                <span class="badge bg-primary"><i class="bi bi-speaker me-1"></i>ระบบเสียง</span>
                            <?php endif; ?>
                            <?php if ($model->has_recording): ?>
                                <span class="badge bg-primary"><i class="bi bi-record-circle me-1"></i>ระบบบันทึก</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">อุปกรณ์ประจำห้อง</h6>
                        <?php 
                        $roomEquipments = $model->roomEquipment;
                        if (!empty($roomEquipments)): 
                        ?>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($roomEquipments as $re): ?>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <?= Html::encode($re->equipment->name_th ?? '-') ?>
                                        <span class="badge bg-light text-dark">x<?= $re->quantity ?></span>
                                        <?php if ($re->is_included): ?>
                                            <span class="badge bg-success">รวมในราคาห้อง</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted mb-0">ไม่มีอุปกรณ์ประจำห้อง</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-event me-2"></i>การจองล่าสุด</span>
                <a href="<?= Url::to(['booking/index', 'room_id' => $model->id]) ?>" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>รหัส</th>
                                <th>วันที่/เวลา</th>
                                <th>ผู้จอง</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= Url::to(['booking/view', 'id' => $booking->id]) ?>" class="text-decoration-none">
                                                <?= Html::encode($booking->booking_code) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small>
                                                <?= Yii::$app->formatter->asDate($booking->booking_date, 'php:d/m/Y') ?><br>
                                                <span class="text-muted"><?= $booking->start_time ?> - <?= $booking->end_time ?></span>
                                            </small>
                                        </td>
                                        <td><?= Html::encode($booking->user->displayName ?? $booking->user->username ?? '-') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = match($booking->status) {
                                                'pending' => 'pending',
                                                'approved' => 'approved',
                                                'rejected' => 'rejected',
                                                'cancelled' => 'cancelled',
                                                'completed' => 'completed',
                                                default => 'secondary'
                                            };
                                            $statusText = match($booking->status) {
                                                'pending' => 'รออนุมัติ',
                                                'approved' => 'อนุมัติแล้ว',
                                                'rejected' => 'ปฏิเสธ',
                                                'cancelled' => 'ยกเลิก',
                                                'completed' => 'เสร็จสิ้น',
                                                default => $booking->status
                                            };
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        ยังไม่มีการจอง
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Sidebar -->
    <div class="col-lg-4">
        <!-- Status Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <?php
                $statusInfo = match($model->status) {
                    1 => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'เปิดใช้งาน'],
                    0 => ['class' => 'secondary', 'icon' => 'x-circle', 'text' => 'ปิดใช้งาน'],
                    2 => ['class' => 'warning', 'icon' => 'tools', 'text' => 'อยู่ระหว่างซ่อมบำรุง'],
                    default => ['class' => 'secondary', 'icon' => 'question-circle', 'text' => 'ไม่ระบุ']
                };
                ?>
                <div class="mb-3">
                    <span class="badge bg-<?= $statusInfo['class'] ?> fs-6 py-2 px-3">
                        <i class="bi bi-<?= $statusInfo['icon'] ?> me-1"></i>
                        <?= $statusInfo['text'] ?>
                    </span>
                </div>
                <?php if ($model->status == 1): ?>
                    <a href="<?= Url::to(['booking/create', 'room_id' => $model->id]) ?>" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-calendar-plus me-2"></i>จองห้องนี้
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="bi bi-calendar-x me-2"></i>ไม่พร้อมให้จอง
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pricing Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-cash me-2"></i>อัตราค่าใช้บริการ
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small">รายชั่วโมง</div>
                        <div class="fs-5 fw-bold text-primary">
                            <?= $model->hourly_rate > 0 ? number_format($model->hourly_rate, 2) . ' ฿' : 'ฟรี' ?>
                        </div>
                    </div>
                    <i class="bi bi-clock text-muted fs-3"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small">ครึ่งวัน (4 ชม.)</div>
                        <div class="fs-5 fw-bold text-success">
                            <?= $model->half_day_rate > 0 ? number_format($model->half_day_rate, 2) . ' ฿' : 'ฟรี' ?>
                        </div>
                    </div>
                    <i class="bi bi-sun text-muted fs-3"></i>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">เต็มวัน (8 ชม.)</div>
                        <div class="fs-5 fw-bold text-warning">
                            <?= $model->full_day_rate > 0 ? number_format($model->full_day_rate, 2) . ' ฿' : 'ฟรี' ?>
                        </div>
                    </div>
                    <i class="bi bi-calendar-day text-muted fs-3"></i>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>สถิติ
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="fs-3 fw-bold text-primary"><?= $model->getBookings()->where(['status' => 'completed'])->count() ?></div>
                        <div class="text-muted small">การจองสำเร็จ</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-3 fw-bold text-warning"><?= $model->getBookings()->where(['status' => 'pending'])->count() ?></div>
                        <div class="text-muted small">รออนุมัติ</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-3 fw-bold text-success"><?= $model->getBookings()->where(['status' => 'approved'])->count() ?></div>
                        <div class="text-muted small">อนุมัติแล้ว</div>
                    </div>
                    <div class="col-6">
                        <div class="fs-3 fw-bold text-danger"><?= $model->getBookings()->where(['status' => 'cancelled'])->count() ?></div>
                        <div class="text-muted small">ยกเลิก</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>ข้อมูลระบบ
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">สร้างเมื่อ:</td>
                        <td><?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">แก้ไขล่าสุด:</td>
                        <td><?= Yii::$app->formatter->asDatetime($model->updated_at, 'php:d/m/Y H:i') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>ยืนยันการลบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                <p class="mt-3 mb-0">คุณต้องการลบห้องประชุม <strong><?= Html::encode($model->name_th) ?></strong> หรือไม่?</p>
                <p class="text-muted small">การดำเนินการนี้ไม่สามารถยกเลิกได้</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <?= Html::beginForm(['room/delete', 'id' => $model->id], 'post', ['style' => 'display:inline']) ?>
                    <?= Html::submitButton('<i class="bi bi-trash me-1"></i>ลบห้องประชุม', ['class' => 'btn btn-danger']) ?>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
</div>
