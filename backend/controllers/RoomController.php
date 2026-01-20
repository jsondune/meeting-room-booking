<?php
/**
 * RoomController - Backend controller for meeting room management
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use common\models\MeetingRoom;
use common\models\RoomImage;
use common\models\Building;
use common\models\Equipment;
use common\models\AuditLog;
use backend\models\RoomSearch;

/**
 * RoomController implements the CRUD actions for MeetingRoom model
 */
class RoomController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'calendar', 'available'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 'bulk-delete', 'toggle-status', 'upload-images', 'delete-image', 'set-primary-image', 'export'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Lists all MeetingRoom models
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new RoomSearch();
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
            'buildings' => Building::getDropdownList(),
            'statusLabels' => MeetingRoom::getStatusLabels(),
        ]);
    }

    /**
     * Displays a single MeetingRoom model
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get booking statistics
        $stats = [
            'totalBookings' => $model->getBookings()->count(),
            'thisMonthBookings' => $model->getBookings()
                ->where(['like', 'booking_date', date('Y-m')])
                ->count(),
            'avgDuration' => $model->getBookings()
                ->average('duration_minutes') ?? 0,
            'utilizationRate' => $this->calculateUtilizationRate($model),
        ];

        // Get upcoming bookings
        $upcomingBookings = $model->getBookings()
            ->where(['>=', 'booking_date', date('Y-m-d')])
            ->andWhere(['in', 'status', ['approved']])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->limit(10)
            ->all();

        return $this->render('view', [
            'model' => $model,
            'stats' => $stats,
            'upcomingBookings' => $upcomingBookings,
        ]);
    }

    /**
     * Calculate room utilization rate for current month
     * @param MeetingRoom $room
     * @return float
     */
    protected function calculateUtilizationRate($room)
    {
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        // Calculate total available hours
        $operatingHoursPerDay = (strtotime($room->operating_end_time) - strtotime($room->operating_start_time)) / 3600;
        $availableDays = explode(',', $room->available_days);
        
        $totalDays = 0;
        $current = strtotime($startDate);
        while ($current <= strtotime($endDate)) {
            if (in_array(date('w', $current), $availableDays)) {
                $totalDays++;
            }
            $current = strtotime('+1 day', $current);
        }
        
        $totalAvailableMinutes = $totalDays * $operatingHoursPerDay * 60;
        
        if ($totalAvailableMinutes <= 0) {
            return 0;
        }
        
        // Calculate used hours
        $usedMinutes = $room->getBookings()
            ->where(['>=', 'booking_date', $startDate])
            ->andWhere(['<=', 'booking_date', $endDate])
            ->andWhere(['in', 'status', ['approved', 'completed']])
            ->sum('duration_minutes') ?? 0;
        
        return round(($usedMinutes / $totalAvailableMinutes) * 100, 1);
    }

    /**
     * Creates a new MeetingRoom model
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new MeetingRoom();
        $model->status = MeetingRoom::STATUS_ACTIVE;
        $model->min_booking_duration = 30;
        $model->max_booking_duration = 480;
        $model->advance_booking_days = 30;
        $model->operating_start_time = '08:00:00';
        $model->operating_end_time = '17:00:00';
        $model->available_days = '1,2,3,4,5';

        if ($model->load(Yii::$app->request->post())) {
            // Handle image uploads
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            if ($model->save()) {
                // Upload images
                if ($model->imageFiles) {
                    $model->uploadImages();
                }
                
                // Save equipment
                $equipment = Yii::$app->request->post('equipment', []);
                if ($equipment) {
                    $model->saveEquipment($equipment);
                }

                $this->setFlash('success', 'สร้างห้องประชุมใหม่เรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'buildings' => Building::getDropdownList(),
            'equipment' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE])->all(),
            'roomTypes' => MeetingRoom::getRoomTypes(),
            'layoutTypes' => MeetingRoom::getLayoutTypes(),
        ]);
    }

    /**
     * Updates an existing MeetingRoom model
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldData = $model->attributes;

        if ($model->load(Yii::$app->request->post())) {
            // Handle image uploads
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            if ($model->save()) {
                // Upload images
                if ($model->imageFiles) {
                    $model->uploadImages();
                }
                
                // Save equipment
                $equipment = Yii::$app->request->post('equipment', []);
                $model->saveEquipment($equipment);

                $this->setFlash('success', 'อัปเดตข้อมูลห้องประชุมเรียบร้อยแล้ว');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        // Get current equipment
        $currentEquipment = ArrayHelper::map(
            $model->roomEquipment,
            'equipment_id',
            function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'is_included' => $item->is_included,
                ];
            }
        );

        return $this->render('update', [
            'model' => $model,
            'buildings' => Building::getDropdownList(),
            'equipment' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE])->all(),
            'currentEquipment' => $currentEquipment,
            'roomTypes' => MeetingRoom::getRoomTypes(),
            'layoutTypes' => MeetingRoom::getLayoutTypes(),
        ]);
    }

    /**
     * Deletes an existing MeetingRoom model (soft delete)
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Check for active bookings
        $activeBookings = $model->getBookings()
            ->where(['in', 'status', ['pending', 'approved']])
            ->andWhere(['>=', 'booking_date', date('Y-m-d')])
            ->count();
            
        if ($activeBookings > 0) {
            $this->setFlash('error', "ไม่สามารถลบห้องประชุมได้ เนื่องจากมีการจองที่ยังไม่เสร็จสิ้น {$activeBookings} รายการ");
            return $this->redirect(['index']);
        }
        
        $model->softDelete();
        $this->setFlash('success', 'ลบห้องประชุมเรียบร้อยแล้ว');

        return $this->redirect(['index']);
    }

    /**
     * Bulk delete rooms
     * @return \yii\web\Response
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('ids', []);
        
        if (empty($ids)) {
            $this->setFlash('error', 'กรุณาเลือกห้องประชุมที่ต้องการลบ');
            return $this->redirect(['index']);
        }

        $deleted = 0;
        $errors = 0;
        
        foreach ($ids as $id) {
            $model = MeetingRoom::findOne($id);
            if ($model) {
                // Check for active bookings
                $activeBookings = $model->getBookings()
                    ->where(['in', 'status', ['pending', 'approved']])
                    ->andWhere(['>=', 'booking_date', date('Y-m-d')])
                    ->count();
                    
                if ($activeBookings == 0) {
                    $model->softDelete();
                    $deleted++;
                } else {
                    $errors++;
                }
            }
        }

        if ($deleted > 0) {
            $this->setFlash('success', "ลบห้องประชุมเรียบร้อยแล้ว {$deleted} รายการ");
        }
        if ($errors > 0) {
            $this->setFlash('warning', "ไม่สามารถลบห้องประชุม {$errors} รายการ เนื่องจากมีการจองที่ยังไม่เสร็จสิ้น");
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle room status
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        
        if ($model->status == MeetingRoom::STATUS_ACTIVE) {
            $model->status = MeetingRoom::STATUS_INACTIVE;
            $message = 'ปิดการใช้งานห้องประชุมเรียบร้อยแล้ว';
        } else {
            $model->status = MeetingRoom::STATUS_ACTIVE;
            $message = 'เปิดการใช้งานห้องประชุมเรียบร้อยแล้ว';
        }
        
        $model->save(false, ['status', 'updated_at']);
        
        if ($this->isAjax()) {
            return $this->handleAjaxRequest(function () use ($model, $message) {
                return ['status' => $model->status, 'message' => $message];
            });
        }

        $this->setFlash('success', $message);
        return $this->redirect(['index']);
    }

    /**
     * Upload images for a room
     * @param int $id
     * @return array
     */
    public function actionUploadImages($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $model = $this->findModel($id);
        $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
        
        if ($model->imageFiles) {
            $uploaded = $model->uploadImages();
            return [
                'success' => true,
                'uploaded' => $uploaded,
                'message' => "อัปโหลดรูปภาพเรียบร้อยแล้ว {$uploaded} ไฟล์",
            ];
        }

        return [
            'success' => false,
            'message' => 'ไม่พบไฟล์รูปภาพ',
        ];
    }

    /**
     * Delete a room image
     * @param int $id Image ID
     * @return array
     */
    public function actionDeleteImage($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $image = RoomImage::findOne($id);
        
        if (!$image) {
            return ['success' => false, 'message' => 'ไม่พบรูปภาพ'];
        }

        // Delete file
        $filePath = Yii::getAlias('@uploads') . '/rooms/' . $image->room_id . '/' . $image->filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $roomId = $image->room_id;
        $image->delete();
        
        // If was primary, set another as primary
        if ($image->is_primary) {
            $newPrimary = RoomImage::find()
                ->where(['room_id' => $roomId])
                ->orderBy(['sort_order' => SORT_ASC])
                ->one();
            if ($newPrimary) {
                $newPrimary->is_primary = true;
                $newPrimary->save(false);
            }
        }

        return ['success' => true, 'message' => 'ลบรูปภาพเรียบร้อยแล้ว'];
    }

    /**
     * Set primary image
     * @param int $id Image ID
     * @return array
     */
    public function actionSetPrimaryImage($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $image = RoomImage::findOne($id);
        
        if (!$image) {
            return ['success' => false, 'message' => 'ไม่พบรูปภาพ'];
        }

        // Remove current primary
        RoomImage::updateAll(['is_primary' => false], ['room_id' => $image->room_id]);
        
        // Set new primary
        $image->is_primary = true;
        $image->save(false);

        return ['success' => true, 'message' => 'ตั้งเป็นรูปหลักเรียบร้อยแล้ว'];
    }

    /**
     * Room calendar view
     * @return string
     */
    public function actionCalendar()
    {
        $rooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->orderBy(['building_id' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        $date = Yii::$app->request->get('date', date('Y-m-d'));
        
        // Get bookings for the week
        $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week', strtotime($date)));
        
        $bookings = \common\models\Booking::find()
            ->where(['>=', 'booking_date', $startOfWeek])
            ->andWhere(['<=', 'booking_date', $endOfWeek])
            ->andWhere(['in', 'status', ['pending', 'approved']])
            ->with(['room', 'user'])
            ->all();

        // Organize bookings by room and date
        $calendar = [];
        foreach ($rooms as $room) {
            $calendar[$room->id] = [
                'room' => $room,
                'bookings' => [],
            ];
        }
        
        foreach ($bookings as $booking) {
            if (isset($calendar[$booking->room_id])) {
                $calendar[$booking->room_id]['bookings'][] = $booking;
            }
        }

        return $this->render('calendar', [
            'calendar' => $calendar,
            'startOfWeek' => $startOfWeek,
            'endOfWeek' => $endOfWeek,
            'currentDate' => $date,
        ]);
    }

    /**
     * Get available rooms for a time slot
     * @return array
     */
    public function actionAvailable()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $date = Yii::$app->request->get('date');
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');
        $capacity = Yii::$app->request->get('capacity', 1);
        $buildingId = Yii::$app->request->get('building_id');

        if (!$date || !$startTime || !$endTime) {
            return ['success' => false, 'message' => 'กรุณาระบุวันที่และเวลา'];
        }

        $query = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->andWhere(['>=', 'capacity', $capacity]);
            
        if ($buildingId) {
            $query->andWhere(['building_id' => $buildingId]);
        }
        
        $rooms = $query->all();
        $available = [];
        
        foreach ($rooms as $room) {
            if ($room->isAvailable($date, $startTime, $endTime)) {
                $available[] = [
                    'id' => $room->id,
                    'room_code' => $room->room_code,
                    'name_th' => $room->name_th,
                    'name_en' => $room->name_en,
                    'building' => $room->building->name_th ?? '',
                    'floor' => $room->floor,
                    'capacity' => $room->capacity,
                    'hourly_rate' => $room->hourly_rate,
                    'features' => $room->getFeaturesList(),
                    'primary_image' => $room->primaryImage ? $room->primaryImage->getUrl() : null,
                ];
            }
        }

        return [
            'success' => true,
            'rooms' => $available,
            'count' => count($available),
        ];
    }

    /**
     * Export rooms to CSV
     * @return void
     */
    public function actionExport()
    {
        $rooms = MeetingRoom::find()
            ->with(['building'])
            ->orderBy(['building_id' => SORT_ASC, 'room_code' => SORT_ASC])
            ->all();

        $columns = [
            'room_code' => ['label' => 'รหัสห้อง', 'attribute' => 'room_code'],
            'name_th' => ['label' => 'ชื่อห้อง (ไทย)', 'attribute' => 'name_th'],
            'name_en' => ['label' => 'ชื่อห้อง (อังกฤษ)', 'attribute' => 'name_en'],
            'building' => [
                'label' => 'อาคาร',
                'value' => function ($row) {
                    return $row->building->name_th ?? '';
                },
            ],
            'floor' => ['label' => 'ชั้น', 'attribute' => 'floor'],
            'room_number' => ['label' => 'หมายเลขห้อง', 'attribute' => 'room_number'],
            'capacity' => ['label' => 'ความจุ', 'attribute' => 'capacity'],
            'room_type' => ['label' => 'ประเภท', 'attribute' => 'room_type'],
            'hourly_rate' => ['label' => 'ราคา/ชั่วโมง', 'attribute' => 'hourly_rate'],
            'status' => [
                'label' => 'สถานะ',
                'value' => function ($row) {
                    return MeetingRoom::getStatusLabels()[$row->status] ?? '';
                },
            ],
        ];

        $data = array_map(function ($room) {
            return $room->attributes + ['building' => $room->building];
        }, $rooms);

        $this->exportToCsv($data, $columns, 'meeting_rooms_' . date('Y-m-d') . '.csv');
    }

    /**
     * Finds the MeetingRoom model based on its primary key value
     * @param int $id
     * @return MeetingRoom
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = MeetingRoom::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบห้องประชุมที่ระบุ');
    }
}
