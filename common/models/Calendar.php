<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $rooms */
/** @var array $events */

$this->title = 'ปฏิทินการจอง';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register FullCalendar CSS and JS
$this->registerCssFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', ['position' => \yii\web\View::POS_HEAD]);
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>ตัวกรอง</h6>
                </div>
                <div class="card-body">
                    <!-- Room Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-medium">ห้องประชุม</label>
                        <select id="room-filter" class="form-select form-select-sm">
                            <option value="">ทุกห้อง</option>
                            <?php if (is_array($rooms)): ?>
                                <?php foreach ($rooms as $roomId => $roomName): ?>
                                    <option value="<?= $roomId ?>">
                                        <?= Html::encode($roomName) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div class="mb-4">
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
                                <input class="form-check-input status-filter" type="checkbox" value="rejected" id="status-rejected">
                                <label class="form-check-label" for="status-rejected">
                                    <span class="badge bg-danger me-1">&nbsp;</span> ถูกปฏิเสธ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input status-filter" type="checkbox" value="cancelled" id="status-cancelled">
                                <label class="form-check-label" for="status-cancelled">
                                    <span class="badge bg-secondary me-1">&nbsp;</span> ยกเลิก
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

                    <!-- Legend -->
                    <div class="mb-3">
                        <label class="form-label fw-medium">คำอธิบายสี</label>
                        <div class="legend-items">
                            <?php 
                            $colorArray = ['#3788d8', '#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997'];
                            $index = 0;
                            if (is_array($rooms)):
                                foreach ($rooms as $roomId => $roomName): 
                                    $color = $colorArray[$index % count($colorArray)];
                            ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="legend-color me-2" style="background-color: <?= $color ?>;"></span>
                                    <small><?= Html::encode(is_string($roomName) ? $roomName : ($roomName['name_th'] ?? '')) ?></small>
                                </div>
                            <?php 
                                    $index++;
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>สถิติเดือนนี้</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">จองทั้งหมด</span>
                        <span class="fw-medium" id="stat-total">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">อนุมัติแล้ว</span>
                        <span class="fw-medium text-success" id="stat-approved">0</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">รอดำเนินการ</span>
                        <span class="fw-medium text-warning" id="stat-pending">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">ยกเลิก/ปฏิเสธ</span>
                        <span class="fw-medium text-danger" id="stat-cancelled">0</span>
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
                <h5 class="modal-title">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4 id="modal-title" class="mb-3"></h4>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">รหัสการจอง</td>
                                <td id="modal-code" class="fw-medium"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ห้องประชุม</td>
                                <td id="modal-room"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">วันที่</td>
                                <td id="modal-date"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">เวลา</td>
                                <td id="modal-time"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">ผู้จอง</td>
                                <td id="modal-user"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">จำนวนผู้เข้าร่วม</td>
                                <td id="modal-attendees"></td>
                            </tr>
                            <tr>
                                <td class="text-muted">วัตถุประสงค์</td>
                                <td id="modal-purpose"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-3">
                            <span id="modal-status" class="badge fs-6 px-3 py-2"></span>
                        </div>
                        <div class="card bg-light border-0">
                            <div class="card-body text-center">
                                <small class="text-muted d-block mb-1">สร้างเมื่อ</small>
                                <span id="modal-created"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div id="modal-actions">
                    <!-- Dynamic buttons based on status -->
                </div>
                <a id="modal-view-link" href="#" class="btn btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> ดูรายละเอียด
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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
                            <?php if (is_array($rooms)): ?>
                                <?php foreach ($rooms as $roomId => $roomName): ?>
                                    <option value="<?= $roomId ?>"><?= Html::encode($roomName) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หัวข้อการประชุม <span class="text-danger">*</span></label>
                        <input type="text" id="quick-title" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่ <span class="text-danger">*</span></label>
                            <input type="date" id="quick-date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">จำนวนผู้เข้าร่วม</label>
                            <input type="number" id="quick-attendees" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เวลาเริ่ม <span class="text-danger">*</span></label>
                            <input type="time" id="quick-start" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เวลาสิ้นสุด <span class="text-danger">*</span></label>
                            <input type="time" id="quick-end" class="form-control" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" id="quick-create-submit">
                    <i class="bi bi-check-lg me-1"></i> สร้างการจอง
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
    display: inline-block;
}

