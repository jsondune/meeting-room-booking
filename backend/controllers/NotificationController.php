<?php
/**
 * Notification Controller - Backend
 * Handles user notifications
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\Notification;

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
     * Lists all notifications
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Notification::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Get recent notifications (AJAX)
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
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'link' => $notification->link,
                'is_read' => (bool)$notification->is_read,
                'created_at' => Yii::$app->formatter->asRelativeTime($notification->created_at),
                'icon' => $this->getTypeIcon($notification->type),
                'color' => $this->getTypeColor($notification->type),
            ];
        }

        return [
            'success' => true,
            'count' => Notification::getUnreadCount(Yii::$app->user->id),
            'items' => $items,
        ];
    }

    /**
     * Mark notification as read
     */
    public function actionMarkRead($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if ($notification) {
            $notification->markAsRead();
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Notification not found'];
    }

    /**
     * Mark all notifications as read
     */
    public function actionMarkAllRead()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        Notification::updateAll(
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
            ['user_id' => Yii::$app->user->id, 'is_read' => 0]
        );

        return ['success' => true];
    }

    /**
     * Get unread count (AJAX)
     */
    public function actionUnreadCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'count' => Notification::getUnreadCount(Yii::$app->user->id),
        ];
    }

    /**
     * Delete notification
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $notification = Notification::findOne([
            'id' => $id,
            'user_id' => Yii::$app->user->id,
        ]);

        if ($notification && $notification->delete()) {
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'Cannot delete notification'];
    }

    /**
     * Get icon for notification type
     */
    protected function getTypeIcon($type)
    {
        $icons = [
            Notification::TYPE_BOOKING_CREATED => 'bi-calendar-plus',
            Notification::TYPE_BOOKING_APPROVED => 'bi-check-circle',
            Notification::TYPE_BOOKING_REJECTED => 'bi-x-circle',
            Notification::TYPE_BOOKING_CANCELLED => 'bi-calendar-x',
            Notification::TYPE_BOOKING_REMINDER => 'bi-alarm',
            Notification::TYPE_BOOKING_UPDATED => 'bi-pencil-square',
            Notification::TYPE_SYSTEM => 'bi-gear',
            Notification::TYPE_INFO => 'bi-info-circle',
            Notification::TYPE_WARNING => 'bi-exclamation-triangle',
        ];

        return $icons[$type] ?? 'bi-bell';
    }

    /**
     * Get color for notification type
     */
    protected function getTypeColor($type)
    {
        $colors = [
            Notification::TYPE_BOOKING_CREATED => 'primary',
            Notification::TYPE_BOOKING_APPROVED => 'success',
            Notification::TYPE_BOOKING_REJECTED => 'danger',
            Notification::TYPE_BOOKING_CANCELLED => 'warning',
            Notification::TYPE_BOOKING_REMINDER => 'info',
            Notification::TYPE_BOOKING_UPDATED => 'secondary',
            Notification::TYPE_SYSTEM => 'dark',
            Notification::TYPE_INFO => 'info',
            Notification::TYPE_WARNING => 'warning',
        ];

        return $colors[$type] ?? 'secondary';
    }
}
