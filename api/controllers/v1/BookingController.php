<?php

namespace api\controllers\v1;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use common\models\Booking;
use common\models\BookingEquipment;
use common\models\BookingAttendee;
use common\models\MeetingRoom;

/**
 * BookingController handles booking API operations
 */
class BookingController extends BaseController
{
    /**
     * @inheritdoc
     */
    public $modelClass = 'common\models\Booking';

    /**
     * List user's bookings
     * GET /api/v1/bookings
     *
     * @return array
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $status = Yii::$app->request->get('status');
        $dateRange = Yii::$app->request->get('date_range', 'all');

        $query = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['room', 'room.building']);

        // Status filter
        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        // Date range filter
        $today = date('Y-m-d');
        switch ($dateRange) {
            case 'today':
                $query->andWhere(['booking_date' => $today]);
                break;
            case 'upcoming':
                $query->andWhere(['>=', 'booking_date', $today]);
                break;
            case 'past':
                $query->andWhere(['<', 'booking_date', $today]);
                break;
            case 'this_week':
                $weekStart = date('Y-m-d', strtotime('monday this week'));
                $weekEnd = date('Y-m-d', strtotime('sunday this week'));
                $query->andWhere(['between', 'booking_date', $weekStart, $weekEnd]);
                break;
            case 'this_month':
                $monthStart = date('Y-m-01');
                $monthEnd = date('Y-m-t');
                $query->andWhere(['between', 'booking_date', $monthStart, $monthEnd]);
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 20),
            ],
            'sort' => [
                'defaultOrder' => [
                    'booking_date' => SORT_DESC,
                    'start_time' => SORT_DESC,
                ],
            ],
        ]);

        $bookings = [];
        foreach ($dataProvider->getModels() as $booking) {
            $bookings[] = $this->formatBooking($booking);
        }

        return $this->success([
            'bookings' => $bookings,
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->getPage() + 1,
                'per_page' => $dataProvider->pagination->getPageSize(),
                'page_count' => $dataProvider->pagination->getPageCount(),
            ],
        ]);
    }

    /**
     * Alias for actionIndex for clarity
     * GET /api/v1/bookings/my-bookings
     *
     * @return array
     */
    public function actionMyBookings()
    {
        return $this->actionIndex();
    }

    /**
     * View single booking details
     * GET /api/v1/bookings/{id}
     *
     * @param int $id
     * @return array
     */
    public function actionView($id)
    {
        $booking = $this->findModel($id);

        // Check permission
        if ($booking->user_id !== Yii::$app->user->id && !Yii::$app->user->can('viewAllBookings')) {
            return $this->error('คุณไม่มีสิทธิ์เข้าถึงข้อมูลการจองนี้', 403);
        }

        // Get attendees
        $attendees = BookingAttendee::find()
            ->where(['booking_id' => $id])
            ->all();

        // Get equipment requests
        $equipmentRequests = BookingEquipment::find()
            ->where(['booking_id' => $id])
            ->with('equipment')
            ->all();

        return $this->success([
            'booking' => $this->formatBooking($booking, true),
            'attendees' => array_map(function($attendee) {
                return [
                    'id' => $attendee->id,
                    'name' => $attendee->name,
                    'email' => $attendee->email,
                    'phone' => $attendee->phone,
                    'is_external' => (bool)$attendee->is_external,
                ];
            }, $attendees),
            'equipment' => array_map(function($eq) {
                return [
                    'id' => $eq->equipment_id,
                    'name' => $eq->equipment ? $eq->equipment->name_th : '',
                    'quantity' => $eq->quantity,
                    'price_per_unit' => $eq->price_per_unit,
                    'total_price' => $eq->total_price,
                ];
            }, $equipmentRequests),
        ]);
    }

