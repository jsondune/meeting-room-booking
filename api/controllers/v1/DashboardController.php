<?php

namespace api\controllers\v1;

use Yii;
use common\models\Booking;
use common\models\MeetingRoom;

/**
 * DashboardController provides dashboard data for mobile apps
 */
class DashboardController extends BaseController
{
    /**
     * Get dashboard data
     * GET /api/v1/dashboard
     *
     * @return array
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $userId = $user->id;
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Get user's statistics
        $stats = [
            'total_bookings' => Booking::find()->where(['user_id' => $userId])->count(),
            'pending_bookings' => Booking::find()->where([
                'user_id' => $userId,
                'status' => Booking::STATUS_PENDING,
            ])->count(),
            'upcoming_bookings' => Booking::find()
                ->where(['user_id' => $userId])
                ->andWhere(['>=', 'booking_date', $today])
                ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
                ->count(),
            'completed_bookings' => Booking::find()->where([
                'user_id' => $userId,
                'status' => Booking::STATUS_COMPLETED,
            ])->count(),
        ];

        // Get today's bookings
        $todayBookings = Booking::find()
            ->where(['user_id' => $userId, 'booking_date' => $today])
            ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
            ->with(['room', 'room.building'])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();

        // Get current booking (if any)
        $currentBooking = Booking::find()
            ->where(['user_id' => $userId, 'booking_date' => $today])
            ->andWhere(['status' => Booking::STATUS_APPROVED])
            ->andWhere(['<=', 'start_time', $now])
            ->andWhere(['>=', 'end_time', $now])
            ->with(['room', 'room.building'])
            ->one();

        // Get next upcoming booking
        $nextBooking = Booking::find()
            ->where(['user_id' => $userId])
            ->andWhere([
                'or',
                ['>', 'booking_date', $today],
                [
                    'and',
                    ['=', 'booking_date', $today],
                    ['>', 'start_time', $now],
                ],
            ])
            ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
            ->with(['room', 'room.building'])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->one();

        // Get recent bookings (last 5)
        $recentBookings = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['room', 'room.building'])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(5)
            ->all();

        // Get available rooms count
        $availableRoomsCount = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->count();

        // Get popular rooms (most booked by this user)
        $favoriteRooms = Booking::find()
            ->select(['room_id', 'COUNT(*) as booking_count'])
            ->where(['user_id' => $userId])
            ->groupBy('room_id')
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(5)
            ->with('room')
            ->asArray()
            ->all();

        // Monthly chart data (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['user_id' => $userId])
                ->andWhere(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyData[] = [
                'month' => date('M', strtotime($month . '-01')),
                'year' => date('Y', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        // Quick actions
        $quickActions = [
            [
                'id' => 'new_booking',
                'title' => 'จองห้องประชุม',
                'icon' => 'calendar-plus',
                'url' => '/booking/create',
            ],
            [
                'id' => 'my_bookings',
                'title' => 'การจองของฉัน',
                'icon' => 'calendar-check',
                'url' => '/booking/my-bookings',
            ],
            [
                'id' => 'browse_rooms',
                'title' => 'ค้นหาห้องประชุม',
                'icon' => 'search',
                'url' => '/room',
            ],
            [
                'id' => 'profile',
                'title' => 'โปรไฟล์',
                'icon' => 'user',
                'url' => '/profile',
            ],
        ];

        return $this->success([
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'avatar' => $user->getAvatarUrl(),
                'department' => $user->department ? $user->department->name_th : null,
            ],
            'statistics' => $stats,
            'current_booking' => $currentBooking ? $this->formatBooking($currentBooking) : null,
            'next_booking' => $nextBooking ? $this->formatBooking($nextBooking) : null,
            'today_bookings' => array_map([$this, 'formatBooking'], $todayBookings),
            'recent_bookings' => array_map([$this, 'formatBooking'], $recentBookings),
            'favorite_rooms' => $favoriteRooms,
            'available_rooms_count' => (int)$availableRoomsCount,
            'monthly_chart' => $monthlyData,
            'quick_actions' => $quickActions,
        ]);
    }

    /**
     * Format booking for response
     *
     * @param Booking $booking
     * @return array
     */
    protected function formatBooking($booking)
    {
        return [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'title' => $booking->title,
            'room' => $booking->room ? [
                'id' => $booking->room->id,
                'name_th' => $booking->room->name_th,
                'room_code' => $booking->room->room_code,
                'building' => $booking->room->building ? $booking->room->building->name_th : '',
                'floor' => $booking->room->floor,
            ] : null,
            'booking_date' => $booking->booking_date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'status' => $booking->status,
            'status_label' => $booking->getStatusLabel(),
            'attendees_count' => $booking->attendees_count,
            'can_check_in' => $this->canCheckIn($booking),
        ];
    }

    /**
     * Check if booking can be checked in
     *
     * @param Booking $booking
     * @return bool
     */
    protected function canCheckIn($booking)
    {
        if ($booking->status !== Booking::STATUS_APPROVED) {
            return false;
        }

        if ($booking->actual_start_time) {
            return false; // Already checked in
        }

        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        if ($booking->booking_date !== $today) {
            return false;
        }

        // Allow check-in 15 minutes before start time
        $allowedCheckIn = date('H:i:s', strtotime($booking->start_time . ' -15 minutes'));
        return $currentTime >= $allowedCheckIn && $currentTime <= $booking->end_time;
    }
}
