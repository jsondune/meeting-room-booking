<?php
/**
 * CalendarController - API controller for calendar views
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace api\controllers\v1;

use Yii;
use common\models\Booking;
use common\models\MeetingRoom;
use common\models\Building;
use common\models\Holiday;

/**
 * CalendarController provides RESTful API for calendar data
 */
class CalendarController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Allow some calendar views without authentication
        $behaviors['authenticator']['optional'] = ['events', 'holidays', 'room-schedule'];
        
        return $behaviors;
    }

    /**
     * GET /api/v1/calendar/events
     * Get calendar events for a date range
     * 
     * @return array
     */
    public function actionEvents()
    {
        $start = Yii::$app->request->get('start', date('Y-m-01'));
        $end = Yii::$app->request->get('end', date('Y-m-t'));
        $roomId = Yii::$app->request->get('room_id');
        $buildingId = Yii::$app->request->get('building_id');
        $departmentId = Yii::$app->request->get('department_id');
        $status = Yii::$app->request->get('status');
        $userId = Yii::$app->request->get('user_id');

        $query = Booking::find()
            ->where(['>=', 'booking_date', $start])
            ->andWhere(['<=', 'booking_date', $end])
            ->with(['room', 'user']);

        // Filter by room
        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }

        // Filter by building
        if ($buildingId) {
            $query->innerJoin(['r' => MeetingRoom::tableName()], 'r.id = room_id')
                  ->andWhere(['r.building_id' => $buildingId]);
        }

        // Filter by department
        if ($departmentId) {
            $query->andWhere(['department_id' => $departmentId]);
        }

        // Filter by status
        if ($status) {
            $statusList = is_array($status) ? $status : explode(',', $status);
            $query->andWhere(['in', 'status', $statusList]);
        } else {
            // Default: show approved and pending
            $query->andWhere(['in', 'status', ['approved', 'pending', 'completed']]);
        }

        // Filter by user (for personal calendar)
        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }

        $bookings = $query->all();

        // Format as calendar events
        $events = [];
        foreach ($bookings as $booking) {
            $events[] = $this->formatCalendarEvent($booking);
        }

        // Add holidays if requested
        $includeHolidays = Yii::$app->request->get('include_holidays', true);
        if ($includeHolidays) {
            $holidays = $this->getHolidayEvents($start, $end);
            $events = array_merge($events, $holidays);
        }

        return $this->success([
            'events' => $events,
            'range' => [
                'start' => $start,
                'end' => $end,
            ],
        ]);
    }

    /**
     * GET /api/v1/calendar/my-events
     * Get current user's calendar events
     * 
     * @return array
     */
    public function actionMyEvents()
    {
        $start = Yii::$app->request->get('start', date('Y-m-01'));
        $end = Yii::$app->request->get('end', date('Y-m-t'));
        $status = Yii::$app->request->get('status');

        $query = Booking::find()
            ->where(['user_id' => $this->getUserId()])
            ->andWhere(['>=', 'booking_date', $start])
            ->andWhere(['<=', 'booking_date', $end])
            ->with(['room']);

        if ($status) {
            $statusList = is_array($status) ? $status : explode(',', $status);
            $query->andWhere(['in', 'status', $statusList]);
        } else {
            $query->andWhere(['in', 'status', ['approved', 'pending', 'completed']]);
        }

        $bookings = $query->all();

        $events = [];
        foreach ($bookings as $booking) {
            $events[] = $this->formatCalendarEvent($booking, true);
        }

        // Add events user is attending
        $attendingEvents = $this->getAttendingEvents($start, $end);
        $events = array_merge($events, $attendingEvents);

        // Add holidays
        $holidays = $this->getHolidayEvents($start, $end);
        $events = array_merge($events, $holidays);

        return $this->success([
            'events' => $events,
            'range' => [
                'start' => $start,
                'end' => $end,
            ],
        ]);
    }

    /**
     * GET /api/v1/calendar/room-schedule/{id}
     * Get room schedule for a specific period
     * 
     * @param int $id Room ID
     * @return array
     */
    public function actionRoomSchedule($id)
    {
        $room = MeetingRoom::findOne($id);
        if (!$room) {
            return $this->error('Room not found', 404);
        }

        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $view = Yii::$app->request->get('view', 'day'); // day, week, month

        switch ($view) {
            case 'week':
                $start = date('Y-m-d', strtotime('monday this week', strtotime($date)));
                $end = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
                break;
            case 'month':
                $start = date('Y-m-01', strtotime($date));
                $end = date('Y-m-t', strtotime($date));
                break;
            default: // day
                $start = $date;
                $end = $date;
        }

        $bookings = Booking::find()
            ->where(['room_id' => $id])
            ->andWhere(['>=', 'booking_date', $start])
            ->andWhere(['<=', 'booking_date', $end])
            ->andWhere(['in', 'status', ['approved', 'pending']])
            ->with(['user'])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        $events = [];
        foreach ($bookings as $booking) {
            $events[] = $this->formatCalendarEvent($booking);
        }

        // Generate time slots for day view
        $timeSlots = [];
        if ($view === 'day') {
            $timeSlots = $this->generateTimeSlots($room, $date, $bookings);
        }

        return $this->success([
            'room' => [
                'id' => $room->id,
                'name_th' => $room->name_th,
                'name_en' => $room->name_en,
                'capacity' => $room->capacity,
                'operating_hours' => [
                    'start' => $room->operating_hours_start,
                    'end' => $room->operating_hours_end,
                ],
            ],
            'view' => $view,
            'range' => [
                'start' => $start,
                'end' => $end,
            ],
            'events' => $events,
            'time_slots' => $timeSlots,
        ]);
    }

    /**
     * GET /api/v1/calendar/day/{date}
     * Get all bookings for a specific date
     * 
     * @param string $date
     * @return array
     */
    public function actionDay($date)
    {
        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $this->error('Invalid date format. Use YYYY-MM-DD', 400);
        }

        $buildingId = Yii::$app->request->get('building_id');
        $roomId = Yii::$app->request->get('room_id');

        $query = Booking::find()
            ->where(['booking_date' => $date])
            ->andWhere(['in', 'status', ['approved', 'pending']])
            ->with(['room', 'room.building', 'user']);

        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        } elseif ($buildingId) {
            $query->innerJoin(['r' => MeetingRoom::tableName()], 'r.id = room_id')
                  ->andWhere(['r.building_id' => $buildingId]);
        }

        $bookings = $query->orderBy(['start_time' => SORT_ASC])->all();

        // Group by room
        $byRoom = [];
        foreach ($bookings as $booking) {
            $roomId = $booking->room_id;
            if (!isset($byRoom[$roomId])) {
                $byRoom[$roomId] = [
                    'room' => [
                        'id' => $booking->room->id,
                        'name_th' => $booking->room->name_th,
                        'name_en' => $booking->room->name_en,
                        'capacity' => $booking->room->capacity,
                        'building' => $booking->room->building ? $booking->room->building->name_th : null,
                    ],
                    'bookings' => [],
                ];
            }
            $byRoom[$roomId]['bookings'][] = $this->formatDayEvent($booking);
        }

        // Check if date is holiday
        $holiday = Holiday::find()
            ->where(['<=', 'start_date', $date])
            ->andWhere(['>=', 'end_date', $date])
            ->one();

        return $this->success([
            'date' => $date,
            'day_name' => $this->getThaiDayName($date),
            'is_holiday' => $holiday !== null,
            'holiday' => $holiday ? [
                'name_th' => $holiday->name_th,
                'name_en' => $holiday->name_en,
            ] : null,
            'total_bookings' => count($bookings),
            'rooms' => array_values($byRoom),
        ]);
    }

    /**
     * GET /api/v1/calendar/week/{date}
     * Get week view data
     * 
     * @param string $date Any date in the week
     * @return array
     */
    public function actionWeek($date)
    {
        $mondayDate = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $sundayDate = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

        $buildingId = Yii::$app->request->get('building_id');

        // Get all rooms (optionally filtered by building)
        $roomQuery = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->orderBy(['building_id' => SORT_ASC, 'floor' => SORT_ASC, 'room_number' => SORT_ASC]);

        if ($buildingId) {
            $roomQuery->andWhere(['building_id' => $buildingId]);
        }

        $rooms = $roomQuery->limit(20)->all(); // Limit for performance
        $roomIds = array_map(function($r) { return $r->id; }, $rooms);

        // Get bookings for the week
        $bookings = Booking::find()
            ->where(['room_id' => $roomIds])
            ->andWhere(['>=', 'booking_date', $mondayDate])
            ->andWhere(['<=', 'booking_date', $sundayDate])
            ->andWhere(['in', 'status', ['approved', 'pending']])
            ->with(['user'])
            ->all();

        // Index bookings by room and date
        $bookingIndex = [];
        foreach ($bookings as $booking) {
            $key = $booking->room_id . '_' . $booking->booking_date;
            if (!isset($bookingIndex[$key])) {
                $bookingIndex[$key] = [];
            }
            $bookingIndex[$key][] = $booking;
        }

        // Get holidays for the week
        $holidays = Holiday::find()
            ->where(['<=', 'start_date', $sundayDate])
            ->andWhere(['>=', 'end_date', $mondayDate])
            ->all();

        $holidayDates = [];
        foreach ($holidays as $holiday) {
            $current = strtotime($holiday->start_date);
            $end = strtotime($holiday->end_date);
            while ($current <= $end) {
                $d = date('Y-m-d', $current);
                if ($d >= $mondayDate && $d <= $sundayDate) {
                    $holidayDates[$d] = [
                        'name_th' => $holiday->name_th,
                        'name_en' => $holiday->name_en,
                    ];
                }
                $current = strtotime('+1 day', $current);
            }
        }

        // Build week grid
        $weekGrid = [];
        foreach ($rooms as $room) {
            $roomRow = [
                'room' => [
                    'id' => $room->id,
                    'name_th' => $room->name_th,
                    'capacity' => $room->capacity,
                    'building' => $room->building ? $room->building->name_th : null,
                ],
                'days' => [],
            ];

            $currentDate = $mondayDate;
            for ($i = 0; $i < 7; $i++) {
                $key = $room->id . '_' . $currentDate;
                $dayBookings = $bookingIndex[$key] ?? [];
                
                $roomRow['days'][$currentDate] = [
                    'date' => $currentDate,
                    'is_holiday' => isset($holidayDates[$currentDate]),
                    'booking_count' => count($dayBookings),
                    'bookings' => array_map(function($b) {
                        return [
                            'id' => $b->id,
                            'title' => $b->title,
                            'start_time' => substr($b->start_time, 0, 5),
                            'end_time' => substr($b->end_time, 0, 5),
                            'status' => $b->status,
                        ];
                    }, $dayBookings),
                ];

                $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
            }

            $weekGrid[] = $roomRow;
        }

        // Generate day headers
        $dayHeaders = [];
        $currentDate = $mondayDate;
        for ($i = 0; $i < 7; $i++) {
            $dayHeaders[] = [
                'date' => $currentDate,
                'day_name' => $this->getThaiDayName($currentDate),
                'day_short' => $this->getThaiDayShort($currentDate),
                'is_holiday' => isset($holidayDates[$currentDate]),
                'holiday' => $holidayDates[$currentDate] ?? null,
            ];
            $currentDate = date('Y-m-d', strtotime('+1 day', strtotime($currentDate)));
        }

        return $this->success([
            'week_start' => $mondayDate,
            'week_end' => $sundayDate,
            'day_headers' => $dayHeaders,
            'grid' => $weekGrid,
            'total_rooms' => count($rooms),
        ]);
    }

    /**
     * GET /api/v1/calendar/month/{year}/{month}
     * Get month view summary
     * 
     * @param int $year
     * @param int $month
     * @return array
     */
    public function actionMonth($year, $month)
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        $daysInMonth = (int) date('t', strtotime($startDate));

        $buildingId = Yii::$app->request->get('building_id');
        $roomId = Yii::$app->request->get('room_id');

        // Get booking counts per day
        $query = Booking::find()
            ->select(['booking_date', 'COUNT(*) as count'])
            ->where(['>=', 'booking_date', $startDate])
            ->andWhere(['<=', 'booking_date', $endDate])
            ->andWhere(['in', 'status', ['approved', 'pending', 'completed']])
            ->groupBy('booking_date');

        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        } elseif ($buildingId) {
            $query->innerJoin(['r' => MeetingRoom::tableName()], 'r.id = room_id')
                  ->andWhere(['r.building_id' => $buildingId]);
        }

        $bookingCounts = $query->asArray()->all();
        $countIndex = [];
        foreach ($bookingCounts as $row) {
            $countIndex[$row['booking_date']] = (int) $row['count'];
        }

        // Get holidays
        $holidays = Holiday::find()
            ->where(['<=', 'start_date', $endDate])
            ->andWhere(['>=', 'end_date', $startDate])
            ->all();

        $holidayIndex = [];
        foreach ($holidays as $holiday) {
            $current = max(strtotime($holiday->start_date), strtotime($startDate));
            $end = min(strtotime($holiday->end_date), strtotime($endDate));
            while ($current <= $end) {
                $d = date('Y-m-d', $current);
                $holidayIndex[$d] = [
                    'name_th' => $holiday->name_th,
                    'name_en' => $holiday->name_en,
                    'type' => $holiday->type,
                ];
                $current = strtotime('+1 day', $current);
            }
        }

        // Build calendar grid
        $firstDayOfWeek = (int) date('N', strtotime($startDate)); // 1=Mon, 7=Sun
        $weeks = [];
        $currentWeek = [];
        
        // Add empty cells for days before the 1st
        for ($i = 1; $i < $firstDayOfWeek; $i++) {
            $currentWeek[] = null;
        }

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $currentWeek[] = [
                'date' => $date,
                'day' => $day,
                'booking_count' => $countIndex[$date] ?? 0,
                'is_holiday' => isset($holidayIndex[$date]),
                'holiday' => $holidayIndex[$date] ?? null,
                'is_today' => $date === date('Y-m-d'),
                'is_weekend' => in_array(date('N', strtotime($date)), [6, 7]),
            ];

            if (count($currentWeek) === 7) {
                $weeks[] = $currentWeek;
                $currentWeek = [];
            }
        }

        // Add empty cells for remaining days
        if (!empty($currentWeek)) {
            while (count($currentWeek) < 7) {
                $currentWeek[] = null;
            }
            $weeks[] = $currentWeek;
        }

        // Summary statistics
        $totalBookings = array_sum($countIndex);
        $busyDays = count(array_filter($countIndex, function($c) { return $c > 0; }));

        return $this->success([
            'year' => (int) $year,
            'month' => (int) $month,
            'month_name_th' => $this->getThaiMonthName($month),
            'month_name_en' => date('F', strtotime($startDate)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days_in_month' => $daysInMonth,
            'weeks' => $weeks,
            'summary' => [
                'total_bookings' => $totalBookings,
                'busy_days' => $busyDays,
                'holiday_count' => count($holidayIndex),
            ],
        ]);
    }

    /**
     * GET /api/v1/calendar/holidays
     * Get holidays for a period
     * 
     * @return array
     */
    public function actionHolidays()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        $month = Yii::$app->request->get('month');

        if ($month) {
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));
        } else {
            $startDate = $year . '-01-01';
            $endDate = $year . '-12-31';
        }

        $holidays = Holiday::find()
            ->where(['<=', 'start_date', $endDate])
            ->andWhere(['>=', 'end_date', $startDate])
            ->orderBy(['start_date' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($holidays as $holiday) {
            $data[] = [
                'id' => $holiday->id,
                'name_th' => $holiday->name_th,
                'name_en' => $holiday->name_en,
                'start_date' => $holiday->start_date,
                'end_date' => $holiday->end_date,
                'type' => $holiday->type,
                'is_recurring' => (bool) $holiday->is_recurring,
            ];
        }

        return $this->success([
            'year' => (int) $year,
            'month' => $month ? (int) $month : null,
            'holidays' => $data,
        ]);
    }

    /**
     * Format booking as calendar event
     * 
     * @param Booking $booking
     * @param bool $isOwner
     * @return array
     */
    protected function formatCalendarEvent($booking, $isOwner = false)
    {
        $event = [
            'id' => $booking->id,
            'title' => $booking->title,
            'start' => $booking->booking_date . 'T' . $booking->start_time,
            'end' => $booking->booking_date . 'T' . $booking->end_time,
            'color' => $this->getStatusColor($booking->status),
            'allDay' => false,
            'extendedProps' => [
                'booking_code' => $booking->booking_code,
                'room_id' => $booking->room_id,
                'room_name' => $booking->room ? $booking->room->name_th : null,
                'status' => $booking->status,
                'attendee_count' => $booking->attendee_count,
            ],
        ];

        if ($isOwner) {
            $event['extendedProps']['can_cancel'] = $booking->canBeCancelled();
            $event['extendedProps']['can_edit'] = $booking->canBeEdited();
        }

        return $event;
    }

    /**
     * Format booking for day view
     * 
     * @param Booking $booking
     * @return array
     */
    protected function formatDayEvent($booking)
    {
        return [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'title' => $booking->title,
            'start_time' => substr($booking->start_time, 0, 5),
            'end_time' => substr($booking->end_time, 0, 5),
            'duration_minutes' => $booking->duration_minutes,
            'status' => $booking->status,
            'user' => $booking->user ? [
                'id' => $booking->user->id,
                'name' => $booking->user->full_name,
            ] : null,
            'attendee_count' => $booking->attendee_count,
        ];
    }

    /**
     * Get holiday events
     * 
     * @param string $start
     * @param string $end
     * @return array
     */
    protected function getHolidayEvents($start, $end)
    {
        $holidays = Holiday::find()
            ->where(['<=', 'start_date', $end])
            ->andWhere(['>=', 'end_date', $start])
            ->all();

        $events = [];
        foreach ($holidays as $holiday) {
            $events[] = [
                'id' => 'holiday_' . $holiday->id,
                'title' => '🎌 ' . $holiday->name_th,
                'start' => $holiday->start_date,
                'end' => date('Y-m-d', strtotime($holiday->end_date . ' +1 day')),
                'color' => '#dc3545',
                'allDay' => true,
                'extendedProps' => [
                    'type' => 'holiday',
                    'holiday_type' => $holiday->type,
                ],
            ];
        }

        return $events;
    }

    /**
     * Get events user is attending
     * 
     * @param string $start
     * @param string $end
     * @return array
     */
    protected function getAttendingEvents($start, $end)
    {
        $attendeeBookings = \common\models\BookingAttendee::find()
            ->alias('ba')
            ->innerJoin(['b' => Booking::tableName()], 'ba.booking_id = b.id')
            ->where(['ba.user_id' => $this->getUserId()])
            ->andWhere(['>=', 'b.booking_date', $start])
            ->andWhere(['<=', 'b.booking_date', $end])
            ->andWhere(['in', 'b.status', ['approved', 'pending']])
            ->with(['booking', 'booking.room'])
            ->all();

        $events = [];
        foreach ($attendeeBookings as $attendee) {
            $booking = $attendee->booking;
            if ($booking && $booking->user_id != $this->getUserId()) {
                $event = $this->formatCalendarEvent($booking);
                $event['title'] = '👤 ' . $event['title'];
                $event['extendedProps']['type'] = 'attending';
                $event['color'] = '#6f42c1'; // Purple for attending
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * Generate time slots for a room on a specific date
     * 
     * @param MeetingRoom $room
     * @param string $date
     * @param array $bookings
     * @return array
     */
    protected function generateTimeSlots($room, $date, $bookings)
    {
        $slots = [];
        $startHour = (int) substr($room->operating_hours_start ?? '08:00', 0, 2);
        $endHour = (int) substr($room->operating_hours_end ?? '18:00', 0, 2);
        $interval = 30; // 30 minute slots

        // Index bookings by time
        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $start = strtotime($booking->start_time);
            $end = strtotime($booking->end_time);
            while ($start < $end) {
                $bookedSlots[date('H:i', $start)] = $booking;
                $start = strtotime('+' . $interval . ' minutes', $start);
            }
        }

        $currentTime = strtotime($startHour . ':00');
        $endTime = strtotime($endHour . ':00');

        while ($currentTime < $endTime) {
            $timeStr = date('H:i', $currentTime);
            $nextTime = strtotime('+' . $interval . ' minutes', $currentTime);
            
            $slot = [
                'time' => $timeStr,
                'end_time' => date('H:i', $nextTime),
                'available' => !isset($bookedSlots[$timeStr]),
            ];

            if (isset($bookedSlots[$timeStr])) {
                $booking = $bookedSlots[$timeStr];
                $slot['booking'] = [
                    'id' => $booking->id,
                    'title' => $booking->title,
                    'status' => $booking->status,
                ];
            }

            // Check if slot is in the past
            if ($date === date('Y-m-d') && $currentTime < time()) {
                $slot['available'] = false;
                $slot['past'] = true;
            }

            $slots[] = $slot;
            $currentTime = $nextTime;
        }

        return $slots;
    }

    /**
     * Get status color
     * 
     * @param string $status
     * @return string
     */
    protected function getStatusColor($status)
    {
        $colors = [
            'pending' => '#ffc107',
            'approved' => '#28a745',
            'rejected' => '#dc3545',
            'cancelled' => '#6c757d',
            'completed' => '#17a2b8',
        ];

        return $colors[$status] ?? '#6c757d';
    }

    /**
     * Get Thai day name
     * 
     * @param string $date
     * @return string
     */
    protected function getThaiDayName($date)
    {
        $days = [
            1 => 'วันจันทร์',
            2 => 'วันอังคาร',
            3 => 'วันพุธ',
            4 => 'วันพฤหัสบดี',
            5 => 'วันศุกร์',
            6 => 'วันเสาร์',
            7 => 'วันอาทิตย์',
        ];
        
        return $days[(int) date('N', strtotime($date))] ?? '';
    }

    /**
     * Get Thai day short name
     * 
     * @param string $date
     * @return string
     */
    protected function getThaiDayShort($date)
    {
        $days = [
            1 => 'จ.',
            2 => 'อ.',
            3 => 'พ.',
            4 => 'พฤ.',
            5 => 'ศ.',
            6 => 'ส.',
            7 => 'อา.',
        ];
        
        return $days[(int) date('N', strtotime($date))] ?? '';
    }

    /**
     * Get Thai month name
     * 
     * @param int $month
     * @return string
     */
    protected function getThaiMonthName($month)
    {
        $months = [
            1 => 'มกราคม',
            2 => 'กุมภาพันธ์',
            3 => 'มีนาคม',
            4 => 'เมษายน',
            5 => 'พฤษภาคม',
            6 => 'มิถุนายน',
            7 => 'กรกฎาคม',
            8 => 'สิงหาคม',
            9 => 'กันยายน',
            10 => 'ตุลาคม',
            11 => 'พฤศจิกายน',
            12 => 'ธันวาคม',
        ];
        
        return $months[(int) $month] ?? '';
    }
}
