<?php

namespace api\controllers\v1;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Notification;

/**
 * NotificationController handles notification API operations
 */
class NotificationController extends BaseController
{
    /**
     * List user's notifications
     * GET /api/v1/notifications
     *
     * @return array
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $unreadOnly = Yii::$app->request->get('unread_only', false);

        $query = Notification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC]);

        if ($unreadOnly) {
            $query->andWhere(['is_read' => 0]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 20),
            ],
        ]);

        $notifications = [];
        foreach ($dataProvider->getModels() as $notification) {
            $notifications[] = $this->formatNotification($notification);
        }

        return $this->success([
            'notifications' => $notifications,
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $dataProvider->pagination->getPage() + 1,
                'per_page' => $dataProvider->pagination->getPageSize(),
                'page_count' => $dataProvider->pagination->getPageCount(),
            ],
        ]);
    }

    /**
     * Get unread notification count
     * GET /api/v1/notifications/unread-count
     *
     * @return array
     */
    public function actionUnreadCount()
    {
        $userId = Yii::$app->user->id;

        $count = Notification::find()
            ->where(['user_id' => $userId, 'is_read' => 0])
            ->count();

        return $this->success([
            'count' => (int)$count,
        ]);
    }

    /**
     * Mark notification as read
     * PUT /api/v1/notifications/{id}/read
     *
     * @param int $id
     * @return array
     */
    public function actionRead($id)
    {
        $userId = Yii::$app->user->id;

        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => $userId,
        ]);

        if (!$notification) {
            return $this->error('ไม่พบการแจ้งเตือน', 404);
        }

        if ($notification->is_read) {
            return $this->success([
                'notification' => $this->formatNotification($notification),
            ], 'การแจ้งเตือนถูกอ่านแล้ว');
        }

        $notification->is_read = 1;
        $notification->read_at = date('Y-m-d H:i:s');

        if ($notification->save(false)) {
            return $this->success([
                'notification' => $this->formatNotification($notification),
            ], 'ทำเครื่องหมายว่าอ่านแล้วสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาด', 500);
    }

    /**
     * Mark all notifications as read
     * PUT /api/v1/notifications/read-all
     *
     * @return array
     */
    public function actionReadAll()
    {
        $userId = Yii::$app->user->id;

        $count = Notification::updateAll(
            [
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => $userId,
                'is_read' => 0,
            ]
        );

        return $this->success([
            'marked_count' => $count,
        ], 'ทำเครื่องหมายอ่านแล้วทั้งหมดสำเร็จ');
    }

    /**
     * Delete notification
     * DELETE /api/v1/notifications/{id}
     *
     * @param int $id
     * @return array
     */
    public function actionDelete($id)
    {
        $userId = Yii::$app->user->id;

        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => $userId,
        ]);

        if (!$notification) {
            return $this->error('ไม่พบการแจ้งเตือน', 404);
        }

        if ($notification->delete()) {
            return $this->success(null, 'ลบการแจ้งเตือนสำเร็จ');
        }

        return $this->error('เกิดข้อผิดพลาดในการลบ', 500);
    }

    /**
     * Format notification data
     *
     * @param Notification $notification
     * @return array
     */
    protected function formatNotification($notification)
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'icon' => $notification->icon,
            'url' => $notification->url,
            'data' => $notification->data,
            'is_read' => (bool)$notification->is_read,
            'read_at' => $notification->read_at,
            'created_at' => $notification->created_at,
            'time_ago' => Yii::$app->formatter->asRelativeTime($notification->created_at),
        ];
    }
}
