<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\MeetingRoom;
use common\models\Booking;

$this->title = 'ภาพรวมการใช้ห้องประชุม';

// Initialize variables with defaults
$rooms = [];
$buildings = [];
$roomsByBuilding = [];
$todayBookings = 0;
$thisMonthBookings = 0;
$totalRooms = 0;

try {
    // Get rooms for filters
    $rooms = MeetingRoom::find()
        ->where(['status' => MeetingRoom::STATUS_ACTIVE])
        ->orderBy(['building_id' => SORT_ASC, 'name_th' => SORT_ASC])
        ->all();
    
    $totalRooms = count($rooms);

    // Get buildings from rooms (avoid direct Building model query)
    $buildingIds = array_unique(array_filter(array_map(function($room) {
        return $room->building_id;
    }, $rooms)));

    if (class_exists('common\models\Building') && !empty($buildingIds)) {
        try {
            $buildings = \common\models\Building::find()
                ->where(['id' => $buildingIds])
                ->orderBy(['name_th' => SORT_ASC])
                ->all();
        } catch (\Exception $e) {
            $buildings = [];
        }
    }

    // Group rooms by building
    foreach ($rooms as $room) {
        $buildingName = ($room->building && isset($room->building->name_th)) ? $room->building->name_th : 'ไม่ระบุอาคาร';
        if (!isset($roomsByBuilding[$buildingName])) {
            $roomsByBuilding[$buildingName] = [];
        }
        $roomsByBuilding[$buildingName][] = $room;
    }

    // Get today's statistics
    $today = date('Y-m-d');
    $todayBookings = Booking::find()
        ->where(['booking_date' => $today])
        ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
        ->count();

    $thisMonthBookings = Booking::find()
        ->where(['>=', 'booking_date', date('Y-m-01')])
        ->andWhere(['<=', 'booking_date', date('Y-m-t')])
        ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
        ->count();

} catch (\Exception $e) {
    Yii::error('Calendar overview error: ' . $e->getMessage());
}

// Room colors for calendar
$roomColors = [
    '#3788d8', '#e74c3c', '#2ecc71', '#9b59b6', '#f39c12', 
    '#1abc9c', '#e67e22', '#34495e', '#16a085', '#c0392b',
    '#8e44ad', '#27ae60', '#d35400', '#2980b9', '#7f8c8d'
];
?>

