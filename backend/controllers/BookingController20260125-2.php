<?php
/**
 * BookingController - Backend controller for booking management
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\models\Booking;
use common\models\MeetingRoom;
use common\models\User;
use common\models\Equipment;
use common\models\AuditLog;
use common\models\Notification;
use backend\models\BookingSearch;

/**
 * BookingController implements the CRUD actions for Booking model
 */
class BookingController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'calendar', 'today', 'pending', 'my-bookings'],
                'allow' => true,
                'roles' => ['user', 'manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'cancel'],
                'allow' => true,
                'roles' => ['user', 'manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['approve', 'reject', 'bulk-approve', 'bulk-reject'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['delete', 'bulk-delete', 'export', 'report'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Lists all Booking models
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if ($this->isAjax()) {
            return $this->renderPartial('_grid', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'rooms' => MeetingRoom::getDropdownList(),
            'statusLabels' => Booking::getStatusLabels(),
        ]);
    }

    /**
     * Displays a single Booking model
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Booking
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Booking();
        $model->user_id = $this->getUserId();
        $model->department_id = $this->getUser()->department_id;
        $model->status = 'pending';
        $model->booking_type = 'internal';

        if ($model->load(Yii::$app->request->post())) {
            // Set attendees from request
            $attendees = Yii::$app->request->post('attendees', []);
            
            // Set equipment requests
            $equipment = Yii::$app->request->post('equipment', []);
            
            if ($model->save()) {
                // Save attendees
                if (!empty($attendees)) {
                    $model->saveAttendees($attendees);
                }
                
                // Save equipment requests
                if (!empty($equipment)) {
                    $model->saveEquipmentRequests($equipment);
                }
                
                // Calculate and save costs
                $model->calculateCosts();
                
                // Send confirmation
                $model->sendBookingConfirmation();
                
                // Handle recurring bookings
                if ($model->is_recurring && $model->recurring_pattern) {
                    $count = $model->createRecurringBookings();
                    $this->setFlash('info', "สร้างการจองซ้ำอีก {$count} รายการ");
                }

                $this->setFlash('success', 'สร้างการจองเรียบร้อยแล้ว รหัสการจอง: ' . $model->booking_code);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Pre-fill from request params
        $roomId = Yii::$app->request->get('room_id');
        $date = Yii::$app->request->get('date');
        $startTime = Yii::$app->request->get('start_time');
        
        if ($roomId) {
            $model->room_id = $roomId;
        }
        if ($date) {
            $model->booking_date = $date;
        }
        if ($startTime) {
            $model->start_time = $startTime;
        }

        return $this->render('create', [
            'model' => $model,
            'rooms' => MeetingRoom::findActive()->all(),
            'equipment' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE, 'is_portable' => true])->all(),
            'users' => User::find()->where(['status' => User::STATUS_ACTIVE])->all(),
        ]);
    }

    /**
     * Updates an existing Booking model
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        // Check if can be edited
        if (!$model->canBeEdited()) {
            $this->setFlash('error', 'ไม่สามารถแก้ไขการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check permission
        if (!$this->canEditBooking($model)) {
            $this->setFlash('error', 'คุณไม่มีสิทธิ์แก้ไขการจองนี้');
            return $this->redirect(['view', 'id' => $id]);
        }

        $oldStatus = $model->status;

        if ($model->load(Yii::$app->request->post())) {
            // Set attendees from request
            $attendees = Yii::$app->request->post('attendees', []);
            
            // Set equipment requests
            $equipment = Yii::$app->request->post('equipment', []);
            
            if ($model->save()) {
                // Save attendees
                $model->saveAttendees($attendees);
                
                // Save equipment requests
                $model->saveEquipmentRequests($equipment);
                
                // Recalculate costs
                $model->calculateCosts();
                
                // Notify if status changed
                if ($oldStatus !== $model->status) {
                    $model->sendStatusNotification($oldStatus);
                }

                $this->setFlash('success', 'อัปเดตการจองเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'rooms' => MeetingRoom::findActive()->all(),
            'equipment' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE, 'is_portable' => true])->all(),
            'users' => User::find()->where(['status' => User::STATUS_ACTIVE])->all(),
            'currentAttendees' => $model->attendees,
            'currentEquipment' => $model->bookingEquipment,
        ]);
    }

    /**
     * Check if current user can edit booking
     * @param Booking $booking
     * @return bool
     */
    protected function canEditBooking($booking)
    {
        $user = $this->getUser();
        
        // Owner can edit
        if ($booking->user_id == $user->id) {
            return true;
        }
        
        // Admin and superadmin can edit all
        if ($user->hasRole('admin') || $user->hasRole('superadmin')) {
            return true;
        }
        
        // Manager can edit bookings in their department
        if ($user->hasRole('manager') && $booking->department_id == $user->department_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Approve a booking
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status !== 'pending') {
            $this->setFlash('error', 'ไม่สามารถอนุมัติการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->approve($this->getUserId());
        $this->setFlash('success', 'อนุมัติการจองเรียบร้อยแล้ว');

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Reject a booking
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status !== 'pending') {
            $this->setFlash('error', 'ไม่สามารถปฏิเสธการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $reason = Yii::$app->request->post('reason', '');
            $model->reject($reason, $this->getUserId());
            $this->setFlash('success', 'ปฏิเสธการจองเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('reject', [
            'model' => $model,
        ]);
    }

    /**
     * Cancel a booking
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionCancel($id)
    {
        $model = $this->findModel($id);
        
        if (!$model->canBeCancelled()) {
            $this->setFlash('error', 'ไม่สามารถยกเลิกการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check permission
        if (!$this->canEditBooking($model)) {
            $this->setFlash('error', 'คุณไม่มีสิทธิ์ยกเลิกการจองนี้');
            return $this->redirect(['view', 'id' => $id]);
        }

        if (Yii::$app->request->isPost) {
            $reason = Yii::$app->request->post('reason', '');
            $model->cancel($reason, $this->getUserId());
            $this->setFlash('success', 'ยกเลิกการจองเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('cancel', [
            'model' => $model,
        ]);
    }

    /**
     * Bulk approve bookings
     * @return \yii\web\Response
     */
    public function actionBulkApprove()
    {
        $ids = Yii::$app->request->post('ids', []);
        
        if (empty($ids)) {
            $this->setFlash('error', 'โปรดเลือกการจองที่ต้องการอนุมัติ');
            return $this->redirect(['index']);
        }

        $approved = 0;
        foreach ($ids as $id) {
            $model = Booking::findOne($id);
            if ($model && $model->status === 'pending') {
                $model->approve($this->getUserId());
                $approved++;
            }
        }

        $this->setFlash('success', "อนุมัติการจองเรียบร้อยแล้ว {$approved} รายการ");
        return $this->redirect(['index']);
    }

    /**
     * Bulk reject bookings
     * @return \yii\web\Response
     */
    public function actionBulkReject()
    {
        $ids = Yii::$app->request->post('ids', []);
        $reason = Yii::$app->request->post('reason', 'ปฏิเสธโดยผู้ดูแลระบบ');
        
        if (empty($ids)) {
            $this->setFlash('error', 'โปรดเลือกการจองที่ต้องการปฏิเสธ');
            return $this->redirect(['index']);
        }

        $rejected = 0;
        foreach ($ids as $id) {
            $model = Booking::findOne($id);
            if ($model && $model->status === 'pending') {
                $model->reject($reason, $this->getUserId());
                $rejected++;
            }
        }

        $this->setFlash('success', "ปฏิเสธการจองเรียบร้อยแล้ว {$rejected} รายการ");
        return $this->redirect(['index']);
    }

    /**
     * Delete a booking
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        
        $this->setFlash('success', 'ลบการจองเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Bulk delete bookings
     * @return \yii\web\Response
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('ids', []);
        
        if (empty($ids)) {
            $this->setFlash('error', 'โปรดเลือกการจองที่ต้องการลบ');
            return $this->redirect(['index']);
        }

        $deleted = Booking::deleteAll(['id' => $ids]);
        $this->setFlash('success', "ลบการจองเรียบร้อยแล้ว {$deleted} รายการ");

        return $this->redirect(['index']);
    }

    /**
     * Today's bookings
     * @return string
     */
    public function actionToday()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Booking::find()
                ->where(['booking_date' => date('Y-m-d')])
                ->andWhere(['in', 'status', ['approved', 'pending']])
                ->with(['room', 'user'])
                ->orderBy(['start_time' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('today', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Pending bookings
     * @return string
     */
    public function actionPending()
    {
        $query = Booking::findPending()->with(['room', 'user', 'department']);
        
        // Filter by department for managers
        $user = $this->getUser();
        if ($user->hasRole('manager') && !$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $query->andWhere(['department_id' => $user->department_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['created_at' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('pending', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Current user's bookings
     * @return string
     */
    public function actionMyBookings()
    {
        $searchModel = new BookingSearch();
        $searchModel->user_id = $this->getUserId();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('my-bookings', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'rooms' => MeetingRoom::getDropdownList(),
            'statusLabels' => Booking::getStatusLabels(),
        ]);
    }

    /**
     * Calendar view of bookings
     * @return string
     */
    public function actionCalendar()
    {
        $month = Yii::$app->request->get('month', date('Y-m'));
        $roomId = Yii::$app->request->get('room_id');
        
        // Get 3 months range for better calendar navigation
        $startDate = date('Y-m-01', strtotime($month . '-01 -1 month'));
        $endDate = date('Y-m-t', strtotime($month . '-01 +1 month'));
        
        $query = Booking::find()
            ->where(['>=', 'booking_date', $startDate])
            ->andWhere(['<=', 'booking_date', $endDate])
            ->andWhere(['in', 'status', ['pending', 'approved', 'completed']])
            ->with(['room', 'user']);
            
        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }
        
        $bookings = $query->all();
        
        // Get holidays for full year
        $currentYear = date('Y', strtotime($month . '-01'));
        $holidayStartDate = $currentYear . '-01-01';
        $holidayEndDate = $currentYear . '-12-31';
        
        $holidays = \common\models\Holiday::find()
            ->where(['between', 'holiday_date', $holidayStartDate, $holidayEndDate])
            ->andWhere(['is_active' => true])
            ->orderBy(['holiday_date' => SORT_ASC])
            ->all();
        
        // Color mapping by room
        $colorArray = ['#3788d8', '#28a745', '#17a2b8', '#6f42c1', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'];
        $roomColors = [];
        $rooms = MeetingRoom::getDropdownList();
        $index = 0;
        foreach ($rooms as $rId => $rName) {
            $roomColors[$rId] = $colorArray[$index % count($colorArray)];
            $index++;
        }
        
        // Format booking events for calendar
        $events = [];
        foreach ($bookings as $booking) {
            $startTime = substr($booking->start_time, 0, 5);
            $endTime = substr($booking->end_time, 0, 5);
            
            $events[] = [
                'id' => $booking->id,
                'title' => $booking->title ?: ($booking->room->name_th ?? 'การจอง'),
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end' => $booking->booking_date . 'T' . $booking->end_time,
                'color' => $roomColors[$booking->room_id] ?? '#3788d8',
                'borderColor' => $this->getStatusBorderColor($booking->status),
                'extendedProps' => [
                    'booking_code' => $booking->booking_code,
                    'room' => $booking->room->name_th ?? '-',
                    'room_id' => $booking->room_id,
                    'user' => $booking->user->fullname ?? ($booking->user->full_name ?? '-'),
                    'status' => $booking->status,
                    'time' => $startTime . ' - ' . $endTime,
                    'viewUrl' => Yii::$app->urlManager->createUrl(['booking/view', 'id' => $booking->id]),
                    'type' => 'booking',
                ],
            ];
        }
        
        // Format holiday events
        $holidayEvents = [];
        $holidayDates = [];
        foreach ($holidays as $holiday) {
            // Background event
            $holidayEvents[] = [
                'id' => 'holiday-' . $holiday->id,
                'title' => $holiday->name_th,
                'start' => $holiday->holiday_date,
                'allDay' => true,
                'display' => 'background',
                'backgroundColor' => '#ffebee',
                'classNames' => ['holiday-event'],
                'extendedProps' => [
                    'type' => 'holiday',
                    'holiday_type' => $holiday->holiday_type,
                    'description' => $holiday->description,
                ],
            ];
            
            // Label event
            $events[] = [
                'id' => 'holiday-label-' . $holiday->id,
                'title' => '🔴 ' . $holiday->name_th,
                'start' => $holiday->holiday_date,
                'allDay' => true,
                'color' => '#dc3545',
                'textColor' => '#ffffff',
                'classNames' => ['holiday-label'],
                'extendedProps' => [
                    'type' => 'holiday',
                    'holiday_type' => $holiday->holiday_type,
                    'description' => $holiday->description,
                ],
            ];
            
            $holidayDates[$holiday->holiday_date] = [
                'name' => $holiday->name_th,
                'type' => $holiday->holiday_type,
            ];
        }

        if ($this->isAjax()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $events;
        }

        // Ensure rooms is always an array
        if (!is_array($rooms)) {
            $rooms = [];
        }

        return $this->render('calendar', [
            'events' => $events,
            'holidayEvents' => $holidayEvents,
            'holidayDates' => $holidayDates,
            'rooms' => $rooms,
            'roomColors' => $roomColors,
            'currentMonth' => $month,
            'currentRoomId' => $roomId,
        ]);
    }

    /**
     * Get border color for booking status
     * @param string $status
     * @return string
     */
    protected function getStatusBorderColor($status)
    {
        $colors = [
            'pending' => '#ffc107',    // Yellow
            'approved' => '#28a745',   // Green
            'rejected' => '#dc3545',   // Red
            'cancelled' => '#6c757d',  // Gray
            'completed' => '#17a2b8',  // Cyan
        ];
        
        return $colors[$status] ?? '#3788d8';
    }

    /**
     * Export bookings
     * @return void
     */
    public function actionExport()
    {
        $searchModel = new BookingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        
        $bookings = $dataProvider->getModels();

        $columns = [
            'booking_code' => ['label' => 'รหัสการจอง', 'attribute' => 'booking_code'],
            'room' => [
                'label' => 'ห้องประชุม',
                'value' => function ($row) {
                    return $row->room->name_th ?? '';
                },
            ],
            'title' => ['label' => 'หัวข้อการประชุม', 'attribute' => 'title'],
            'user' => [
                'label' => 'ผู้จอง',
                'value' => function ($row) {
                    return $row->user->full_name ?? '';
                },
            ],
            'booking_date' => ['label' => 'วันที่', 'attribute' => 'booking_date'],
            'start_time' => ['label' => 'เวลาเริ่ม', 'attribute' => 'start_time'],
            'end_time' => ['label' => 'เวลาสิ้นสุด', 'attribute' => 'end_time'],
            'attendee_count' => ['label' => 'จำนวนผู้เข้าร่วม', 'attribute' => 'attendee_count'],
            'status' => [
                'label' => 'สถานะ',
                'value' => function ($row) {
                    return Booking::getStatusLabels()[$row->status] ?? '';
                },
            ],
            'total_cost' => ['label' => 'ค่าใช้จ่ายรวม', 'attribute' => 'total_cost'],
        ];

        $data = array_map(function ($booking) {
            return $booking->attributes + [
                'room' => $booking->room,
                'user' => $booking->user,
            ];
        }, $bookings);

        $this->exportToCsv($data, $columns, 'bookings_' . date('Y-m-d') . '.csv');
    }

    /**
     * Booking reports
     * @return string
     */
    public function actionReport()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        $roomId = Yii::$app->request->get('room_id');
        $departmentId = Yii::$app->request->get('department_id');

        $query = Booking::find()
            ->where(['>=', 'booking_date', $dateFrom])
            ->andWhere(['<=', 'booking_date', $dateTo]);
            
        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }
        if ($departmentId) {
            $query->andWhere(['department_id' => $departmentId]);
        }

        // Summary statistics
        $stats = [
            'total' => (clone $query)->count(),
            'approved' => (clone $query)->andWhere(['status' => 'approved'])->count(),
            'pending' => (clone $query)->andWhere(['status' => 'pending'])->count(),
            'rejected' => (clone $query)->andWhere(['status' => 'rejected'])->count(),
            'cancelled' => (clone $query)->andWhere(['status' => 'cancelled'])->count(),
            'completed' => (clone $query)->andWhere(['status' => 'completed'])->count(),
            'totalHours' => (clone $query)
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->sum('duration_minutes') / 60 ?? 0,
            'totalCost' => (clone $query)
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->sum('total_cost') ?? 0,
        ];

        // By room
        $byRoom = (clone $query)
            ->select(['room_id', 'COUNT(*) as count', 'SUM(duration_minutes) as total_minutes'])
            ->andWhere(['in', 'status', ['approved', 'completed']])
            ->groupBy('room_id')
            ->with('room')
            ->asArray()
            ->all();

        // By department
        $byDepartment = (clone $query)
            ->select(['department_id', 'COUNT(*) as count', 'SUM(duration_minutes) as total_minutes'])
            ->andWhere(['in', 'status', ['approved', 'completed']])
            ->groupBy('department_id')
            ->asArray()
            ->all();

        // Daily trend
        $dailyTrend = (clone $query)
            ->select(['booking_date', 'COUNT(*) as count'])
            ->andWhere(['in', 'status', ['approved', 'completed']])
            ->groupBy('booking_date')
            ->orderBy(['booking_date' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('report', [
            'stats' => $stats,
            'byRoom' => $byRoom,
            'byDepartment' => $byDepartment,
            'dailyTrend' => $dailyTrend,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'rooms' => MeetingRoom::getDropdownList(),
            'departments' => \common\models\Department::getDropdownList(),
        ]);
    }

    /**
     * Finds the Booking model based on its primary key value
     * @param int $id
     * @return Booking
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Booking::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบการจองที่ระบุ');
    }
}
