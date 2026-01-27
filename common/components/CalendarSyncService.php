<?php
/**
 * Calendar Synchronization Service
 * 
 * Handles synchronization of bookings with external calendars
 * (Google Calendar, Microsoft Outlook)
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\components;

use Yii;
use yii\base\Component;
use common\models\Booking;
use common\models\User;
use common\models\UserOauth;

class CalendarSyncService extends Component
{
    /**
     * @var GoogleOAuth2Client Google OAuth client
     */
    public $googleClient;
    
    /**
     * @var MicrosoftOAuth2Client Microsoft OAuth client
     */
    public $microsoftClient;
    
    /**
     * @var bool Enable calendar sync feature
     */
    public $enabled = true;
    
    /**
     * Initialize the component
     */
    public function init()
    {
        parent::init();
        
        $this->enabled = filter_var(
            getenv('CALENDAR_SYNC_ENABLED') ?: true,
            FILTER_VALIDATE_BOOLEAN
        );
        
        // Initialize OAuth clients if not set
        if ($this->googleClient === null && Yii::$app->has('googleOAuth')) {
            $this->googleClient = Yii::$app->googleOAuth;
        }
        
        if ($this->microsoftClient === null && Yii::$app->has('microsoftOAuth')) {
            $this->microsoftClient = Yii::$app->microsoftOAuth;
        }
    }
    
    /**
     * Check if user has calendar sync enabled
     * @param int $userId User ID
     * @param string $provider Provider name (google, microsoft)
     * @return bool
     */
    public function isUserSyncEnabled(int $userId, string $provider): bool
    {
        $user = User::findOne($userId);
        if (!$user) {
            return false;
        }
        
        // Check user preferences (stored in JSON settings field or separate table)
        $settings = $this->getUserCalendarSettings($userId);
        
        return isset($settings[$provider]['enabled']) && $settings[$provider]['enabled'];
    }
    
    /**
     * Get user calendar sync settings
     * @param int $userId User ID
     * @return array
     */
    public function getUserCalendarSettings(int $userId): array
    {
        $cacheKey = "user_calendar_settings_{$userId}";
        $settings = Yii::$app->cache->get($cacheKey);
        
        if ($settings === false) {
            $user = User::findOne($userId);
            if ($user && isset($user->settings)) {
                $allSettings = is_string($user->settings) ? json_decode($user->settings, true) : $user->settings;
                $settings = $allSettings['calendar_sync'] ?? [];
            } else {
                $settings = [];
            }
            
            Yii::$app->cache->set($cacheKey, $settings, 300); // Cache 5 minutes
        }
        
        return $settings;
    }
    
    /**
     * Update user calendar sync settings
     * @param int $userId User ID
     * @param array $calendarSettings Calendar settings
     * @return bool
     */
    public function updateUserCalendarSettings(int $userId, array $calendarSettings): bool
    {
        $user = User::findOne($userId);
        if (!$user) {
            return false;
        }
        
        $settings = is_string($user->settings) ? json_decode($user->settings, true) : ($user->settings ?? []);
        $settings['calendar_sync'] = $calendarSettings;
        $user->settings = json_encode($settings);
        
        if ($user->save(false)) {
            Yii::$app->cache->delete("user_calendar_settings_{$userId}");
            return true;
        }
        
        return false;
    }
    
    /**
     * Sync booking to external calendars
     * @param Booking $booking Booking model
     * @return array Results for each provider
     */
    public function syncBooking(Booking $booking): array
    {
        if (!$this->enabled) {
            return ['success' => false, 'message' => 'Calendar sync is disabled'];
        }
        
        $results = [];
        $userId = $booking->user_id;
        
        // Sync to Google Calendar
        if ($this->shouldSyncToGoogle($userId)) {
            $results['google'] = $this->syncToGoogleCalendar($booking);
        }
        
        // Sync to Microsoft Outlook
        if ($this->shouldSyncToMicrosoft($userId)) {
            $results['microsoft'] = $this->syncToMicrosoftCalendar($booking);
        }
        
        return $results;
    }
    
    /**
     * Check if should sync to Google
     * @param int $userId User ID
     * @return bool
     */
    protected function shouldSyncToGoogle(int $userId): bool
    {
        if (!$this->googleClient || !$this->googleClient->isConfigured()) {
            return false;
        }
        
        return $this->isUserSyncEnabled($userId, 'google') 
            && $this->hasValidOAuthConnection($userId, 'google');
    }
    
    /**
     * Check if should sync to Microsoft
     * @param int $userId User ID
     * @return bool
     */
    protected function shouldSyncToMicrosoft(int $userId): bool
    {
        if (!$this->microsoftClient || !$this->microsoftClient->isConfigured()) {
            return false;
        }
        
        return $this->isUserSyncEnabled($userId, 'microsoft')
            && $this->hasValidOAuthConnection($userId, 'microsoft');
    }
    
    /**
     * Check if user has valid OAuth connection
     * @param int $userId User ID
     * @param string $provider Provider name
     * @return bool
     */
    protected function hasValidOAuthConnection(int $userId, string $provider): bool
    {
        $oauth = UserOauth::find()
            ->where(['user_id' => $userId, 'provider' => $provider])
            ->one();
        
        if (!$oauth) {
            return false;
        }
        
        // Check if token is expired and needs refresh
        if ($oauth->token_expires_at && strtotime($oauth->token_expires_at) < time()) {
            return $this->refreshOAuthToken($oauth);
        }
        
        return true;
    }
    
    /**
     * Refresh OAuth token
     * @param UserOauth $oauth OAuth connection
     * @return bool
     */
    protected function refreshOAuthToken(UserOauth $oauth): bool
    {
        if (empty($oauth->refresh_token)) {
            return false;
        }
        
        $client = null;
        if ($oauth->provider === 'google' && $this->googleClient) {
            $client = $this->googleClient;
        } elseif ($oauth->provider === 'microsoft' && $this->microsoftClient) {
            $client = $this->microsoftClient;
        }
        
        if (!$client) {
            return false;
        }
        
        try {
            $tokens = $client->refreshToken($oauth->refresh_token);
            if ($tokens) {
                $oauth->access_token = $tokens['access_token'];
                if (isset($tokens['refresh_token'])) {
                    $oauth->refresh_token = $tokens['refresh_token'];
                }
                if (isset($tokens['expires_in'])) {
                    $oauth->token_expires_at = date('Y-m-d H:i:s', time() + $tokens['expires_in']);
                }
                return $oauth->save(false);
            }
        } catch (\Exception $e) {
            Yii::error("Failed to refresh {$oauth->provider} token for user {$oauth->user_id}: " . $e->getMessage(), 'calendar-sync');
        }
        
        return false;
    }
    
    /**
     * Get OAuth access token for user
     * @param int $userId User ID
     * @param string $provider Provider name
     * @return string|null
     */
    protected function getAccessToken(int $userId, string $provider): ?string
    {
        $oauth = UserOauth::find()
            ->where(['user_id' => $userId, 'provider' => $provider])
            ->one();
        
        if (!$oauth) {
            return null;
        }
        
        // Refresh if expired
        if ($oauth->token_expires_at && strtotime($oauth->token_expires_at) < time()) {
            if (!$this->refreshOAuthToken($oauth)) {
                return null;
            }
            $oauth->refresh();
        }
        
        return $oauth->access_token;
    }
    
    /**
     * Sync booking to Google Calendar
     * @param Booking $booking Booking model
     * @return array
     */
    protected function syncToGoogleCalendar(Booking $booking): array
    {
        $accessToken = $this->getAccessToken($booking->user_id, 'google');
        if (!$accessToken) {
            return ['success' => false, 'error' => 'No valid Google access token'];
        }
        
        $eventData = $this->buildEventData($booking);
        
        try {
            // Check if event already exists (update vs create)
            $existingEventId = $this->getExternalEventId($booking->id, 'google');
            
            if ($existingEventId) {
                // Update existing event
                $result = $this->updateGoogleCalendarEvent($accessToken, $existingEventId, $eventData);
            } else {
                // Create new event
                $result = $this->googleClient->createCalendarEvent($accessToken, $eventData);
            }
            
            if ($result) {
                // Store external event ID
                $this->storeExternalEventId($booking->id, 'google', $result['id']);
                
                return [
                    'success' => true,
                    'event_id' => $result['id'],
                    'event_link' => $result['htmlLink'] ?? null,
                ];
            }
            
            return ['success' => false, 'error' => 'Failed to sync with Google Calendar'];
            
        } catch (\Exception $e) {
            Yii::error('Google Calendar sync error: ' . $e->getMessage(), 'calendar-sync');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Sync booking to Microsoft Outlook Calendar
     * @param Booking $booking Booking model
     * @return array
     */
    protected function syncToMicrosoftCalendar(Booking $booking): array
    {
        $accessToken = $this->getAccessToken($booking->user_id, 'microsoft');
        if (!$accessToken) {
            return ['success' => false, 'error' => 'No valid Microsoft access token'];
        }
        
        $eventData = $this->buildEventData($booking);
        
        try {
            $existingEventId = $this->getExternalEventId($booking->id, 'microsoft');
            
            if ($existingEventId) {
                $result = $this->updateMicrosoftCalendarEvent($accessToken, $existingEventId, $eventData);
            } else {
                $result = $this->microsoftClient->createCalendarEvent($accessToken, $eventData);
            }
            
            if ($result) {
                $this->storeExternalEventId($booking->id, 'microsoft', $result['id']);
                
                return [
                    'success' => true,
                    'event_id' => $result['id'],
                    'event_link' => $result['webLink'] ?? null,
                ];
            }
            
            return ['success' => false, 'error' => 'Failed to sync with Outlook Calendar'];
            
        } catch (\Exception $e) {
            Yii::error('Outlook Calendar sync error: ' . $e->getMessage(), 'calendar-sync');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Update Google Calendar event
     * @param string $accessToken Access token
     * @param string $eventId Event ID
     * @param array $eventData Event data
     * @return array|null
     */
    protected function updateGoogleCalendarEvent(string $accessToken, string $eventId, array $eventData): ?array
    {
        $calendarEvent = [
            'summary' => $eventData['title'],
            'description' => $eventData['description'] ?? '',
            'start' => [
                'dateTime' => date('c', strtotime($eventData['start'])),
                'timeZone' => 'Asia/Bangkok',
            ],
            'end' => [
                'dateTime' => date('c', strtotime($eventData['end'])),
                'timeZone' => 'Asia/Bangkok',
            ],
            'location' => $eventData['location'] ?? '',
        ];
        
        try {
            $response = Yii::$app->httpclient->createRequest()
                ->setMethod('PUT')
                ->setUrl("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($calendarEvent))
                ->send();
            
            if ($response->isOk) {
                return $response->data;
            }
            
        } catch (\Exception $e) {
            Yii::error('Google Calendar update error: ' . $e->getMessage(), 'calendar-sync');
        }
        
        return null;
    }
    
    /**
     * Update Microsoft Calendar event
     * @param string $accessToken Access token
     * @param string $eventId Event ID
     * @param array $eventData Event data
     * @return array|null
     */
    protected function updateMicrosoftCalendarEvent(string $accessToken, string $eventId, array $eventData): ?array
    {
        $outlookEvent = [
            'subject' => $eventData['title'],
            'body' => [
                'contentType' => 'HTML',
                'content' => $eventData['description'] ?? '',
            ],
            'start' => [
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($eventData['start'])),
                'timeZone' => 'SE Asia Standard Time',
            ],
            'end' => [
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($eventData['end'])),
                'timeZone' => 'SE Asia Standard Time',
            ],
            'location' => [
                'displayName' => $eventData['location'] ?? '',
            ],
        ];
        
        try {
            $response = Yii::$app->httpclient->createRequest()
                ->setMethod('PATCH')
                ->setUrl("https://graph.microsoft.com/v1.0/me/events/{$eventId}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($outlookEvent))
                ->send();
            
            if ($response->isOk) {
                return $response->data;
            }
            
        } catch (\Exception $e) {
            Yii::error('Outlook Calendar update error: ' . $e->getMessage(), 'calendar-sync');
        }
        
        return null;
    }
    
    /**
     * Build event data from booking
     * @param Booking $booking Booking model
     * @return array
     */
    protected function buildEventData(Booking $booking): array
    {
        $room = $booking->room;
        $roomName = $room ? $room->name : 'ห้องประชุม';
        $buildingName = $room && $room->building ? $room->building : '';
        
        $location = $roomName;
        if ($buildingName) {
            $location .= " - {$buildingName}";
        }
        
        $description = $booking->purpose ?? '';
        if ($booking->attendees_count) {
            $description .= "\nจำนวนผู้เข้าร่วม: {$booking->attendees_count} คน";
        }
        if ($booking->equipment_ids) {
            $description .= "\nอุปกรณ์ที่ขอใช้: " . $this->getEquipmentNames($booking->equipment_ids);
        }
        $description .= "\n\nจองผ่านระบบ Meeting Room Booking System";
        
        $attendees = [];
        if ($booking->attendee_emails) {
            $emails = is_string($booking->attendee_emails) 
                ? json_decode($booking->attendee_emails, true) 
                : $booking->attendee_emails;
            if (is_array($emails)) {
                $attendees = $emails;
            }
        }
        
        return [
            'title' => "[การจอง] {$booking->title} - {$roomName}",
            'description' => $description,
            'start' => $booking->start_time,
            'end' => $booking->end_time,
            'location' => $location,
            'attendees' => $attendees,
        ];
    }
    
    /**
     * Get equipment names from IDs
     * @param mixed $equipmentIds Equipment IDs (JSON string or array)
     * @return string
     */
    protected function getEquipmentNames($equipmentIds): string
    {
        if (is_string($equipmentIds)) {
            $equipmentIds = json_decode($equipmentIds, true);
        }
        
        if (!is_array($equipmentIds) || empty($equipmentIds)) {
            return '-';
        }
        
        $equipments = \common\models\Equipment::find()
            ->where(['id' => $equipmentIds])
            ->select(['name'])
            ->column();
        
        return implode(', ', $equipments);
    }
    
    /**
     * Get external calendar event ID
     * @param int $bookingId Booking ID
     * @param string $provider Provider name
     * @return string|null
     */
    protected function getExternalEventId(int $bookingId, string $provider): ?string
    {
        // Store in booking_calendar_events table or in booking metadata
        $cacheKey = "booking_calendar_{$bookingId}_{$provider}";
        return Yii::$app->cache->get($cacheKey) ?: null;
    }
    
    /**
     * Store external calendar event ID
     * @param int $bookingId Booking ID
     * @param string $provider Provider name
     * @param string $eventId External event ID
     */
    protected function storeExternalEventId(int $bookingId, string $provider, string $eventId): void
    {
        $cacheKey = "booking_calendar_{$bookingId}_{$provider}";
        // Store permanently (or use database table for persistence)
        Yii::$app->cache->set($cacheKey, $eventId, 86400 * 365); // 1 year
        
        // Also store in database for persistence
        Yii::$app->db->createCommand()->upsert('{{%booking_calendar_sync}}', [
            'booking_id' => $bookingId,
            'provider' => $provider,
            'external_event_id' => $eventId,
            'synced_at' => date('Y-m-d H:i:s'),
        ], [
            'external_event_id' => $eventId,
            'synced_at' => date('Y-m-d H:i:s'),
        ])->execute();
    }
    
    /**
     * Delete calendar event when booking is cancelled
     * @param Booking $booking Booking model
     * @return array Results for each provider
     */
    public function deleteCalendarEvent(Booking $booking): array
    {
        $results = [];
        
        // Delete from Google Calendar
        $googleEventId = $this->getExternalEventId($booking->id, 'google');
        if ($googleEventId) {
            $accessToken = $this->getAccessToken($booking->user_id, 'google');
            if ($accessToken && $this->googleClient) {
                $deleted = $this->googleClient->deleteCalendarEvent($accessToken, $googleEventId);
                $results['google'] = ['success' => $deleted];
                
                if ($deleted) {
                    $this->removeExternalEventId($booking->id, 'google');
                }
            }
        }
        
        // Delete from Microsoft Calendar
        $microsoftEventId = $this->getExternalEventId($booking->id, 'microsoft');
        if ($microsoftEventId) {
            $accessToken = $this->getAccessToken($booking->user_id, 'microsoft');
            if ($accessToken && $this->microsoftClient) {
                $deleted = $this->microsoftClient->deleteCalendarEvent($accessToken, $microsoftEventId);
                $results['microsoft'] = ['success' => $deleted];
                
                if ($deleted) {
                    $this->removeExternalEventId($booking->id, 'microsoft');
                }
            }
        }
        
        return $results;
    }
    
    /**
     * Remove external event ID
     * @param int $bookingId Booking ID
     * @param string $provider Provider name
     */
    protected function removeExternalEventId(int $bookingId, string $provider): void
    {
        $cacheKey = "booking_calendar_{$bookingId}_{$provider}";
        Yii::$app->cache->delete($cacheKey);
        
        Yii::$app->db->createCommand()->delete('{{%booking_calendar_sync}}', [
            'booking_id' => $bookingId,
            'provider' => $provider,
        ])->execute();
    }
    
    /**
     * Get sync status for a booking
     * @param int $bookingId Booking ID
     * @return array
     */
    public function getSyncStatus(int $bookingId): array
    {
        $status = [];
        
        foreach (['google', 'microsoft'] as $provider) {
            $eventId = $this->getExternalEventId($bookingId, $provider);
            $status[$provider] = [
                'synced' => !empty($eventId),
                'event_id' => $eventId,
            ];
        }
        
        return $status;
    }
}
