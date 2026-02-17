<?php
/**
 * NotificationSettings Controller
 * 
 * Manages user notification preferences including email, push,
 * calendar sync, and reminder settings
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use common\models\UserNotificationSetting;
use common\models\UserPushToken;
use common\models\UserOauth;

class NotificationSettingsController extends Controller
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
                    'save' => ['POST'],
                    'register-token' => ['POST'],
                    'unregister-token' => ['POST'],
                    'remove-device' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Display notification settings page
     * @return string
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $settings = UserNotificationSetting::getForUser($userId);
        
        // Get OAuth connections for calendar sync
        $oauthConnections = UserOauth::find()
            ->where(['user_id' => $userId])
            ->indexBy('provider')
            ->all();
        
        // Get registered devices
        $devices = UserPushToken::getUserDevices($userId);
        
        // Check if push notifications are available
        $pushEnabled = Yii::$app->has('pushNotification') 
            && Yii::$app->pushNotification->isEnabled();
        
        // Check if calendar sync is available
        $calendarSyncEnabled = Yii::$app->has('calendarSync');
        
        return $this->render('index', [
            'settings' => $settings,
            'oauthConnections' => $oauthConnections,
            'devices' => $devices,
            'pushEnabled' => $pushEnabled,
            'calendarSyncEnabled' => $calendarSyncEnabled,
            'reminderOptions' => UserNotificationSetting::getReminderOptions(),
        ]);
    }
    
    /**
     * Save notification settings
     * @return Response
     */
    public function actionSave()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $settings = UserNotificationSetting::getForUser($userId);
        
        $data = Yii::$app->request->post();
        
        if ($settings->updateFromForm($data)) {
            return [
                'success' => true,
                'message' => 'บันทึกการตั้งค่าเรียบร้อยแล้ว',
            ];
        }
        
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาดในการบันทึก',
            'errors' => $settings->errors,
        ];
    }
    
    /**
     * Register push notification token
     * @return Response
     */
    public function actionRegisterToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $userId = Yii::$app->user->id;
        $token = Yii::$app->request->post('token');
        $provider = Yii::$app->request->post('provider', 'fcm');
        $platform = Yii::$app->request->post('platform', 'web');
        
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token is required',
            ];
        }
        
        $deviceInfo = [
            'device_id' => Yii::$app->request->post('device_id'),
            'device_name' => Yii::$app->request->post('device_name'),
            'app_version' => Yii::$app->request->post('app_version'),
        ];
        
        $pushToken = UserPushToken::register($userId, $token, $provider, $platform, $deviceInfo);
        
        if ($pushToken) {
            // Subscribe to user topic for FCM
            if ($provider === 'fcm' && Yii::$app->has('pushNotification')) {
                Yii::$app->pushNotification->subscribeToTopic($token, "user_{$userId}");
            }
            
            return [
                'success' => true,
                'message' => 'Token registered successfully',
                'token_id' => $pushToken->id,
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to register token',
        ];
    }
    
    /**
     * Unregister push notification token
     * @return Response
     */
    public function actionUnregisterToken()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $token = Yii::$app->request->post('token');
        
        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Token is required',
            ];
        }
        
        // Verify token belongs to current user
        $pushToken = UserPushToken::findOne([
            'token' => $token,
            'user_id' => Yii::$app->user->id,
        ]);
        
        if (!$pushToken) {
            return [
                'success' => false,
                'message' => 'Token not found',
            ];
        }
        
        // Unsubscribe from topic
        if ($pushToken->provider === 'fcm' && Yii::$app->has('pushNotification')) {
            Yii::$app->pushNotification->unsubscribeFromTopic($token, "user_" . Yii::$app->user->id);
        }
        
        if (UserPushToken::unregister($token)) {
            return [
                'success' => true,
                'message' => 'Token unregistered successfully',
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to unregister token',
        ];
    }
    
    /**
     * Remove a device from push notifications
     * @return Response
     */
    public function actionRemoveDevice()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $tokenId = Yii::$app->request->post('token_id');
        
        if (empty($tokenId)) {
            return [
                'success' => false,
                'message' => 'Token ID is required',
            ];
        }
        
        $pushToken = UserPushToken::findOne([
            'id' => $tokenId,
            'user_id' => Yii::$app->user->id,
        ]);
        
        if (!$pushToken) {
            return [
                'success' => false,
                'message' => 'Device not found',
            ];
        }
        
        // Unsubscribe from topic
        if ($pushToken->provider === 'fcm' && Yii::$app->has('pushNotification')) {
            Yii::$app->pushNotification->unsubscribeFromTopic($pushToken->token, "user_" . Yii::$app->user->id);
        }
        
        if ($pushToken->delete()) {
            return [
                'success' => true,
                'message' => 'ลบอุปกรณ์เรียบร้อยแล้ว',
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to remove device',
        ];
    }
    
    /**
     * Toggle calendar sync for a provider
     * @return Response
     */
    public function actionToggleCalendarSync()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $provider = Yii::$app->request->post('provider');
        $enabled = Yii::$app->request->post('enabled');
        
        if (!in_array($provider, ['google', 'microsoft'])) {
            return [
                'success' => false,
                'message' => 'Invalid provider',
            ];
        }
        
        $userId = Yii::$app->user->id;
        
        // Check if user has OAuth connection
        $oauth = UserOauth::findOne([
            'user_id' => $userId,
            'provider' => $provider,
        ]);
        
        if (!$oauth && $enabled) {
            return [
                'success' => false,
                'message' => 'โปรดเชื่อมต่อบัญชี ' . ucfirst($provider) . ' ก่อน',
                'require_oauth' => true,
            ];
        }
        
        $settings = UserNotificationSetting::getForUser($userId);
        $attribute = 'calendar_sync_' . $provider;
        $settings->$attribute = (bool)$enabled;
        
        if ($settings->save(false)) {
            return [
                'success' => true,
                'message' => $enabled 
                    ? 'เปิดการซิงค์ปฏิทินเรียบร้อยแล้ว' 
                    : 'ปิดการซิงค์ปฏิทินเรียบร้อยแล้ว',
            ];
        }
        
        return [
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด',
        ];
    }
    
    /**
     * Test push notification
     * @return Response
     */
    public function actionTestPush()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (!Yii::$app->has('pushNotification')) {
            return [
                'success' => false,
                'message' => 'Push notification service not available',
            ];
        }
        
        $userId = Yii::$app->user->id;
        
        $result = Yii::$app->pushNotification->sendToUser(
            $userId,
            'ทดสอบการแจ้งเตือน',
            'นี่คือการทดสอบ Push Notification จากระบบจองห้องประชุม',
            ['test' => true]
        );
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'ส่งการแจ้งเตือนทดสอบเรียบร้อยแล้ว',
            ];
        }
        
        return [
            'success' => false,
            'message' => 'ไม่สามารถส่งการแจ้งเตือนได้: ' . ($result['error'] ?? 'Unknown error'),
        ];
    }
}
