<?php
/**
 * ApprovalController - Backend approval workflow management
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
use common\models\User;
use common\models\AuditLog;
use common\models\Notification;

/**
 * ApprovalController handles all approval-related operations
 */
class ApprovalController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'pending', 'history', 'view', 'statistics'],
                'allow' => true,
                'roles' => ['approver', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['approve', 'reject', 'bulk-approve', 'bulk-reject', 'reassign'],
                'allow' => true,
                'roles' => ['approver', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['settings', 'delegation'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Approval dashboard
     * @return string
     */
    public function actionIndex()
    {
        $user = $this->getUser();
        
        // Get pending bookings for approval
        $pendingQuery = Booking::findPending()->with(['room', 'user', 'department']);
        
        // Filter by department for managers
        if ($user->hasRole('manager') && !$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $pendingQuery->andWhere(['department_id' => $user->department_id]);
        }
        
        $pendingCount = $pendingQuery->count();
        
        // Get urgent bookings (within 48 hours)
        $urgentBookings = (clone $pendingQuery)
            ->andWhere(['<=', 'booking_date', date('Y-m-d', strtotime('+2 days'))])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->limit(5)
            ->all();
        
        // Get today's pending
        $todayPending = (clone $pendingQuery)
            ->andWhere(['booking_date' => date('Y-m-d')])
            ->all();
        
        // Get recent approvals by this user
        $recentApprovals = Booking::find()
            ->where(['approved_by' => $user->id])
            ->andWhere(['in', 'status', ['approved', 'rejected']])
            ->orderBy(['approved_at' => SORT_DESC])
            ->limit(10)
            ->with(['room', 'user'])
            ->all();
        
        // Statistics
        $stats = $this->getApprovalStatistics($user);
        
        return $this->render('index', [
            'pendingCount' => $pendingCount,
            'urgentBookings' => $urgentBookings,
            'todayPending' => $todayPending,
            'recentApprovals' => $recentApprovals,
            'stats' => $stats,
        ]);
    }

    /**
     * List all pending bookings
     * @return string
     */
    public function actionPending()
    {
        $user = $this->getUser();
        
        $query = Booking::findPending()->with(['room', 'user', 'department']);
        
        // Filter by department for managers
        if ($user->hasRole('manager') && !$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $query->andWhere(['department_id' => $user->department_id]);
        }
        
        // Apply filters
        $roomId = Yii::$app->request->get('room_id');
        $departmentId = Yii::$app->request->get('department_id');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        $urgentOnly = Yii::$app->request->get('urgent_only');
        
        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }
        if ($departmentId) {
            $query->andWhere(['department_id' => $departmentId]);
        }
        if ($dateFrom) {
            $query->andWhere(['>=', 'booking_date', $dateFrom]);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'booking_date', $dateTo]);
        }
        if ($urgentOnly) {
            $query->andWhere(['<=', 'booking_date', date('Y-m-d', strtotime('+2 days'))]);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['booking_date' => SORT_ASC, 'created_at' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        return $this->render('pending', [
            'dataProvider' => $dataProvider,
            'rooms' => \common\models\MeetingRoom::getDropdownList(),
            'departments' => \common\models\Department::getDropdownList(),
        ]);
    }

    /**
     * Approval history
     * @return string
     */
    public function actionHistory()
    {
        $user = $this->getUser();
        
        $query = Booking::find()
            ->where(['in', 'status', ['approved', 'rejected']])
            ->with(['room', 'user', 'approver']);
        
        // Filter by approver for non-admins
        if (!$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $query->andWhere(['or',
                ['approved_by' => $user->id],
                ['department_id' => $user->department_id],
            ]);
        }
        
        // Apply filters
        $status = Yii::$app->request->get('status');
        $approverId = Yii::$app->request->get('approver_id');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');
        
        if ($status) {
            $query->andWhere(['status' => $status]);
        }
        if ($approverId) {
            $query->andWhere(['approved_by' => $approverId]);
        }
        if ($dateFrom) {
            $query->andWhere(['>=', 'approved_at', $dateFrom . ' 00:00:00']);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'approved_at', $dateTo . ' 23:59:59']);
        }
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['approved_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        
        // Get approvers for filter
        $approvers = User::find()
            ->where(['in', 'role', ['manager', 'admin', 'superadmin']])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->all();
        
        return $this->render('history', [
            'dataProvider' => $dataProvider,
            'approvers' => ArrayHelper::map($approvers, 'id', 'full_name'),
        ]);
    }

    /**
     * View booking details for approval
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Get approval history for this booking
        $auditLogs = AuditLog::find()
            ->where(['model_type' => 'Booking', 'model_id' => $id])
            ->andWhere(['in', 'action', ['approve', 'reject', 'status_change']])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('user')
            ->all();
        
        // Check for conflicts
        $conflicts = $this->checkConflicts($model);
        
        // Get similar bookings by same user
        $userBookings = Booking::find()
            ->where(['user_id' => $model->user_id])
            ->andWhere(['!=', 'id', $model->id])
            ->andWhere(['>=', 'booking_date', date('Y-m-d')])
            ->orderBy(['booking_date' => SORT_ASC])
            ->limit(5)
            ->all();
        
        return $this->render('view', [
            'model' => $model,
            'auditLogs' => $auditLogs,
            'conflicts' => $conflicts,
            'userBookings' => $userBookings,
        ]);
    }

    /**
     * Approve a booking
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $user = $this->getUser();
        
        if ($model->status !== 'pending') {
            $this->setFlash('error', 'ไม่สามารถอนุมัติการจองนี้ได้ เนื่องจากสถานะไม่ใช่ "รออนุมัติ"');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check permission
        if (!$this->canApprove($model)) {
            $this->setFlash('error', 'คุณไม่มีสิทธิ์อนุมัติการจองนี้');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check for conflicts
        $conflicts = $this->checkConflicts($model);
        if (!empty($conflicts)) {
            $this->setFlash('warning', 'พบการจองที่ทับซ้อน กรุณาตรวจสอบก่อนอนุมัติ');
        }
        
        $model->approve($user->id);
        
        // Log the action
        AuditLog::log('approve', 'Booking', $model->id, null, [
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => date('Y-m-d H:i:s'),
        ], $user->id);
        
        $this->setFlash('success', 'อนุมัติการจอง ' . $model->booking_code . ' เรียบร้อยแล้ว');
        
        // Return to pending list if came from there
        $returnUrl = Yii::$app->request->get('return') ?: ['pending'];
        return $this->redirect($returnUrl);
    }

    /**
     * Reject a booking with reason
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $user = $this->getUser();
        
        if ($model->status !== 'pending') {
            $this->setFlash('error', 'ไม่สามารถปฏิเสธการจองนี้ได้ เนื่องจากสถานะไม่ใช่ "รออนุมัติ"');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        // Check permission
        if (!$this->canApprove($model)) {
            $this->setFlash('error', 'คุณไม่มีสิทธิ์ปฏิเสธการจองนี้');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if (Yii::$app->request->isPost) {
            $reason = Yii::$app->request->post('reason', '');
            
            if (empty($reason)) {
                $this->setFlash('error', 'กรุณาระบุเหตุผลในการปฏิเสธ');
                return $this->redirect(['view', 'id' => $id]);
            }
            
            $model->reject($reason, $user->id);
            
            // Log the action
            AuditLog::log('reject', 'Booking', $model->id, null, [
                'status' => 'rejected',
                'reason' => $reason,
                'rejected_by' => $user->id,
                'rejected_at' => date('Y-m-d H:i:s'),
            ], $user->id);
            
            $this->setFlash('success', 'ปฏิเสธการจอง ' . $model->booking_code . ' เรียบร้อยแล้ว');
            
            return $this->redirect(['pending']);
        }
        
        return $this->render('reject', [
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
        $user = $this->getUser();
        
        if (empty($ids)) {
            $this->setFlash('error', 'กรุณาเลือกการจองที่ต้องการอนุมัติ');
            return $this->redirect(['pending']);
        }
        
        $approved = 0;
        $skipped = 0;
        
        foreach ($ids as $id) {
            $model = Booking::findOne($id);
            if ($model && $model->status === 'pending' && $this->canApprove($model)) {
                $model->approve($user->id);
                $approved++;
                
                AuditLog::log('bulk_approve', 'Booking', $model->id, null, [
                    'status' => 'approved',
                ], $user->id);
            } else {
                $skipped++;
            }
        }
        
        if ($approved > 0) {
            $this->setFlash('success', "อนุมัติการจองเรียบร้อยแล้ว {$approved} รายการ");
        }
        if ($skipped > 0) {
            $this->setFlash('warning', "ข้ามการจอง {$skipped} รายการ (ไม่มีสิทธิ์หรือสถานะไม่ถูกต้อง)");
        }
        
        return $this->redirect(['pending']);
    }

    /**
     * Bulk reject bookings
     * @return \yii\web\Response
     */
    public function actionBulkReject()
    {
        $ids = Yii::$app->request->post('ids', []);
        $reason = Yii::$app->request->post('reason', 'ปฏิเสธโดยผู้ดูแลระบบ');
        $user = $this->getUser();
        
        if (empty($ids)) {
            $this->setFlash('error', 'กรุณาเลือกการจองที่ต้องการปฏิเสธ');
            return $this->redirect(['pending']);
        }
        
        $rejected = 0;
        $skipped = 0;
        
        foreach ($ids as $id) {
            $model = Booking::findOne($id);
            if ($model && $model->status === 'pending' && $this->canApprove($model)) {
                $model->reject($reason, $user->id);
                $rejected++;
                
                AuditLog::log('bulk_reject', 'Booking', $model->id, null, [
                    'status' => 'rejected',
                    'reason' => $reason,
                ], $user->id);
            } else {
                $skipped++;
            }
        }
        
        if ($rejected > 0) {
            $this->setFlash('success', "ปฏิเสธการจองเรียบร้อยแล้ว {$rejected} รายการ");
        }
        if ($skipped > 0) {
            $this->setFlash('warning', "ข้ามการจอง {$skipped} รายการ");
        }
        
        return $this->redirect(['pending']);
    }

    /**
     * Reassign booking to another approver
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionReassign($id)
    {
        $model = $this->findModel($id);
        $user = $this->getUser();
        
        if ($model->status !== 'pending') {
            $this->setFlash('error', 'ไม่สามารถส่งต่อการจองนี้ได้');
            return $this->redirect(['view', 'id' => $id]);
        }
        
        if (Yii::$app->request->isPost) {
            $newApproverId = Yii::$app->request->post('approver_id');
            $note = Yii::$app->request->post('note', '');
            
            if (empty($newApproverId)) {
                $this->setFlash('error', 'กรุณาเลือกผู้อนุมัติ');
                return $this->redirect(['reassign', 'id' => $id]);
            }
            
            // Send notification to new approver
            Notification::create(
                $newApproverId,
                'approval_assignment',
                'มีการจองรอการพิจารณา',
                "การจอง {$model->booking_code} ถูกส่งต่อให้คุณพิจารณา" . ($note ? "\n\nหมายเหตุ: {$note}" : ''),
                ['booking_id' => $model->id]
            );
            
            // Log
            AuditLog::log('reassign', 'Booking', $model->id, null, [
                'from_user_id' => $user->id,
                'to_user_id' => $newApproverId,
                'note' => $note,
            ], $user->id);
            
            $this->setFlash('success', 'ส่งต่อการจองเรียบร้อยแล้ว');
            return $this->redirect(['pending']);
        }
        
        // Get possible approvers
        $approvers = User::find()
            ->where(['in', 'role', ['manager', 'admin', 'superadmin']])
            ->andWhere(['status' => User::STATUS_ACTIVE])
            ->andWhere(['!=', 'id', $user->id])
            ->all();
        
        return $this->render('reassign', [
            'model' => $model,
            'approvers' => ArrayHelper::map($approvers, 'id', 'full_name'),
        ]);
    }

    /**
     * Approval statistics
     * @return string
     */
    public function actionStatistics()
    {
        $user = $this->getUser();
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        
        // Overall stats
        $stats = $this->getApprovalStatistics($user, $dateFrom, $dateTo);
        
        // By approver
        $byApprover = Booking::find()
            ->select(['approved_by', 'COUNT(*) as total', 'status'])
            ->where(['>=', 'approved_at', $dateFrom])
            ->andWhere(['<=', 'approved_at', $dateTo . ' 23:59:59'])
            ->andWhere(['is not', 'approved_by', null])
            ->groupBy(['approved_by', 'status'])
            ->asArray()
            ->all();
        
        // By department
        $byDepartment = Booking::find()
            ->select(['department_id', 'COUNT(*) as total', 'status'])
            ->where(['>=', 'created_at', $dateFrom])
            ->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59'])
            ->andWhere(['in', 'status', ['approved', 'rejected']])
            ->groupBy(['department_id', 'status'])
            ->asArray()
            ->all();
        
        // Response time analysis
        $avgResponseTime = Booking::find()
            ->select(['AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours'])
            ->where(['>=', 'approved_at', $dateFrom])
            ->andWhere(['<=', 'approved_at', $dateTo . ' 23:59:59'])
            ->andWhere(['is not', 'approved_at', null])
            ->asArray()
            ->one();
        
        return $this->render('statistics', [
            'stats' => $stats,
            'byApprover' => $byApprover,
            'byDepartment' => $byDepartment,
            'avgResponseTime' => $avgResponseTime['avg_hours'] ?? 0,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Check if current user can approve the booking
     * @param Booking $booking
     * @return bool
     */
    protected function canApprove($booking)
    {
        $user = $this->getUser();
        
        // Superadmin and admin can approve all
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        
        // Manager can approve in their department
        if ($user->hasRole('manager') && $booking->department_id == $user->department_id) {
            return true;
        }
        
        return false;
    }

    /**
     * Check for booking conflicts
     * @param Booking $booking
     * @return array
     */
    protected function checkConflicts($booking)
    {
        return Booking::find()
            ->where(['room_id' => $booking->room_id])
            ->andWhere(['booking_date' => $booking->booking_date])
            ->andWhere(['!=', 'id', $booking->id])
            ->andWhere(['in', 'status', ['approved', 'pending']])
            ->andWhere([
                'or',
                ['and', 
                    ['<=', 'start_time', $booking->start_time],
                    ['>', 'end_time', $booking->start_time]
                ],
                ['and',
                    ['<', 'start_time', $booking->end_time],
                    ['>=', 'end_time', $booking->end_time]
                ],
                ['and',
                    ['>=', 'start_time', $booking->start_time],
                    ['<=', 'end_time', $booking->end_time]
                ]
            ])
            ->all();
    }

    /**
     * Get approval statistics
     * @param User $user
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    protected function getApprovalStatistics($user, $dateFrom = null, $dateTo = null)
    {
        $dateFrom = $dateFrom ?: date('Y-m-01');
        $dateTo = $dateTo ?: date('Y-m-d');
        
        $baseQuery = Booking::find()
            ->where(['>=', 'created_at', $dateFrom])
            ->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        
        // Filter by department for managers
        if ($user->hasRole('manager') && !$user->hasRole('admin') && !$user->hasRole('superadmin')) {
            $baseQuery->andWhere(['department_id' => $user->department_id]);
        }
        
        return [
            'total' => (clone $baseQuery)->count(),
            'approved' => (clone $baseQuery)->andWhere(['status' => 'approved'])->count(),
            'rejected' => (clone $baseQuery)->andWhere(['status' => 'rejected'])->count(),
            'pending' => (clone $baseQuery)->andWhere(['status' => 'pending'])->count(),
            'myApprovals' => Booking::find()
                ->where(['approved_by' => $user->id])
                ->andWhere(['>=', 'approved_at', $dateFrom])
                ->andWhere(['<=', 'approved_at', $dateTo . ' 23:59:59'])
                ->count(),
            'approvalRate' => (function() use ($baseQuery) {
                $total = (clone $baseQuery)->andWhere(['in', 'status', ['approved', 'rejected']])->count();
                $approved = (clone $baseQuery)->andWhere(['status' => 'approved'])->count();
                return $total > 0 ? round(($approved / $total) * 100, 1) : 0;
            })(),
        ];
    }

    /**
     * Find booking model
     * @param int $id
     * @return Booking
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = Booking::find()
            ->where(['id' => $id])
            ->with(['room', 'user', 'department', 'approver', 'attendees', 'bookingEquipment'])
            ->one();
            
        if ($model === null) {
            throw new NotFoundHttpException('ไม่พบการจองที่ระบุ');
        }
        
        return $model;
    }
}
