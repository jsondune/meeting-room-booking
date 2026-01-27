<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'จองห้องประชุม';
$this->params['breadcrumbs'][] = ['label' => 'การจองของฉัน', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Use rooms from controller or get from database if not provided
if (!isset($rooms) || empty($rooms)) {
    $rooms = \common\models\MeetingRoom::find()
        ->where(['status' => \common\models\MeetingRoom::STATUS_ACTIVE])
        ->all();
}

// If still empty, use sample data for demo
if (empty($rooms)) {
    $rooms = [
        ['id' => 1, 'name_th' => 'ห้องประชุมใหญ่ A', 'building_name' => 'อาคาร A', 'floor' => 3, 'capacity' => 50, 'hourly_rate' => 500],
        ['id' => 2, 'name_th' => 'ห้องประชุมกลาง B', 'building_name' => 'อาคาร A', 'floor' => 2, 'capacity' => 20, 'hourly_rate' => 300],
        ['id' => 3, 'name_th' => 'ห้องประชุมย่อย C', 'building_name' => 'อาคาร B', 'floor' => 1, 'capacity' => 10, 'hourly_rate' => 200],
        ['id' => 4, 'name_th' => 'ห้องประชุม VIP', 'building_name' => 'อาคาร A', 'floor' => 5, 'capacity' => 15, 'hourly_rate' => 800],
    ];
}

// Meeting types (matching Booking model constants)
$purposes = [
    'internal' => 'ประชุมภายใน',
    'external' => 'ประชุมภายนอก',
    'training' => 'อบรม/สัมมนา',
    'interview' => 'สัมภาษณ์',
    'seminar' => 'สัมมนา/Workshop',
    'other' => 'อื่นๆ',
];

// Pre-selected room
$selectedRoomId = Yii::$app->request->get('room_id');
?>

<div class="booking-create">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-1">
                            <i class="bi bi-calendar-plus me-2"></i><?= Html::encode($this->title) ?>
                        </h1>
                        <p class="text-muted mb-0">กรอกข้อมูลเพื่อจองห้องประชุม</p>
                    </div>
                    <a href="<?= Url::to(['booking/index']) ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>กลับ
                    </a>
                </div>

                <form id="bookingForm" method="post" action="<?= Url::to(['booking/create']) ?>">
                    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                    
                    <!-- Step Indicator -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body py-4">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="step-indicator active" data-step="1">
                                        <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">1</div>
                                        <div class="small fw-semibold">เลือกห้อง & วันเวลา</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="step-indicator" data-step="2">
                                        <div class="step-number bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">2</div>
                                        <div class="small fw-semibold">รายละเอียดการประชุม</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="step-indicator" data-step="3">
                                        <div class="step-number bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 40px; height: 40px;">3</div>
                                        <div class="small fw-semibold">ยืนยันการจอง</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Room & Time Selection -->
                    <div class="step-content" id="step1">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-door-open me-2"></i>เลือกห้องประชุม
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <?php foreach ($rooms as $room): ?>
                                    <?php
                                        // Support both array and MeetingRoom model
                                        $roomId = is_array($room) ? $room['id'] : $room->id;
                                        $roomName = is_array($room) ? ($room['name_th'] ?? $room['name'] ?? '') : ($room->name_th ?? $room->name ?? '');
                                        $roomBuilding = is_array($room) ? ($room['building_name'] ?? $room['building'] ?? '') : ($room->building ? $room->building->name_th : '');
                                        $roomFloor = is_array($room) ? ($room['floor'] ?? '') : ($room->floor ?? '');
                                        $roomCapacity = is_array($room) ? ($room['capacity'] ?? 0) : ($room->capacity ?? 0);
                                        $roomHourlyRate = is_array($room) ? ($room['hourly_rate'] ?? 0) : ($room->hourly_rate ?? 0);
                                        
                                        // Get room image
                                        $roomImage = null;
                                        if (!is_array($room) && method_exists($room, 'getPrimaryImage')) {
                                            $primaryImage = $room->getPrimaryImage();
                                            $roomImage = $primaryImage ? $primaryImage->getUrl() : null;
                                        }
                                    ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="form-check room-card border rounded h-100 position-relative <?= $selectedRoomId == $roomId ? 'border-primary border-2' : '' ?>">
                                            <input class="form-check-input position-absolute" type="radio" name="Booking[room_id]" value="<?= $roomId ?>" 
                                                   id="room<?= $roomId ?>" <?= $selectedRoomId == $roomId ? 'checked' : '' ?> required
                                                   data-hourly-rate="<?= $roomHourlyRate ?>"
                                                   style="top: 12px; left: 12px; z-index: 20; width: 20px; height: 20px;">
                                            <label class="form-check-label w-100 d-block" for="room<?= $roomId ?>" style="cursor: pointer;">
                                                <!-- Room Thumbnail -->
                                                <div class="room-thumb position-relative" style="height: 120px; overflow: hidden; border-radius: 0.375rem 0.375rem 0 0;">
                                                    <?php if ($roomImage): ?>
                                                        <img src="<?= Html::encode($roomImage) ?>" alt="<?= Html::encode($roomName) ?>" 
                                                             class="w-100 h-100" style="object-fit: cover;"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="room-thumb-placeholder d-none flex-column align-items-center justify-content-center text-white w-100 h-100 position-absolute top-0"
                                                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                            <i class="bi bi-door-open" style="font-size: 2rem; opacity: 0.8;"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="room-thumb-placeholder d-flex flex-column align-items-center justify-content-center text-white w-100 h-100"
                                                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                            <i class="bi bi-door-open" style="font-size: 2rem; opacity: 0.8;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Status Badge -->
                                                    <span class="badge bg-success position-absolute" style="bottom: 8px; right: 8px;">ว่าง</span>
                                                </div>
                                                
                                                <!-- Room Info -->
                                                <div class="p-3">
                                                    <div class="fw-semibold mb-1"><?= Html::encode($roomName) ?></div>
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="bi bi-geo-alt me-1"></i><?= Html::encode($roomBuilding) ?> <?= $roomFloor ? 'ชั้น ' . $roomFloor : '' ?>
                                                    </small>
                                                    <div class="d-flex justify-content-between align-items-center small">
                                                        <span>
                                                            <i class="bi bi-people me-1"></i><?= $roomCapacity ?> คน
                                                        </span>
                                                        <span class="text-primary fw-semibold">
                                                            <?= number_format($roomHourlyRate) ?> ฿/ชม.
                                                        </span>
                                                    </div>
                                                </div>
                                            </label>
                                            
                                            <!-- View Detail Link -->
                                            <div class="px-3 pb-3">
                                                <a href="<?= Url::to(['room/view', 'id' => $roomId]) ?>" target="_blank" 
                                                   class="btn btn-sm btn-outline-secondary w-100"
                                                   onclick="event.stopPropagation();">
                                                    <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="text-end mt-3">
                                    <a href="<?= Url::to(['room/index']) ?>" class="text-primary">
                                        <i class="bi bi-search me-1"></i>ค้นหาห้องประชุมเพิ่มเติม
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-calendar3 me-2"></i>เลือกวันและเวลา
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">วันที่ประชุม <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" id="thaiDateDisplay" class="form-control" 
                                                   readonly placeholder="เลือกวันที่" style="background-color: #fff; cursor: pointer;" required>
                                            <input type="hidden" name="Booking[booking_date]" id="bookingDate" value="">
                                            <i class="fas fa-calendar-alt position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">เวลาเริ่ม <span class="text-danger">*</span></label>
                                        <select class="form-select" name="Booking[start_time]" id="startTime" required>
                                            <option value="">-- เลือก --</option>
                                            <?php for ($h = 8; $h <= 17; $h++): ?>
                                                <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00 น.', $h) ?></option>
                                                <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30 น.', $h) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                                        <select class="form-select" name="Booking[end_time]" id="endTime" required>
                                            <option value="">-- เลือก --</option>
                                            <?php for ($h = 9; $h <= 18; $h++): ?>
                                                <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00 น.', $h) ?></option>
                                                <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30 น.', $h) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Availability Display -->
                                <div class="mt-4" id="availabilitySection" style="display: none;">
                                    <h6 class="mb-3">ตารางการใช้งานห้อง</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <?php for ($h = 8; $h <= 17; $h++): ?>
                                                        <th style="min-width: 60px;"><?= sprintf('%02d:00', $h) ?></th>
                                                    <?php endfor; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr id="timeSlots">
                                                    <?php for ($h = 8; $h <= 17; $h++): ?>
                                                        <td class="time-slot available" data-hour="<?= $h ?>">
                                                            <span class="badge bg-success">ว่าง</span>
                                                        </td>
                                                    <?php endfor; ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="small text-muted mt-2">
                                        <span class="badge bg-success me-2">ว่าง</span>
                                        <span class="badge bg-danger me-2">ไม่ว่าง</span>
                                        <span class="badge bg-primary">เลือก</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div></div>
                            <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(2)">
                                ถัดไป <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Meeting Details -->
                    <div class="step-content" id="step2" style="display: none;">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-info-circle me-2"></i>รายละเอียดการประชุม
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">หัวข้อ/เรื่อง <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Booking[meeting_title]" id="topic" 
                                               placeholder="ระบุหัวข้อการประชุม" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">วัตถุประสงค์ <span class="text-danger">*</span></label>
                                        <select class="form-select" name="Booking[meeting_type]" id="purpose" required>
                                            <option value="">-- เลือกวัตถุประสงค์ --</option>
                                            <?php foreach ($purposes as $key => $label): ?>
                                                <option value="<?= $key ?>"><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนผู้เข้าร่วม <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="Booking[attendees_count]" id="attendees" 
                                               min="1" placeholder="ระบุจำนวนคน" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ผู้ติดต่อ</label>
                                        <input type="text" class="form-control" name="Booking[contact_person]" 
                                               placeholder="ชื่อผู้ติดต่อ">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">รายละเอียดเพิ่มเติม</label>
                                        <textarea class="form-control" name="Booking[meeting_description]" rows="3" 
                                                  placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-laptop me-2"></i>อุปกรณ์และบริการเพิ่มเติม
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-3">อุปกรณ์เพิ่มเติม</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="equipment[]" value="laptop" data-price="200">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>โน๊ตบุ๊ค</span>
                                                <span class="text-muted">+200 ฿</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="equipment[]" value="wireless_mic" data-price="100">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>ไมโครโฟนไร้สาย</span>
                                                <span class="text-muted">+100 ฿</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="equipment[]" value="webcam" data-price="150">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>Webcam HD</span>
                                                <span class="text-muted">+150 ฿</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input extra-item" type="checkbox" name="equipment[]" value="flipchart" data-price="50">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>Flip Chart</span>
                                                <span class="text-muted">+50 ฿</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="mb-3">บริการเพิ่มเติม</h6>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="services[]" value="water" data-price="60">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>น้ำดื่ม (12 ขวด)</span>
                                                <span class="text-muted">+60 ฿</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="services[]" value="coffee" data-price="30">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>กาแฟ/ชา (ต่อคน)</span>
                                                <span class="text-muted">+30 ฿/คน</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input extra-item" type="checkbox" name="services[]" value="snack" data-price="80">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>อาหารว่าง (ต่อคน)</span>
                                                <span class="text-muted">+80 ฿/คน</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input extra-item" type="checkbox" name="services[]" value="lunch" data-price="150">
                                            <label class="form-check-label d-flex justify-content-between">
                                                <span>อาหารกลางวัน (ต่อคน)</span>
                                                <span class="text-muted">+150 ฿/คน</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-gear me-2"></i>ตัวเลือกเพิ่มเติม
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">รูปแบบการจัดโต๊ะ</label>
                                        <select class="form-select" name="table_layout">
                                            <option value="theater">Theater Style</option>
                                            <option value="classroom">Classroom Style</option>
                                            <option value="u_shape">U-Shape</option>
                                            <option value="boardroom" selected>Boardroom</option>
                                            <option value="banquet">Banquet</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">หมายเหตุ/ความต้องการพิเศษ</label>
                                        <textarea class="form-control" name="Booking[special_requests]" rows="2" 
                                                  placeholder="เช่น ต้องการเปิดแอร์ล่วงหน้า 30 นาที"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-lg" onclick="goToStep(1)">
                                <i class="bi bi-arrow-left me-1"></i> ก่อนหน้า
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(3)">
                                ถัดไป <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Confirmation -->
                    <div class="step-content" id="step3" style="display: none;">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-check-circle me-2"></i>ยืนยันข้อมูลการจอง
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-muted mb-3">ข้อมูลห้องประชุม</h6>
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 40%;">ห้องประชุม:</td>
                                                <td class="fw-semibold" id="confirmRoom">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">วันที่:</td>
                                                <td class="fw-semibold" id="confirmDate">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">เวลา:</td>
                                                <td class="fw-semibold" id="confirmTime">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-muted mb-3">รายละเอียดการประชุม</h6>
                                        <table class="table table-borderless mb-0">
                                            <tr>
                                                <td class="text-muted" style="width: 40%;">หัวข้อ:</td>
                                                <td class="fw-semibold" id="confirmTopic">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">วัตถุประสงค์:</td>
                                                <td class="fw-semibold" id="confirmPurpose">-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted">ผู้เข้าร่วม:</td>
                                                <td class="fw-semibold" id="confirmAttendees">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <hr>

                                <!-- Price Summary -->
                                <div class="row justify-content-end">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-3">สรุปค่าใช้จ่าย</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td>ค่าห้องประชุม</td>
                                                <td class="text-end" id="confirmRoomPrice">0 ฿</td>
                                            </tr>
                                            <tr>
                                                <td>อุปกรณ์/บริการเพิ่มเติม</td>
                                                <td class="text-end" id="confirmExtraPrice">0 ฿</td>
                                            </tr>
                                            <tr class="border-top">
                                                <td class="fw-bold">รวมทั้งหมด</td>
                                                <td class="text-end fw-bold text-primary fs-5" id="confirmTotalPrice">0 ฿</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label" for="termsCheck">
                                        ข้าพเจ้ายอมรับ <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">ข้อกำหนดการใช้งานห้องประชุม</a>
                                        และยืนยันว่าข้อมูลที่กรอกถูกต้อง
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-lg" onclick="goToStep(2)">
                                <i class="bi bi-arrow-left me-1"></i> ก่อนหน้า
                            </button>
                            <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                                <i class="bi bi-calendar-check me-2"></i>ยืนยันการจอง
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ข้อกำหนดการใช้งานห้องประชุม</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. การจองห้องประชุม</h6>
                <ul>
                    <li>ผู้จองต้องเป็นบุคลากรของหน่วยงานเท่านั้น</li>
                    <li>ต้องจองล่วงหน้าอย่างน้อย 1 วันทำการ</li>
                    <li>การจองจะสมบูรณ์เมื่อได้รับการอนุมัติจากเจ้าหน้าที่</li>
                </ul>
                
                <h6>2. การใช้งานห้องประชุม</h6>
                <ul>
                    <li>ใช้ห้องประชุมตามวัตถุประสงค์ที่ระบุเท่านั้น</li>
                    <li>ห้ามนำอาหารและเครื่องดื่มเข้าห้องประชุม (ยกเว้นน้ำเปล่า)</li>
                    <li>รักษาความสะอาดและปิดไฟ/แอร์เมื่อเลิกใช้งาน</li>
                </ul>
                
                <h6>3. การยกเลิกการจอง</h6>
                <ul>
                    <li>สามารถยกเลิกได้ล่วงหน้าอย่างน้อย 24 ชั่วโมง</li>
                    <li>การยกเลิกกะทันหันอาจส่งผลต่อสิทธิ์การจองในอนาคต</li>
                </ul>
                
                <h6>4. ความรับผิดชอบ</h6>
                <ul>
                    <li>ผู้จองต้องรับผิดชอบต่อความเสียหายที่เกิดขึ้น</li>
                    <li>แจ้งเจ้าหน้าที่ทันทีหากพบอุปกรณ์ชำรุด</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">เข้าใจและยอมรับ</button>
            </div>
        </div>
    </div>
</div>

<?php
$css = <<<CSS
.room-card {
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}

.room-card:hover {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.room-card input[type="radio"] {
    transform: scale(1.3);
    background-color: #fff;
    border: 2px solid #dee2e6;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    cursor: pointer;
}

.room-card input[type="radio"]:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.room-card input:checked ~ label {
    color: var(--bs-primary);
}

.room-card input:checked ~ label .room-thumb {
    border-bottom: 3px solid var(--bs-primary);
}

.room-thumb img {
    transition: transform 0.3s ease;
}

.room-card:hover .room-thumb img {
    transform: scale(1.05);
}

.step-indicator .step-number {
    transition: all 0.3s ease;
}

.step-indicator.active .step-number {
    transform: scale(1.1);
}

.time-slot {
    padding: 8px 4px;
}

.time-slot.available {
    background-color: rgba(var(--bs-success-rgb), 0.1);
}

.time-slot.booked {
    background-color: rgba(var(--bs-danger-rgb), 0.1);
}

.time-slot.selected {
    background-color: rgba(var(--bs-primary-rgb), 0.2);
}
CSS;
$this->registerCss($css);
?>

<script>
(function() {
    'use strict';
    
    var currentStep = 1;
    var purposeLabels = <?= json_encode($purposes) ?>;

    // Make goToStep globally accessible
    window.goToStep = function(step) {
        console.log('goToStep called with step:', step, 'currentStep:', currentStep);
        
        // Validate current step before moving forward
        if (step > currentStep) {
            if (!validateStep(currentStep)) {
                console.log('Validation failed for step:', currentStep);
                return false;
            }
        }
        
        console.log('Switching to step:', step);
        
        // Update step content visibility
        document.querySelectorAll('.step-content').forEach(function(el) {
            el.style.display = 'none';
        });
        
        var stepElement = document.getElementById('step' + step);
        if (stepElement) {
            stepElement.style.display = 'block';
        } else {
            console.error('Step element not found:', 'step' + step);
            return false;
        }
        
        // Update step indicators
        document.querySelectorAll('.step-indicator').forEach(function(el, index) {
            var stepNum = index + 1;
            el.classList.remove('active');
            var numEl = el.querySelector('.step-number');
            
            if (stepNum <= step) {
                numEl.classList.remove('bg-secondary');
                numEl.classList.add('bg-primary');
            } else {
                numEl.classList.remove('bg-primary');
                numEl.classList.add('bg-secondary');
            }
            
            if (stepNum === step) {
                el.classList.add('active');
            }
        });
        
        currentStep = step;
        
        // Update confirmation if step 3
        if (step === 3) {
            updateConfirmation();
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        return true;
    };

    function validateStep(step) {
        console.log('validateStep called for step:', step);
        
        if (step === 1) {
            var roomSelected = document.querySelector('input[name="Booking[room_id]"]:checked');
            var dateInput = document.getElementById('bookingDate');
            var startTimeSelect = document.getElementById('startTime');
            var endTimeSelect = document.getElementById('endTime');
            
            var date = dateInput ? dateInput.value : '';
            var startTime = startTimeSelect ? startTimeSelect.value : '';
            var endTime = endTimeSelect ? endTimeSelect.value : '';
            
            console.log('Step 1 validation - Room:', roomSelected, 'Date:', date, 'Start:', startTime, 'End:', endTime);
            
            if (!roomSelected) {
                alert('โปรดเลือกห้องประชุม');
                return false;
            }
            if (!date) {
                alert('โปรดเลือกวันที่');
                return false;
            }
            if (!startTime || !endTime) {
                alert('โปรดเลือกเวลาเริ่มและเวลาสิ้นสุด');
                return false;
            }
            
            // Compare times properly
            var startMinutes = timeToMinutes(startTime);
            var endMinutes = timeToMinutes(endTime);
            
            console.log('Time comparison - Start minutes:', startMinutes, 'End minutes:', endMinutes);
            
            if (startMinutes >= endMinutes) {
                alert('เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น');
                return false;
            }
        }
        
        if (step === 2) {
            var topic = document.getElementById('topic').value;
            var purpose = document.getElementById('purpose').value;
            var attendees = document.getElementById('attendees').value;
            
            console.log('Step 2 validation - Topic:', topic, 'Purpose:', purpose, 'Attendees:', attendees);
            
            if (!topic.trim()) {
                alert('โปรดระบุหัวข้อการประชุม');
                document.getElementById('topic').focus();
                return false;
            }
            if (!purpose) {
                alert('โปรดเลือกวัตถุประสงค์');
                document.getElementById('purpose').focus();
                return false;
            }
            if (!attendees || parseInt(attendees) < 1) {
                alert('โปรดระบุจำนวนผู้เข้าร่วม');
                document.getElementById('attendees').focus();
                return false;
            }
        }
        
        console.log('Validation passed for step:', step);
        return true;
    }

    function timeToMinutes(timeStr) {
        if (!timeStr) return 0;
        var parts = timeStr.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1] || 0);
    }

    function updateConfirmation() {
        var roomEl = document.querySelector('input[name="Booking[room_id]"]:checked');
        var roomName = roomEl ? roomEl.closest('.room-card').querySelector('.fw-semibold').textContent : '-';
        var date = document.getElementById('bookingDate').value;
        var startTime = document.getElementById('startTime').value;
        var endTime = document.getElementById('endTime').value;
        var topic = document.getElementById('topic').value;
        var purpose = document.getElementById('purpose').value;
        var attendees = document.getElementById('attendees').value;
        
        document.getElementById('confirmRoom').textContent = roomName;
        // Use ThaiDate helper if available
        if (date) {
            if (typeof ThaiDate !== 'undefined') {
                document.getElementById('confirmDate').textContent = ThaiDate.format(date, 'long');
            } else {
                // Fallback with Buddhist calendar
                document.getElementById('confirmDate').textContent = new Date(date).toLocaleDateString('th-TH-u-ca-buddhist', {day: 'numeric', month: 'long', year: 'numeric'});
            }
        } else {
            document.getElementById('confirmDate').textContent = '-';
        }
        document.getElementById('confirmTime').textContent = startTime + ' - ' + endTime + ' น.';
        document.getElementById('confirmTopic').textContent = topic || '-';
        document.getElementById('confirmPurpose').textContent = purposeLabels[purpose] || '-';
        document.getElementById('confirmAttendees').textContent = attendees ? attendees + ' คน' : '-';
        
        // Calculate prices
        var hourlyRate = roomEl ? parseInt(roomEl.dataset.hourlyRate) : 0;
        var hours = calculateHours(startTime, endTime);
        var roomPrice = hours * hourlyRate;
        
        var extraPrice = 0;
        document.querySelectorAll('.extra-item:checked').forEach(function(item) {
            extraPrice += parseInt(item.dataset.price) || 0;
        });
        
        document.getElementById('confirmRoomPrice').textContent = roomPrice.toLocaleString() + ' ฿';
        document.getElementById('confirmExtraPrice').textContent = extraPrice.toLocaleString() + ' ฿';
        document.getElementById('confirmTotalPrice').textContent = (roomPrice + extraPrice).toLocaleString() + ' ฿';
    }

    function calculateHours(start, end) {
        if (!start || !end) return 0;
        var startParts = start.split(':');
        var endParts = end.split(':');
        var startMinutes = parseInt(startParts[0]) * 60 + parseInt(startParts[1]);
        var endMinutes = parseInt(endParts[0]) * 60 + parseInt(endParts[1]);
        return Math.max(0, (endMinutes - startMinutes) / 60);
    }

    // Show availability section when date is selected
    document.getElementById('bookingDate').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('availabilitySection').style.display = 'block';
        }
    });

    // Room card selection styling
    document.querySelectorAll('.room-card input[type="radio"]').forEach(function(input) {
        input.addEventListener('change', function() {
            document.querySelectorAll('.room-card').forEach(function(card) {
                card.classList.remove('border-primary', 'border-2');
            });
            if (this.checked) {
                this.closest('.room-card').classList.add('border-primary', 'border-2');
            }
        });
    });

    // Thai Date Picker for Booking
    (function initThaiDatePicker() {
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                           'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        const thaiMonthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                                 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
        const thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
        
        const displayInput = document.getElementById('thaiDateDisplay');
        const hiddenInput = document.getElementById('bookingDate');
        
        if (!displayInput || !hiddenInput) return;
        
        let selectedDate = new Date();
        selectedDate.setDate(selectedDate.getDate() + 1); // Default to tomorrow
        let viewDate = new Date(selectedDate);
        const minDate = new Date();
        minDate.setHours(0, 0, 0, 0);
        
        function formatThaiDateShort(date) {
            const day = date.getDate();
            const month = thaiMonthsShort[date.getMonth()];
            const year = date.getFullYear() + 543;
            return `${day} ${month} ${year}`;
        }
        
        function updateDisplay() {
            displayInput.value = formatThaiDateShort(selectedDate);
            hiddenInput.value = selectedDate.toISOString().split('T')[0];
            // Trigger change event for form validation
            hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
        
        // Create picker container
        const pickerContainer = document.createElement('div');
        pickerContainer.className = 'thai-datepicker-booking';
        pickerContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1050;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 1rem;
            min-width: 280px;
            display: none;
        `;
        displayInput.parentElement.style.position = 'relative';
        displayInput.parentElement.appendChild(pickerContainer);
        
        function renderCalendar() {
            const year = viewDate.getFullYear();
            const month = viewDate.getMonth();
            const thaiYear = year + 543;
            
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const daysInPrevMonth = new Date(year, month, 0).getDate();
            
            let html = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="prevMonthBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span style="font-weight: 600;">${thaiMonths[month]} ${thaiYear}</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="nextMonthBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center;">
            `;
            
            // Day headers
            thaiDaysShort.forEach(day => {
                html += `<div style="font-size: 0.75rem; font-weight: 600; color: #6c757d; padding: 0.25rem;">${day}</div>`;
            });
            
            // Previous month days
            for (let i = firstDay - 1; i >= 0; i--) {
                const day = daysInPrevMonth - i;
                html += `<div style="padding: 0.5rem; color: #adb5bd; font-size: 0.875rem;">${day}</div>`;
            }
            
            // Current month days
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            for (let day = 1; day <= daysInMonth; day++) {
                const date = new Date(year, month, day);
                date.setHours(0, 0, 0, 0);
                
                let style = 'padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem;';
                let classes = '';
                
                if (date < minDate) {
                    style += ' color: #dee2e6; cursor: not-allowed;';
                } else {
                    style += ' cursor: pointer;';
                    classes = 'date-selectable';
                }
                
                if (date.toDateString() === today.toDateString()) {
                    style += ' border: 1px solid #0d6efd;';
                }
                
                if (date.toDateString() === selectedDate.toDateString()) {
                    style += ' background-color: #0d6efd; color: #fff;';
                }
                
                html += `<div class="${classes}" data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}" style="${style}">${day}</div>`;
            }
            
            html += '</div>';
            pickerContainer.innerHTML = html;
            
            // Bind events
            document.getElementById('prevMonthBtn')?.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                viewDate.setMonth(viewDate.getMonth() - 1);
                renderCalendar();
            });
            
            document.getElementById('nextMonthBtn')?.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                viewDate.setMonth(viewDate.getMonth() + 1);
                renderCalendar();
            });
            
            pickerContainer.querySelectorAll('.date-selectable').forEach(el => {
                el.addEventListener('click', function() {
                    const dateStr = this.dataset.date;
                    selectedDate = new Date(dateStr);
                    updateDisplay();
                    pickerContainer.style.display = 'none';
                });
                
                el.addEventListener('mouseenter', function() {
                    if (!this.style.backgroundColor.includes('13, 110, 253')) {
                        this.style.backgroundColor = '#e9ecef';
                    }
                });
                
                el.addEventListener('mouseleave', function() {
                    if (!this.style.backgroundColor.includes('13, 110, 253')) {
                        this.style.backgroundColor = '';
                    }
                });
            });
        }
        
        // Toggle picker
        displayInput.addEventListener('click', function(e) {
            e.stopPropagation();
            viewDate = new Date(selectedDate);
            renderCalendar();
            pickerContainer.style.display = pickerContainer.style.display === 'none' ? 'block' : 'none';
        });
        
        // Close on outside click
        document.addEventListener('click', function(e) {
            if (!pickerContainer.contains(e.target) && e.target !== displayInput) {
                pickerContainer.style.display = 'none';
            }
        });
        
        // Initial display
        updateDisplay();
    })();

    console.log('Booking form script loaded');
})();
</script>
