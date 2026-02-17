<?php
/**
 * Google OAuth2 Client Component
 * 
 * Handles authentication via Google OAuth2
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\components;

use Yii;

class GoogleOAuth2Client extends OAuth2Client
{
    /**
     * @var string[] Default scopes for Google OAuth2
     */
    public $scopes = [
        'openid',
        'email',
        'profile',
    ];
    
    /**
     * @var bool Include Google Calendar scope
     */
    public $includeCalendarScope = false;
    
    /**
     * @var string Google hosted domain restriction (e.g., 'example.com')
     */
    public $hostedDomain;
    
    /**
     * Initialize component from environment
     */
    public function init()
    {
        // Load from environment if not set
        if (empty($this->clientId)) {
            $this->clientId = getenv('GOOGLE_CLIENT_ID') ?: Yii::$app->params['google']['clientId'] ?? '';
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = getenv('GOOGLE_CLIENT_SECRET') ?: Yii::$app->params['google']['clientSecret'] ?? '';
        }
        if (empty($this->redirectUri)) {
            $this->redirectUri = getenv('GOOGLE_REDIRECT_URI') ?: Yii::$app->params['google']['redirectUri'] ?? '';
        }
        
        $this->enabled = filter_var(
            getenv('GOOGLE_ENABLED') ?: Yii::$app->params['google']['enabled'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
        
        // Add calendar scope if enabled
        if ($this->includeCalendarScope) {
            $this->scopes[] = 'https://www.googleapis.com/auth/calendar.events';
        }
        
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function getProviderName(): string
    {
        return 'google';
    }
    
    /**
     * @inheritdoc
     */
    protected function getAuthorizationUrl(): string
    {
        return 'https://accounts.google.com/o/oauth2/v2/auth';
    }
    
    /**
     * @inheritdoc
     */
    protected function getTokenUrl(): string
    {
        return 'https://oauth2.googleapis.com/token';
    }
    
    /**
     * @inheritdoc
     */
    protected function getUserInfoUrl(): string
    {
        return 'https://www.googleapis.com/oauth2/v3/userinfo';
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthUrl(array $extraParams = []): string
    {
        // Add hosted domain restriction if set
        if (!empty($this->hostedDomain)) {
            $extraParams['hd'] = $this->hostedDomain;
        }
        
        return parent::getAuthUrl($extraParams);
    }
    
    /**
     * @inheritdoc
     */
    protected function parseUserInfo(array $data): array
    {
        return [
            'provider_user_id' => $data['sub'] ?? '',
            'email' => $data['email'] ?? null,
            'email_verified' => $data['email_verified'] ?? false,
            'first_name' => $data['given_name'] ?? '',
            'last_name' => $data['family_name'] ?? '',
            'full_name' => $data['name'] ?? '',
            'avatar' => $data['picture'] ?? null,
            'locale' => $data['locale'] ?? 'th',
            'hosted_domain' => $data['hd'] ?? null,
        ];
    }
    
    /**
     * Verify ID token (for mobile/client apps)
     * @param string $idToken Google ID token
     * @return array|null User info or null if invalid
     */
    public function verifyIdToken(string $idToken): ?array
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('GET')
                ->setUrl('https://oauth2.googleapis.com/tokeninfo')
                ->setData(['id_token' => $idToken])
                ->send();
            
            if (!$response->isOk) {
                Yii::error('Google ID token verification failed', 'oauth2');
                return null;
            }
            
            $data = $response->data;
            
            // Verify audience
            if (($data['aud'] ?? '') !== $this->clientId) {
                Yii::error('Google ID token audience mismatch', 'oauth2');
                return null;
            }
            
            return $this->parseUserInfo([
                'sub' => $data['sub'],
                'email' => $data['email'],
                'email_verified' => $data['email_verified'] === 'true',
                'given_name' => $data['given_name'] ?? '',
                'family_name' => $data['family_name'] ?? '',
                'name' => $data['name'] ?? '',
                'picture' => $data['picture'] ?? null,
            ]);
            
        } catch (\Exception $e) {
            Yii::error('Google ID token verification error: ' . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Create calendar event using Google Calendar API
     * @param string $accessToken User's access token
     * @param array $event Event data
     * @return array|null Created event or null on failure
     */
    public function createCalendarEvent(string $accessToken, array $event): ?array
    {
        $calendarEvent = [
            'summary' => $event['title'],
            'description' => $event['description'] ?? '',
            'start' => [
                'dateTime' => date('c', strtotime($event['start'])),
                'timeZone' => 'Asia/Bangkok',
            ],
            'end' => [
                'dateTime' => date('c', strtotime($event['end'])),
                'timeZone' => 'Asia/Bangkok',
            ],
            'location' => $event['location'] ?? '',
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'popup', 'minutes' => 30],
                    ['method' => 'email', 'minutes' => 60],
                ],
            ],
        ];
        
        // Add attendees if provided
        if (!empty($event['attendees'])) {
            $calendarEvent['attendees'] = array_map(function($email) {
                return ['email' => $email];
            }, $event['attendees']);
        }
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl('https://www.googleapis.com/calendar/v3/calendars/primary/events')
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($calendarEvent))
                ->send();
            
            if (!$response->isOk) {
                Yii::error('Google Calendar event creation failed: ' . $response->content, 'oauth2');
                return null;
            }
            
            return $response->data;
            
        } catch (\Exception $e) {
            Yii::error('Google Calendar error: ' . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Delete calendar event
     * @param string $accessToken User's access token
     * @param string $eventId Google Calendar event ID
     * @return bool
     */
    public function deleteCalendarEvent(string $accessToken, string $eventId): bool
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('DELETE')
                ->setUrl("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$eventId}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->send();
            
            return $response->isOk || $response->statusCode === 204;
            
        } catch (\Exception $e) {
            Yii::error('Google Calendar delete error: ' . $e->getMessage(), 'oauth2');
            return false;
        }
    }
    
    /**
     * Get button HTML for Google Sign-In
     * @param string $returnUrl URL to redirect after login
     * @return string HTML
     */
    public function getButtonHtml(string $returnUrl = ''): string
    {
        if (!$this->isConfigured()) {
            return '';
        }
        
        $authUrl = $this->getAuthUrl();
        if ($returnUrl) {
            Yii::$app->session->set('oauth_return_url', $returnUrl);
        }
        
        return <<<HTML
<a href="{$authUrl}" class="btn btn-google d-flex align-items-center justify-content-center gap-2" style="background-color: #fff; border: 1px solid #dadce0; color: #3c4043;">
    <svg width="18" height="18" viewBox="0 0 24 24">
        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
    </svg>
    <span>เข้าสู่ระบบด้วย Google</span>
</a>
HTML;
    }
}
