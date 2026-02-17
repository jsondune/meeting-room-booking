<?php

/** @var yii\web\View $this */
/** @var common\models\MeetingRoom[] $featuredRooms */
/** @var int $availableRoomsCount */
/** @var common\models\Building[] $buildings */
/** @var int $todayBookings */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'หน้าแรก';
?>

<!-- Hero Section -->
<section class="hero-section mb-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="display-5 fw-bold text-dark mb-3">
                จองห้องประชุม<br>
                <span class="text-primary">ง่าย สะดวก รวดเร็ว</span>
            </h1>
            <p class="lead text-muted mb-4">
                ระบบจองห้องประชุมออนไลน์ที่ช่วยให้คุณสามารถค้นหา ตรวจสอบ และจองห้องประชุมได้ทุกที่ทุกเวลา
                พร้อมระบบแจ้งเตือนและการจัดการที่ครบครัน
            </p>
            <div class="d-flex flex-wrap gap-3">
                <a href="<?= Url::to(['/room/index']) ?>" class="btn btn-primary btn-lg px-4">
                    <i class="fas fa-search me-2"></i> ค้นหาห้องประชุม
                </a>
                <?php if (Yii::$app->user->isGuest): ?>
                    <a href="<?= Url::to(['/site/login']) ?>" class="btn btn-outline-primary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i> เข้าสู่ระบบ
                    </a>
                <?php else: ?>
                    <a href="<?= Url::to(['/booking/create']) ?>" class="btn btn-success btn-lg px-4">
                        <i class="fas fa-calendar-plus me-2"></i> จองห้องประชุม
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <img src="<?= Yii::getAlias('@web') ?>/images/meeting-room-illustration.svg" 
                 alt="Meeting Room" 
                 class="img-fluid"
                 onerror="this.src='https://illustrations.popsy.co/amber/work-party.svg'">
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="stats-section mb-5">
    <div class="row g-4">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-door-open fa-2x text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($availableRoomsCount) ?></h3>
                    <p class="text-muted mb-0">ห้องประชุม</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-building fa-2x text-success"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= count($buildings) ?></h3>
                    <p class="text-muted mb-0">อาคาร</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-calendar-check fa-2x text-info"></i>
                    </div>
                    <h3 class="fw-bold mb-1"><?= number_format($todayBookings) ?></h3>
                    <p class="text-muted mb-0">การจองวันนี้</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="stat-icon mb-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                    <h3 class="fw-bold mb-1">24/7</h3>
                    <p class="text-muted mb-0">พร้อมให้บริการ</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Search -->