    /**
     * Create new booking
     * POST /api/v1/bookings
     *
     * @return array
     */
    public function actionCreate()
    {
        $model = new Booking();
        $model->user_id = Yii::$app->user->id;
        $model->department_id = Yii::$app->user->identity->department_id;
        $model->status = Booking::STATUS_PENDING;

        // Load data from request
        $model->room_id = Yii::$app->request->post('room_id');
        $model->booking_date = Yii::$app->request->post('booking_date');
        $model->start_time = Yii::$app->request->post('start_time');
        $model->end_time = Yii::$app->request->post('end_time');
        $model->title = Yii::$app->request->post('title');
        $model->description = Yii::$app->request->post('description');
        $model->purpose = Yii::$app->request->post('purpose');
        $model->attendees_count = Yii::$app->request->post('attendees_count', 1);
        $model->notes = Yii::$app->request->post('notes');
        $model->is_recurring = Yii::$app->request->post('is_recurring', 0);
        $model->recurrence_pattern = Yii::$app->request->post('recurrence_pattern');

        // Validate room
        $room = MeetingRoom::findOne([
            'id' => $model->room_id,
            'status' => MeetingRoom::STATUS_ACTIVE,
        ]);

        if (!$room) {
            return $this->error('ไม่พบห้องประชุมที่ระบุ', 400);
        }

        // Check availability
        if (!$room->isAvailable($model->booking_date, $model->start_time, $model->end_time)) {
            return $this->error('ห้องประชุมไม่ว่างในช่วงเวลาที่เลือก', 400);
        }

        // Auto-approve if room doesn't require approval
        if (!$room->requires_approval) {
            $model->status = Booking::STATUS_APPROVED;
            $model->approved_at = date('Y-m-d H:i:s');
        }

        // Get attendees and equipment from request
        $attendeesData = Yii::$app->request->post('attendees', []);
        $equipmentData = Yii::$app->request->post('equipment', []);

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Calculate costs
            $model->calculateCosts();

            if ($errors = $this->validateModel($model)) {
                $transaction->rollBack();
                return $this->error('ข้อมูลการจองไม่ถูกต้อง', 400, $errors);
            }

            if ($model->save()) {
                // Save attendees
                if (!empty($attendeesData)) {
                    $model->saveAttendees($attendeesData);
                }

                // Save equipment requests
                if (!empty($equipmentData)) {
                    $model->saveEquipmentRequests($equipmentData);
                }

                // Handle recurring bookings
                if ($model->is_recurring && $model->recurrence_pattern) {
                    $model->createRecurringBookings();
                }

                // Send confirmation notification
                $model->sendBookingConfirmation();

                $transaction->commit();

                return $this->success([
                    'booking' => $this->formatBooking($model, true),
                ], 'สร้างการจองสำเร็จ');
            }

            $transaction->rollBack();
            return $this->error('เกิดข้อผิดพลาดในการสร้างการจอง', 500);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('Booking creation failed: ' . $e->getMessage(), __METHOD__);
            return $this->error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update booking
     * PUT /api/v1/bookings/{id}
     *
     * @param int $id
     * @return array
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ($model->user_id !== Yii::$app->user->id) {
            return $this->error('คุณไม่มีสิทธิ์แก้ไขการจองนี้', 403);
        }

        // Check if booking can be edited
        if (!$model->canBeEdited()) {
            return $this->error('ไม่สามารถแก้ไขการจองนี้ได้', 400);
        }

        $request = Yii::$app->request;
        $oldRoomId = $model->room_id;
        $oldDate = $model->booking_date;
        $oldStartTime = $model->start_time;
        $oldEndTime = $model->end_time;

        // Update fields if provided
        if ($request->post('room_id')) {
            $model->room_id = $request->post('room_id');
        }
        if ($request->post('booking_date')) {
            $model->booking_date = $request->post('booking_date');
        }
        if ($request->post('start_time')) {
            $model->start_time = $request->post('start_time');
        }
        if ($request->post('end_time')) {
            $model->end_time = $request->post('end_time');
        }
        if ($request->post('title')) {
            $model->title = $request->post('title');
        }
        if ($request->post('description')) {
            $model->description = $request->post('description');
        }
        if ($request->post('purpose')) {
            $model->purpose = $request->post('purpose');
        }
        if ($request->post('attendees_count')) {
            $model->attendees_count = $request->post('attendees_count');
        }
        if ($request->post('notes')) {
            $model->notes = $request->post('notes');
        }

        // Check if significant changes were made
        $significantChange = (
            $model->room_id != $oldRoomId ||
            $model->booking_date != $oldDate ||
            $model->start_time != $oldStartTime ||
            $model->end_time != $oldEndTime
        );

        // Check availability if room/time changed
        if ($significantChange) {
            $room = MeetingRoom::findOne($model->room_id);
            if (!$room || !$room->isAvailable($model->booking_date, $model->start_time, $model->end_time, $id)) {
                return $this->error('ห้องประชุมไม่ว่างในช่วงเวลาที่เลือก', 400);
            }

            // Reset to pending if requires approval
            if ($room->requires_approval) {
                $model->status = Booking::STATUS_PENDING;
                $model->approved_at = null;
                $model->approved_by = null;
            }
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Recalculate costs
            $model->calculateCosts();

            if ($errors = $this->validateModel($model)) {
                $transaction->rollBack();
                return $this->error('ข้อมูลการจองไม่ถูกต้อง', 400, $errors);
            }

            if ($model->save()) {
                // Update attendees if provided
                $attendeesData = $request->post('attendees');
                if ($attendeesData !== null) {
                    BookingAttendee::deleteAll(['booking_id' => $id]);
                    if (!empty($attendeesData)) {
                        $model->saveAttendees($attendeesData);
                    }
                }

                // Update equipment if provided
                $equipmentData = $request->post('equipment');
                if ($equipmentData !== null) {
                    BookingEquipment::deleteAll(['booking_id' => $id]);
                    if (!empty($equipmentData)) {
                        $model->saveEquipmentRequests($equipmentData);
                    }
                }

                $transaction->commit();

                return $this->success([
                    'booking' => $this->formatBooking($model, true),
                ], 'อัปเดตการจองสำเร็จ');
            }

            $transaction->rollBack();
            return $this->error('เกิดข้อผิดพลาดในการอัปเดตการจอง', 500);

        } catch (\Exception $e) {
            $transaction->rollBack();
            return $this->error('เกิดข้อผิดพลาด: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cancel booking
     * POST /api/v1/bookings/{id}/cancel
     *
     * @param int $id
     * @return array
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ($model->user_id !== Yii::$app->user->id && !Yii::$app->user->can('cancelAnyBooking')) {
            return $this->error('คุณไม่มีสิทธิ์ยกเลิกการจองนี้', 403);
        }

        // Check if booking can be cancelled
        if (!$model->canBeCancelled()) {
            return $this->error('ไม่สามารถยกเลิกการจองนี้ได้', 400);
        }

        $reason = Yii::$app->request->post('reason', 'ยกเลิกโดยผู้จอง');

        if ($model->cancel($reason, Yii::$app->user->id)) {
            return $this->success([
                'booking' => $this->formatBooking($model),
            ], 'ยกเลิกการจองสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการยกเลิกการจอง', 500);
    }

    /**
     * Check-in for a booking
     * POST /api/v1/bookings/{id}/check-in
     *
     * @param int $id
     * @return array
     */
    public function actionCheckIn($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ($model->user_id !== Yii::$app->user->id) {
            return $this->error('คุณไม่มีสิทธิ์เช็คอินการจองนี้', 403);
        }

        // Check if booking can be checked in
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        if ($model->booking_date !== $today) {
            return $this->error('สามารถเช็คอินได้เฉพาะวันที่จองเท่านั้น', 400);
        }

        if ($model->status !== Booking::STATUS_APPROVED) {
            return $this->error('การจองต้องได้รับการอนุมัติก่อนเช็คอิน', 400);
        }

        // Allow check-in 15 minutes before
        $allowedCheckIn = date('H:i:s', strtotime($model->start_time . ' -15 minutes'));
        if ($currentTime < $allowedCheckIn) {
            return $this->error('สามารถเช็คอินได้ก่อนเวลาเริ่ม 15 นาที', 400);
        }

        if ($model->checkIn()) {
            return $this->success([
                'booking' => $this->formatBooking($model),
                'check_in_time' => $model->actual_start_time,
            ], 'เช็คอินสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการเช็คอิน', 500);
    }

    /**
     * Check-out for a booking
     * POST /api/v1/bookings/{id}/check-out
     *
     * @param int $id
     * @return array
     */
    public function actionCheckOut($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ($model->user_id !== Yii::$app->user->id) {
            return $this->error('คุณไม่มีสิทธิ์เช็คเอาท์การจองนี้', 403);
        }

        if (!$model->actual_start_time) {
            return $this->error('กรุณาเช็คอินก่อนเช็คเอาท์', 400);
        }

        if ($model->checkOut()) {
            return $this->success([
                'booking' => $this->formatBooking($model),
                'check_out_time' => $model->actual_end_time,
            ], 'เช็คเอาท์สำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการเช็คเอาท์', 500);
    }

    /**
     * Get booking statistics
     * GET /api/v1/bookings/statistics
     *
     * @return array
     */
    public function actionStatistics()
    {
        $userId = Yii::$app->user->id;
        $today = date('Y-m-d');

        $stats = [
            'total' => Booking::find()->where(['user_id' => $userId])->count(),
            'pending' => Booking::find()->where(['user_id' => $userId, 'status' => Booking::STATUS_PENDING])->count(),
            'approved' => Booking::find()
                ->where(['user_id' => $userId, 'status' => Booking::STATUS_APPROVED])
                ->andWhere(['>=', 'booking_date', $today])
                ->count(),
            'completed' => Booking::find()->where(['user_id' => $userId, 'status' => Booking::STATUS_COMPLETED])->count(),
            'cancelled' => Booking::find()->where(['user_id' => $userId, 'status' => Booking::STATUS_CANCELLED])->count(),
        ];

        // Monthly data for last 6 months
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['user_id' => $userId])
                ->andWhere(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyData[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        return $this->success([
            'statistics' => $stats,
            'monthly_data' => $monthlyData,
        ]);
    }

    /**
     * Find booking model
     *
     * @param int $id
     * @return Booking
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบข้อมูลการจอง');
    }

    /**
     * Format booking data for API response
     *
     * @param Booking $booking
     * @param bool $detailed Include detailed information
     * @return array
     */
    protected function formatBooking($booking, $detailed = false)
    {
        $data = [
            'id' => $booking->id,
            'booking_code' => $booking->booking_code,
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
            'title' => $booking->title,
            'attendees_count' => $booking->attendees_count,
            'status' => $booking->status,
            'status_label' => $booking->getStatusLabel(),
            'room_cost' => $booking->room_cost,
            'equipment_cost' => $booking->equipment_cost,
            'total_cost' => $booking->total_cost,
            'created_at' => $booking->created_at,
        ];

        if ($detailed) {
            $data['description'] = $booking->description;
            $data['purpose'] = $booking->purpose;
            $data['notes'] = $booking->notes;
            $data['is_recurring'] = (bool)$booking->is_recurring;
            $data['recurrence_pattern'] = $booking->recurrence_pattern;
            $data['parent_booking_id'] = $booking->parent_booking_id;
            $data['approved_at'] = $booking->approved_at;
            $data['approved_by'] = $booking->approved_by;
            $data['cancellation_reason'] = $booking->cancellation_reason;
            $data['cancelled_at'] = $booking->cancelled_at;
            $data['actual_start_time'] = $booking->actual_start_time;
            $data['actual_end_time'] = $booking->actual_end_time;
            $data['payment_status'] = $booking->payment_status;
            $data['service_cost'] = $booking->service_cost;
            $data['qr_code_url'] = $booking->getQrCodeUrl();
        }

        return $data;
    }
}
