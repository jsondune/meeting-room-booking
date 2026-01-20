<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use common\models\Booking;
use common\models\MeetingRoom;
use common\models\Department;
use common\models\User;
use common\models\ActivityLog;

/**
 * ReportController - Backend reports and analytics
 */
class ReportController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'usage', 'revenue', 'audit', 'export',
                             'room-usage', 'department-usage', 'user-usage',
                             'daily-report', 'monthly-report', 'custom-report'],
                'allow' => true,
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Reports dashboard
     *
     * @return string
     */
    public function actionIndex()
    {
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');
        $yearStart = date('Y-01-01');
        $yearEnd = date('Y-12-31');

        // Summary statistics
        $stats = [
            'today_bookings' => Booking::find()
                ->where(['booking_date' => $today])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
                ->count(),
            'month_bookings' => Booking::find()
                ->where(['between', 'booking_date', $monthStart, $monthEnd])
                ->count(),
            'year_bookings' => Booking::find()
                ->where(['between', 'booking_date', $yearStart, $yearEnd])
                ->count(),
            'total_rooms' => MeetingRoom::find()->where(['status' => MeetingRoom::STATUS_ACTIVE])->count(),
            'total_users' => User::find()->where(['status' => User::STATUS_ACTIVE])->count(),
            'total_departments' => Department::find()->where(['is_active' => true])->count(),
        ];

        // Monthly booking trend (last 12 months)
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyTrend[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        // Top 5 rooms
        $topRooms = Booking::find()
            ->select(['room_id', 'COUNT(*) as booking_count'])
            ->where(['between', 'booking_date', $monthStart, $monthEnd])
            ->groupBy('room_id')
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(5)
            ->with('room')
            ->asArray()
            ->all();

        // Top 5 departments
        $topDepartments = Booking::find()
            ->select(['department_id', 'COUNT(*) as booking_count'])
            ->where(['between', 'booking_date', $monthStart, $monthEnd])
            ->andWhere(['not', ['department_id' => null]])
            ->groupBy('department_id')
            ->orderBy(['booking_count' => SORT_DESC])
            ->limit(5)
            ->asArray()
            ->all();

        // Status distribution
        $statusDistribution = Booking::find()
            ->select(['status', 'COUNT(*) as count'])
            ->where(['between', 'booking_date', $monthStart, $monthEnd])
            ->groupBy('status')
            ->asArray()
            ->all();

        return $this->render('index', [
            'stats' => $stats,
            'monthlyTrend' => $monthlyTrend,
            'topRooms' => $topRooms,
            'topDepartments' => $topDepartments,
            'statusDistribution' => $statusDistribution,
        ]);
    }

    /**
     * Usage report
     *
     * @return string
     */
    public function actionUsage()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        $roomId = Yii::$app->request->get('room_id');
        $departmentId = Yii::$app->request->get('department_id');

        // Room usage
        $roomUsageQuery = Booking::find()
            ->select([
                'room_id',
                'COUNT(*) as booking_count',
                'SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours',
            ])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->groupBy('room_id')
            ->orderBy(['booking_count' => SORT_DESC]);

        if ($roomId) {
            $roomUsageQuery->andWhere(['room_id' => $roomId]);
        }

        $roomUsage = $roomUsageQuery->asArray()->all();

        // Enrich with room data
        $rooms = MeetingRoom::find()->indexBy('id')->all();
        foreach ($roomUsage as &$usage) {
            $room = isset($rooms[$usage['room_id']]) ? $rooms[$usage['room_id']] : null;
            $usage['room_name'] = $room ? $room->name_th : 'ไม่ระบุ';
            $usage['room_capacity'] = $room ? $room->capacity : 0;

            // Calculate utilization rate
            $totalDays = (strtotime($dateTo) - strtotime($dateFrom)) / 86400 + 1;
            $operatingHoursPerDay = 8; // Assume 8 hours per day
            $maxHours = $totalDays * $operatingHoursPerDay;
            $usage['utilization_rate'] = $maxHours > 0 
                ? round(($usage['total_hours'] / $maxHours) * 100, 1) 
                : 0;
        }

        // Hourly distribution
        $hourlyDistribution = Booking::find()
            ->select([
                'HOUR(start_time) as hour',
                'COUNT(*) as count',
            ])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->groupBy('hour')
            ->orderBy(['hour' => SORT_ASC])
            ->asArray()
            ->all();

        // Day of week distribution
        $dowDistribution = Booking::find()
            ->select([
                'DAYOFWEEK(booking_date) as dow',
                'COUNT(*) as count',
            ])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->groupBy('dow')
            ->orderBy(['dow' => SORT_ASC])
            ->asArray()
            ->all();

        // Filter options
        $roomsList = MeetingRoom::getDropdownList();
        $departments = Department::getDropdownList();

        return $this->render('usage', [
            'roomUsage' => $roomUsage,
            'hourlyDistribution' => $hourlyDistribution,
            'dowDistribution' => $dowDistribution,
            'roomsList' => $roomsList,
            'departments' => $departments,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'room_id' => $roomId,
                'department_id' => $departmentId,
            ],
        ]);
    }

    /**
     * Revenue report
     *
     * @return string
     */
    public function actionRevenue()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        $groupBy = Yii::$app->request->get('group_by', 'room');

        // Total revenue
        $totalRevenue = Booking::find()
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->sum('total_cost') ?: 0;

        // Revenue breakdown
        $revenueBreakdown = Booking::find()
            ->select([
                'SUM(total_room_cost) as room_revenue',
                'SUM(total_equipment_cost) as equipment_revenue',
            ])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->asArray()
            ->one();
        
        // Add service_revenue as 0 (not stored in database)
        $revenueBreakdown['service_revenue'] = 0;

        // Revenue by group
        if ($groupBy === 'room') {
            $revenueByGroup = Booking::find()
                ->select(['room_id', 'SUM(total_cost) as total', 'COUNT(*) as count'])
                ->where(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->groupBy('room_id')
                ->orderBy(['total' => SORT_DESC])
                ->asArray()
                ->all();

            // Enrich with room names
            $rooms = MeetingRoom::find()->indexBy('id')->all();
            foreach ($revenueByGroup as &$item) {
                $room = isset($rooms[$item['room_id']]) ? $rooms[$item['room_id']] : null;
                $item['name'] = $room ? $room->name_th : 'ไม่ระบุ';
            }
        } elseif ($groupBy === 'department') {
            $revenueByGroup = Booking::find()
                ->select(['department_id', 'SUM(total_cost) as total', 'COUNT(*) as count'])
                ->where(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->andWhere(['not', ['department_id' => null]])
                ->groupBy('department_id')
                ->orderBy(['total' => SORT_DESC])
                ->asArray()
                ->all();

            // Enrich with department names
            $departments = Department::find()->indexBy('id')->all();
            foreach ($revenueByGroup as &$item) {
                $dept = isset($departments[$item['department_id']]) ? $departments[$item['department_id']] : null;
                $item['name'] = $dept ? $dept->name_th : 'ไม่ระบุ';
            }
        } else {
            // Group by month
            $revenueByGroup = Booking::find()
                ->select([
                    'DATE_FORMAT(booking_date, "%Y-%m") as month',
                    'SUM(total_cost) as total',
                    'COUNT(*) as count',
                ])
                ->where(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->groupBy('month')
                ->orderBy(['month' => SORT_ASC])
                ->asArray()
                ->all();

            foreach ($revenueByGroup as &$item) {
                $item['name'] = date('M Y', strtotime($item['month'] . '-01'));
            }
        }

        // Daily revenue trend
        $dailyTrend = Booking::find()
            ->select(['booking_date', 'SUM(total_cost) as total'])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->groupBy('booking_date')
            ->orderBy(['booking_date' => SORT_ASC])
            ->asArray()
            ->all();

        return $this->render('revenue', [
            'totalRevenue' => $totalRevenue,
            'revenueBreakdown' => $revenueBreakdown,
            'revenueByGroup' => $revenueByGroup,
            'dailyTrend' => $dailyTrend,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'group_by' => $groupBy,
            ],
        ]);
    }

    /**
     * Audit log report
     *
     * @return string
     */
    public function actionAudit()
    {
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-d', strtotime('-30 days')));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-d'));
        $userId = Yii::$app->request->get('user_id');
        $actionType = Yii::$app->request->get('action_type');

        $query = ActivityLog::find()->with(['user']);

        $query->andWhere(['between', 'DATE(created_at)', $dateFrom, $dateTo]);

        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }
        if ($actionType) {
            $query->andWhere(['action_type' => $actionType]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);

        // Get unique action types
        $actionTypes = ActivityLog::find()
            ->select('action_type')
            ->distinct()
            ->column();

        // Activity summary
        $activitySummary = ActivityLog::find()
            ->select(['action_type', 'COUNT(*) as count'])
            ->where(['between', 'DATE(created_at)', $dateFrom, $dateTo])
            ->groupBy('action_type')
            ->orderBy(['count' => SORT_DESC])
            ->asArray()
            ->all();

        // Daily activity trend
        $dailyActivity = ActivityLog::find()
            ->select(['DATE(created_at) as date', 'COUNT(*) as count'])
            ->where(['between', 'DATE(created_at)', $dateFrom, $dateTo])
            ->groupBy('date')
            ->orderBy(['date' => SORT_ASC])
            ->asArray()
            ->all();

        // Top users by activity
        $topUsers = ActivityLog::find()
            ->select(['user_id', 'COUNT(*) as count'])
            ->where(['between', 'DATE(created_at)', $dateFrom, $dateTo])
            ->andWhere(['not', ['user_id' => null]])
            ->groupBy('user_id')
            ->orderBy(['count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Enrich with user data
        $users = User::find()->indexBy('id')->all();
        foreach ($topUsers as &$item) {
            $user = isset($users[$item['user_id']]) ? $users[$item['user_id']] : null;
            $item['username'] = $user ? $user->username : 'ไม่ระบุ';
            $item['fullname'] = $user ? $user->fullname : 'ไม่ระบุ';
        }

        return $this->render('audit', [
            'dataProvider' => $dataProvider,
            'actionTypes' => $actionTypes,
            'activitySummary' => $activitySummary,
            'dailyActivity' => $dailyActivity,
            'topUsers' => $topUsers,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'user_id' => $userId,
                'action_type' => $actionType,
            ],
        ]);
    }

    /**
     * Room usage report
     *
     * @param int $id Room ID
     * @return string
     */
    public function actionRoomUsage($id)
    {
        $room = MeetingRoom::findOne($id);
        if (!$room) {
            throw new \yii\web\NotFoundHttpException('ไม่พบห้องประชุม');
        }

        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));

        // Room statistics
        $stats = [
            'total_bookings' => Booking::find()
                ->where(['room_id' => $id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->count(),
            'completed' => Booking::find()
                ->where(['room_id' => $id, 'status' => Booking::STATUS_COMPLETED])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->count(),
            'cancelled' => Booking::find()
                ->where(['room_id' => $id, 'status' => Booking::STATUS_CANCELLED])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->count(),
            'total_revenue' => Booking::find()
                ->where(['room_id' => $id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->sum('total_cost') ?: 0,
        ];

        // Daily usage
        $dailyUsage = Booking::find()
            ->select(['booking_date', 'COUNT(*) as count', 'SUM(total_cost) as revenue'])
            ->where(['room_id' => $id])
            ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
            ->groupBy('booking_date')
            ->orderBy(['booking_date' => SORT_ASC])
            ->asArray()
            ->all();

        // Top departments using this room
        $topDepartments = Booking::find()
            ->select(['department_id', 'COUNT(*) as count'])
            ->where(['room_id' => $id])
            ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
            ->andWhere(['not', ['department_id' => null]])
            ->groupBy('department_id')
            ->orderBy(['count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Enrich with department names
        $departments = Department::find()->indexBy('id')->all();
        foreach ($topDepartments as &$item) {
            $dept = isset($departments[$item['department_id']]) ? $departments[$item['department_id']] : null;
            $item['name'] = $dept ? $dept->name_th : 'ไม่ระบุ';
        }

        return $this->render('room-usage', [
            'room' => $room,
            'stats' => $stats,
            'dailyUsage' => $dailyUsage,
            'topDepartments' => $topDepartments,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Department usage report
     *
     * @param int $id Department ID
     * @return string
     */
    public function actionDepartmentUsage($id)
    {
        $department = Department::findOne($id);
        if (!$department) {
            throw new \yii\web\NotFoundHttpException('ไม่พบหน่วยงาน');
        }

        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));

        // Department statistics
        $stats = [
            'total_bookings' => Booking::find()
                ->where(['department_id' => $id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->count(),
            'total_users' => User::find()
                ->where(['department_id' => $id, 'status' => User::STATUS_ACTIVE])
                ->count(),
            'active_bookers' => Booking::find()
                ->select('user_id')
                ->where(['department_id' => $id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->distinct()
                ->count(),
            'total_cost' => Booking::find()
                ->where(['department_id' => $id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->sum('total_cost') ?: 0,
        ];

        // Top bookers in department
        $topBookers = Booking::find()
            ->select(['user_id', 'COUNT(*) as count'])
            ->where(['department_id' => $id])
            ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
            ->groupBy('user_id')
            ->orderBy(['count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Enrich with user names
        $users = User::find()->indexBy('id')->all();
        foreach ($topBookers as &$item) {
            $user = isset($users[$item['user_id']]) ? $users[$item['user_id']] : null;
            $item['fullname'] = $user ? $user->fullname : 'ไม่ระบุ';
        }

        // Most used rooms
        $topRooms = Booking::find()
            ->select(['room_id', 'COUNT(*) as count'])
            ->where(['department_id' => $id])
            ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
            ->groupBy('room_id')
            ->orderBy(['count' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Enrich with room names
        $rooms = MeetingRoom::find()->indexBy('id')->all();
        foreach ($topRooms as &$item) {
            $room = isset($rooms[$item['room_id']]) ? $rooms[$item['room_id']] : null;
            $item['name'] = $room ? $room->name_th : 'ไม่ระบุ';
        }

        return $this->render('department-usage', [
            'department' => $department,
            'stats' => $stats,
            'topBookers' => $topBookers,
            'topRooms' => $topRooms,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * Export report data
     *
     * @return Response
     */
    public function actionExport()
    {
        $type = Yii::$app->request->get('type', 'bookings');
        $dateFrom = Yii::$app->request->get('date_from', date('Y-m-01'));
        $dateTo = Yii::$app->request->get('date_to', date('Y-m-t'));
        $format = Yii::$app->request->get('format', 'csv');

        $filename = "report-{$type}-{$dateFrom}-to-{$dateTo}";

        switch ($type) {
            case 'bookings':
                $data = $this->getBookingsExportData($dateFrom, $dateTo);
                $headers = ['รหัสการจอง', 'วันที่', 'เวลา', 'ห้อง', 'ผู้จอง', 'หน่วยงาน', 'สถานะ', 'ค่าใช้จ่าย'];
                break;

            case 'rooms':
                $data = $this->getRoomsExportData($dateFrom, $dateTo);
                $headers = ['ห้องประชุม', 'จำนวนการจอง', 'ชั่วโมงใช้งาน', 'รายได้', 'อัตราใช้งาน'];
                break;

            case 'users':
                $data = $this->getUsersExportData($dateFrom, $dateTo);
                $headers = ['ผู้ใช้', 'หน่วยงาน', 'จำนวนการจอง', 'ค่าใช้จ่ายรวม'];
                break;

            default:
                throw new \yii\web\BadRequestHttpException('Invalid report type');
        }

        if ($format === 'csv') {
            return $this->exportCsv($data, $headers, $filename);
        } else {
            return $this->exportJson($data, $filename);
        }
    }

    /**
     * Get bookings export data
     */
    protected function getBookingsExportData($dateFrom, $dateTo)
    {
        $bookings = Booking::find()
            ->with(['room', 'user', 'department'])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($bookings as $booking) {
            $data[] = [
                $booking->booking_code,
                $booking->booking_date,
                $booking->start_time . ' - ' . $booking->end_time,
                $booking->room ? $booking->room->name_th : '',
                $booking->user ? $booking->user->fullname : '',
                $booking->department ? $booking->department->name_th : '',
                $booking->getStatusLabel(),
                $booking->total_cost,
            ];
        }

        return $data;
    }

    /**
     * Get rooms export data
     */
    protected function getRoomsExportData($dateFrom, $dateTo)
    {
        $rooms = MeetingRoom::find()->where(['status' => MeetingRoom::STATUS_ACTIVE])->all();

        $data = [];
        foreach ($rooms as $room) {
            $stats = Booking::find()
                ->select([
                    'COUNT(*) as count',
                    'SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as hours',
                    'SUM(total_cost) as revenue',
                ])
                ->where(['room_id' => $room->id])
                ->andWhere(['between', 'booking_date', $dateFrom, $dateTo])
                ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED]])
                ->asArray()
                ->one();

            $totalDays = (strtotime($dateTo) - strtotime($dateFrom)) / 86400 + 1;
            $maxHours = $totalDays * 8;
            $utilization = $maxHours > 0 ? round(($stats['hours'] / $maxHours) * 100, 1) : 0;

            $data[] = [
                $room->name_th,
                $stats['count'] ?: 0,
                $stats['hours'] ?: 0,
                $stats['revenue'] ?: 0,
                $utilization . '%',
            ];
        }

        return $data;
    }

    /**
     * Get users export data
     */
    protected function getUsersExportData($dateFrom, $dateTo)
    {
        $userStats = Booking::find()
            ->select(['user_id', 'COUNT(*) as count', 'SUM(total_cost) as total'])
            ->where(['between', 'booking_date', $dateFrom, $dateTo])
            ->groupBy('user_id')
            ->orderBy(['count' => SORT_DESC])
            ->asArray()
            ->all();

        $users = User::find()->indexBy('id')->all();
        $departments = Department::find()->indexBy('id')->all();

        $data = [];
        foreach ($userStats as $stat) {
            $user = isset($users[$stat['user_id']]) ? $users[$stat['user_id']] : null;
            $dept = $user && isset($departments[$user->department_id]) 
                ? $departments[$user->department_id] 
                : null;

            $data[] = [
                $user ? $user->fullname : 'ไม่ระบุ',
                $dept ? $dept->name_th : 'ไม่ระบุ',
                $stat['count'],
                $stat['total'] ?: 0,
            ];
        }

        return $data;
    }

    /**
     * Export to CSV
     */
    protected function exportCsv($data, $headers, $filename)
    {
        $handle = fopen('php://temp', 'r+');

        // BOM for UTF-8
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($handle, $headers);

        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return Yii::$app->response->sendContentAsFile($content, $filename . '.csv', [
            'mimeType' => 'text/csv',
        ]);
    }

    /**
     * Export to JSON
     */
    protected function exportJson($data, $filename)
    {
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return Yii::$app->response->sendContentAsFile($content, $filename . '.json', [
            'mimeType' => 'application/json',
        ]);
    }
}