<section class="quick-search-section mb-5">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="card-title fw-bold mb-4">
                <i class="fas fa-search text-primary me-2"></i> ค้นหาห้องประชุมด่วน
            </h5>
            <form action="<?= Url::to(['/room/index']) ?>" method="get">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">วันที่ต้องการ</label>
                        <div class="position-relative">
                            <input type="text" id="thaiDateDisplay" class="form-control" 
                                   readonly placeholder="เลือกวันที่" style="background-color: #fff; cursor: pointer;">
                            <input type="hidden" name="date" id="dateValue" value="<?= date('Y-m-d') ?>">
                            <i class="fas fa-calendar-alt position-absolute" style="right: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; pointer-events: none;"></i>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เวลาเริ่ม</label>
                        <select name="start_time" class="form-select">
                            <?php for ($h = 8; $h <= 17; $h++): ?>
                                <option value="<?= sprintf('%02d:00', $h) ?>"
                                    <?= $h == 9 ? 'selected' : '' ?>>
                                    <?= sprintf('%02d:00', $h) ?>
                                </option>
                                <option value="<?= sprintf('%02d:30', $h) ?>">
                                    <?= sprintf('%02d:30', $h) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">เวลาสิ้นสุด</label>
                        <select name="end_time" class="form-select">
                            <?php for ($h = 9; $h <= 18; $h++): ?>
                                <option value="<?= sprintf('%02d:00', $h) ?>"
                                    <?= $h == 10 ? 'selected' : '' ?>>
                                    <?= sprintf('%02d:00', $h) ?>
                                </option>
                                <?php if ($h < 18): ?>
                                <option value="<?= sprintf('%02d:30', $h) ?>">
                                    <?= sprintf('%02d:30', $h) ?>
                                </option>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">จำนวนคน</label>
                        <select name="capacity" class="form-select">
                            <option value="">ไม่จำกัด</option>
                            <option value="5">5+ คน</option>
                            <option value="10">10+ คน</option>
                            <option value="20">20+ คน</option>
                            <option value="50">50+ คน</option>
                            <option value="100">100+ คน</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> ค้นหา
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Rooms -->
<?php if (!empty($featuredRooms)): ?>
<section class="featured-rooms-section mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">
            <i class="fas fa-star text-warning me-2"></i> ห้องประชุมแนะนำ
        </h4>
        <a href="<?= Url::to(['/room/index']) ?>" class="btn btn-outline-primary btn-sm">
            ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        <?php foreach ($featuredRooms as $room): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm room-card">
                    <div class="room-image">
                        <?php 
                        $primaryImage = $room->getPrimaryImage();
                        $imageUrl = $primaryImage ? $primaryImage->getUrl() : 'https://via.placeholder.com/400x250?text=Meeting+Room';
                        ?>
                        <img src="<?= Html::encode($imageUrl) ?>" 
                             class="card-img-top" 
                             alt="<?= Html::encode($room->name_th) ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="room-badge">
                            <span class="badge bg-primary"><?= Html::encode($room->room_code) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><?= Html::encode($room->name_th) ?></h5>
                        <p class="text-muted mb-2">
                            <i class="fas fa-building me-1"></i> <?= Html::encode($room->building->name_th ?? '-') ?>
                            <span class="mx-2">|</span>
                            <i class="fas fa-users me-1"></i> <?= $room->capacity ?> คน
                        </p>
                        <div class="room-features mb-3">
                            <?php if ($room->has_projector): ?>
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    <i class="fas fa-projector"></i> โปรเจคเตอร์
                                </span>
                            <?php endif; ?>
                            <?php if ($room->has_video_conference): ?>
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    <i class="fas fa-video"></i> Video Conference
                                </span>
                            <?php endif; ?>
                            <?php if ($room->has_wifi): ?>
                                <span class="badge bg-light text-dark me-1 mb-1">
                                    <i class="fas fa-wifi"></i> WiFi
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="room-price">
                                <?php if ($room->hourly_rate > 0): ?>
                                    <span class="fw-bold text-primary"><?= number_format($room->hourly_rate) ?></span>
                                    <span class="text-muted">บาท/ชม.</span>
                                <?php else: ?>
                                    <span class="text-success fw-bold">ฟรี</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= Url::to(['/room/view', 'id' => $room->id]) ?>" 
                               class="btn btn-sm btn-outline-primary">
                                ดูรายละเอียด
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Buildings -->
<?php if (!empty($buildings)): ?>
<section class="buildings-section mb-5">
    <h4 class="fw-bold mb-4">
        <i class="fas fa-building text-primary me-2"></i> อาคารในระบบ
    </h4>
    <div class="row g-3">
        <?php foreach ($buildings as $building): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= Url::to(['/room/index', 'building_id' => $building->id]) ?>" 
                   class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm building-card">
                        <div class="card-body text-center py-4">
                            <i class="fas fa-building fa-2x text-primary mb-3"></i>
                            <h6 class="fw-bold mb-1"><?= Html::encode($building->name_th) ?></h6>
                            <small class="text-muted">
                                <?= $building->getRooms()->where(['status' => 1])->count() ?> ห้อง
                            </small>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- How It Works -->
<section class="how-it-works-section mb-5">
    <h4 class="fw-bold text-center mb-5">
        <i class="fas fa-question-circle text-primary me-2"></i> วิธีการใช้งาน
    </h4>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="text-center">
                <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">1</div>
                <h5 class="fw-bold">ค้นหาห้อง</h5>
                <p class="text-muted">ค้นหาห้องประชุมตามความต้องการ วันที่ เวลา และจำนวนผู้เข้าประชุม</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">2</div>
                <h5 class="fw-bold">เลือกห้อง</h5>
                <p class="text-muted">ดูรายละเอียดห้อง สิ่งอำนวยความสะดวก และตรวจสอบตารางว่าง</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="step-number bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">3</div>
                <h5 class="fw-bold">ทำการจอง</h5>
                <p class="text-muted">กรอกรายละเอียดการประชุม เลือกอุปกรณ์เสริม และยืนยันการจอง</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center">
                <div class="step-number bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                     style="width: 60px; height: 60px; font-size: 1.5rem; font-weight: bold;">✓</div>
                <h5 class="fw-bold">รับการยืนยัน</h5>
                <p class="text-muted">รับอีเมลยืนยันการจอง และเช็คอินเมื่อถึงเวลาประชุม</p>
            </div>
        </div>
    </div>
</section>

<style>
.hero-section {
    padding: 2rem 0;
}

.room-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.room-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.room-image {
    position: relative;
}

.room-badge {
    position: absolute;
    top: 10px;
    left: 10px;
}

.building-card {
    transition: all 0.2s;
}

.building-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    border-color: #0d6efd !important;
}

/* Thai Date Picker Styles */
.thai-datepicker {
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
}

.thai-datepicker.show {
    display: block;
}

.thai-datepicker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.thai-datepicker-header button {
    border: none;
    background: none;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
}