<div class="calendar-overview">
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-calendar-check fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" id="todayBookingsCount"><?= $todayBookings ?></h3>
                            <small class="text-white-50">การจองวันนี้</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-door-open fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" id="availableRoomsCount"><?= $totalRooms ?></h3>
                            <small class="text-white-50">ห้องทั้งหมด</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-graph-up fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0"><?= $thisMonthBookings ?></h3>
                            <small class="text-white-50">การจองเดือนนี้</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-white bg-opacity-50 rounded-circle p-3">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0" id="currentTime"><?= date('H:i') ?></h3>
                            <small class="text-dark-50">เวลาปัจจุบัน</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 100;">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>ตัวกรอง</h5>
                </div>
                <div class="card-body">
                    <!-- View Mode -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">มุมมอง</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="viewMode" id="viewWeek" value="timeGridWeek" checked>
                            <label class="btn btn-outline-primary" for="viewWeek"><i class="bi bi-calendar-week"></i> สัปดาห์</label>
                            
                            <input type="radio" class="btn-check" name="viewMode" id="viewMonth" value="dayGridMonth">
                            <label class="btn btn-outline-primary" for="viewMonth"><i class="bi bi-calendar-month"></i> เดือน</label>
                            
                            <input type="radio" class="btn-check" name="viewMode" id="viewDay" value="timeGridDay">
                            <label class="btn btn-outline-primary" for="viewDay"><i class="bi bi-calendar-day"></i> วัน</label>
                        </div>
                    </div>

                    <!-- Building Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">อาคาร</label>
                        <select class="form-select" id="buildingFilter">
                            <option value="">ทุกอาคาร</option>
                            <?php foreach ($buildings as $building): ?>
                            <option value="<?= $building->id ?>"><?= Html::encode($building->name_th) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Room Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold d-flex justify-content-between">
                            <span>ห้องประชุม</span>
                            <?php if (!empty($roomsByBuilding)): ?>
                            <a href="#" id="selectAllRooms" class="small text-decoration-none">เลือกทั้งหมด</a>
                            <?php endif; ?>
                        </label>
                        <div class="room-filter-list" style="max-height: 250px; overflow-y: auto;">
                            <?php if (empty($roomsByBuilding)): ?>
                            <p class="text-muted small mb-0">ยังไม่มีห้องประชุม</p>
                            <?php else: ?>
                            <?php $colorIndex = 0; foreach ($roomsByBuilding as $buildingName => $buildingRooms): ?>
                            <div class="mb-2">
                                <small class="text-muted fw-semibold"><?= Html::encode($buildingName) ?></small>
                                <?php foreach ($buildingRooms as $room): 
                                    $color = $roomColors[$colorIndex % count($roomColors)];
                                    $colorIndex++;
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input room-checkbox" type="checkbox" 
                                           id="room<?= $room->id ?>" value="<?= $room->id ?>" 
                                           data-color="<?= $color ?>"
                                           data-building="<?= $room->building_id ?>"
                                           data-name="<?= Html::encode($room->name_th) ?>"
                                           checked>
                                    <label class="form-check-label d-flex align-items-center" for="room<?= $room->id ?>">
                                        <span class="room-color-dot me-2" style="background-color: <?= $color ?>; width: 12px; height: 12px; border-radius: 50%; display: inline-block;"></span>
                                        <?= Html::encode($room->name_th) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">สถานะการจอง</label>
                        <div class="form-check">
                            <input class="form-check-input status-checkbox" type="checkbox" id="statusApproved" value="approved" checked>
                            <label class="form-check-label" for="statusApproved">
                                <span class="badge bg-success me-1">●</span> อนุมัติแล้ว
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input status-checkbox" type="checkbox" id="statusPending" value="pending" checked>
                            <label class="form-check-label" for="statusPending">
                                <span class="badge bg-warning me-1">●</span> รออนุมัติ
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input status-checkbox" type="checkbox" id="statusCompleted" value="completed">
                            <label class="form-check-label" for="statusCompleted">
                                <span class="badge bg-secondary me-1">●</span> เสร็จสิ้น
                            </label>
                        </div>
                    </div>

                    <!-- Quick Jump to Date -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">ไปยังวันที่</label>
                        <input type="date" class="form-control" id="gotoDate" value="<?= date('Y-m-d') ?>">
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary" id="btnToday">
                            <i class="bi bi-calendar-event me-1"></i>วันนี้
                        </button>
                        <?php if (!Yii::$app->user->isGuest): ?>
                        <a href="<?= Url::to(['booking/create']) ?>" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>จองห้องประชุม
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-calendar3 me-2"></i>ปฏิทินห้องประชุม</h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPrev">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnNext">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="calendar" style="min-height: 600px;"></div>
                </div>
            </div>
            
            <!-- Room Availability Summary -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>สถานะห้องประชุมขณะนี้</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3" id="roomStatusGrid">
                        <?php if (empty($rooms)): ?>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle me-2"></i>ยังไม่มีห้องประชุมในระบบ
                            </div>
                        </div>
                        <?php else: ?>
                        <?php foreach ($rooms as $index => $room): 
                            $color = $roomColors[$index % count($roomColors)];
                        ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="room-status-card p-3 border rounded" data-room-id="<?= $room->id ?>">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="room-color-dot me-2" style="background-color: <?= $color ?>; width: 12px; height: 12px; border-radius: 50%;"></span>
                                    <strong class="small"><?= Html::encode($room->name_th) ?></strong>
                                </div>
                                <div class="room-status">
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>ว่าง</span>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-people me-1"></i><?= $room->capacity ?? 0 ?> คน
                                </small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Detail Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="bookingDetailContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <a href="#" class="btn btn-primary" id="viewBookingBtn">
                    <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

<style>
.calendar-overview {
    padding: 1.5rem 0;
}

/* Room color dots */
.room-color-dot {
    flex-shrink: 0;
}

/* Calendar styling */
#calendar {
    padding: 1rem;
}

.fc-event {
    cursor: pointer;
    border: none !important;
    font-size: 0.85rem;
    padding: 2px 6px;
}

.fc-event:hover {
    opacity: 0.85;
}

.fc-timegrid-event {
    border-radius: 4px;
}

.fc-daygrid-event {
    border-radius: 4px;
}

/* Status colors */
.fc-event.status-approved {
    opacity: 1;
}

.fc-event.status-pending {
    opacity: 0.7;
    background-image: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 5px,
        rgba(255,255,255,0.1) 5px,
        rgba(255,255,255,0.1) 10px
    );
}

.fc-event.status-completed {
    opacity: 0.5;
}

/* Room status cards */
.room-status-card {
    transition: all 0.3s ease;
    background: #fff;
}

.room-status-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.room-status-card.in-use {
    border-color: #dc3545 !important;
    background: #fff5f5;
}

/* Sticky sidebar */
.sticky-top {
    position: -webkit-sticky;
    position: sticky;
}

/* Room filter list scrollbar */
.room-filter-list::-webkit-scrollbar {
    width: 6px;
}

.room-filter-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.room-filter-list::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.room-filter-list::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* Responsive */
@media (max-width: 991px) {
    .sticky-top {
        position: relative !important;
        top: 0 !important;
    }
}

