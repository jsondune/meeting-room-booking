<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Booking;
use common\models\BookingEquipment;
use common\models\BookingAttendee;
use common\models\MeetingRoom;
use common\models\Equipment;
use common\models\User;

/**
 * BookingController - Create and manage bookings
 */
class BookingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'cancel', 'my-bookings', 
                                     'check-in', 'check-out', 'print', 'ical', 'equipment-request'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['confirm'],
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'cancel' => ['post'],
                    'check-in' => ['post'],
                    'check-out' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists user's bookings.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->redirect(['my-bookings']);
    }

    /**
     * Lists current user's bookings
     *
     * @return string
     */
    public function actionMyBookings()
    {
        $userId = Yii::$app->user->id;
        $status = Yii::$app->request->get('status');
        $dateRange = Yii::$app->request->get('date_range', 'upcoming');

        $query = Booking::find()
            ->where(['user_id' => $userId]);

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
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'booking_date' => SORT_DESC,
                    'start_time' => SORT_DESC,
                ],
            ],
        ]);

        // Get statistics
        $stats = [
            'total' => Booking::find()->where(['user_id' => $userId])->count(),
            'pending' => Booking::find()->where(['user_id' => $userId, 'status' => Booking::STATUS_PENDING])->count(),
            'approved' => Booking::find()
                ->where(['user_id' => $userId, 'status' => Booking::STATUS_APPROVED])
                ->andWhere(['>=', 'booking_date', $today])
                ->count(),
            'completed' => Booking::find()->where(['user_id' => $userId, 'status' => Booking::STATUS_COMPLETED])->count(),
        ];

        return $this->render('my-bookings', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'dateRange' => $dateRange,
            'stats' => $stats,
        ]);
    }

    /**
     * Displays a single Booking model.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException|ForbiddenHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Check permission - use int cast for user_id comparison (int vs string)
        if ((int)$model->user_id !== (int)Yii::$app->user->id && !Yii::$app->user->can('viewAllBookings')) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าถึงข้อมูลการจองนี้');
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

        return $this->render('view', [
            'model' => $model,
            'attendees' => $attendees,
            'equipmentRequests' => $equipmentRequests,
        ]);
    }

    /**
     * Creates a new Booking.
     *
     * @param int|null $room_id Pre-selected room
     * @return string|Response
     */
    public function actionCreate($room_id = null)
    {
        $model = new Booking();
        $model->user_id = Yii::$app->user->id;
        $model->department_id = Yii::$app->user->identity->department_id;
        $model->status = Booking::STATUS_PENDING;

        // Pre-fill room if specified
        if ($room_id) {
            $room = MeetingRoom::findOne(['id' => $room_id, 'status' => MeetingRoom::STATUS_ACTIVE]);
            if ($room) {
                $model->room_id = $room_id;
            }
        }

        // Pre-fill date/time if specified
        $model->booking_date = Yii::$app->request->get('date', date('Y-m-d', strtotime('+1 day')));
        $model->start_time = Yii::$app->request->get('start_time', '09:00');
        $model->end_time = Yii::$app->request->get('end_time', '10:00');

        if ($model->load(Yii::$app->request->post())) {
            // Get attendees and equipment from POST
            $attendeesData = Yii::$app->request->post('attendees', []);
            $equipmentData = Yii::$app->request->post('equipment', []);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Calculate costs
                $model->calculateCosts();

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

                    Yii::$app->session->setFlash('success', 'สร้างการจองสำเร็จ รหัสการจอง: ' . $model->booking_code);
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    // Show validation errors
                    $errors = $model->getErrors();
                    $errorMsg = [];
                    foreach ($errors as $field => $messages) {
                        $errorMsg[] = $model->getAttributeLabel($field) . ': ' . implode(', ', $messages);
                    }
                    Yii::$app->session->setFlash('error', 'ไม่สามารถบันทึกได้: ' . implode(' | ', $errorMsg));
                }

                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        // Get available rooms (full model objects for display)
        $rooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->with('building')
            ->orderBy(['building_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        // Get available equipment
        $equipment = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->andWhere(['is_portable' => 1])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'rooms' => $rooms,
            'equipment' => $equipment,
        ]);
    }

    /**
     * Updates an existing Booking.
     *
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException|ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์แก้ไขการจองนี้');
        }

        // Check if booking can be edited
        if (!$model->canBeEdited()) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถแก้ไขการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        $oldStatus = $model->status;

        if ($model->load(Yii::$app->request->post())) {
            $attendeesData = Yii::$app->request->post('attendees', []);
            $equipmentData = Yii::$app->request->post('equipment', []);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Recalculate costs
                $model->calculateCosts();

                // Reset status to pending if significant changes
                if ($model->isAttributeChanged('room_id') || 
                    $model->isAttributeChanged('booking_date') ||
                    $model->isAttributeChanged('start_time') ||
                    $model->isAttributeChanged('end_time')) {
                    
                    $room = $model->room;
                    if ($room && $room->requires_approval) {
                        $model->status = Booking::STATUS_PENDING;
                    }
                }

                if ($model->save()) {
                    // Update attendees
                    BookingAttendee::deleteAll(['booking_id' => $id]);
                    if (!empty($attendeesData)) {
                        $model->saveAttendees($attendeesData);
                    }

                    // Update equipment requests
                    BookingEquipment::deleteAll(['booking_id' => $id]);
                    if (!empty($equipmentData)) {
                        $model->saveEquipmentRequests($equipmentData);
                    }

                    $transaction->commit();

                    Yii::$app->session->setFlash('success', 'อัปเดตการจองสำเร็จ');
                    return $this->redirect(['view', 'id' => $model->id]);
                }

                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        // Get existing attendees
        $attendees = BookingAttendee::find()
            ->where(['booking_id' => $id])
            ->all();

        // Get existing equipment requests
        $equipmentRequests = BookingEquipment::find()
            ->where(['booking_id' => $id])
            ->all();

        // Get available rooms
        $rooms = MeetingRoom::getDropdownList();

        // Get available equipment
        $equipment = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->andWhere(['is_portable' => 1])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'rooms' => $rooms,
            'equipment' => $equipment,
            'attendees' => $attendees,
            'equipmentRequests' => $equipmentRequests,
        ]);
    }

    /**
     * Cancels an existing Booking.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException|ForbiddenHttpException
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id && !Yii::$app->user->can('cancelAnyBooking')) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์ยกเลิกการจองนี้');
        }

        // Check if booking can be cancelled
        if (!$model->canBeCancelled()) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยกเลิกการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        $reason = Yii::$app->request->post('cancellation_reason', 'ยกเลิกโดยผู้จอง');

        if ($model->cancel($reason, Yii::$app->user->id)) {
            Yii::$app->session->setFlash('success', 'ยกเลิกการจองสำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการยกเลิกการจอง');
        }

        return $this->redirect(['my-bookings']);
    }

    /**
     * Check-in for a booking
     *
     * @param int $id
     * @return Response
     */
    public function actionCheckIn($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id) {
            return [
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เช็คอินการจองนี้',
            ];
        }

        // Check if booking can be checked in
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');

        if ($model->booking_date !== $today) {
            return [
                'success' => false,
                'message' => 'สามารถเช็คอินได้เฉพาะวันที่จองเท่านั้น',
            ];
        }

        if ($model->status !== Booking::STATUS_APPROVED) {
            return [
                'success' => false,
                'message' => 'การจองต้องได้รับการอนุมัติก่อนเช็คอิน',
            ];
        }

        // Allow check-in 15 minutes before start time
        $allowedCheckIn = date('H:i:s', strtotime($model->start_time . ' -15 minutes'));
        if ($currentTime < $allowedCheckIn) {
            return [
                'success' => false,
                'message' => 'สามารถเช็คอินได้ก่อนเวลาเริ่ม 15 นาที',
            ];
        }

        if ($model->checkIn()) {
            return [
                'success' => true,
                'message' => 'เช็คอินสำเร็จ',
                'check_in_time' => $model->actual_start_time,
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการเช็คอิน',
        ];
    }

    /**
     * Check-out for a booking
     *
     * @param int $id
     * @return Response
     */
    public function actionCheckOut($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id) {
            return [
                'success' => false,
                'message' => 'คุณไม่มีสิทธิ์เช็คเอาท์การจองนี้',
            ];
        }

        if (!$model->actual_start_time) {
            return [
                'success' => false,
                'message' => 'โปรดเช็คอินก่อนเช็คเอาท์',
            ];
        }

        if ($model->checkOut()) {
            return [
                'success' => true,
                'message' => 'เช็คเอาท์สำเร็จ',
                'check_out_time' => $model->actual_end_time,
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการเช็คเอาท์',
        ];
    }

    /**
     * Print booking confirmation
     *
     * @param int $id
     * @return string
     */
    public function actionPrint($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id && !Yii::$app->user->can('viewAllBookings')) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าถึงข้อมูลการจองนี้');
        }

        $this->layout = 'print';

        $attendees = BookingAttendee::find()
            ->where(['booking_id' => $id])
            ->all();

        $equipmentRequests = BookingEquipment::find()
            ->where(['booking_id' => $id])
            ->with('equipment')
            ->all();

        return $this->render('print', [
            'model' => $model,
            'attendees' => $attendees,
            'equipmentRequests' => $equipmentRequests,
        ]);
    }

    /**
     * Export booking to iCal format
     *
     * @param int $id
     * @return Response
     */
    public function actionIcal($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id && !Yii::$app->user->can('viewAllBookings')) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์เข้าถึงข้อมูลการจองนี้');
        }

        $filename = 'booking-' . $model->booking_code . '.ics';

        $start = new \DateTime($model->booking_date . ' ' . $model->start_time);
        $end = new \DateTime($model->booking_date . ' ' . $model->end_time);

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//Meeting Room Booking System//EN\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:" . $model->booking_code . "@" . Yii::$app->request->serverName . "\r\n";
        $ical .= "DTSTART:" . $start->format('Ymd\THis') . "\r\n";
        $ical .= "DTEND:" . $end->format('Ymd\THis') . "\r\n";
        $ical .= "SUMMARY:" . $this->escapeIcal($model->title) . "\r\n";
        $ical .= "DESCRIPTION:" . $this->escapeIcal($model->description ?? '') . "\r\n";
        $ical .= "LOCATION:" . $this->escapeIcal($model->room->name_th . ' - ' . $model->room->building->name_th) . "\r\n";
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";

        return Yii::$app->response->sendContentAsFile(
            $ical,
            $filename,
            ['mimeType' => 'text/calendar']
        );
    }

    /**
     * Request additional equipment for a booking
     *
     * @param int $id
     * @return string|Response
     */
    public function actionEquipmentRequest($id)
    {
        $model = $this->findModel($id);

        // Check permission
        if ((int)$model->user_id !== (int)Yii::$app->user->id) {
            throw new ForbiddenHttpException('คุณไม่มีสิทธิ์แก้ไขการจองนี้');
        }

        // Check if booking allows equipment changes
        if (!$model->canBeEdited()) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถเปลี่ยนแปลงอุปกรณ์ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $equipmentData = Yii::$app->request->post('equipment', []);

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Remove existing equipment requests
                BookingEquipment::deleteAll(['booking_id' => $id]);

                // Add new equipment requests
                if (!empty($equipmentData)) {
                    $model->saveEquipmentRequests($equipmentData);
                }

                // Recalculate costs
                $model->calculateCosts();
                $model->save(false, ['equipment_cost', 'total_cost']);

                $transaction->commit();

                Yii::$app->session->setFlash('success', 'อัปเดตรายการอุปกรณ์สำเร็จ');
                return $this->redirect(['view', 'id' => $id]);
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        // Get existing equipment requests
        $existingEquipment = BookingEquipment::find()
            ->where(['booking_id' => $id])
            ->indexBy('equipment_id')
            ->all();

        // Get available equipment
        $availableEquipment = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->andWhere(['is_portable' => 1])
            ->orderBy(['category_id' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        return $this->render('equipment-request', [
            'model' => $model,
            'existingEquipment' => $existingEquipment,
            'availableEquipment' => $availableEquipment,
        ]);
    }

    /**
     * Booking confirmation page (for email links)
     *
     * @param string $code Booking code
     * @param string $token Confirmation token
     * @return string
     */
    public function actionConfirm($code, $token)
    {
        $model = Booking::findOne(['booking_code' => $code]);

        if (!$model) {
            throw new NotFoundHttpException('ไม่พบข้อมูลการจอง');
        }

        // Verify token
        if (!$model->confirmation_token || $model->confirmation_token !== $token) {
            throw new NotFoundHttpException('ลิงก์ยืนยันไม่ถูกต้อง');
        }

        return $this->render('confirm', [
            'model' => $model,
        ]);
    }

    /**
     * Escape string for iCal format
     *
     * @param string $text
     * @return string
     */
    protected function escapeIcal($text)
    {
        $text = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);
        return $text;
    }

    /**
     * Finds the Booking model based on its primary key value.
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
}
