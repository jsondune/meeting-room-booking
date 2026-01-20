<?php

namespace api\controllers\v1;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\MeetingRoom;
use common\models\Booking;
use common\models\RoomImage;

/**
 * RoomController handles meeting room API operations
 */
class RoomController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $authExcept = ['index', 'view', 'search', 'availability', 'time-slots'];

    /**
     * List all available rooms
     * GET /api/v1/rooms
     *
     * @return array
     */
    public function actionIndex()
    {
        $query = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE]);

        // Apply filters
        $buildingId = Yii::$app->request->get('building_id');
        $capacity = Yii::$app->request->get('capacity');
        $roomType = Yii::$app->request->get('room_type');
        $keyword = Yii::$app->request->get('keyword');

        // Feature filters
        $hasProjector = Yii::$app->request->get('has_projector');
        $hasVideoConference = Yii::$app->request->get('has_video_conference');
        $hasWhiteboard = Yii::$app->request->get('has_whiteboard');
        $hasWifi = Yii::$app->request->get('has_wifi');

        if ($buildingId) {
            $query->andWhere(['building_id' => $buildingId]);
        }

        if ($capacity) {
            $query->andWhere(['>=', 'capacity', $capacity]);
        }

        if ($roomType) {
            $query->andWhere(['room_type' => $roomType]);
        }

        if ($keyword) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $keyword],
                ['like', 'name_en', $keyword],
                ['like', 'room_code', $keyword],
            ]);
        }

        // Feature filters
        if ($hasProjector) {
            $query->andWhere(['has_projector' => 1]);
        }
        if ($hasVideoConference) {
            $query->andWhere(['has_video_conference' => 1]);
        }
        if ($hasWhiteboard) {
            $query->andWhere(['has_whiteboard' => 1]);
        }
        if ($hasWifi) {
            $query->andWhere(['has_wifi' => 1]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
                    'name_th' => SORT_ASC,
                ],
            ],
        ]);

        $rooms = [];
        foreach ($dataProvider->getModels() as $room) {
            $rooms[] = $this->formatRoom($room);
        }

        return $this->success([
            'rooms' => $rooms,
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->getPage() + 1,
                'per_page' => $dataProvider->pagination->getPageSize(),
                'page_count' => $dataProvider->pagination->getPageCount(),
            ],
        ]);
    }

    /**
     * View single room details
     * GET /api/v1/rooms/{id}
     *
     * @param int $id
     * @return array
     */
    public function actionView($id)
    {
        $room = MeetingRoom::findOne([
            'id' => $id,
            'status' => MeetingRoom::STATUS_ACTIVE,
        ]);

        if (!$room) {
            return $this->error('ไม่พบห้องประชุมที่ระบุ', 404);
        }

        // Get room images
        $images = RoomImage::find()
            ->where(['room_id' => $id])
            ->orderBy(['is_primary' => SORT_DESC, 'sort_order' => SORT_ASC])
            ->all();

        // Get today's bookings
        $todayBookings = Booking::find()
            ->where(['room_id' => $id, 'booking_date' => date('Y-m-d')])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();

        return $this->success([
            'room' => $this->formatRoom($room, true),
            'images' => array_map(function($img) {
                return [
                    'id' => $img->id,
                    'url' => $img->getUrl(),
                    'is_primary' => (bool)$img->is_primary,
                ];
            }, $images),
            'today_bookings' => array_map(function($booking) {
                return [
                    'id' => $booking->id,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'status' => $booking->status,
                ];
            }, $todayBookings),
        ]);
    }

    /**
     * Search rooms with autocomplete
     * GET /api/v1/rooms/search
     *
     * @return array
     */
    public function actionSearch()
    {
        $term = Yii::$app->request->get('term', '');

        if (strlen($term) < 2) {
            return $this->success(['rooms' => []]);
        }

        $rooms = MeetingRoom::find()
            ->select(['id', 'room_code', 'name_th', 'name_en', 'capacity', 'building_id'])
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->andWhere([
                'or',
                ['like', 'name_th', $term],
                ['like', 'name_en', $term],
                ['like', 'room_code', $term],
            ])
            ->with('building')
            ->limit(10)
            ->all();

        $results = [];
        foreach ($rooms as $room) {
            $results[] = [
                'id' => $room->id,
                'room_code' => $room->room_code,
                'name_th' => $room->name_th,
                'name_en' => $room->name_en,
                'capacity' => $room->capacity,
                'building' => $room->building ? $room->building->name_th : '',
            ];
        }

        return $this->success(['rooms' => $results]);
    }

    /**
     * Check room availability for a date range
     * GET /api/v1/rooms/{id}/availability
     *
     * @param int $id
     * @return array
     */
    public function actionAvailability($id)
    {
        $room = MeetingRoom::findOne([
            'id' => $id,
            'status' => MeetingRoom::STATUS_ACTIVE,
        ]);

        if (!$room) {
            return $this->error('ไม่พบห้องประชุมที่ระบุ', 404);
        }

        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');

        // Get all bookings for the date
        $bookings = Booking::find()
            ->select(['start_time', 'end_time', 'status'])
            ->where(['room_id' => $id, 'booking_date' => $date])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->orderBy(['start_time' => SORT_ASC])
            ->asArray()
            ->all();

        // Check specific time slot if provided
        $isAvailable = true;
        if ($startTime && $endTime) {
            $isAvailable = $room->isAvailable($date, $startTime, $endTime);
        }

        return $this->success([
            'date' => $date,
            'available' => $isAvailable,
            'operating_hours' => [
                'start' => $room->operating_start_time,
                'end' => $room->operating_end_time,
            ],
            'bookings' => $bookings,
        ]);
    }

    /**
     * Get available time slots for a specific date
     * GET /api/v1/rooms/{id}/time-slots
     *
     * @param int $id
     * @return array
     */
    public function actionTimeSlots($id)
    {
        $room = MeetingRoom::findOne([
            'id' => $id,
            'status' => MeetingRoom::STATUS_ACTIVE,
        ]);

        if (!$room) {
            return $this->error('ไม่พบห้องประชุมที่ระบุ', 404);
        }

        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $duration = Yii::$app->request->get('duration', 60); // minutes

        $slots = $room->getAvailableSlots($date, $duration);

        return $this->success([
            'date' => $date,
            'duration' => $duration,
            'operating_hours' => [
                'start' => $room->operating_start_time,
                'end' => $room->operating_end_time,
            ],
            'slots' => $slots,
        ]);
    }

    /**
     * Format room data for API response
     *
     * @param MeetingRoom $room
     * @param bool $detailed Include detailed information
     * @return array
     */
    protected function formatRoom($room, $detailed = false)
    {
        $data = [
            'id' => $room->id,
            'room_code' => $room->room_code,
            'name_th' => $room->name_th,
            'name_en' => $room->name_en,
            'capacity' => $room->capacity,
            'floor' => $room->floor,
            'room_type' => $room->room_type,
            'hourly_rate' => $room->hourly_rate,
            'building' => $room->building ? [
                'id' => $room->building->id,
                'name_th' => $room->building->name_th,
                'name_en' => $room->building->name_en,
            ] : null,
            'features' => [
                'projector' => (bool)$room->has_projector,
                'video_conference' => (bool)$room->has_video_conference,
                'whiteboard' => (bool)$room->has_whiteboard,
                'wifi' => (bool)$room->has_wifi,
                'air_conditioning' => (bool)$room->has_air_conditioning,
                'audio_system' => (bool)$room->has_audio_system,
                'tv_monitor' => (bool)$room->has_tv_monitor,
            ],
            'primary_image' => $room->getPrimaryImageUrl(),
        ];

        if ($detailed) {
            $data['description'] = $room->description;
            $data['area_sqm'] = $room->area_sqm;
            $data['operating_hours'] = [
                'start' => $room->operating_start_time,
                'end' => $room->operating_end_time,
            ];
            $data['available_days'] = explode(',', $room->available_days);
            $data['requires_approval'] = (bool)$room->requires_approval;
            $data['rules'] = $room->rules;
            $data['contact_person'] = $room->contact_person;
            $data['contact_phone'] = $room->contact_phone;
            $data['equipment'] = array_map(function($eq) {
                return [
                    'id' => $eq->id,
                    'name_th' => $eq->name_th,
                    'quantity' => $eq->pivot ? $eq->pivot->quantity : 1,
                ];
            }, $room->equipments ?? []);
        }

        return $data;
    }
}