/* Thai Buddhist year in calendar */
.fc-toolbar-title {
    font-size: 1.25rem !important;
}
</style>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/th.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    
    // Room colors mapping
    const roomColors = {};
    document.querySelectorAll('.room-checkbox').forEach(checkbox => {
        roomColors[checkbox.value] = checkbox.dataset.color;
    });
    
    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'th',
        initialView: 'timeGridWeek',
        headerToolbar: false,
        height: 'auto',
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,
        weekends: true,
        nowIndicator: true,
        selectable: <?= Yii::$app->user->isGuest ? 'false' : 'true' ?>,
        selectMirror: true,
        dayMaxEvents: true,
        navLinks: true,
        
        // Custom title format with Buddhist year
        titleFormat: function(date) {
            const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                               'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
            const d = date.date;
            const month = thaiMonths[d.month];
            const year = d.year + 543;
            return `${month} ${year}`;
        },
        
        // Event sources
        events: function(info, successCallback, failureCallback) {
            // Get selected rooms
            const selectedRooms = [];
            document.querySelectorAll('.room-checkbox:checked').forEach(cb => {
                selectedRooms.push(cb.value);
            });
            
            // Get selected statuses
            const selectedStatuses = [];
            document.querySelectorAll('.status-checkbox:checked').forEach(cb => {
                selectedStatuses.push(cb.value);
            });
            
            // Fetch events
            fetch('<?= Url::to(['site/calendar-overview-events']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                },
                body: JSON.stringify({
                    start: info.startStr,
                    end: info.endStr,
                    rooms: selectedRooms,
                    statuses: selectedStatuses
                })
            })
            .then(response => response.json())
            .then(data => {
                const events = data.map(event => ({
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    backgroundColor: roomColors[event.room_id] || '#3788d8',
                    borderColor: roomColors[event.room_id] || '#3788d8',
                    classNames: ['status-' + event.status],
                    extendedProps: {
                        roomId: event.room_id,
                        roomName: event.room_name,
                        userName: event.user_name,
                        userPhone: event.user_phone,
                        userEmail: event.user_email,
                        department: event.department,
                        status: event.status,
                        attendees: event.attendees_count,
                        description: event.description
                    }
                }));
                successCallback(events);
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
        },
        
        // Click on event to show details
        eventClick: function(info) {
            showBookingDetail(info.event);
        },
        
        // Select time slot to create booking
        select: function(info) {
            const startDate = info.startStr.split('T')[0];
            const startTime = info.startStr.split('T')[1]?.substring(0, 5) || '09:00';
            const endTime = info.endStr.split('T')[1]?.substring(0, 5) || '10:00';
            
            window.location.href = '<?= Url::to(['booking/create']) ?>?date=' + startDate + 
                                   '&start_time=' + startTime + '&end_time=' + endTime;
        },
        
        // View render for updating title
        datesSet: function(info) {
            updateCalendarTitle(info.view);
            updateRoomStatus();
        }
    });
    
    calendar.render();
    
    // Update calendar title with Buddhist year
    function updateCalendarTitle(view) {
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                           'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        
        const start = view.currentStart;
        const month = thaiMonths[start.getMonth()];
        const year = start.getFullYear() + 543;
        
        const titleEl = document.querySelector('.fc-toolbar-title');
        if (titleEl) {
            titleEl.textContent = `${month} ${year}`;
        }
    }
    
    // Show booking detail modal
    function showBookingDetail(event) {
        const props = event.extendedProps;
        const startTime = event.start.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
        const endTime = event.end.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
        const dateStr = event.start.toLocaleDateString('th-TH', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        
        const statusBadge = {
            'approved': '<span class="badge bg-success">อนุมัติแล้ว</span>',
            'pending': '<span class="badge bg-warning text-dark">รออนุมัติ</span>',
            'completed': '<span class="badge bg-secondary">เสร็จสิ้น</span>',
            'cancelled': '<span class="badge bg-danger">ยกเลิก</span>'
        };
        
        const content = `
            <div class="row">
                <div class="col-md-8">
                    <h4 class="mb-3">${event.title}</h4>
                    <table class="table table-borderless">
                        <tr>
                            <td width="140"><i class="bi bi-door-open me-2 text-primary"></i>ห้องประชุม:</td>
                            <td><strong>${props.roomName}</strong></td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-calendar3 me-2 text-primary"></i>วันที่:</td>
                            <td>${dateStr}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-clock me-2 text-primary"></i>เวลา:</td>
                            <td>${startTime} - ${endTime}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-check-circle me-2 text-primary"></i>สถานะ:</td>
                            <td>${statusBadge[props.status] || props.status}</td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-people me-2 text-primary"></i>ผู้เข้าร่วม:</td>
                            <td>${props.attendees} คน</td>
                        </tr>
                        ${props.description ? `
                        <tr>
                            <td><i class="bi bi-card-text me-2 text-primary"></i>รายละเอียด:</td>
                            <td>${props.description}</td>
                        </tr>
                        ` : ''}
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-person me-2"></i>ข้อมูลผู้จอง</h6>
                            <p class="mb-1"><strong>${props.userName}</strong></p>
                            <p class="mb-1 small text-muted">${props.department || '-'}</p>
                            ${props.userPhone ? `
                            <p class="mb-1">
                                <a href="tel:${props.userPhone}" class="text-decoration-none">
                                    <i class="bi bi-telephone me-1"></i>${props.userPhone}
                                </a>
                            </p>
                            ` : ''}
                            ${props.userEmail ? `
                            <p class="mb-0">
                                <a href="mailto:${props.userEmail}" class="text-decoration-none">
                                    <i class="bi bi-envelope me-1"></i>${props.userEmail}
                                </a>
                            </p>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="mt-3 d-grid gap-2">
                        ${props.userPhone ? `
                        <a href="tel:${props.userPhone}" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-telephone me-1"></i>โทรหาผู้จอง
                        </a>
                        ` : ''}
                        ${props.userEmail ? `
                        <a href="mailto:${props.userEmail}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-envelope me-1"></i>ส่งอีเมล
                        </a>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('bookingDetailContent').innerHTML = content;
        document.getElementById('viewBookingBtn').href = '<?= Url::to(['booking/view']) ?>?id=' + event.id;
        
        new bootstrap.Modal(document.getElementById('bookingDetailModal')).show();
    }
    
    // Update room status cards
    function updateRoomStatus() {
        const now = new Date();
        const currentTime = now.getHours() * 60 + now.getMinutes();
        
        fetch('<?= Url::to(['site/room-status']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
            },
            body: JSON.stringify({
                date: now.toISOString().split('T')[0],
                time: now.toTimeString().substring(0, 5)
            })
        })
        .then(response => response.json())
        .then(data => {
            document.querySelectorAll('.room-status-card').forEach(card => {
                const roomId = card.dataset.roomId;
                const statusEl = card.querySelector('.room-status');
                const roomData = data[roomId];
                
                if (roomData && roomData.in_use) {
                    card.classList.add('in-use');
                    statusEl.innerHTML = `
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>กำลังใช้งาน</span>
                        <small class="d-block text-muted mt-1">ว่าง ${roomData.available_at}</small>
                    `;
                } else {
                    card.classList.remove('in-use');
                    statusEl.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>ว่าง</span>';
                }
            });
        })
        .catch(error => console.error('Error updating room status:', error));
    }
    
    // View mode change
    document.querySelectorAll('input[name="viewMode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            calendar.changeView(this.value);
        });
    });
    
    // Building filter
    document.getElementById('buildingFilter').addEventListener('change', function() {
        const buildingId = this.value;
        document.querySelectorAll('.room-checkbox').forEach(checkbox => {
            if (!buildingId || checkbox.dataset.building === buildingId) {
                checkbox.checked = true;
            } else {
                checkbox.checked = false;
            }
        });
        calendar.refetchEvents();
    });
    
    // Room checkbox change
    document.querySelectorAll('.room-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
    
    // Status checkbox change
    document.querySelectorAll('.status-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
    
    // Select all rooms
    document.getElementById('selectAllRooms').addEventListener('click', function(e) {
        e.preventDefault();
        const allChecked = document.querySelectorAll('.room-checkbox:checked').length === 
                          document.querySelectorAll('.room-checkbox').length;
        document.querySelectorAll('.room-checkbox').forEach(cb => cb.checked = !allChecked);
        this.textContent = allChecked ? 'เลือกทั้งหมด' : 'ยกเลิกทั้งหมด';
        calendar.refetchEvents();
    });
    
    // Go to date
    document.getElementById('gotoDate').addEventListener('change', function() {
        calendar.gotoDate(this.value);
    });
    
    // Navigation buttons
    document.getElementById('btnPrev').addEventListener('click', function() {
        calendar.prev();
    });
    
    document.getElementById('btnNext').addEventListener('click', function() {
        calendar.next();
    });
    
    document.getElementById('btnToday').addEventListener('click', function() {
        calendar.today();
        document.getElementById('gotoDate').value = new Date().toISOString().split('T')[0];
    });
    
    // Update current time
    setInterval(function() {
        const now = new Date();
        document.getElementById('currentTime').textContent = 
            now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
    }, 60000);
    
    // Update room status every 5 minutes
    setInterval(updateRoomStatus, 300000);
    
    // Initial room status update
    updateRoomStatus();
});
</script>
