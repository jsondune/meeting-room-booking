<?php
/**
 * EquipmentController - API controller for equipment information
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace api\controllers\v1;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Equipment;
use common\models\BookingEquipment;
use common\models\Booking;

/**
 * EquipmentController provides RESTful API for equipment
 */
class EquipmentController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Allow listing equipment without authentication
        $behaviors['authenticator']['optional'] = ['index', 'view', 'categories', 'available'];
        
        return $behaviors;
    }

    /**
     * GET /api/v1/equipment
     * List all available equipment
     * 
     * @return array
     */
    public function actionIndex()
    {
        $query = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->orderBy(['category' => SORT_ASC, 'name_th' => SORT_ASC]);

        // Search filter
        $search = Yii::$app->request->get('search');
        if ($search) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $search],
                ['like', 'name_en', $search],
                ['like', 'code', $search],
            ]);
        }

        // Filter by category
        $category = Yii::$app->request->get('category');
        if ($category) {
            $query->andWhere(['category' => $category]);
        }

        // Filter by portable
        $portable = Yii::$app->request->get('portable');
        if ($portable !== null) {
            $query->andWhere(['is_portable' => $portable ? 1 : 0]);
        }

        // Filter by room
        $roomId = Yii::$app->request->get('room_id');
        if ($roomId) {
            $query->andWhere(['or', ['room_id' => $roomId], ['room_id' => null]]);
        }

        // Filter by availability for date/time
        $date = Yii::$app->request->get('date');
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');
        
        if ($date && $startTime && $endTime) {
            // Exclude equipment already booked for this time slot
            $bookedEquipmentIds = BookingEquipment::find()
                ->alias('be')
                ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
                ->where(['b.booking_date' => $date])
                ->andWhere(['in', 'b.status', ['approved', 'pending']])
                ->andWhere([
                    'or',
                    ['and', ['<=', 'b.start_time', $startTime], ['>', 'b.end_time', $startTime]],
                    ['and', ['<', 'b.start_time', $endTime], ['>=', 'b.end_time', $endTime]],
                    ['and', ['>=', 'b.start_time', $startTime], ['<', 'b.end_time', $endTime]],
                ])
                ->select('be.equipment_id')
                ->column();
            
            if (!empty($bookedEquipmentIds)) {
                $query->andWhere(['not in', 'id', $bookedEquipmentIds]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 50),
            ],
        ]);

        $equipment = [];
        foreach ($dataProvider->getModels() as $item) {
            $equipment[] = $this->formatEquipment($item);
        }

        return $this->paginate($equipment, $dataProvider->getPagination());
    }

    /**
     * GET /api/v1/equipment/{id}
     * Get equipment details
     * 
     * @param int $id
     * @return array
     */
    public function actionView($id)
    {
        $equipment = $this->findEquipment($id);

        $data = $this->formatEquipment($equipment, true);
        
        // Add usage statistics
        $data['statistics'] = $this->getEquipmentStatistics($equipment->id);
        
        // Add current/upcoming bookings
        $data['upcoming_bookings'] = $this->getUpcomingBookings($equipment->id);

        return $this->success($data);
    }

    /**
     * GET /api/v1/equipment/categories
     * Get equipment categories
     * 
     * @return array
     */
    public function actionCategories()
    {
        $categories = Equipment::find()
            ->select(['category', 'COUNT(*) as count'])
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->groupBy('category')
            ->orderBy(['category' => SORT_ASC])
            ->asArray()
            ->all();

        $data = [];
        $categoryLabels = Equipment::getCategoryLabels();
        
        foreach ($categories as $cat) {
            $data[] = [
                'value' => $cat['category'],
                'label' => $categoryLabels[$cat['category']] ?? $cat['category'],
                'count' => (int) $cat['count'],
            ];
        }

        return $this->success($data);
    }

    /**
     * GET /api/v1/equipment/available
     * Check equipment availability for a time slot
     * 
     * @return array
     */
    public function actionAvailable()
    {
        $date = Yii::$app->request->get('date');
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');
        $roomId = Yii::$app->request->get('room_id');
        $excludeBookingId = Yii::$app->request->get('exclude_booking_id');

        if (!$date || !$startTime || !$endTime) {
            return $this->error('date, start_time, and end_time are required', 400);
        }

        // Get equipment already booked for this time slot
        $bookedQuery = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->where(['b.booking_date' => $date])
            ->andWhere(['in', 'b.status', ['approved', 'pending']])
            ->andWhere([
                'or',
                ['and', ['<=', 'b.start_time', $startTime], ['>', 'b.end_time', $startTime]],
                ['and', ['<', 'b.start_time', $endTime], ['>=', 'b.end_time', $endTime]],
                ['and', ['>=', 'b.start_time', $startTime], ['<', 'b.end_time', $endTime]],
            ]);
        
        if ($excludeBookingId) {
            $bookedQuery->andWhere(['!=', 'b.id', $excludeBookingId]);
        }
        
        $bookedEquipmentIds = $bookedQuery->select('be.equipment_id')->column();

        // Get available portable equipment
        $query = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->andWhere(['is_portable' => 1]);
        
        if (!empty($bookedEquipmentIds)) {
            $query->andWhere(['not in', 'id', $bookedEquipmentIds]);
        }

        $portableEquipment = [];
        foreach ($query->all() as $item) {
            $portableEquipment[] = $this->formatEquipment($item);
        }

        // Get room equipment if room specified
        $roomEquipment = [];
        if ($roomId) {
            $room = \common\models\MeetingRoom::findOne($roomId);
            if ($room) {
                $roomEquipmentQuery = Equipment::find()
                    ->where(['room_id' => $roomId, 'status' => Equipment::STATUS_AVAILABLE]);
                
                foreach ($roomEquipmentQuery->all() as $item) {
                    $roomEquipment[] = $this->formatEquipment($item);
                }
            }
        }

        return $this->success([
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'portable_equipment' => $portableEquipment,
            'room_equipment' => $roomEquipment,
            'booked_equipment_ids' => array_map('intval', $bookedEquipmentIds),
        ]);
    }

    /**
     * GET /api/v1/equipment/{id}/schedule
     * Get equipment booking schedule
     * 
     * @param int $id
     * @return array
     */
    public function actionSchedule($id)
    {
        $equipment = $this->findEquipment($id);
        
        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $days = (int) Yii::$app->request->get('days', 7);
        
        $endDate = date('Y-m-d', strtotime($date . ' + ' . ($days - 1) . ' days'));

        // Get bookings for this equipment
        $bookings = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->innerJoin(['r' => \common\models\MeetingRoom::tableName()], 'b.room_id = r.id')
            ->where(['be.equipment_id' => $equipment->id])
            ->andWhere(['>=', 'b.booking_date', $date])
            ->andWhere(['<=', 'b.booking_date', $endDate])
            ->andWhere(['in', 'b.status', ['approved', 'pending']])
            ->select([
                'b.id', 'b.booking_date', 'b.start_time', 'b.end_time',
                'b.title', 'b.status', 'r.name_th as room_name'
            ])
            ->orderBy(['b.booking_date' => SORT_ASC, 'b.start_time' => SORT_ASC])
            ->asArray()
            ->all();

        // Group by date
        $schedule = [];
        foreach ($bookings as $booking) {
            $bookingDate = $booking['booking_date'];
            if (!isset($schedule[$bookingDate])) {
                $schedule[$bookingDate] = [];
            }
            $schedule[$bookingDate][] = [
                'booking_id' => (int) $booking['id'],
                'title' => $booking['title'],
                'room' => $booking['room_name'],
                'start_time' => substr($booking['start_time'], 0, 5),
                'end_time' => substr($booking['end_time'], 0, 5),
                'status' => $booking['status'],
            ];
        }

        return $this->success([
            'equipment' => $this->formatEquipment($equipment),
            'date_range' => [
                'start' => $date,
                'end' => $endDate,
            ],
            'schedule' => $schedule,
        ]);
    }

    /**
     * POST /api/v1/equipment/{id}/request
     * Request equipment for a booking (authenticated)
     * 
     * @param int $id
     * @return array
     */
    public function actionRequest($id)
    {
        $equipment = $this->findEquipment($id);
        
        if (!$equipment->is_portable) {
            return $this->error('This equipment cannot be requested. It is assigned to a specific room.', 400);
        }

        $bookingId = Yii::$app->request->post('booking_id');
        $quantity = Yii::$app->request->post('quantity', 1);
        $notes = Yii::$app->request->post('notes', '');

        if (!$bookingId) {
            return $this->error('booking_id is required', 400);
        }

        // Verify booking belongs to user
        $booking = Booking::findOne($bookingId);
        if (!$booking || $booking->user_id != $this->getUserId()) {
            return $this->error('Booking not found or access denied', 404);
        }

        if (!in_array($booking->status, ['pending', 'approved'])) {
            return $this->error('Cannot add equipment to this booking', 400);
        }

        // Check if equipment is available for this time
        $isAvailable = $this->checkAvailability(
            $equipment->id,
            $booking->booking_date,
            $booking->start_time,
            $booking->end_time,
            $booking->id
        );

        if (!$isAvailable) {
            return $this->error('Equipment is not available for the requested time slot', 409);
        }

        // Check quantity
        if ($quantity > $equipment->quantity_available) {
            return $this->error('Requested quantity exceeds available quantity', 400);
        }

        // Add equipment to booking
        $bookingEquipment = BookingEquipment::findOne([
            'booking_id' => $bookingId,
            'equipment_id' => $equipment->id,
        ]);

        if ($bookingEquipment) {
            $bookingEquipment->quantity = $quantity;
            $bookingEquipment->notes = $notes;
        } else {
            $bookingEquipment = new BookingEquipment();
            $bookingEquipment->booking_id = $bookingId;
            $bookingEquipment->equipment_id = $equipment->id;
            $bookingEquipment->quantity = $quantity;
            $bookingEquipment->unit_price = $equipment->rental_price ?? 0;
            $bookingEquipment->notes = $notes;
        }

        if (!$bookingEquipment->save()) {
            return $this->validationError($bookingEquipment);
        }

        // Recalculate booking cost
        $booking->calculateCosts();

        return $this->success([
            'message' => 'Equipment added to booking',
            'equipment' => $this->formatEquipment($equipment),
            'booking_equipment' => [
                'id' => $bookingEquipment->id,
                'quantity' => $bookingEquipment->quantity,
                'total_cost' => $bookingEquipment->quantity * $bookingEquipment->unit_price,
            ],
        ]);
    }

    /**
     * DELETE /api/v1/equipment/{id}/request
     * Remove equipment from a booking
     * 
     * @param int $id
     * @return array
     */
    public function actionRemoveRequest($id)
    {
        $equipment = $this->findEquipment($id);
        
        $bookingId = Yii::$app->request->post('booking_id');
        if (!$bookingId) {
            return $this->error('booking_id is required', 400);
        }

        // Verify booking belongs to user
        $booking = Booking::findOne($bookingId);
        if (!$booking || $booking->user_id != $this->getUserId()) {
            return $this->error('Booking not found or access denied', 404);
        }

        $bookingEquipment = BookingEquipment::findOne([
            'booking_id' => $bookingId,
            'equipment_id' => $equipment->id,
        ]);

        if (!$bookingEquipment) {
            return $this->error('Equipment not found in booking', 404);
        }

        $bookingEquipment->delete();

        // Recalculate booking cost
        $booking->calculateCosts();

        return $this->success([
            'message' => 'Equipment removed from booking',
        ]);
    }

    /**
     * Format equipment data
     * 
     * @param Equipment $equipment
     * @param bool $detailed
     * @return array
     */
    protected function formatEquipment($equipment, $detailed = false)
    {
        $data = [
            'id' => $equipment->id,
            'code' => $equipment->code,
            'name_th' => $equipment->name_th,
            'name_en' => $equipment->name_en,
            'category' => $equipment->category,
            'category_label' => Equipment::getCategoryLabels()[$equipment->category] ?? $equipment->category,
            'is_portable' => (bool) $equipment->is_portable,
            'quantity_available' => $equipment->quantity_available,
            'rental_price' => (float) ($equipment->rental_price ?? 0),
            'image_url' => $equipment->getImageUrl(),
        ];

        if ($detailed) {
            $data['description'] = $equipment->description;
            $data['brand'] = $equipment->brand;
            $data['model'] = $equipment->model;
            $data['serial_number'] = $equipment->serial_number;
            $data['specifications'] = $equipment->specifications;
            $data['room_id'] = $equipment->room_id;
            $data['room'] = $equipment->room ? [
                'id' => $equipment->room->id,
                'name_th' => $equipment->room->name_th,
            ] : null;
            $data['last_maintenance_date'] = $equipment->last_maintenance_date;
            $data['next_maintenance_date'] = $equipment->next_maintenance_date;
            $data['purchase_date'] = $equipment->purchase_date;
            $data['warranty_expiry'] = $equipment->warranty_expiry;
        }

        return $data;
    }

    /**
     * Get equipment usage statistics
     * 
     * @param int $equipmentId
     * @return array
     */
    protected function getEquipmentStatistics($equipmentId)
    {
        // This month usage
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $monthUsage = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->where(['be.equipment_id' => $equipmentId])
            ->andWhere(['>=', 'b.booking_date', $monthStart])
            ->andWhere(['<=', 'b.booking_date', $monthEnd])
            ->andWhere(['in', 'b.status', ['approved', 'completed']])
            ->count();

        // Total usage all time
        $totalUsage = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->where(['be.equipment_id' => $equipmentId])
            ->andWhere(['in', 'b.status', ['approved', 'completed']])
            ->count();

        return [
            'month_usage' => (int) $monthUsage,
            'total_usage' => (int) $totalUsage,
        ];
    }

    /**
     * Get upcoming bookings for equipment
     * 
     * @param int $equipmentId
     * @return array
     */
    protected function getUpcomingBookings($equipmentId)
    {
        $bookings = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->innerJoin(['r' => \common\models\MeetingRoom::tableName()], 'b.room_id = r.id')
            ->where(['be.equipment_id' => $equipmentId])
            ->andWhere(['>=', 'b.booking_date', date('Y-m-d')])
            ->andWhere(['in', 'b.status', ['approved', 'pending']])
            ->select([
                'b.id', 'b.booking_date', 'b.start_time', 'b.end_time',
                'b.title', 'b.status', 'r.name_th as room_name'
            ])
            ->orderBy(['b.booking_date' => SORT_ASC, 'b.start_time' => SORT_ASC])
            ->limit(10)
            ->asArray()
            ->all();

        $result = [];
        foreach ($bookings as $booking) {
            $result[] = [
                'booking_id' => (int) $booking['id'],
                'title' => $booking['title'],
                'room' => $booking['room_name'],
                'date' => $booking['booking_date'],
                'start_time' => substr($booking['start_time'], 0, 5),
                'end_time' => substr($booking['end_time'], 0, 5),
                'status' => $booking['status'],
            ];
        }

        return $result;
    }

    /**
     * Check equipment availability
     * 
     * @param int $equipmentId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeBookingId
     * @return bool
     */
    protected function checkAvailability($equipmentId, $date, $startTime, $endTime, $excludeBookingId = null)
    {
        $query = BookingEquipment::find()
            ->alias('be')
            ->innerJoin(['b' => Booking::tableName()], 'be.booking_id = b.id')
            ->where(['be.equipment_id' => $equipmentId])
            ->andWhere(['b.booking_date' => $date])
            ->andWhere(['in', 'b.status', ['approved', 'pending']])
            ->andWhere([
                'or',
                ['and', ['<=', 'b.start_time', $startTime], ['>', 'b.end_time', $startTime]],
                ['and', ['<', 'b.start_time', $endTime], ['>=', 'b.end_time', $endTime]],
                ['and', ['>=', 'b.start_time', $startTime], ['<', 'b.end_time', $endTime]],
            ]);
        
        if ($excludeBookingId) {
            $query->andWhere(['!=', 'b.id', $excludeBookingId]);
        }
        
        return $query->count() == 0;
    }

    /**
     * Find equipment by ID
     * 
     * @param int $id
     * @return Equipment
     */
    protected function findEquipment($id)
    {
        $equipment = Equipment::findOne($id);
        
        if (!$equipment) {
            return $this->error('Equipment not found', 404);
        }
        
        return $equipment;
    }
}
