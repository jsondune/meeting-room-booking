<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $rooms */
/** @var array $events */
/** @var array $holidayEvents */
/** @var array $holidayDates */
/** @var array $roomColors */

$this->title = 'ปฏิทินการจอง';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Ensure arrays
$roomsArray = (is_array($rooms)) ? $rooms : [];
$roomColorsArray = (is_array($roomColors ?? null)) ? $roomColors : [];
$holidayDatesArray = (is_array($holidayDates ?? null)) ? $holidayDates : [];
?>

<div class="booking-calendar">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">ดูตารางการจองห้องประชุมทั้งหมด</p>
        </div>
        <div class="d-flex gap-2">
            <?= Html::a('<i class="bi bi-list-ul me-1"></i> มุมมองรายการ', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-plus-lg me-1"></i> จองห้องประชุม', ['create'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <!-- Filters Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>ตัวกรอง</h6>
                </div>
                <div class="card-body">
                    <!-- Room Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">ห้องประชุม</label>
                        <select id="room-filter" class="form-select form-select-sm">
                            <option value="">ทุกห้อง</option>
                            <?php foreach ($roomsArray as $roomId => $roomName): ?>
                                <option value="<?= Html::encode($roomId) ?>">
                                    <?= Html::encode($roomName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">สถานะ</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input status-filter" type="checkbox" value="pending" id="status-pending" checked>
                                <label class="form-check-label" for="status-pending">
                                    <span class="badge bg-warning me-1">&nbsp;</span> รอดำเนินการ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input status-filter" type="checkbox" value="approved" id="status-approved" checked>
                                <label class="form-check-label" for="status-approved">
                                    <span class="badge bg-success me-1">&nbsp;</span> อนุมัติแล้ว
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input status-filter" type="checkbox" value="completed" id="status-completed">
                                <label class="form-check-label" for="status-completed">
                                    <span class="badge bg-info me-1">&nbsp;</span> เสร็จสิ้น
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-palette me-2"></i>คำอธิบายสี (ตามห้อง)</h6>
                </div>
                <div class="card-body">
                    <div class="legend-items" style="max-height: 200px; overflow-y: auto;">
                        <?php foreach ($roomsArray as $roomId => $roomName): ?>
                            <div class="d-flex align-items-center mb-2">
                                <span class="me-2" style="width:14px;height:14px;border-radius:3px;background-color:<?= $roomColorsArray[$roomId] ?? '#3788d8' ?>;display:inline-block;"></span>
                                <small><?= Html::encode($roomName) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr class="my-2">
                    <p class="text-muted small mb-2">วันพิเศษ</p>
                    <div class="d-flex align-items-center">
                        <span class="me-2" style="width:14px;height:14px;border-radius:3px;background:linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);border:1px solid #dc3545;display:inline-block;"></span>
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
                        <span class="text-muted">จองทั้งหมด</span>
                        <span class="fw-medium" id="stat-total">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">รออนุมัติ</span>
                        <span class="fw-medium text-warning" id="stat-pending">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">อนุมัติแล้ว</span>
                        <span class="fw-medium text-success" id="stat-approved">0</span>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">วันหยุด</span>
                        <span class="fw-medium text-danger" id="stat-holidays"><?= count($holidayDatesArray) ?></span>
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

<!-- Booking Detail Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-header-title">
                    <i class="bi bi-calendar-event text-primary me-2"></i>รายละเอียดการจอง
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Booking Details -->
                <div id="booking-details">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>รหัสการจอง:</strong> <span id="modal-code">-</span></p>
                            <p><strong>หัวข้อ:</strong> <span id="modal-title">-</span></p>
                            <p><strong>ห้องประชุม:</strong> <span id="modal-room">-</span></p>
                            <p><strong>ผู้จอง:</strong> <span id="modal-user">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>วันที่:</strong> <span id="modal-date">-</span></p>
                            <p><strong>เวลา:</strong> <span id="modal-time">-</span></p>
                            <p><strong>สถานะ:</strong> <span id="modal-status">-</span></p>
                        </div>
                    </div>
                </div>
                
                <!-- Holiday Details -->
                <div id="holiday-details" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-calendar-heart text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-danger mb-2" id="holiday-name">-</h5>
                        <p class="text-muted mb-0" id="holiday-type-label">วันหยุดราชการ</p>
                        <p class="text-muted small mt-2" id="holiday-description"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modal-actions">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <a href="#" id="modal-view-btn" class="btn btn-primary">ดูรายละเอียด</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Create Modal -->
<div class="modal fade" id="quickCreateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">จองห้องประชุมด่วน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quick-create-form">
                    <div class="mb-3">
                        <label class="form-label">ห้องประชุม <span class="text-danger">*</span></label>
                        <select id="quick-room" class="form-select" required>
                            <option value="">-- เลือกห้องประชุม --</option>
                            <?php foreach ($roomsArray as $roomId => $roomName): ?>
                                <option value="<?= Html::encode($roomId) ?>">
                                    <?= Html::encode($roomName) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input type="date" id="quick-date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">จำนวนผู้เข้าร่วม</label>
                            <input type="number" id="quick-attendees" class="form-control" min="1" value="5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เวลาเริ่ม <span class="text-danger">*</span></label>
                            <input type="time" id="quick-start" class="form-control" required value="09:00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                            <input type="time" id="quick-end" class="form-control" required value="12:00">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="quick-submit">
                    <i class="bi bi-check-lg me-1"></i>จองห้องประชุม
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#calendar {
    min-height: 600px;
}
.fc {
    font-family: inherit;
}
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.85em;
    padding: 2px 4px;
}
.fc-daygrid-event {
    white-space: normal !important;
}
.fc-toolbar-title {
    font-size: 1.25rem !important;
}
.fc-button {
    text-transform: capitalize !important;
}
.fc-day-today {
    background-color: rgba(13, 110, 253, 0.1) !important;
}
.fc-daygrid-day-number {
    font-weight: 500;
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

/* Status border indicator */
.fc-event.status-pending {
    border-left: 3px solid #ffc107 !important;
}
.fc-event.status-approved {
    border-left: 3px solid #28a745 !important;
}
.fc-event.status-completed {
    border-left: 3px solid #17a2b8 !important;
}
</style>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<?php
$eventsJson = json_encode($events ?? []);
$holidayEventsJson = json_encode($holidayEvents ?? []);
$holidayDatesJson = json_encode($holidayDates ?? []);
$csrfToken = Yii::$app->request->csrfToken;
$createUrl = Url::to(['create']);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }
    
    if (typeof FullCalendar === 'undefined') {
        calendarEl.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>ไม่สามารถโหลด FullCalendar ได้ กรุณารีเฟรชหน้า</div>';
        return;
    }
    
    var events = {$eventsJson};
    var holidayEvents = {$holidayEventsJson};
    var holidayDates = {$holidayDatesJson};
    var allEvents = events.slice();
    var currentRoomId = '';
    var checkedStatuses = ['pending', 'approved'];
    
    // Filter booking events only
    var bookingEvents = events.filter(function(e) {
        return e.extendedProps && e.extendedProps.type === 'booking';
    });
    
    console.log('Loaded', bookingEvents.length, 'bookings and', Object.keys(holidayDates).length, 'holidays');
    
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
        
        dayCellDidMount: function(info) {
            var dateStr = info.date.toISOString().split('T')[0];
            if (holidayDates[dateStr]) {
                info.el.classList.add('fc-day-holiday');
                info.el.title = holidayDates[dateStr].name;
            }
        },
        
        eventDidMount: function(info) {
            var props = info.event.extendedProps || {};
            if (props.type === 'booking' && props.status) {
                info.el.classList.add('status-' + props.status);
            }
            if (props.type === 'booking') {
                info.el.title = info.event.title + ' (' + (props.time || '') + ')';
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
                document.getElementById('modal-actions').querySelector('#modal-view-btn').style.display = 'none';
                document.getElementById('modal-header-title').innerHTML = '<i class="bi bi-calendar-heart text-danger me-2"></i>วันหยุด';
                document.getElementById('holiday-name').textContent = event.title.replace('🔴 ', '');
                document.getElementById('holiday-type-label').textContent = getHolidayTypeLabel(props.holiday_type);
                document.getElementById('holiday-description').textContent = props.description || '';
            } else {
                // Show booking modal
                document.getElementById('booking-details').style.display = 'block';
                document.getElementById('holiday-details').style.display = 'none';
                document.getElementById('modal-actions').querySelector('#modal-view-btn').style.display = 'inline-block';
                document.getElementById('modal-header-title').innerHTML = '<i class="bi bi-calendar-event text-primary me-2"></i>รายละเอียดการจอง';
                
                document.getElementById('modal-code').textContent = props.booking_code || '-';
                document.getElementById('modal-title').textContent = event.title || '-';
                document.getElementById('modal-room').textContent = props.room || '-';
                document.getElementById('modal-user').textContent = props.user || '-';
                
                var dateStr = '-';
                if (event.start) {
                    var d = event.start;
                    dateStr = d.getDate() + ' ' + thaiMonths[d.getMonth()] + ' ' + (d.getFullYear() + 543);
                }
                document.getElementById('modal-date').textContent = dateStr;
                document.getElementById('modal-time').textContent = props.time || '-';
                document.getElementById('modal-status').innerHTML = getStatusBadge(props.status);
                document.getElementById('modal-view-btn').href = props.viewUrl || '#';
            }
            
            var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
            modal.show();
        },
        
        dateClick: function(info) {
            document.getElementById('quick-date').value = info.dateStr;
            var modal = new bootstrap.Modal(document.getElementById('quickCreateModal'));
            modal.show();
        }
    });
    
    calendar.render();
    updateStats(bookingEvents);
    
    // Room filter
    document.getElementById('room-filter').addEventListener('change', function() {
        currentRoomId = this.value;
        filterEvents();
    });
    
    // Status filter
    document.querySelectorAll('.status-filter').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            checkedStatuses = [];
            document.querySelectorAll('.status-filter:checked').forEach(function(cb) {
                checkedStatuses.push(cb.value);
            });
            filterEvents();
        });
    });
    
    function filterEvents() {
        var filtered = allEvents.filter(function(event) {
            var props = event.extendedProps || {};
            if (props.type === 'holiday') return true;
            var roomMatch = !currentRoomId || props.room_id == currentRoomId;
            var statusMatch = checkedStatuses.includes(props.status);
            return roomMatch && statusMatch;
        });
        
        calendar.removeAllEvents();
        calendar.addEventSource(filtered);
        calendar.addEventSource(holidayEvents);
        
        var bookingsOnly = filtered.filter(function(e) {
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
            'rejected': '<span class="badge bg-danger">ไม่อนุมัติ</span>',
            'cancelled': '<span class="badge bg-secondary">ยกเลิก</span>',
            'completed': '<span class="badge bg-info">เสร็จสิ้น</span>'
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
    
    // Quick create submit
    document.getElementById('quick-submit').addEventListener('click', function() {
        var roomId = document.getElementById('quick-room').value;
        var date = document.getElementById('quick-date').value;
        var start = document.getElementById('quick-start').value;
        var end = document.getElementById('quick-end').value;
        
        if (!roomId || !date || !start || !end) {
            alert('กรุณากรอกข้อมูลให้ครบถ้วน');
            return;
        }
        
        window.location.href = '{$createUrl}?room_id=' + roomId + '&date=' + date + '&start=' + start + '&end=' + end;
    });
});
JS;

$this->registerJs($js, \yii\web\View::POS_END);
?>
