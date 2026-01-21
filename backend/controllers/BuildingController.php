<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\Building;
use common\models\BuildingImage;
use common\models\MeetingRoom;
use common\models\AuditLog;

/**
 * BuildingController - Building management
 */
class BuildingController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 'bulk-action', 'toggle-status'],
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
            'bulk-action' => ['post'],
        ];
    }

    /**
     * Lists all Building models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Building::find();

        // Search filter
        $keyword = Yii::$app->request->get('keyword');
        if ($keyword) {
            $query->andWhere([
                'or',
                ['like', 'code', $keyword],
                ['like', 'name_th', $keyword],
                ['like', 'name_en', $keyword],
            ]);
        }

        // Status filter
        $status = Yii::$app->request->get('status');
        if ($status !== null && $status !== '') {
            $query->andWhere(['is_active' => $status]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'name_th' => SORT_ASC,
                ],
            ],
        ]);

        // Get statistics
        $stats = [
            'total' => Building::find()->count(),
            'active' => Building::find()->where(['is_active' => true])->count(),
            'inactive' => Building::find()->where(['is_active' => false])->count(),
            'total_rooms' => MeetingRoom::find()->count(),
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'stats' => $stats,
            'keyword' => $keyword,
            'status' => $status,
        ]);
    }

    /**
     * Displays a single Building model.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get rooms in this building
        $roomsDataProvider = new ActiveDataProvider([
            'query' => MeetingRoom::find()->where(['building_id' => $id]),
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'floor' => SORT_ASC,
                    'name_th' => SORT_ASC,
                ],
            ],
        ]);

        // Get room statistics
        $roomStats = [
            'total' => MeetingRoom::find()->where(['building_id' => $id])->count(),
            'active' => MeetingRoom::find()->where(['building_id' => $id, 'status' => MeetingRoom::STATUS_ACTIVE])->count(),
            'maintenance' => MeetingRoom::find()->where(['building_id' => $id, 'status' => MeetingRoom::STATUS_MAINTENANCE])->count(),
        ];

        // Get floor summary
        $floorSummary = MeetingRoom::find()
            ->select(['floor', 'COUNT(*) as room_count', 'SUM(capacity) as total_capacity'])
            ->where(['building_id' => $id])
            ->groupBy(['floor'])
            ->orderBy(['floor' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('view', [
            'model' => $model,
            'roomsDataProvider' => $roomsDataProvider,
            'roomStats' => $roomStats,
            'floorSummary' => $floorSummary,
        ]);
    }

    /**
     * Creates a new Building model.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Building();
        $model->is_active = true;
        $model->floor_count = 1;

        if ($model->load(Yii::$app->request->post())) {
            // Handle image upload
            $imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($imageFile) {
                $imagePath = $this->uploadImage($imageFile, 'building');
                if ($imagePath) {
                    $model->image = $imagePath;
                }
            }

            if ($model->save()) {
                AuditLog::log('create', Building::class, $model->id, [], $model->attributes, 'Created building: ' . $model->name_th);
                Yii::$app->session->setFlash('success', 'เพิ่มอาคารสำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Building model.
     *
     * @param int $id
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldValues = $model->attributes;

        if ($model->load(Yii::$app->request->post())) {
            // Handle delete images
            $deleteImages = Yii::$app->request->post('deleteImages', []);
            if (!empty($deleteImages)) {
                foreach ($deleteImages as $imageId) {
                    $image = BuildingImage::findOne(['id' => $imageId, 'building_id' => $model->id]);
                    if ($image) {
                        // Delete physical file
                        $filePath = Yii::getAlias('@backend/web/' . $image->file_path);
                        $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
                        if (file_exists($filePath)) {
                            @unlink($filePath);
                        }
                        $image->delete();
                    }
                }
                
                // After deleting, check if there's still a primary image
                $hasPrimary = BuildingImage::find()->where(['building_id' => $model->id, 'is_primary' => 1])->exists();
                if (!$hasPrimary) {
                    // Set first remaining image as primary
                    $firstImage = BuildingImage::find()->where(['building_id' => $model->id])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->one();
                    if ($firstImage) {
                        $firstImage->is_primary = 1;
                        $firstImage->save(false);
                    }
                }
            }

            // Handle set primary image
            $primaryImageId = Yii::$app->request->post('primaryImage');
            if ($primaryImageId) {
                // Check if the selected primary image still exists (not deleted)
                $primaryExists = BuildingImage::find()->where(['id' => $primaryImageId, 'building_id' => $model->id])->exists();
                if ($primaryExists) {
                    // Reset all to non-primary
                    BuildingImage::updateAll(['is_primary' => 0], ['building_id' => $model->id]);
                    // Set new primary
                    BuildingImage::updateAll(['is_primary' => 1], ['id' => $primaryImageId, 'building_id' => $model->id]);
                }
            }

            // Handle image uploads (max 5 total)
            $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
            
            if ($model->save()) {
                // Upload new images (check limit)
                if ($model->imageFiles && count($model->imageFiles) > 0) {
                    $existingCount = BuildingImage::find()->where(['building_id' => $model->id])->count();
                    $maxAllowed = 5 - $existingCount;
                    
                    if ($maxAllowed > 0) {
                        // Limit files to upload
                        $filesToUpload = array_slice($model->imageFiles, 0, $maxAllowed);
                        $model->imageFiles = $filesToUpload;
                        $model->uploadImages();
                    }
                }

                AuditLog::log('update', Building::class, $model->id, $oldValues, $model->attributes, 'Updated building: ' . $model->name_th);
                Yii::$app->session->setFlash('success', 'แก้ไขข้อมูลอาคารสำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'บันทึกไม่สำเร็จ กรุณาตรวจสอบข้อมูล');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Building model.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Check if building has rooms
        $roomCount = MeetingRoom::find()->where(['building_id' => $id])->count();
        if ($roomCount > 0) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบอาคารได้ เนื่องจากมีห้องประชุมในอาคารนี้ ' . $roomCount . ' ห้อง');
            return $this->redirect(['index']);
        }

        $oldValues = $model->attributes;
        $name = $model->name_th;

        // Delete image
        if ($model->image) {
            $this->deleteImage($model->image);
        }

        if ($model->delete()) {
            AuditLog::log('delete', Building::class, $id, $oldValues, [], 'Deleted building: ' . $name);
            Yii::$app->session->setFlash('success', 'ลบอาคารสำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการลบอาคาร');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle building active status
     *
     * @param int $id
     * @return Response
     */
    public function actionToggleStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $oldStatus = $model->is_active;
        $model->is_active = !$model->is_active;

        if ($model->save(false, ['is_active'])) {
            AuditLog::log(
                'update',
                Building::class,
                $model->id,
                ['is_active' => $oldStatus],
                ['is_active' => $model->is_active],
                ($model->is_active ? 'Activated' : 'Deactivated') . ' building: ' . $model->name_th
            );

            return [
                'success' => true,
                'message' => $model->is_active ? 'เปิดใช้งานอาคารสำเร็จ' : 'ปิดการใช้งานอาคารสำเร็จ',
                'is_active' => $model->is_active,
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
            return ['success' => false, 'message' => 'กรุณาเลือกรายการ'];
        }

        $count = 0;
        switch ($action) {
            case 'activate':
                $count = Building::updateAll(['is_active' => true], ['id' => $ids]);
                AuditLog::log('bulk_activate', Building::class, null, [], ['ids' => $ids], 'Bulk activated ' . $count . ' buildings');
                break;

            case 'deactivate':
                $count = Building::updateAll(['is_active' => false], ['id' => $ids]);
                AuditLog::log('bulk_deactivate', Building::class, null, [], ['ids' => $ids], 'Bulk deactivated ' . $count . ' buildings');
                break;

            case 'delete':
                // Check for rooms before delete
                foreach ($ids as $id) {
                    $roomCount = MeetingRoom::find()->where(['building_id' => $id])->count();
                    if ($roomCount > 0) {
                        return ['success' => false, 'message' => 'ไม่สามารถลบได้ มีอาคารที่มีห้องประชุมอยู่'];
                    }
                }
                $count = Building::deleteAll(['id' => $ids]);
                AuditLog::log('bulk_delete', Building::class, null, [], ['ids' => $ids], 'Bulk deleted ' . $count . ' buildings');
                break;

            default:
                return ['success' => false, 'message' => 'ไม่รู้จัก action'];
        }

        return [
            'success' => true,
            'message' => "ดำเนินการสำเร็จ {$count} รายการ",
            'affected' => $count,
        ];
    }

    /**
     * Update sort order via AJAX (disabled - no sort_order column)
     *
     * @return Response
     */
    public function actionUpdateSort()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Note: sort_order column not available in building table
        // Sorting is done by name_th instead
        
        return ['success' => true, 'message' => 'อัปเดตลำดับสำเร็จ'];
    }

    /**
     * Get rooms for building (AJAX)
     *
     * @param int $id
     * @return Response
     */
    public function actionGetRooms($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $rooms = MeetingRoom::find()
            ->where(['building_id' => $id])
            ->orderBy(['floor' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        $result = [];
        foreach ($rooms as $room) {
            $result[] = [
                'id' => $room->id,
                'room_code' => $room->room_code,
                'name_th' => $room->name_th,
                'floor' => $room->floor,
                'capacity' => $room->capacity,
                'status' => $room->status,
                'status_label' => $room->getStatusLabel(),
            ];
        }

        return ['success' => true, 'rooms' => $result];
    }

    /**
     * Export buildings to Excel
     *
     * @return Response
     */
    public function actionExport()
    {
        $buildings = Building::find()
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        // Create CSV content
        $content = "\xEF\xBB\xBF"; // UTF-8 BOM
        $content .= "รหัสอาคาร,ชื่ออาคาร (ไทย),ชื่ออาคาร (English),ที่อยู่,จำนวนชั้น,สถานะ,จำนวนห้องประชุม\n";

        foreach ($buildings as $building) {
            $roomCount = MeetingRoom::find()->where(['building_id' => $building->id])->count();
            $content .= sprintf(
                '"%s","%s","%s","%s",%d,"%s",%d' . "\n",
                $building->code,
                $building->name_th,
                $building->name_en ?? '',
                str_replace('"', '""', $building->address ?? ''),
                $building->floor_count,
                $building->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน',
                $roomCount
            );
        }

        AuditLog::log('export', Building::class, null, [], [], 'Exported buildings list');

        return Yii::$app->response->sendContentAsFile(
            $content,
            'buildings-' . date('Y-m-d') . '.csv',
            ['mimeType' => 'text/csv']
        );
    }

    /**
     * Building floor map view
     *
     * @param int $id
     * @return string
     */
    public function actionFloorMap($id)
    {
        $model = $this->findModel($id);

        // Get rooms grouped by floor
        $roomsByFloor = [];
        $rooms = MeetingRoom::find()
            ->where(['building_id' => $id])
            ->orderBy(['floor' => SORT_ASC, 'room_code' => SORT_ASC])
            ->all();

        foreach ($rooms as $room) {
            $floor = $room->floor ?? 1;
            if (!isset($roomsByFloor[$floor])) {
                $roomsByFloor[$floor] = [];
            }
            $roomsByFloor[$floor][] = $room;
        }

        return $this->render('floor-map', [
            'model' => $model,
            'roomsByFloor' => $roomsByFloor,
        ]);
    }

    /**
     * Upload image helper
     *
     * @param UploadedFile $file
     * @param string $type
     * @return string|false
     */
    protected function uploadImage($file, $type)
    {
        $uploadPath = Yii::getAlias('@webroot/uploads/' . $type);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $filename = $type . '_' . time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $file->extension;
        $filePath = $uploadPath . '/' . $filename;

        if ($file->saveAs($filePath)) {
            return 'uploads/' . $type . '/' . $filename;
        }

        return false;
    }

    /**
     * Delete image helper
     *
     * @param string $imagePath
     * @return bool
     */
    protected function deleteImage($imagePath)
    {
        $fullPath = Yii::getAlias('@webroot/' . $imagePath);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }

    /**
     * Finds the Building model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     * @return Building the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Building::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบข้อมูลอาคาร');
    }
}
