<?php
/**
 * Push Notification Service
 * 
 * Handles push notifications via Firebase Cloud Messaging (FCM)
 * and OneSignal for mobile and web push notifications
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\components;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use common\models\User;

class PushNotificationService extends Component
{
    /**
     * @var string Firebase project ID
     */
    public $firebaseProjectId;
    
    /**
     * @var string Firebase Server Key (legacy) or Service Account JSON path
     */
    public $firebaseCredentials;
    
    /**
     * @var string OneSignal App ID
     */
    public $oneSignalAppId;
    
    /**
     * @var string OneSignal REST API Key
     */
    public $oneSignalApiKey;
    
    /**
     * @var bool Enable FCM
     */
    public $fcmEnabled = false;
    
    /**
     * @var bool Enable OneSignal
     */
    public $oneSignalEnabled = false;
    
    /**
     * @var Client HTTP client
     */
    protected $httpClient;
    
    /**
     * @var string FCM access token (for HTTP v1 API)
     */
    protected $fcmAccessToken;
    
    /**
     * @var int FCM token expiry time
     */
    protected $fcmTokenExpiry = 0;
    
    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
        
        // Load from environment
        $this->firebaseProjectId = getenv('FIREBASE_PROJECT_ID') ?: $this->firebaseProjectId;
        $this->firebaseCredentials = getenv('FIREBASE_CREDENTIALS') ?: $this->firebaseCredentials;
        $this->oneSignalAppId = getenv('ONESIGNAL_APP_ID') ?: $this->oneSignalAppId;
        $this->oneSignalApiKey = getenv('ONESIGNAL_API_KEY') ?: $this->oneSignalApiKey;
        
        $this->fcmEnabled = filter_var(
            getenv('FCM_ENABLED') ?: $this->fcmEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        
        $this->oneSignalEnabled = filter_var(
            getenv('ONESIGNAL_ENABLED') ?: $this->oneSignalEnabled,
            FILTER_VALIDATE_BOOLEAN
        );
        
        $this->httpClient = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
        ]);
    }
    
    /**
     * Check if push notifications are enabled
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->fcmEnabled || $this->oneSignalEnabled;
    }
    
    /**
     * Send push notification to a user
     * @param int $userId User ID
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @param array $options Notification options
     * @return array Results from each provider
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = [], array $options = []): array
    {
        $results = [];
        $user = User::findOne($userId);
        
        if (!$user) {
            return ['success' => false, 'error' => 'User not found'];
        }
        
        // Get user's push tokens
        $tokens = $this->getUserPushTokens($userId);
        
        if (empty($tokens['fcm']) && empty($tokens['onesignal'])) {
            return ['success' => false, 'error' => 'No push tokens registered'];
        }
        
        // Send via FCM
        if ($this->fcmEnabled && !empty($tokens['fcm'])) {
            $results['fcm'] = $this->sendViaFCM($tokens['fcm'], $title, $body, $data, $options);
        }
        
        // Send via OneSignal
        if ($this->oneSignalEnabled && !empty($tokens['onesignal'])) {
            $results['onesignal'] = $this->sendViaOneSignal($tokens['onesignal'], $title, $body, $data, $options);
        }
        
        $results['success'] = !empty($results['fcm']['success']) || !empty($results['onesignal']['success']);
        
        return $results;
    }
    
    /**
     * Send push notification to multiple users
     * @param array $userIds User IDs
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @return array Results
     */
    public function sendToUsers(array $userIds, string $title, string $body, array $data = []): array
    {
        $results = ['success' => 0, 'failed' => 0, 'details' => []];
        
        foreach ($userIds as $userId) {
            $result = $this->sendToUser($userId, $title, $body, $data);
            if ($result['success']) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
            $results['details'][$userId] = $result;
        }
        
        return $results;
    }
    
    /**
     * Send push notification to a topic/segment
     * @param string $topic Topic name
     * @param string $title Notification title
     * @param string $body Notification body
     * @param array $data Additional data payload
     * @return array
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        if ($this->fcmEnabled) {
            $results['fcm'] = $this->sendToFCMTopic($topic, $title, $body, $data);
        }
        
        if ($this->oneSignalEnabled) {
            $results['onesignal'] = $this->sendToOneSignalSegment($topic, $title, $body, $data);
        }
        
        return $results;
    }
    
    /**
     * Send notification via Firebase Cloud Messaging (HTTP v1 API)
     * @param array $tokens FCM device tokens
     * @param string $title Title
     * @param string $body Body
     * @param array $data Data payload
     * @param array $options Options
     * @return array
     */
    protected function sendViaFCM(array $tokens, string $title, string $body, array $data = [], array $options = []): array
    {
        if (!$this->firebaseProjectId || !$this->firebaseCredentials) {
            return ['success' => false, 'error' => 'FCM not configured'];
        }
        
        $accessToken = $this->getFCMAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'error' => 'Failed to get FCM access token'];
        }
        
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        foreach ($tokens as $token) {
            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => $options['channel_id'] ?? 'default',
                            'icon' => $options['icon'] ?? 'ic_notification',
                            'color' => $options['color'] ?? '#4F46E5',
                        ],
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'badge' => $options['badge'] ?? 1,
                                'sound' => $options['sound'] ?? 'default',
                            ],
                        ],
                    ],
                    'webpush' => [
                        'notification' => [
                            'icon' => $options['web_icon'] ?? '/images/notification-icon.png',
                            'badge' => $options['web_badge'] ?? '/images/badge-icon.png',
                        ],
                        'fcm_options' => [
                            'link' => $options['click_action'] ?? null,
                        ],
                    ],
                ],
            ];
            
            try {
                $response = $this->httpClient->createRequest()
                    ->setMethod('POST')
                    ->setUrl("https://fcm.googleapis.com/v1/projects/{$this->firebaseProjectId}/messages:send")
                    ->setHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Content-Type' => 'application/json',
                    ])
                    ->setContent(json_encode($message))
                    ->send();
                
                if ($response->isOk) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'token' => substr($token, 0, 20) . '...',
                        'error' => $response->data['error']['message'] ?? 'Unknown error',
                    ];
                    
                    // Remove invalid token
                    if ($this->isInvalidTokenError($response->data)) {
                        $this->removeInvalidToken($token, 'fcm');
                    }
                }
                
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = ['error' => $e->getMessage()];
            }
        }
        
        $results['success'] = $results['success'] > 0;
        
        return $results;
    }
    
    /**
     * Get FCM access token using service account
     * @return string|null
     */
    protected function getFCMAccessToken(): ?string
    {
        // Check if current token is still valid
        if ($this->fcmAccessToken && $this->fcmTokenExpiry > time()) {
            return $this->fcmAccessToken;
        }
        
        // Try cache first
        $cacheKey = 'fcm_access_token';
        $cached = Yii::$app->cache->get($cacheKey);
        if ($cached) {
            $this->fcmAccessToken = $cached['token'];
            $this->fcmTokenExpiry = $cached['expiry'];
            return $this->fcmAccessToken;
        }
        
        // Load service account credentials
        $credentials = null;
        if (file_exists($this->firebaseCredentials)) {
            $credentials = json_decode(file_get_contents($this->firebaseCredentials), true);
        } elseif (is_string($this->firebaseCredentials) && strpos($this->firebaseCredentials, '{') === 0) {
            $credentials = json_decode($this->firebaseCredentials, true);
        }
        
        if (!$credentials) {
            Yii::error('Invalid Firebase credentials', 'push-notification');
            return null;
        }
        
        try {
            // Create JWT for service account
            $jwt = $this->createServiceAccountJWT($credentials);
            
            // Exchange JWT for access token
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl('https://oauth2.googleapis.com/token')
                ->setData([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ])
                ->send();
            
            if ($response->isOk && isset($response->data['access_token'])) {
                $this->fcmAccessToken = $response->data['access_token'];
                $this->fcmTokenExpiry = time() + ($response->data['expires_in'] ?? 3600) - 60;
                
                // Cache the token
                Yii::$app->cache->set($cacheKey, [
                    'token' => $this->fcmAccessToken,
                    'expiry' => $this->fcmTokenExpiry,
                ], $response->data['expires_in'] ?? 3600 - 120);
                
                return $this->fcmAccessToken;
            }
            
        } catch (\Exception $e) {
            Yii::error('Failed to get FCM access token: ' . $e->getMessage(), 'push-notification');
        }
        
        return null;
    }
    
    /**
     * Create JWT for service account authentication
     * @param array $credentials Service account credentials
     * @return string JWT
     */
    protected function createServiceAccountJWT(array $credentials): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
        
        $now = time();
        $claims = [
            'iss' => $credentials['client_email'],
            'sub' => $credentials['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $claimsEncoded = $this->base64UrlEncode(json_encode($claims));
        
        $signatureInput = $headerEncoded . '.' . $claimsEncoded;
        
        openssl_sign($signatureInput, $signature, $credentials['private_key'], 'SHA256');
        $signatureEncoded = $this->base64UrlEncode($signature);
        
        return $signatureInput . '.' . $signatureEncoded;
    }
    
    /**
     * Base64 URL encode
     * @param string $data Data to encode
     * @return string
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Send to FCM topic
     * @param string $topic Topic name
     * @param string $title Title
     * @param string $body Body
     * @param array $data Data payload
     * @return array
     */
    protected function sendToFCMTopic(string $topic, string $title, string $body, array $data = []): array
    {
        $accessToken = $this->getFCMAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'error' => 'Failed to get FCM access token'];
        }
        
        $message = [
            'message' => [
                'topic' => $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data),
            ],
        ];
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl("https://fcm.googleapis.com/v1/projects/{$this->firebaseProjectId}/messages:send")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($message))
                ->send();
            
            return [
                'success' => $response->isOk,
                'response' => $response->data,
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send notification via OneSignal
     * @param array $playerIds OneSignal player IDs
     * @param string $title Title
     * @param string $body Body
     * @param array $data Data payload
     * @param array $options Options
     * @return array
     */
    protected function sendViaOneSignal(array $playerIds, string $title, string $body, array $data = [], array $options = []): array
    {
        if (!$this->oneSignalAppId || !$this->oneSignalApiKey) {
            return ['success' => false, 'error' => 'OneSignal not configured'];
        }
        
        $notification = [
            'app_id' => $this->oneSignalAppId,
            'include_player_ids' => $playerIds,
            'headings' => ['en' => $title, 'th' => $title],
            'contents' => ['en' => $body, 'th' => $body],
            'data' => $data,
            'android_channel_id' => $options['channel_id'] ?? null,
            'small_icon' => $options['icon'] ?? 'ic_notification',
            'android_accent_color' => $options['color'] ?? '4F46E5',
            'ios_badgeType' => 'Increase',
            'ios_badgeCount' => 1,
        ];
        
        if (isset($options['click_action'])) {
            $notification['url'] = $options['click_action'];
        }
        
        if (isset($options['image'])) {
            $notification['big_picture'] = $options['image'];
            $notification['ios_attachments'] = ['image' => $options['image']];
        }
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl('https://onesignal.com/api/v1/notifications')
                ->setHeaders([
                    'Authorization' => 'Basic ' . $this->oneSignalApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($notification))
                ->send();
            
            if ($response->isOk) {
                return [
                    'success' => true,
                    'notification_id' => $response->data['id'] ?? null,
                    'recipients' => $response->data['recipients'] ?? 0,
                ];
            }
            
            return [
                'success' => false,
                'error' => $response->data['errors'] ?? 'Unknown error',
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send to OneSignal segment
     * @param string $segment Segment name
     * @param string $title Title
     * @param string $body Body
     * @param array $data Data payload
     * @return array
     */
    protected function sendToOneSignalSegment(string $segment, string $title, string $body, array $data = []): array
    {
        if (!$this->oneSignalAppId || !$this->oneSignalApiKey) {
            return ['success' => false, 'error' => 'OneSignal not configured'];
        }
        
        $notification = [
            'app_id' => $this->oneSignalAppId,
            'included_segments' => [$segment],
            'headings' => ['en' => $title, 'th' => $title],
            'contents' => ['en' => $body, 'th' => $body],
            'data' => $data,
        ];
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl('https://onesignal.com/api/v1/notifications')
                ->setHeaders([
                    'Authorization' => 'Basic ' . $this->oneSignalApiKey,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($notification))
                ->send();
            
            return [
                'success' => $response->isOk,
                'response' => $response->data,
            ];
            
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Register push token for a user
     * @param int $userId User ID
     * @param string $token Push token
     * @param string $provider Provider (fcm, onesignal)
     * @param string $platform Platform (android, ios, web)
     * @param string|null $deviceId Device identifier
     * @return bool
     */
    public function registerToken(int $userId, string $token, string $provider, string $platform, ?string $deviceId = null): bool
    {
        try {
            Yii::$app->db->createCommand()->upsert('{{%user_push_tokens}}', [
                'user_id' => $userId,
                'token' => $token,
                'provider' => $provider,
                'platform' => $platform,
                'device_id' => $deviceId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], [
                'token' => $token,
                'updated_at' => date('Y-m-d H:i:s'),
            ])->execute();
            
            // Subscribe to user-specific topic
            if ($provider === 'fcm') {
                $this->subscribeToTopic($token, "user_{$userId}");
            }
            
            return true;
            
        } catch (\Exception $e) {
            Yii::error('Failed to register push token: ' . $e->getMessage(), 'push-notification');
            return false;
        }
    }
    
    /**
     * Unregister push token
     * @param string $token Push token
     * @return bool
     */
    public function unregisterToken(string $token): bool
    {
        try {
            return Yii::$app->db->createCommand()
                ->delete('{{%user_push_tokens}}', ['token' => $token])
                ->execute() > 0;
        } catch (\Exception $e) {
            Yii::error('Failed to unregister push token: ' . $e->getMessage(), 'push-notification');
            return false;
        }
    }
    
    /**
     * Get user's push tokens
     * @param int $userId User ID
     * @return array
     */
    protected function getUserPushTokens(int $userId): array
    {
        try {
            $tokens = Yii::$app->db->createCommand(
                'SELECT token, provider, platform FROM {{%user_push_tokens}} WHERE user_id = :userId'
            )->bindValue(':userId', $userId)->queryAll();
            
            $result = ['fcm' => [], 'onesignal' => []];
            foreach ($tokens as $row) {
                $result[$row['provider']][] = $row['token'];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            Yii::error('Failed to get user push tokens: ' . $e->getMessage(), 'push-notification');
            return ['fcm' => [], 'onesignal' => []];
        }
    }
    
    /**
     * Subscribe token to FCM topic
     * @param string $token FCM token
     * @param string $topic Topic name
     * @return bool
     */
    public function subscribeToTopic(string $token, string $topic): bool
    {
        $accessToken = $this->getFCMAccessToken();
        if (!$accessToken) {
            return false;
        }
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl("https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->send();
            
            return $response->isOk;
            
        } catch (\Exception $e) {
            Yii::error('Failed to subscribe to FCM topic: ' . $e->getMessage(), 'push-notification');
            return false;
        }
    }
    
    /**
     * Unsubscribe token from FCM topic
     * @param string $token FCM token
     * @param string $topic Topic name
     * @return bool
     */
    public function unsubscribeFromTopic(string $token, string $topic): bool
    {
        $accessToken = $this->getFCMAccessToken();
        if (!$accessToken) {
            return false;
        }
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('DELETE')
                ->setUrl("https://iid.googleapis.com/iid/v1/{$token}/rel/topics/{$topic}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->send();
            
            return $response->isOk;
            
        } catch (\Exception $e) {
            Yii::error('Failed to unsubscribe from FCM topic: ' . $e->getMessage(), 'push-notification');
            return false;
        }
    }
    
    /**
     * Check if error indicates invalid token
     * @param array $response FCM response
     * @return bool
     */
    protected function isInvalidTokenError(array $response): bool
    {
        $errorCode = $response['error']['details'][0]['errorCode'] ?? '';
        return in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT']);
    }
    
    /**
     * Remove invalid token from database
     * @param string $token Token
     * @param string $provider Provider
     */
    protected function removeInvalidToken(string $token, string $provider): void
    {
        try {
            Yii::$app->db->createCommand()
                ->delete('{{%user_push_tokens}}', [
                    'token' => $token,
                    'provider' => $provider,
                ])
                ->execute();
            
            Yii::info("Removed invalid {$provider} token", 'push-notification');
            
        } catch (\Exception $e) {
            Yii::error('Failed to remove invalid token: ' . $e->getMessage(), 'push-notification');
        }
    }
    
    /**
     * Send booking notification
     * @param int $userId User ID
     * @param string $type Notification type
     * @param array $bookingData Booking data
     * @return array
     */
    public function sendBookingNotification(int $userId, string $type, array $bookingData): array
    {
        $titles = [
            'booking_created' => 'การจองใหม่',
            'booking_approved' => 'การจองได้รับอนุมัติ',
            'booking_rejected' => 'การจองถูกปฏิเสธ',
            'booking_cancelled' => 'การจองถูกยกเลิก',
            'booking_reminder' => 'แจ้งเตือนการประชุม',
            'pending_approval' => 'รอการอนุมัติ',
        ];
        
        $bodies = [
            'booking_created' => "การจอง '{$bookingData['title']}' ถูกสร้างเรียบร้อย",
            'booking_approved' => "การจอง '{$bookingData['title']}' ได้รับการอนุมัติแล้ว",
            'booking_rejected' => "การจอง '{$bookingData['title']}' ถูกปฏิเสธ",
            'booking_cancelled' => "การจอง '{$bookingData['title']}' ถูกยกเลิกแล้ว",
            'booking_reminder' => "การประชุม '{$bookingData['title']}' จะเริ่มในอีก 30 นาที",
            'pending_approval' => "มีการจองใหม่รอการอนุมัติ: {$bookingData['title']}",
        ];
        
        $title = $titles[$type] ?? 'แจ้งเตือนการจอง';
        $body = $bodies[$type] ?? $bookingData['title'];
        
        $data = [
            'type' => $type,
            'booking_id' => (string)($bookingData['id'] ?? ''),
            'room_id' => (string)($bookingData['room_id'] ?? ''),
        ];
        
        $options = [
            'click_action' => Yii::$app->params['frontendUrl'] . '/booking/view?id=' . ($bookingData['id'] ?? ''),
            'channel_id' => 'booking_notifications',
        ];
        
        return $this->sendToUser($userId, $title, $body, $data, $options);
    }
}
