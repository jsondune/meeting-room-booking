<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use common\models\Notification;

/**
 * NotificationController handles user notifications
 */
class NotificationController extends Controller
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
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'mark-read' => ['post'],
                    'mark-all-read' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all notifications for current user
     *
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        
        $notifications = Notification::find()
            ->where(['user_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(100)
            ->all();
        
        $unreadCount = Notification::find()
            ->where(['user_id' => $userId, 'is_read' => false])
            ->count();

        return $this->render('index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Get unread notifications count (AJAX)
     *
     * @return array
     */
    public function actionUnreadCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $count = Notification::find()
            ->where(['user_id' => Yii::$app->user->id, 'is_read' => false])
            ->count();

        return ['count' => $count];
    }

    /**
     * Get recent notifications (AJAX)
     *
     * @return array
     */
    public function actionRecent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $notifications = Notification::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(10)
            ->all();

        $items = [];
        foreach ($notifications as $notification) {
            $items[] = [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'is_read' => (bool)$notification->is_read,
                'created_at' => Yii::$app->formatter->asRelativeTime($notification->created_at),
                'url' => $notification->url,
            ];
        }

        return [
            'success' => true,
            'notifications' => $items,
            'unread_count' => Notification::find()
                ->where(['user_id' => Yii::$app->user->id, 'is_read' => false])
                ->count(),
        ];
    }

    /**
     * Mark notification as read
     *
     * @param int $id
     * @return array
     */
    public function actionMarkRead($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$notification) {
            return ['success' => false, 'message' => 'ไม่พบการแจ้งเตือน'];
        }

        $notification->is_read = true;
        $notification->read_at = date('Y-m-d H:i:s');
        $notification->save(false);

        return ['success' => true];
    }

    /**
     * Mark all notifications as read
     *
     * @return array
     */
    public function actionMarkAllRead()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        Notification::updateAll(
            ['is_read' => true, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => Yii::$app->user->id, 'is_read' => false]
        );

        return ['success' => true];
    }

    /**
     * Delete notification
     *
     * @param int $id
     * @return array
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if (!$notification) {
            return ['success' => false, 'message' => 'ไม่พบการแจ้งเตือน'];
        }

        $notification->delete();

        return ['success' => true];
    }
}
