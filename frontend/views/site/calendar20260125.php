<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\MeetingRoom[] $rooms */
/** @var array $events */
/** @var array $holidayEvents */
/** @var array $holidayDates */
/** @var array $roomColors */

$this->title = 'ปฏิทินการจองห้องประชุม';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-calendar">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-calendar3 text-primary me-2"></i><?= Html::encode($this->title) ?>
                </h1>
                <p class="text-muted mb-0">ดูตารางการจองห้องประชุมทั้งหมด</p>
            </div>
            <div class="d-flex gap-2">
                <?= Html::a('<i class="bi bi-list-ul me-1"></i> ดูห้องประชุม', ['/room/index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?php if (!Yii::$app->user->isGuest): ?>
                    <?= Html::a('<i class="bi bi-plus-lg me-1"></i> จองห้องประชุม', ['/booking/create'], ['class' => 'btn btn-primary']) ?>
                <?php else: ?>
                    <?= Html::a('<i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบเพื่อจอง', ['/site/login'], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <!-- Room Filter -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>กรองห้องประชุม</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="room-filter-item active" data-room-id="">
                            <i class="bi bi-grid me-2 text-primary"></i>ทุกห้อง
                            <span class="badge bg-primary float-end"><?= count($rooms) ?></span>
                        </div>
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-filter-item" data-room-id="<?= $room->id ?>">
                                <span class="room-color-dot me-2" style="background-color: <?= $roomColors[$room->id] ?? '#3788d8' ?>;"></span>
                                <?= Html::encode($room->name_th) ?>
                                <small class="text-muted d-block ps-4">
                                    <i class="bi bi-building me-1"></i><?= Html::encode($room->building->name_th ?? '-') ?>
                                    • <i class="bi bi-people me-1"></i><?= $room->capacity ?> คน
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Legend - Status -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>คำอธิบายสี</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-2">สถานะการจอง</p>
                        <div class="d-flex align-items-center mb-2">
                            <span class="status-indicator status-approved me-2"></span>
                            <small>อนุมัติแล้ว</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="status-indicator status-pending me-2"></span>
                            <small>รออนุมัติ</small>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="status-indicator status-completed me-2"></span>
                            <small>เสร็จสิ้น</small>
                        </div>
                        
                        <hr class="my-2">
                        <p class="text-muted small mb-2">วันพิเศษ</p>
                        <div class="d-flex align-items-center">
                            <span class="status-indicator holiday-indicator me-2"></span>
                            <small>วันหยุด</small>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>สถิติ</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">การจองทั้งหมด</span>
                            <span class="fw-bold" id="stat-total">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">รออนุมัติ</span>
                            <span class="fw-bold text-warning" id="stat-pending">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">อนุมัติแล้ว</span>
                            <span class="fw-bold text-success" id="stat-approved">0</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">วันหยุด</span>
                            <span class="fw-bold text-danger" id="stat-holidays"><?= count($holidayDates ?? []) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendar -->
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-3">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="modal-title-header">
                    <i class="bi bi-calendar-event text-primary me-2"></i>รายละเอียดการจอง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Booking Details -->
                <div id="booking-details">
                    <div class="mb-3 p-3 bg-light rounded">
                        <h6 class="mb-1" id="event-title">-</h6>
                        <small class="text-muted" id="event-code">-</small>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 text-primary me-2">
                                    <i class="bi bi-door-open"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">ห้องประชุม</small>
                                    <span id="event-room">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-success bg-opacity-10 text-success me-2">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">เวลา</small>
                                    <span id="event-time">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-info bg-opacity-10 text-info me-2">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">ผู้จอง</small>
                                    <span id="event-user">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-warning bg-opacity-10 text-warning me-2">
                                    <i class="bi bi-flag"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block">สถานะ</small>
                                    <span id="event-status">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Holiday Details -->
                <div id="holiday-details" style="display: none;">
                    <div class="text-center py-4">
                        <div class="holiday-icon mb-3">
                            <i class="bi bi-calendar-heart text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-danger mb-2" id="holiday-name">-</h5>
                        <p class="text-muted mb-0" id="holiday-type-label">วันหยุดราชการ</p>
                        <p class="text-muted small mt-2" id="holiday-description"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Calendar Styles */
#calendar {
    min-height: 600px;
}
.fc {
    font-family: inherit;
}
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.8em;
    padding: 2px 4px;
    border-width: 0 0 0 3px !important;
}
.fc-daygrid-event {
    white-space: normal !important;
}
.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
}
.fc-button {
    text-transform: capitalize !important;
}
.fc-day-today {
    background-color: rgba(13, 110, 253, 0.08) !important;
}
.fc-daygrid-day-number {
    font-weight: 500;
    padding: 8px !important;
}
.fc-col-header-cell-cushion {
    font-weight: 600;
    padding: 10px !important;
}

