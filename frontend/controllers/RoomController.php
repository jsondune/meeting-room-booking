<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\MeetingRoom;
use common\models\Building;
use common\models\Booking;
use common\models\RoomImage;

/**
 * RoomController - Browse and search meeting rooms
 */
class RoomController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['favorite', 'quick-book'],
                'rules' => [
                    [
                        'actions' => ['favorite', 'quick-book'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'favorite' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all available meeting rooms.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE]);

        // Apply filters
        $buildingId = Yii::$app->request->get('building_id');
        $capacity = Yii::$app->request->get('capacity');
        $roomType = Yii::$app->request->get('room_type');
        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');
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
                ['like', 'description', $keyword],
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

        // Check availability if time specified
        $availableRoomIds = null;
        if ($startTime && $endTime) {
            $availableRoomIds = $this->getAvailableRoomIds($date, $startTime, $endTime);
            if (!empty($availableRoomIds)) {
                $query->andWhere(['id' => $availableRoomIds]);
            } else {
                // No rooms available
                $query->andWhere('1=0');
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC,
                    'name_th' => SORT_ASC,
                ],
            ],
        ]);

        // Get filter options
        $buildings = Building::getDropdownList();
        $roomTypes = MeetingRoom::getRoomTypes();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'buildings' => $buildings,
            'roomTypes' => $roomTypes,
            'filters' => [
                'building_id' => $buildingId,
                'capacity' => $capacity,
                'room_type' => $roomType,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'keyword' => $keyword,
                'has_projector' => $hasProjector,
                'has_video_conference' => $hasVideoConference,
                'has_whiteboard' => $hasWhiteboard,
                'has_wifi' => $hasWifi,
            ],
        ]);
    }

    /**
     * Displays a single MeetingRoom model.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get room images
        $images = RoomImage::find()
            ->where(['room_id' => $id])
            ->orderBy(['is_primary' => SORT_DESC, 'sort_order' => SORT_ASC])
            ->all();

        // Get room equipment
        $equipment = $model->equipments;

        // Get today's bookings for this room
        $todayBookings = Booking::find()
            ->where(['room_id' => $id, 'booking_date' => date('Y-m-d')])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();

        // Get available time slots for today
        $availableSlots = $model->getAvailableSlots(date('Y-m-d'), 60);

        // Get upcoming bookings (next 7 days)
        $upcomingBookings = Booking::find()
            ->where(['room_id' => $id])
            ->andWhere(['>=', 'booking_date', date('Y-m-d')])
            ->andWhere(['<=', 'booking_date', date('Y-m-d', strtotime('+7 days'))])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        // Similar rooms (same building, similar capacity)
        $similarRooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->andWhere(['building_id' => $model->building_id])
            ->andWhere(['<>', 'id', $id])
            ->andWhere(['between', 'capacity', $model->capacity - 10, $model->capacity + 10])
            ->limit(4)
            ->all();

        return $this->render('view', [
            'model' => $model,
            'images' => $images,
            'equipment' => $equipment,
            'todayBookings' => $todayBookings,
            'availableSlots' => $availableSlots,
            'upcomingBookings' => $upcomingBookings,
            'similarRooms' => $similarRooms,
        ]);
    }

    /**
     * Room availability calendar view
     *
     * @param int $id
     * @return string
     */
    public function actionCalendar($id)
    {
        $model = $this->findModel($id);

        return $this->render('calendar', [
            'model' => $model,
        ]);
    }

    /**
     * Get room availability for calendar (AJAX)
     *
     * @param int $id
     * @return array
     */
    public function actionAvailability($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');

        // Get bookings for the period
        $bookings = Booking::find()
            ->where(['room_id' => $id])
            ->andWhere(['between', 'booking_date', $start, $end])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->all();

        $events = [];
        foreach ($bookings as $booking) {
            $color = $booking->status === Booking::STATUS_APPROVED ? '#dc3545' : '#ffc107';
            $events[] = [
                'id' => $booking->id,
                'title' => $booking->status === Booking::STATUS_APPROVED ? 'จองแล้ว' : 'รออนุมัติ',
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end' => $booking->booking_date . 'T' . $booking->end_time,
                'color' => $color,
                'extendedProps' => [
                    'status' => $booking->status,
                ],
            ];
        }

        // Add operating hours as background events
        $currentDate = new \DateTime($start);
        $endDate = new \DateTime($end);
        $availableDays = explode(',', $model->available_days);

        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->format('w');
            if (in_array($dayOfWeek, $availableDays)) {
                // Room is available this day
                $events[] = [
                    'id' => 'operating-' . $currentDate->format('Y-m-d'),
                    'start' => $currentDate->format('Y-m-d') . 'T' . $model->operating_start_time,
                    'end' => $currentDate->format('Y-m-d') . 'T' . $model->operating_end_time,
                    'display' => 'background',
                    'color' => '#e8f5e9',
                ];
            }
            $currentDate->modify('+1 day');
        }

        return $events;
    }

    /**
     * Get available time slots for a specific date (AJAX)
     *
     * @param int $id
     * @return array
     */
    public function actionTimeSlots($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $duration = Yii::$app->request->get('duration', 60);

        $slots = $model->getAvailableSlots($date, $duration);

        return [
            'success' => true,
            'date' => $date,
            'slots' => $slots,
            'operating_hours' => [
                'start' => $model->operating_start_time,
                'end' => $model->operating_end_time,
            ],
        ];
    }

    /**
     * Compare multiple rooms
     *
     * @return string
     */
    public function actionCompare()
    {
        $roomIds = Yii::$app->request->get('rooms', []);

        if (!is_array($roomIds)) {
            $roomIds = explode(',', $roomIds);
        }

        $roomIds = array_filter(array_map('intval', $roomIds));

        if (empty($roomIds)) {
            Yii::$app->session->setFlash('warning', 'โปรดเลือกห้องประชุมที่ต้องการเปรียบเทียบ');
            return $this->redirect(['index']);
        }

        if (count($roomIds) > 4) {
            $roomIds = array_slice($roomIds, 0, 4);
            Yii::$app->session->setFlash('info', 'แสดงการเปรียบเทียบได้สูงสุด 4 ห้อง');
        }

        $rooms = MeetingRoom::find()
            ->where(['id' => $roomIds])
            ->andWhere(['status' => MeetingRoom::STATUS_ACTIVE])
            ->all();

        if (empty($rooms)) {
            Yii::$app->session->setFlash('error', 'ไม่พบห้องประชุมที่เลือก');
            return $this->redirect(['index']);
        }

        return $this->render('compare', [
            'rooms' => $rooms,
        ]);
    }

    /**
     * Search rooms with autocomplete (AJAX)
     *
     * @return array
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $term = Yii::$app->request->get('term', '');

        if (strlen($term) < 2) {
            return [];
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
                'value' => $room->name_th,
                'label' => $room->room_code . ' - ' . $room->name_th,
                'room_code' => $room->room_code,
                'capacity' => $room->capacity,
                'building' => $room->building ? $room->building->name_th : '',
            ];
        }

        return $results;
    }

    /**
     * Get room details (AJAX)
     *
     * @param int $id
     * @return array
     */
    public function actionDetails($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

        // Get primary image
        $primaryImage = RoomImage::find()
            ->where(['room_id' => $id, 'is_primary' => 1])
            ->one();

        return [
            'success' => true,
            'room' => [
                'id' => $model->id,
                'room_code' => $model->room_code,
                'name_th' => $model->name_th,
                'name_en' => $model->name_en,
                'capacity' => $model->capacity,
                'floor' => $model->floor,
                'room_type' => $model->room_type,
                'hourly_rate' => $model->hourly_rate,
                'building' => $model->building ? $model->building->name_th : '',
                'features' => [
                    'projector' => (bool)$model->has_projector,
                    'video_conference' => (bool)$model->has_video_conference,
                    'whiteboard' => (bool)$model->has_whiteboard,
                    'wifi' => (bool)$model->has_wifi,
                    'air_conditioning' => (bool)$model->has_air_conditioning,
                    'audio_system' => (bool)$model->has_audio_system,
                ],
                'operating_hours' => [
                    'start' => $model->operating_start_time,
                    'end' => $model->operating_end_time,
                ],
                'image' => $primaryImage ? $primaryImage->getUrl() : null,
            ],
        ];
    }

    /**
     * Get available room IDs for specific time slot
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @return array
     */
    protected function getAvailableRoomIds($date, $startTime, $endTime)
    {
        // Get all active rooms
        $allRoomIds = MeetingRoom::find()
            ->select('id')
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->column();

        // Get rooms with conflicting bookings
        $bookedRoomIds = Booking::find()
            ->select('room_id')
            ->where(['booking_date' => $date])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->andWhere([
                'and',
                ['<', 'start_time', $endTime],
                ['>', 'end_time', $startTime],
            ])
            ->column();

        // Return available rooms
        return array_diff($allRoomIds, $bookedRoomIds);
    }

    /**
     * Finds the MeetingRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     * @return MeetingRoom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeetingRoom::findOne(['id' => $id, 'status' => MeetingRoom::STATUS_ACTIVE])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบห้องประชุมที่ระบุ หรือห้องประชุมไม่พร้อมใช้งาน');
    }
}
