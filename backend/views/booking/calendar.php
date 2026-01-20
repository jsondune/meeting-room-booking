<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var array $rooms */
/** @var array $events */

$this->title = 'ปฏิทินการจอง';
$this->params['breadcrumbs'][] = ['label' => 'การจองห้องประชุม', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Register FullCalendar JS
$this->registerJsFile('https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js', [
    'position' => \yii\web\View::POS_HEAD
]);

// Ensure rooms is an array
$roomsArray = (is_array($rooms)) ? $rooms : [];
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
                            <?php foreach ($roomsArray as $roomId => $roomName): ?>
                                <option value="<?= Html::encode($roomId) ?>">
                                    <?= Html::encode($roomName) ?>
                                </option>
                            <?php endforeach; ?>
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
                            foreach ($roomsArray as $roomId => $roomName):
                                $color = $colorArray[$index % count($colorArray)];
                            ?>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="me-2" style="width:16px;height:16px;border-radius:4px;background-color:<?= $color ?>;display:inline-block;"></span>
                                    <small><?= Html::encode($roomName) ?></small>
                                </div>
                            <?php
                                $index++;
                            endforeach;
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
                        <span class="text-muted">รออนุมัติ</span>
                        <span class="fw-medium text-warning" id="stat-pending">0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">อนุมัติแล้ว</span>
                        <span class="fw-medium text-success" id="stat-approved">0</span>
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
                                <option value="<?= Html::encode($roomId) ?>"><?= Html::encode($roomName) ?></option>
                            <?php endforeach; ?>
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
                            <input type="number" id="quick-attendees" class="form-control" min="1" value="5">
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
                <button type="button" class="btn btn-primary" id="quick-submit">
                    <i class="bi bi-check-lg me-1"></i>จองห้องประชุม
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    font-size: 0.85em;
}
.fc-daygrid-event {
    white-space: normal !important;
}
.fc-toolbar-title {
    font-size: 1.25rem !important;
}
</style>

<?php
$eventsJson = json_encode($events ?? []);
$csrfToken = Yii::$app->request->csrfToken;
$createUrl = Url::to(['create']);

$js = <<<JS
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    
    var events = {$eventsJson};
    var currentRoomId = '';
    var checkedStatuses = ['pending', 'approved'];
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'th',
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
        // Thai day header
        dayHeaderFormat: { weekday: 'short' },
        events: events,
        
        // Update title to Buddhist Era
        datesSet: function(info) {
            var titleEl = calendarEl.querySelector('.fc-toolbar-title');
            if (titleEl && typeof ThaiDate !== 'undefined') {
                var viewDate = info.view.currentStart;
                var month = ThaiDate.months[viewDate.getMonth()];
                var year = ThaiDate.toBuddhistYear(viewDate.getFullYear());
                titleEl.textContent = month + ' ' + year;
            }
        },
        
        eventClick: function(info) {
            var event = info.event;
            var props = event.extendedProps || {};
            
            document.getElementById('modal-code').textContent = props.booking_code || '-';
            document.getElementById('modal-title').textContent = event.title || '-';
            document.getElementById('modal-room').textContent = props.room || '-';
            document.getElementById('modal-user').textContent = props.user || '-';
            // Format date as Thai Buddhist Era
            var dateStr = '-';
            if (event.start) {
                if (typeof ThaiDate !== 'undefined') {
                    dateStr = ThaiDate.format(event.start, 'long');
                } else {
                    dateStr = event.start.toLocaleDateString('th-TH');
                }
            }
            document.getElementById('modal-date').textContent = dateStr;
            document.getElementById('modal-time').textContent = props.time || '-';
            document.getElementById('modal-status').innerHTML = getStatusBadge(props.status);
            document.getElementById('modal-view-btn').href = props.viewUrl || '#';
            
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
    
    // Update stats
    var totalEvents = events.length;
    var pendingEvents = events.filter(function(e) { return e.extendedProps && e.extendedProps.status === 'pending'; }).length;
    var approvedEvents = events.filter(function(e) { return e.extendedProps && e.extendedProps.status === 'approved'; }).length;
    
    document.getElementById('stat-total').textContent = totalEvents;
    document.getElementById('stat-pending').textContent = pendingEvents;
    document.getElementById('stat-approved').textContent = approvedEvents;
    
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
        var filtered = events.filter(function(event) {
            var props = event.extendedProps || {};
            var roomMatch = !currentRoomId || props.room_id == currentRoomId;
            var statusMatch = checkedStatuses.includes(props.status);
            return roomMatch && statusMatch;
        });
        
        calendar.removeAllEvents();
        calendar.addEventSource(filtered);
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
    
    // Quick create submit
    document.getElementById('quick-submit').addEventListener('click', function() {
        window.location.href = '{$createUrl}' + 
            '?room_id=' + document.getElementById('quick-room').value +
            '&date=' + document.getElementById('quick-date').value +
            '&start=' + document.getElementById('quick-start').value +
            '&end=' + document.getElementById('quick-end').value;
    });
});
JS;

$this->registerJs($js);
?>
