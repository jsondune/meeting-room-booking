<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\Department;
use common\models\User;
use common\models\Booking;

/**
 * DepartmentController - Backend department management
 */
class DepartmentController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'search', 'statistics'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 
                             'toggle-status', 'bulk-action', 'export', 'move-users'],
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
            'toggle-status' => ['post'],
            'bulk-action' => ['post'],
            'move-users' => ['post'],
        ];
    }

    /**
     * Lists all Department models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Department::find();

        // Apply filters
        $status = Yii::$app->request->get('status');
        $parentId = Yii::$app->request->get('parent_id');
        $keyword = Yii::$app->request->get('keyword');

        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }
        if ($parentId !== null && $parentId !== '') {
            if ($parentId === '0') {
                $query->andWhere(['parent_id' => null]);
            } else {
                $query->andWhere(['parent_id' => $parentId]);
            }
        }
        if ($keyword) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $keyword],
                ['like', 'name_en', $keyword],
                ['like', 'code', $keyword],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['sort_order' => SORT_ASC, 'name_th' => SORT_ASC],
            ],
        ]);

        // Get parent departments for filter
        $parentDepartments = Department::find()
            ->where(['parent_id' => null])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        // Get statistics
        $stats = [
            'total' => Department::find()->count(),
            'active' => Department::find()->where(['is_active' => true])->count(),
            'inactive' => Department::find()->where(['is_active' => false])->count(),
            'total_users' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'parentDepartments' => $parentDepartments,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'parent_id' => $parentId,
                'keyword' => $keyword,
            ],
        ]);
    }

    /**
     * Displays a single Department model.
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get users in department
        $usersDataProvider = new ActiveDataProvider([
            'query' => User::find()->where(['department_id' => $id, 'status' => User::STATUS_ACTIVE]),
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['full_name' => SORT_ASC],
            ],
        ]);

        // Get child departments
        $childDepartments = Department::find()
            ->where(['parent_id' => $id])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();

        // Get booking statistics
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $bookingStats = [
            'total' => Booking::find()->where(['department_id' => $id])->count(),
            'this_month' => Booking::find()
                ->where(['department_id' => $id])
                ->andWhere(['between', 'booking_date', $monthStart, $monthEnd])
                ->count(),
            'pending' => Booking::find()
                ->where(['department_id' => $id, 'status' => Booking::STATUS_PENDING])
                ->count(),
            'upcoming' => Booking::find()
                ->where(['department_id' => $id])
                ->andWhere(['>=', 'booking_date', $today])
                ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
                ->count(),
        ];

        // Get recent bookings
        $recentBookings = Booking::find()
            ->where(['department_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Monthly booking chart data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['department_id' => $id])
                ->andWhere(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyData[] = [
                'month' => date('M', strtotime($month . '-01')),
                'year' => date('Y', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        return $this->render('view', [
            'model' => $model,
            'usersDataProvider' => $usersDataProvider,
            'childDepartments' => $childDepartments,
            'bookingStats' => $bookingStats,
            'recentBookings' => $recentBookings,
            'monthlyData' => $monthlyData,
        ]);
    }

    /**
     * Creates a new Department model.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new Department();
        $model->is_active = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'เพิ่มหน่วยงานสำเร็จ');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $parentDepartments = Department::find()
            ->where(['parent_id' => null, 'is_active' => true])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'parentDepartments' => $parentDepartments,
        ]);
    }

    /**
     * Updates an existing Department model.
     *
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'บันทึกข้อมูลหน่วยงานสำเร็จ');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $parentDepartments = Department::find()
            ->where(['parent_id' => null, 'is_active' => true])
            ->andWhere(['<>', 'id', $id])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'parentDepartments' => $parentDepartments,
        ]);
    }

    /**
     * Deletes an existing Department model.
     *
     * @param int $id
     * @return Response
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Check if department has users
        $userCount = User::find()->where(['department_id' => $id])->count();
        if ($userCount > 0) {
            Yii::$app->session->setFlash('error', "ไม่สามารถลบหน่วยงานได้ เนื่องจากมีผู้ใช้ในหน่วยงาน ({$userCount} คน)");
            return $this->redirect(['view', 'id' => $id]);
        }

        // Check if department has children
        $childCount = Department::find()->where(['parent_id' => $id])->count();
        if ($childCount > 0) {
            Yii::$app->session->setFlash('error', "ไม่สามารถลบหน่วยงานได้ เนื่องจากมีหน่วยงานย่อย ({$childCount} หน่วยงาน)");
            return $this->redirect(['view', 'id' => $id]);
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'ลบหน่วยงานสำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบหน่วยงานได้');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle department status
     *
     * @param int $id
     * @return Response
     */
    public function actionToggleStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $model->status = $model->is_active 
            ? false 
            : true;

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
     * Move users to another department
     *
     * @return Response
     */
    public function actionMoveUsers()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $fromDeptId = Yii::$app->request->post('from_department_id');
        $toDeptId = Yii::$app->request->post('to_department_id');
        $userIds = Yii::$app->request->post('user_ids', []);

        if (empty($userIds)) {
            return ['success' => false, 'message' => 'โปรดเลือกผู้ใช้'];
        }

        $toDept = Department::findOne($toDeptId);
        if (!$toDept) {
            return ['success' => false, 'message' => 'ไม่พบหน่วยงานปลายทาง'];
        }

        $count = User::updateAll(
            ['department_id' => $toDeptId],
            ['id' => $userIds]
        );

        return [
            'success' => true,
            'message' => "ย้ายผู้ใช้ {$count} คนสำเร็จ",
            'count' => $count,
        ];
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
            return ['success' => false, 'message' => 'โปรดเลือกหน่วยงาน'];
        }

        $count = 0;

        switch ($action) {
            case 'activate':
                $count = Department::updateAll(
                    ['is_active' => true],
                    ['id' => $ids]
                );
                break;

            case 'deactivate':
                $count = Department::updateAll(
                    ['is_active' => false],
                    ['id' => $ids]
                );
                break;

            case 'delete':
                foreach ($ids as $id) {
                    $dept = Department::findOne($id);
                    if ($dept) {
                        // Check constraints
                        $userCount = User::find()->where(['department_id' => $id])->count();
                        $childCount = Department::find()->where(['parent_id' => $id])->count();

                        if ($userCount == 0 && $childCount == 0) {
                            if ($dept->delete()) {
                                $count++;
                            }
                        }
                    }
                }
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
     * Department statistics
     *
     * @return string
     */
    public function actionStatistics()
    {
        // Get all departments with user counts
        $departments = Department::find()
            ->where(['is_active' => true])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        $deptStats = [];
        foreach ($departments as $dept) {
            $userCount = User::find()
                ->where(['department_id' => $dept->id, 'status' => User::STATUS_ACTIVE])
                ->count();

            $monthStart = date('Y-m-01');
            $monthEnd = date('Y-m-t');

            $bookingCount = Booking::find()
                ->where(['department_id' => $dept->id])
                ->andWhere(['between', 'booking_date', $monthStart, $monthEnd])
                ->count();

            $deptStats[] = [
                'department' => $dept,
                'user_count' => $userCount,
                'booking_count' => $bookingCount,
            ];
        }

        // Sort by booking count
        usort($deptStats, function($a, $b) {
            return $b['booking_count'] - $a['booking_count'];
        });

        return $this->render('statistics', [
            'deptStats' => $deptStats,
        ]);
    }

    /**
     * Export departments list
     *
     * @return Response
     */
    public function actionExport()
    {
        $departments = Department::find()
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        $filename = 'departments-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // BOM for UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($handle, [
            'รหัสหน่วยงาน', 'ชื่อ (TH)', 'ชื่อ (EN)', 
            'หน่วยงานหลัก', 'สถานะ', 'จำนวนผู้ใช้'
        ]);

        foreach ($departments as $dept) {
            $userCount = User::find()
                ->where(['department_id' => $dept->id, 'status' => User::STATUS_ACTIVE])
                ->count();

            fputcsv($handle, [
                $dept->code,
                $dept->name_th,
                $dept->name_en,
                $dept->parent ? $dept->parent->name_th : '',
                $dept->getStatusLabel(),
                $userCount,
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
     * Search departments (AJAX)
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

        $departments = Department::find()
            ->where(['is_active' => true])
            ->andWhere([
                'or',
                ['like', 'name_th', $term],
                ['like', 'name_en', $term],
                ['like', 'code', $term],
            ])
            ->limit(10)
            ->all();

        $results = [];
        foreach ($departments as $dept) {
            $results[] = [
                'id' => $dept->id,
                'value' => $dept->name_th,
                'label' => $dept->code . ' - ' . $dept->name_th,
            ];
        }

        return $results;
    }

    /**
     * Finds the Department model based on its primary key value.
     *
     * @param int $id
     * @return Department
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Department::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบหน่วยงานที่ระบุ');
    }
}
