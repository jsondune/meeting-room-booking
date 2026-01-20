<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\Equipment;
use common\models\EquipmentCategory;
use common\models\EquipmentMaintenance;
use common\models\BookingEquipment;
use common\models\MeetingRoom;

/**
 * EquipmentController - Backend equipment management
 */
class EquipmentController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'categories', 'maintenance', 'search'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 
                             'create-category', 'update-category', 'delete-category',
                             'add-maintenance', 'complete-maintenance',
                             'assign-room', 'unassign-room', 'toggle-status', 'bulk-action',
                             'export', 'import'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbActions()
    {
        return [
            'delete' => ['post'],
            'delete-category' => ['post'],
            'toggle-status' => ['post'],
            'bulk-action' => ['post'],
            'assign-room' => ['post'],
            'unassign-room' => ['post'],
            'complete-maintenance' => ['post'],
        ];
    }

    /**
     * Lists all Equipment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Equipment::find()->with(['category', 'rooms']);

        // Apply filters
        $categoryId = Yii::$app->request->get('category_id');
        $status = Yii::$app->request->get('status');
        $roomId = Yii::$app->request->get('room_id');
        $isPortable = Yii::$app->request->get('is_portable');
        $keyword = Yii::$app->request->get('keyword');

        if ($categoryId) {
            $query->andWhere(['category_id' => $categoryId]);
        }
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }
        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }
        if ($isPortable !== null && $isPortable !== '') {
            $query->andWhere(['is_portable' => $isPortable]);
        }
        if ($keyword) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $keyword],
                ['like', 'name_en', $keyword],
                ['like', 'equipment_code', $keyword],
                ['like', 'serial_number', $keyword],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['name_th' => SORT_ASC],
            ],
        ]);

        // Get filter options
        $categories = EquipmentCategory::getDropdownList();
        $rooms = MeetingRoom::getDropdownList();

        // Get statistics
        $stats = [
            'total' => Equipment::find()->count(),
            'available' => Equipment::find()->where(['status' => Equipment::STATUS_AVAILABLE])->count(),
            'in_use' => Equipment::find()->where(['status' => Equipment::STATUS_IN_USE])->count(),
            'maintenance' => Equipment::find()->where(['status' => Equipment::STATUS_MAINTENANCE])->count(),
            'retired' => Equipment::find()->where(['status' => Equipment::STATUS_RETIRED])->count(),
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'categories' => $categories,
            'rooms' => $rooms,
            'stats' => $stats,
            'filters' => [
                'category_id' => $categoryId,
                'status' => $status,
                'room_id' => $roomId,
                'is_portable' => $isPortable,
                'keyword' => $keyword,
            ],
        ]);
    }

    /**
     * Displays a single Equipment model.
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get maintenance history
        $maintenanceHistory = EquipmentMaintenance::find()
            ->where(['equipment_id' => $id])
            ->orderBy(['scheduled_date' => SORT_DESC])
            ->limit(10)
            ->all();

        // Get usage statistics
        $usageStats = BookingEquipment::find()
            ->select(['COUNT(*) as count', 'SUM(quantity) as total_quantity'])
            ->where(['equipment_id' => $id])
            ->asArray()
            ->one();

        // Get recent bookings
        $recentBookings = BookingEquipment::find()
            ->where(['equipment_id' => $id])
            ->with('booking')
            ->orderBy(['id' => SORT_DESC])
            ->limit(10)
            ->all();

        return $this->render('view', [
            'model' => $model,
            'maintenanceHistory' => $maintenanceHistory,
            'usageStats' => $usageStats,
            'recentBookings' => $recentBookings,
        ]);
    }

    /**
     * Creates a new Equipment model.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Equipment();
        $model->status = Equipment::STATUS_AVAILABLE;

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($imageFile) {
                $imagePath = $model->uploadImage($imageFile);
                if ($imagePath) {
                    $model->image = $imagePath;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'เพิ่มอุปกรณ์สำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $categories = EquipmentCategory::getDropdownList();
        $rooms = MeetingRoom::getDropdownList();

        return $this->render('create', [
            'model' => $model,
            'categories' => $categories,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Updates an existing Equipment model.
     *
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Handle remove image request
            $removeImage = Yii::$app->request->post('Equipment')['removeImage'] ?? 0;
            if ($removeImage == 1) {
                $model->deleteImage();
            }

            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($imageFile) {
                $imagePath = $model->uploadImage($imageFile);
                if ($imagePath) {
                    $model->image = $imagePath;
                }
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'บันทึกข้อมูลอุปกรณ์สำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $categories = EquipmentCategory::getDropdownList();
        $rooms = MeetingRoom::getDropdownList();

        return $this->render('update', [
            'model' => $model,
            'categories' => $categories,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Deletes an existing Equipment model.
     *
     * @param int $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Check if equipment is being used
        $bookingCount = BookingEquipment::find()
            ->where(['equipment_id' => $id])
            ->count();

        if ($bookingCount > 0) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบอุปกรณ์ได้ เนื่องจากมีการใช้งานในการจอง');
            return $this->redirect(['index']);
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'ลบอุปกรณ์สำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบอุปกรณ์ได้');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle equipment status
     *
     * @param int $id
     * @return Response
     */
    public function actionToggleStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status');

        if (!in_array($newStatus, [Equipment::STATUS_AVAILABLE, Equipment::STATUS_IN_USE, 
                                    Equipment::STATUS_MAINTENANCE, Equipment::STATUS_RETIRED])) {
            return ['success' => false, 'message' => 'สถานะไม่ถูกต้อง'];
        }

        $model->status = $newStatus;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'อัปเดตสถานะสำเร็จ',
                'status' => $model->status,
                'statusLabel' => $model->getStatusLabel(),
            ];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตสถานะ'];
    }

    /**
     * Assign equipment to room
     *
     * @return Response
     */
    public function actionAssignRoom()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $equipmentId = Yii::$app->request->post('equipment_id');
        $roomId = Yii::$app->request->post('room_id');

        $model = $this->findModel($equipmentId);
        $model->room_id = $roomId;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'กำหนดห้องสำเร็จ',
            ];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];
    }

    /**
     * Unassign equipment from room
     *
     * @param int $id
     * @return Response
     */
    public function actionUnassignRoom($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $model->room_id = null;

        if ($model->save(false)) {
            return [
                'success' => true,
                'message' => 'ยกเลิกการกำหนดห้องสำเร็จ',
            ];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];
    }

    /**
     * Equipment categories management
     *
     * @return string
     */
    public function actionCategories()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EquipmentCategory::find(),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'name_th' => SORT_ASC],
            ],
        ]);

        $model = new EquipmentCategory();

        return $this->render('categories', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Create equipment category
     *
     * @return Response
     */
    public function actionCreateCategory()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EquipmentCategory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'เพิ่มหมวดหมู่สำเร็จ',
                'category' => [
                    'id' => $model->id,
                    'name_th' => $model->name_th,
                    'name_en' => $model->name_en,
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
        ];
    }

    /**
     * Update equipment category
     *
     * @param int $id
     * @return Response
     */
    public function actionUpdateCategory($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EquipmentCategory::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบหมวดหมู่'];
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'success' => true,
                'message' => 'บันทึกหมวดหมู่สำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
        ];
    }

    /**
     * Delete equipment category
     *
     * @param int $id
     * @return Response
     */
    public function actionDeleteCategory($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EquipmentCategory::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบหมวดหมู่'];
        }

        // Check if category has equipment
        $equipmentCount = Equipment::find()->where(['category_id' => $id])->count();
        if ($equipmentCount > 0) {
            return [
                'success' => false,
                'message' => 'ไม่สามารถลบหมวดหมู่ได้ เนื่องจากมีอุปกรณ์ในหมวดหมู่นี้',
            ];
        }

        if ($model->delete()) {
            return ['success' => true, 'message' => 'ลบหมวดหมู่สำเร็จ'];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบหมวดหมู่'];
    }

    /**
     * Equipment maintenance management
     *
     * @return string
     */
    public function actionMaintenance()
    {
        $status = Yii::$app->request->get('status', 'pending');

        $query = EquipmentMaintenance::find()->with(['equipment', 'performedBy']);

        if ($status === 'pending') {
            $query->andWhere(['status' => EquipmentMaintenance::STATUS_PENDING]);
        } elseif ($status === 'in_progress') {
            $query->andWhere(['status' => EquipmentMaintenance::STATUS_IN_PROGRESS]);
        } elseif ($status === 'completed') {
            $query->andWhere(['status' => EquipmentMaintenance::STATUS_COMPLETED]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['scheduled_date' => SORT_ASC],
            ],
        ]);

        // Get statistics
        $stats = [
            'pending' => EquipmentMaintenance::find()->where(['status' => EquipmentMaintenance::STATUS_PENDING])->count(),
            'in_progress' => EquipmentMaintenance::find()->where(['status' => EquipmentMaintenance::STATUS_IN_PROGRESS])->count(),
            'completed' => EquipmentMaintenance::find()->where(['status' => EquipmentMaintenance::STATUS_COMPLETED])->count(),
            'overdue' => EquipmentMaintenance::find()
                ->where(['status' => EquipmentMaintenance::STATUS_PENDING])
                ->andWhere(['<', 'scheduled_date', date('Y-m-d')])
                ->count(),
        ];

        $equipments = Equipment::getDropdownList();

        return $this->render('maintenance', [
            'dataProvider' => $dataProvider,
            'status' => $status,
            'stats' => $stats,
            'equipments' => $equipments,
        ]);
    }

    /**
     * Add maintenance record
     *
     * @return Response
     */
    public function actionAddMaintenance()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new EquipmentMaintenance();
        $model->status = EquipmentMaintenance::STATUS_PENDING;
        $model->created_by = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Update equipment status to maintenance
            $equipment = Equipment::findOne($model->equipment_id);
            if ($equipment) {
                $equipment->status = Equipment::STATUS_MAINTENANCE;
                $equipment->save(false);
            }

            return [
                'success' => true,
                'message' => 'เพิ่มรายการบำรุงรักษาสำเร็จ',
            ];
        }

        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . implode(', ', $model->getFirstErrors()),
        ];
    }

    /**
     * Complete maintenance
     *
     * @param int $id
     * @return Response
     */
    public function actionCompleteMaintenance($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = EquipmentMaintenance::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'ไม่พบรายการบำรุงรักษา'];
        }

        $model->status = EquipmentMaintenance::STATUS_COMPLETED;
        $model->completed_date = date('Y-m-d');
        $model->performed_by = Yii::$app->user->id;
        $model->notes = Yii::$app->request->post('notes', $model->notes);
        $model->actual_cost = Yii::$app->request->post('actual_cost', $model->estimated_cost);

        if ($model->save()) {
            // Update equipment status back to available
            $equipment = Equipment::findOne($model->equipment_id);
            if ($equipment) {
                $equipment->status = Equipment::STATUS_AVAILABLE;
                $equipment->last_maintenance_date = date('Y-m-d');
                $equipment->save(false);
            }

            return [
                'success' => true,
                'message' => 'บันทึกการบำรุงรักษาสำเร็จ',
            ];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];
    }

    /**
     * Bulk action
     *
     * @return Response
     */
    public function actionBulkAction()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $action = Yii::$app->request->post('action');
        $ids = Yii::$app->request->post('ids', []);

        if (empty($ids)) {
            return ['success' => false, 'message' => 'กรุณาเลือกอุปกรณ์'];
        }

        $count = 0;

        switch ($action) {
            case 'set_available':
                $count = Equipment::updateAll(
                    ['status' => Equipment::STATUS_AVAILABLE],
                    ['id' => $ids]
                );
                break;

            case 'set_maintenance':
                $count = Equipment::updateAll(
                    ['status' => Equipment::STATUS_MAINTENANCE],
                    ['id' => $ids]
                );
                break;

            case 'set_retired':
                $count = Equipment::updateAll(
                    ['status' => Equipment::STATUS_RETIRED],
                    ['id' => $ids]
                );
                break;

            case 'delete':
                // Check if any equipment is being used
                $usedCount = BookingEquipment::find()
                    ->where(['equipment_id' => $ids])
                    ->count();

                if ($usedCount > 0) {
                    return [
                        'success' => false,
                        'message' => 'ไม่สามารถลบอุปกรณ์บางรายการได้ เนื่องจากมีการใช้งานในการจอง',
                    ];
                }

                $count = Equipment::deleteAll(['id' => $ids]);
                break;

            default:
                return ['success' => false, 'message' => 'การดำเนินการไม่ถูกต้อง'];
        }

        return [
            'success' => true,
            'message' => "ดำเนินการสำเร็จ {$count} รายการ",
            'count' => $count,
        ];
    }

    /**
     * Export equipment list
     *
     * @return Response
     */
    public function actionExport()
    {
        $equipments = Equipment::find()
            ->with(['category', 'room'])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        $filename = 'equipment-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // BOM for UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($handle, [
            'รหัสอุปกรณ์', 'ชื่อ (TH)', 'ชื่อ (EN)', 'หมวดหมู่', 'ห้อง',
            'Serial Number', 'ราคา/ชม.', 'สถานะ', 'พกพาได้', 'วันที่ซื้อ'
        ]);

        foreach ($equipments as $equipment) {
            fputcsv($handle, [
                $equipment->equipment_code,
                $equipment->name_th,
                $equipment->name_en,
                $equipment->category ? $equipment->category->name_th : '',
                $equipment->room ? $equipment->room->name_th : '',
                $equipment->serial_number,
                $equipment->hourly_rate,
                $equipment->getStatusLabel(),
                $equipment->is_portable ? 'ใช่' : 'ไม่',
                $equipment->purchase_date,
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Yii::$app->response->sendContentAsFile($content, $filename, [
            'mimeType' => 'text/csv',
        ]);
    }

    /**
     * Search equipment (AJAX)
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

        $equipments = Equipment::find()
            ->where(['status' => Equipment::STATUS_AVAILABLE])
            ->andWhere([
                'or',
                ['like', 'name_th', $term],
                ['like', 'name_en', $term],
                ['like', 'equipment_code', $term],
            ])
            ->limit(10)
            ->all();

        $results = [];
        foreach ($equipments as $equipment) {
            $results[] = [
                'id' => $equipment->id,
                'value' => $equipment->name_th,
                'label' => $equipment->equipment_code . ' - ' . $equipment->name_th,
                'hourly_rate' => $equipment->hourly_rate,
            ];
        }

        return $results;
    }

    /**
     * Finds the Equipment model based on its primary key value.
     *
     * @param int $id
     * @return Equipment
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Equipment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบอุปกรณ์ที่ระบุ');
    }
}