/* Holiday Styles */
.fc-day-holiday {
    background-color: #fff5f5 !important;
}
.fc-day-holiday .fc-daygrid-day-number {
    color: #dc3545 !important;
    font-weight: 600 !important;
}
.holiday-event {
    opacity: 0.6;
}
.holiday-label {
    border-left: 3px solid #dc3545 !important;
    font-weight: 500;
}

/* Room Filter */
.room-filter-item {
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: all 0.2s;
}
.room-filter-item:last-child {
    border-bottom: none;
}
.room-filter-item:hover {
    background-color: #f8f9fa;
}
.room-filter-item.active {
    background-color: #e7f1ff;
    border-left: 3px solid #0d6efd;
}
.room-color-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

/* Status Indicators */
.status-indicator {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    display: inline-block;
}
.status-approved {
    background-color: #28a745;
}
.status-pending {
    background-color: #ffc107;
}
.status-completed {
    background-color: #17a2b8;
}
.holiday-indicator {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    border: 2px solid #dc3545;
}

/* Icon Box */
.icon-box {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Event Status Border */
.fc-event.status-pending {
    border-left-color: #ffc107 !important;
}
.fc-event.status-approved {
    border-left-color: #28a745 !important;
}
.fc-event.status-completed {
    border-left-color: #17a2b8 !important;
}

/* Responsive */
@media (max-width: 991.98px) {
    .room-filter-item small {
        display: none !important;
    }
}
</style>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<?php
$eventsJson = json_encode($events ?? []);
$holidayEventsJson = json_encode($holidayEvents ?? []);
$holidayDatesJson = json_encode($holidayDates ?? []);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }
    
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<div class="alert alert-danger text-center py-5"><i class="bi bi-exclamation-triangle fs-1 d-block mb-3"></i>ไม่สามารถโหลดปฏิทินได้ โปรดรีเฟรชหน้า</div>';
        console.error('FullCalendar not loaded');
        return;
    }
    
    var events = {$eventsJson};
    var holidayEvents = {$holidayEventsJson};
    var holidayDates = {$holidayDatesJson};
    var allEvents = events.slice();
    var currentRoomId = '';
    
    // Filter out holiday labels from booking events for counting
    var bookingEvents = events.filter(function(e) {
        return e.extendedProps && e.extendedProps.type === 'booking';
    });
    
    console.log('Loaded', bookingEvents.length, 'bookings and', Object.keys(holidayDates).length, 'holidays');
    
    // Thai months
    var thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                      'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'th',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์',
            list: 'รายการ'
        },
        dayHeaderFormat: { weekday: 'short' },
        eventSources: [
            { events: events },
            { events: holidayEvents }
        ],
        eventDisplay: 'block',
        
        titleFormat: function(date) {
            var d = date.date.marker;
            var month = thaiMonths[d.getMonth()];
            var year = d.getFullYear() + 543;
            return month + ' ' + year;
        },
        
        // Highlight holiday cells
        dayCellDidMount: function(info) {
            var dateStr = info.date.toISOString().split('T')[0];
            if (holidayDates[dateStr]) {
                info.el.classList.add('fc-day-holiday');
                info.el.title = holidayDates[dateStr].name;
            }
        },
        
        eventDidMount: function(info) {
            var props = info.event.extendedProps || {};
            
            // Add status class for booking events
            if (props.type === 'booking' && props.status) {
                info.el.classList.add('status-' + props.status);
            }
            
            // Add tooltip
            if (props.type === 'booking') {
                info.el.title = info.event.title + ' (' + (props.time || '') + ')';
            } else if (props.type === 'holiday') {
                info.el.title = info.event.title;
            }
        },
        
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            
            var event = info.event;
            var props = event.extendedProps || {};
            
            if (props.type === 'holiday') {
                // Show holiday modal
                document.getElementById('booking-details').style.display = 'none';
                document.getElementById('holiday-details').style.display = 'block';
                document.getElementById('modal-title-header').innerHTML = '<i class="bi bi-calendar-heart text-danger me-2"></i>วันหยุด';
                document.getElementById('holiday-name').textContent = event.title.replace('🔴 ', '');
                document.getElementById('holiday-type-label').textContent = getHolidayTypeLabel(props.holiday_type);
                document.getElementById('holiday-description').textContent = props.description || '';
            } else {
                // Show booking modal
                document.getElementById('booking-details').style.display = 'block';
                document.getElementById('holiday-details').style.display = 'none';
                document.getElementById('modal-title-header').innerHTML = '<i class="bi bi-calendar-event text-primary me-2"></i>รายละเอียดการจอง';
                document.getElementById('event-title').textContent = event.title || '-';
                document.getElementById('event-code').textContent = props.booking_code || '-';
                document.getElementById('event-room').textContent = props.room || '-';
                document.getElementById('event-time').textContent = props.time || '-';
                document.getElementById('event-user').textContent = props.user || '-';
                document.getElementById('event-status').innerHTML = getStatusBadge(props.status);
            }
            
            var modal = new bootstrap.Modal(document.getElementById('eventModal'));
            modal.show();
        }
    });
    
    calendar.render();
    console.log('Calendar rendered');
    
    // Update stats
    updateStats(bookingEvents);
    
    // Room filter
    document.querySelectorAll('.room-filter-item').forEach(function(item) {
        item.addEventListener('click', function() {
            document.querySelectorAll('.room-filter-item').forEach(function(i) {
                i.classList.remove('active');
            });
            this.classList.add('active');
            
            currentRoomId = this.dataset.roomId;
            filterEvents();
        });
    });
    
    function filterEvents() {
        var filteredBookings = allEvents.filter(function(event) {
            var props = event.extendedProps || {};
            if (props.type === 'holiday') return true; // Always show holidays
            if (!currentRoomId) return true;
            return props.room_id == currentRoomId;
        });
        
        calendar.removeAllEvents();
        calendar.addEventSource(filteredBookings);
        calendar.addEventSource(holidayEvents);
        
        // Update stats with filtered bookings
        var bookingsOnly = filteredBookings.filter(function(e) {
            return e.extendedProps && e.extendedProps.type === 'booking';
        });
        updateStats(bookingsOnly);
    }
    
    function updateStats(evts) {
        var total = evts.length;
        var pending = evts.filter(function(e) { return e.extendedProps && e.extendedProps.status === 'pending'; }).length;
        var approved = evts.filter(function(e) { return e.extendedProps && e.extendedProps.status === 'approved'; }).length;
        
        document.getElementById('stat-total').textContent = total;
        document.getElementById('stat-pending').textContent = pending;
        document.getElementById('stat-approved').textContent = approved;
    }
    
    function getStatusBadge(status) {
        var badges = {
            'pending': '<span class="badge bg-warning text-dark">รออนุมัติ</span>',
            'approved': '<span class="badge bg-success">อนุมัติแล้ว</span>',
            'completed': '<span class="badge bg-info">เสร็จสิ้น</span>',
            'rejected': '<span class="badge bg-danger">ไม่อนุมัติ</span>',
            'cancelled': '<span class="badge bg-secondary">ยกเลิก</span>'
        };
        return badges[status] || '<span class="badge bg-secondary">' + (status || '-') + '</span>';
    }
    
    function getHolidayTypeLabel(type) {
        var types = {
            'national': 'วันหยุดราชการ',
            'regional': 'วันหยุดภูมิภาค',
            'organization': 'วันหยุดองค์กร',
            'special': 'วันพิเศษ'
        };
        return types[type] || 'วันหยุด';
    }
});
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
