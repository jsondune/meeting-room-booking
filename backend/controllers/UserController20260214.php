<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\models\Department;
use common\models\Booking;
use common\models\AuditLog;

/**
 * UserController - Backend user management
 */
class UserController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'search', 'activity-log'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 
                             'toggle-status', 'reset-password', 'bulk-action',
                             'export', 'import', 'assign-role', 'revoke-role', 'impersonate'],
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
            'reset-password' => ['post'],
            'bulk-action' => ['post'],
            'assign-role' => ['post'],
            'revoke-role' => ['post'],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = User::find()->with(['department']);

        // Apply filters
        $departmentId = Yii::$app->request->get('department_id');
        $status = Yii::$app->request->get('status');
        $role = Yii::$app->request->get('role');
        $keyword = Yii::$app->request->get('keyword');
        $emailVerified = Yii::$app->request->get('email_verified');

        if ($departmentId) {
            $query->andWhere(['department_id' => $departmentId]);
        }
        if ($status !== null && $status !== '') {
            $query->andWhere(['status' => $status]);
        }
        if ($keyword) {
            $query->andWhere([
                'or',
                ['like', 'username', $keyword],
                ['like', 'email', $keyword],
                ['like', 'first_name', $keyword],
                ['like', 'last_name', $keyword],
            ]);
        }

        // Filter by role using auth_assignment table
        if ($role) {
            $query->innerJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                  ->andWhere(['auth_assignment.item_name' => $role]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        // Get filter options
        $departments = Department::getDropdownList();
        $roles = $this->getAllRoles();

        // Get statistics
        $stats = [
            'total' => User::find()->count(),
            'active' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
            'inactive' => User::find()->where(['status' => User::STATUS_INACTIVE])->count(),
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'departments' => $departments,
            'roles' => $roles,
            'stats' => $stats,
            'filters' => [
                'department_id' => $departmentId,
                'status' => $status,
                'role' => $role,
                'keyword' => $keyword,
            ],
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        // Get user's bookings statistics
        $bookingStats = [
            'total' => Booking::find()->where(['user_id' => $id])->count(),
            'completed' => Booking::find()->where(['user_id' => $id, 'status' => Booking::STATUS_COMPLETED])->count(),
            'cancelled' => Booking::find()->where(['user_id' => $id, 'status' => Booking::STATUS_CANCELLED])->count(),
            'pending' => Booking::find()->where(['user_id' => $id, 'status' => Booking::STATUS_PENDING])->count(),
        ];

        // Get recent bookings
        $recentBookings = Booking::find()
            ->where(['user_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        // Get recent activity
        $recentActivity = AuditLog::find()
            ->where(['user_id' => $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(20)
            ->all();

        // Get user's roles
        $userRoles = Yii::$app->authManager->getRolesByUser($id);

        return $this->render('view', [
            'model' => $model,
            'bookingStats' => $bookingStats,
            'recentBookings' => $recentBookings,
            'recentActivity' => $recentActivity,
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * Creates a new User model.
     *
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new User();
        $model->status = User::STATUS_ACTIVE;
        $model->scenario = 'admin-create';

        if ($model->load(Yii::$app->request->post())) {
            // Get password from User array
            $userData = Yii::$app->request->post('User', []);
            $password = $userData['password'] ?? '';
            if (!empty($password)) {
                $model->setPassword($password);
            }
            $model->generateAuthKey();
            $model->generateEmailVerificationToken();

            // Handle avatar upload
            $avatarFile = UploadedFile::getInstance($model, 'avatarFile');
            if ($avatarFile) {
                $avatarPath = $model->uploadAvatar($avatarFile);
                if ($avatarPath) {
                    $model->avatar = $avatarPath;
                }
            }

            if ($model->save()) {
                // Assign role - get from User array or separate field
                $role = $userData['role'] ?? Yii::$app->request->post('role');
                if ($role) {
                    $auth = Yii::$app->authManager;
                    $roleObj = $auth->getRole($role);
                    if ($roleObj) {
                        $auth->assign($roleObj, $model->id);
                    }
                }

                // Log activity
                AuditLog::log(AuditLog::TYPE_USER_CREATED, 'ผู้ใช้ถูกสร้างโดยผู้ดูแลระบบ', [
                    'user_id' => $model->id,
                    'created_by' => Yii::$app->user->id,
                ]);

                Yii::$app->session->setFlash('success', 'เพิ่มผู้ใช้สำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $departments = Department::getDropdownList();
        $roles = $this->getAllRoles();

        return $this->render('create', [
            'model' => $model,
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    /**
     * Updates an existing User model.
     *
     * @param int $id
     * @return string|Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'admin-update';

        if ($model->load(Yii::$app->request->post())) {
            // Handle password update - get from User array
            $userData = Yii::$app->request->post('User', []);
            $password = $userData['password'] ?? '';
            if (!empty($password)) {
                $model->setPassword($password);
                $model->generateAuthKey();
            }

            // Handle avatar upload
            $avatarFile = UploadedFile::getInstance($model, 'avatarFile');
            if ($avatarFile) {
                $avatarPath = $model->uploadAvatar($avatarFile);
                if ($avatarPath) {
                    $model->avatar = $avatarPath;
                }
            }
            
            // Handle avatar removal
            if (!empty($userData['removeAvatar'])) {
                $model->avatar = null;
            }

            if ($model->save()) {
                // Update role - get from User array or separate field
                $role = $userData['role'] ?? Yii::$app->request->post('role');
                if ($role) {
                    $auth = Yii::$app->authManager;
                    // Remove all existing roles
                    $auth->revokeAll($model->id);
                    // Assign new role
                    $roleObj = $auth->getRole($role);
                    if ($roleObj) {
                        $auth->assign($roleObj, $model->id);
                    }
                }

                // Log activity
                AuditLog::log(AuditLog::TYPE_USER_UPDATED, 'ข้อมูลผู้ใช้ถูกแก้ไขโดยผู้ดูแลระบบ', [
                    'user_id' => $model->id,
                    'updated_by' => Yii::$app->user->id,
                ]);

                Yii::$app->session->setFlash('success', 'บันทึกข้อมูลผู้ใช้สำเร็จ');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        $departments = Department::getDropdownList();
        $roles = $this->getAllRoles();
        $userRoles = Yii::$app->authManager->getRolesByUser($id);
        $currentRole = !empty($userRoles) ? array_key_first($userRoles) : null;

        return $this->render('update', [
            'model' => $model,
            'departments' => $departments,
            'roles' => $roles,
            'currentRole' => $currentRole,
        ]);
    }

    /**
     * Deletes an existing User model.
     *
     * @param int $id
     * @return Response
     */
    public function actionDelete($id)
    {
        if ($id == Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบบัญชีของตัวเองได้');
            return $this->redirect(['index']);
        }

        $model = $this->findModel($id);

        // Soft delete - just change status
        $model->status = User::STATUS_DELETED;
        $model->email = 'deleted_' . time() . '_' . $model->email;
        $model->username = 'deleted_' . time() . '_' . $model->username;

        if ($model->save(false)) {
            // Revoke all roles
            Yii::$app->authManager->revokeAll($model->id);

            // Log activity
            AuditLog::log(AuditLog::TYPE_USER_DELETED, 'ผู้ใช้ถูกลบโดยผู้ดูแลระบบ', [
                'user_id' => $model->id,
                'deleted_by' => Yii::$app->user->id,
            ]);

            Yii::$app->session->setFlash('success', 'ลบผู้ใช้สำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบผู้ใช้ได้');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle user status
     *
     * @param int $id
     * @return Response
     */
    public function actionToggleStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($id == Yii::$app->user->id) {
            return ['success' => false, 'message' => 'ไม่สามารถเปลี่ยนสถานะบัญชีของตัวเองได้'];
        }

        $model = $this->findModel($id);
        $newStatus = Yii::$app->request->post('status');

        if (!in_array($newStatus, [User::STATUS_ACTIVE, User::STATUS_SUSPENDED])) {
            return ['success' => false, 'message' => 'สถานะไม่ถูกต้อง'];
        }

        $model->status = $newStatus;

        if ($model->save(false)) {
            // Log activity
            $action = $newStatus == User::STATUS_ACTIVE ? 'เปิดใช้งาน' : 'ระงับ';
            AuditLog::log(AuditLog::TYPE_STATUS_CHANGE, "บัญชีผู้ใช้ถูก{$action}โดยผู้ดูแลระบบ", [
                'user_id' => $model->id,
                'changed_by' => Yii::$app->user->id,
            ]);

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
     * Reset user password
     *
     * @param int $id
     * @return Response
     */
    public function actionResetPassword($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = $this->findModel($id);
        $newPassword = Yii::$app->request->post('password');
        $sendEmail = Yii::$app->request->post('send_email', true);

        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร'];
        }

        $model->setPassword($newPassword);
        $model->generateAuthKey();

        if ($model->save(false)) {
            // Send email if requested
            if ($sendEmail) {
                // TODO: Send password reset email
            }

            // Log activity
            AuditLog::log(AuditLog::TYPE_PASSWORD_RESET, 'รหัสผ่านถูกรีเซ็ตโดยผู้ดูแลระบบ', [
                'user_id' => $model->id,
                'reset_by' => Yii::$app->user->id,
            ]);

            return [
                'success' => true,
                'message' => 'รีเซ็ตรหัสผ่านสำเร็จ',
            ];
        }

        return ['success' => false, 'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน'];
    }

    /**
     * Assign role to user
     *
     * @return Response
     */
    public function actionAssignRole()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->request->post('user_id');
        $roleName = Yii::$app->request->post('role');

        $model = $this->findModel($userId);
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);

        if (!$role) {
            return ['success' => false, 'message' => 'ไม่พบบทบาทที่ระบุ'];
        }

        try {
            $auth->assign($role, $userId);

            // Log activity
            AuditLog::log(AuditLog::TYPE_ROLE_ASSIGNED, "กำหนดบทบาท {$roleName} ให้ผู้ใช้", [
                'user_id' => $userId,
                'assigned_by' => Yii::$app->user->id,
            ]);

            return [
                'success' => true,
                'message' => 'กำหนดบทบาทสำเร็จ',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Revoke role from user
     *
     * @return Response
     */
    public function actionRevokeRole()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $userId = Yii::$app->request->post('user_id');
        $roleName = Yii::$app->request->post('role');

        if ($userId == Yii::$app->user->id) {
            return ['success' => false, 'message' => 'ไม่สามารถยกเลิกบทบาทของตัวเองได้'];
        }

        $model = $this->findModel($userId);
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);

        if (!$role) {
            return ['success' => false, 'message' => 'ไม่พบบทบาทที่ระบุ'];
        }

        try {
            $auth->revoke($role, $userId);

            // Log activity
            AuditLog::log(AuditLog::TYPE_ROLE_REVOKED, "ยกเลิกบทบาท {$roleName} จากผู้ใช้", [
                'user_id' => $userId,
                'revoked_by' => Yii::$app->user->id,
            ]);

            return [
                'success' => true,
                'message' => 'ยกเลิกบทบาทสำเร็จ',
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
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
            return ['success' => false, 'message' => 'โปรดเลือกผู้ใช้'];
        }

        // Remove current user from the list
        $ids = array_diff($ids, [Yii::$app->user->id]);

        if (empty($ids)) {
            return ['success' => false, 'message' => 'ไม่สามารถดำเนินการกับบัญชีของตัวเองได้'];
        }

        $count = 0;

        switch ($action) {
            case 'activate':
                $count = User::updateAll(
                    ['status' => User::STATUS_ACTIVE],
                    ['id' => $ids]
                );
                break;

            case 'suspend':
                $count = User::updateAll(
                    ['status' => User::STATUS_SUSPENDED],
                    ['id' => $ids]
                );
                break;

            case 'delete':
                foreach ($ids as $id) {
                    $user = User::findOne($id);
                    if ($user) {
                        $user->status = User::STATUS_DELETED;
                        $user->email = 'deleted_' . time() . '_' . $user->email;
                        $user->username = 'deleted_' . time() . '_' . $user->username;
                        if ($user->save(false)) {
                            Yii::$app->authManager->revokeAll($id);
                            $count++;
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
     * View user activity log
     *
     * @param int $id
     * @return string
     */
    public function actionActivityLog($id)
    {
        $model = $this->findModel($id);

        $dataProvider = new ActiveDataProvider([
            'query' => AuditLog::find()->where(['user_id' => $id]),
            'pagination' => ['pageSize' => 50],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        return $this->render('activity-log', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Export users list
     *
     * @return Response
     */
    public function actionExport()
    {
        $users = User::find()
            ->with(['department'])
            ->where(['<>', 'status', User::STATUS_DELETED])
            ->orderBy(['username' => SORT_ASC])
            ->all();

        $filename = 'users-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // BOM for UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($handle, [
            'Username', 'Email', 'ชื่อ-นามสกุล', 
            'หน่วยงาน', 'ตำแหน่ง', 'โทรศัพท์', 'สถานะ', 'วันที่สร้าง'
        ]);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->username,
                $user->email,
                $user->first_name . ' ' . $user->last_name,
                $user->department ? ($user->department->name_th ?? $user->department->name) : '',
                $user->position,
                $user->phone,
                $user->getStatusLabel(),
                $user->created_at,
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
     * Search users (AJAX)
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

        $users = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->andWhere([
                'or',
                ['like', 'username', $term],
                ['like', 'fullname', $term],
                ['like', 'email', $term],
            ])
            ->limit(10)
            ->all();

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'value' => $user->fullname,
                'label' => $user->fullname . ' (' . $user->email . ')',
                'email' => $user->email,
            ];
        }

        return $results;
    }

    /**
     * Impersonate user (login as user)
     *
     * @param int $id
     * @return Response
     */
    public function actionImpersonate($id)
    {
        if (!Yii::$app->user->can('admin')) {
            throw new \yii\web\ForbiddenHttpException('คุณไม่มีสิทธิ์ใช้ฟีเจอร์นี้');
        }

        $model = $this->findModel($id);

        // Store original user ID in session
        Yii::$app->session->set('impersonator_id', Yii::$app->user->id);

        // Login as the target user
        Yii::$app->user->login($model);

        // Log activity
        AuditLog::log(AuditLog::TYPE_IMPERSONATION, 'ผู้ดูแลระบบเข้าสู่ระบบในนามผู้ใช้', [
            'impersonated_user_id' => $id,
            'impersonator_id' => Yii::$app->session->get('impersonator_id'),
        ]);

        Yii::$app->session->setFlash('warning', 'คุณกำลังเข้าสู่ระบบในนามของ ' . $model->fullname);

        return $this->redirect(Yii::$app->homeUrl);
    }

    /**
     * Get all roles
     *
     * @return array
     */
    protected function getAllRoles()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        $result = [];
        foreach ($roles as $name => $role) {
            $result[$name] = $role->description ?: $name;
        }

        return $result;
    }

    /**
     * Finds the User model based on its primary key value.
     *
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบผู้ใช้ที่ระบุ');
    }
}