.thai-datepicker-header button:hover {
    background-color: #f0f0f0;
}

.thai-datepicker-title {
    font-weight: 600;
    color: #333;
}

.thai-datepicker-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    text-align: center;
}

.thai-datepicker-day-header {
    font-size: 0.75rem;
    font-weight: 600;
    color: #6c757d;
    padding: 0.25rem;
}

.thai-datepicker-day {
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.thai-datepicker-day:hover:not(.disabled):not(.selected) {
    background-color: #e9ecef;
}

.thai-datepicker-day.today {
    border: 1px solid #0d6efd;
}

.thai-datepicker-day.selected {
    background-color: #0d6efd;
    color: #fff;
}

.thai-datepicker-day.other-month {
    color: #adb5bd;
}

.thai-datepicker-day.disabled {
    color: #dee2e6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thai Date Picker Implementation
    const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    const thaiMonthsShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 
                             'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
    
    const displayInput = document.getElementById('thaiDateDisplay');
    const hiddenInput = document.getElementById('dateValue');
    
    if (!displayInput || !hiddenInput) return;
    
    let currentDate = new Date();
    let selectedDate = hiddenInput.value ? new Date(hiddenInput.value) : new Date();
    let viewDate = new Date(selectedDate);
    const minDate = new Date();
    minDate.setHours(0, 0, 0, 0);
    
    // Format Thai date
    function formatThaiDate(date) {
        const day = date.getDate();
        const month = thaiMonths[date.getMonth()];
        const year = date.getFullYear() + 543;
        return `${day} ${month} พ.ศ. ${year}`;
    }
    
    function formatThaiDateShort(date) {
        const day = date.getDate();
        const month = thaiMonthsShort[date.getMonth()];
        const year = date.getFullYear() + 543;
        return `${day} ${month} ${year}`;
    }
    
    // Update display
    function updateDisplay() {
        displayInput.value = formatThaiDateShort(selectedDate);
        hiddenInput.value = selectedDate.toISOString().split('T')[0];
    }
    
    // Create picker
    const picker = document.createElement('div');
    picker.className = 'thai-datepicker';
    displayInput.parentElement.appendChild(picker);
    
    function renderCalendar() {
        const year = viewDate.getFullYear();
        const month = viewDate.getMonth();
        const thaiYear = year + 543;
        
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();
        
        let html = `
            <div class="thai-datepicker-header">
                <button type="button" onclick="window.thaiDatePickerPrev()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="thai-datepicker-title">${thaiMonths[month]} ${thaiYear}</span>
                <button type="button" onclick="window.thaiDatePickerNext()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="thai-datepicker-days">
        `;
        
        // Day headers
        thaiDaysShort.forEach(day => {
            html += `<div class="thai-datepicker-day-header">${day}</div>`;
        });
        
        // Previous month days
        for (let i = firstDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            html += `<div class="thai-datepicker-day other-month disabled">${day}</div>`;
        }
        
        // Current month days
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            
            let classes = ['thai-datepicker-day'];
            
            if (date < minDate) {
                classes.push('disabled');
            }
            
            if (date.toDateString() === today.toDateString()) {
                classes.push('today');
            }
            
            if (date.toDateString() === selectedDate.toDateString()) {
                classes.push('selected');
            }
            
            const disabled = date < minDate;
            html += `<div class="${classes.join(' ')}" ${disabled ? '' : `onclick="window.thaiDatePickerSelect(${year}, ${month}, ${day})"`}>${day}</div>`;
        }
        
        // Next month days
        const totalCells = firstDay + daysInMonth;
        const remainingCells = 42 - totalCells;
        for (let day = 1; day <= remainingCells && totalCells < 42; day++) {
            html += `<div class="thai-datepicker-day other-month disabled">${day}</div>`;
        }
        
        html += '</div>';
        picker.innerHTML = html;
    }
    
    // Global functions for onclick
    window.thaiDatePickerPrev = function() {
        viewDate.setMonth(viewDate.getMonth() - 1);
        renderCalendar();
    };
    
    window.thaiDatePickerNext = function() {
        viewDate.setMonth(viewDate.getMonth() + 1);
        renderCalendar();
    };
    
    window.thaiDatePickerSelect = function(year, month, day) {
        selectedDate = new Date(year, month, day);
        updateDisplay();
        picker.classList.remove('show');
    };
    
    // Toggle picker
    displayInput.addEventListener('click', function(e) {
        e.stopPropagation();
        viewDate = new Date(selectedDate);
        renderCalendar();
        picker.classList.toggle('show');
    });
    
    // Close on outside click
    document.addEventListener('click', function(e) {
        if (!picker.contains(e.target) && e.target !== displayInput) {
            picker.classList.remove('show');
        }
    });
    
    // Initial display
    updateDisplay();
});
</script>