#calendar {
    min-height: 700px;
}

.fc {
    font-family: inherit;
}

.fc .fc-toolbar-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.fc .fc-button {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
}

.fc .fc-button-primary {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: var(--bs-primary);
    border-color: var(--bs-primary);
}

.fc .fc-daygrid-day-number {
    padding: 8px;
    font-weight: 500;
}

.fc .fc-daygrid-day.fc-day-today {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.fc-event {
    cursor: pointer;
    border-radius: 4px;
    padding: 2px 6px;
    font-size: 0.8rem;
}

.fc-event-pending {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000 !important;
}

.fc-event-approved {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
}

.fc-event-rejected {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
}

.fc-event-cancelled {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

.fc-event-completed {
    background-color: #17a2b8 !important;
    border-color: #17a2b8 !important;
}

.fc .fc-timegrid-slot {
    height: 40px;
}

.fc .fc-col-header-cell-cushion {
    padding: 10px 4px;
    font-weight: 600;
}

@media (max-width: 767.98px) {
    .fc .fc-toolbar {
        flex-direction: column;
        gap: 10px;
    }
    
    .fc .fc-toolbar-chunk {
        display: flex;
        justify-content: center;
    }
}
</style>

<?php
$eventsJson = json_encode($events ?? []);
$csrfToken = Yii::$app->request->csrfToken;
$approveUrl = Url::to(['approve']);
$rejectUrl = Url::to(['reject']);
$createUrl = Url::to(['create']);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var allEvents = {$eventsJson};
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'th',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์',
            day: 'วัน',
            list: 'รายการ'
        },
        events: allEvents,
        eventClick: function(info) {
            showBookingModal(info.event);
        },
        dateClick: function(info) {
            // Open quick create modal
            document.getElementById('quick-date').value = info.dateStr;
            document.getElementById('quick-start').value = '09:00';
            document.getElementById('quick-end').value = '10:00';
            var quickModal = new bootstrap.Modal(document.getElementById('quickCreateModal'));
            quickModal.show();
        },
        eventDidMount: function(info) {
            // Add status class
            var status = info.event.extendedProps.status;
            info.el.classList.add('fc-event-' + status);
            
            // Add tooltip
            info.el.setAttribute('title', info.event.title + ' (' + getStatusText(status) + ')');
        },
        height: 'auto',
        navLinks: true,
        editable: false,
        selectable: true,
        selectMirror: true,
        dayMaxEvents: 3,
        weekends: true,
        nowIndicator: true,
        slotMinTime: '07:00:00',
        slotMaxTime: '20:00:00'
    });
    
    calendar.render();
    
    // Update stats
    updateStats(allEvents);
    
    // Filter by room
    document.getElementById('room-filter').addEventListener('change', function() {
        filterEvents();
    });
    
    // Filter by status
    document.querySelectorAll('.status-filter').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            filterEvents();
        });
    });
    
    function filterEvents() {
        var roomId = document.getElementById('room-filter').value;
        var checkedStatuses = [];
        document.querySelectorAll('.status-filter:checked').forEach(function(cb) {
            checkedStatuses.push(cb.value);
        });
        
        var filteredEvents = allEvents.filter(function(event) {
            var roomMatch = !roomId || event.room_id == roomId;
            var statusMatch = checkedStatuses.includes(event.status);
            return roomMatch && statusMatch;
        });
        
        calendar.removeAllEvents();
        calendar.addEventSource(filteredEvents);
        updateStats(filteredEvents);
    }
    
    function updateStats(events) {
        var total = events.length;
        var approved = events.filter(e => e.status === 'approved').length;
        var pending = events.filter(e => e.status === 'pending').length;
        var cancelled = events.filter(e => e.status === 'cancelled' || e.status === 'rejected').length;
        
        document.getElementById('stat-total').textContent = total;
        document.getElementById('stat-approved').textContent = approved;
        document.getElementById('stat-pending').textContent = pending;
        document.getElementById('stat-cancelled').textContent = cancelled;
    }
    
    function getStatusText(status) {
        var statusMap = {
            'pending': 'รอดำเนินการ',
            'approved': 'อนุมัติแล้ว',
            'rejected': 'ถูกปฏิเสธ',
            'cancelled': 'ยกเลิก',
            'completed': 'เสร็จสิ้น'
        };
        return statusMap[status] || status;
    }
    
    function getStatusClass(status) {
        var classMap = {
            'pending': 'bg-warning text-dark',
            'approved': 'bg-success',
            'rejected': 'bg-danger',
            'cancelled': 'bg-secondary',
            'completed': 'bg-info'
        };
        return classMap[status] || 'bg-secondary';
    }
    
    function showBookingModal(event) {
        var props = event.extendedProps;
        
        document.getElementById('modal-title').textContent = event.title;
        document.getElementById('modal-code').textContent = props.booking_code || '-';
        document.getElementById('modal-room').textContent = props.room_name || '-';
        document.getElementById('modal-date').textContent = props.booking_date || '-';
        document.getElementById('modal-time').textContent = props.time_range || '-';
        document.getElementById('modal-user').textContent = props.user_name || '-';
        document.getElementById('modal-attendees').textContent = (props.attendees_count || 0) + ' คน';
        document.getElementById('modal-purpose').textContent = props.purpose || '-';
        document.getElementById('modal-created').textContent = props.created_at || '-';
        
        var statusEl = document.getElementById('modal-status');
        statusEl.textContent = getStatusText(props.status);
        statusEl.className = 'badge fs-6 px-3 py-2 ' + getStatusClass(props.status);
        
        document.getElementById('modal-view-link').href = 'view?id=' + event.id;
        
        // Action buttons based on status
        var actionsEl = document.getElementById('modal-actions');
        actionsEl.innerHTML = '';
        
        if (props.status === 'pending') {
            actionsEl.innerHTML = '<button class="btn btn-success me-2" onclick="approveBooking(' + event.id + ')"><i class="bi bi-check-lg me-1"></i> อนุมัติ</button>' +
                '<button class="btn btn-danger me-2" onclick="rejectBooking(' + event.id + ')"><i class="bi bi-x-lg me-1"></i> ปฏิเสธ</button>';
        } else if (props.status === 'approved') {
            actionsEl.innerHTML = '<button class="btn btn-secondary me-2" onclick="cancelBooking(' + event.id + ')"><i class="bi bi-x-circle me-1"></i> ยกเลิก</button>';
        }
        
        var modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        modal.show();
    }
    
    // Quick create submit
    document.getElementById('quick-create-submit').addEventListener('click', function() {
        var form = document.getElementById('quick-create-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Redirect to create page with pre-filled data
        var params = new URLSearchParams({
            room_id: document.getElementById('quick-room').value,
            title: document.getElementById('quick-title').value,
            booking_date: document.getElementById('quick-date').value,
            start_time: document.getElementById('quick-start').value,
            end_time: document.getElementById('quick-end').value,
            attendees_count: document.getElementById('quick-attendees').value
        });
        
        window.location.href = '{$createUrl}?' + params.toString();
    });
});

function approveBooking(id) {
    if (confirm('ยืนยันการอนุมัติการจองนี้?')) {
        window.location.href = '{$approveUrl}?id=' + id;
    }
}

function rejectBooking(id) {
    var reason = prompt('โปรดระบุเหตุผลในการปฏิเสธ:');
    if (reason !== null) {
        window.location.href = '{$rejectUrl}?id=' + id + '&reason=' + encodeURIComponent(reason);
    }
}

function cancelBooking(id) {
    var reason = prompt('โปรดระบุเหตุผลในการยกเลิก:');
    if (reason !== null) {
        window.location.href = 'cancel?id=' + id + '&reason=' + encodeURIComponent(reason);
    }
}
JS;

$this->registerJs($js);
?>
