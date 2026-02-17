<?php
/**
 * Microsoft Azure AD OAuth2 Client Component
 * 
 * Handles authentication via Microsoft Azure AD OAuth2
 * Supports personal accounts, work/school accounts, and tenant-specific
 * 
 * @author BIzCO
 * @version 1.0
 */

namespace common\components;

use Yii;

class MicrosoftOAuth2Client extends OAuth2Client
{
    /**
     * @var string Azure AD tenant ID or 'common'/'organizations'/'consumers'
     */
    public $tenantId = 'common';
    
    /**
     * @var string[] Default scopes for Microsoft OAuth2
     */
    public $scopes = [
        'openid',
        'email',
        'profile',
        'User.Read',
    ];
    
    /**
     * @var bool Include Outlook Calendar scope
     */
    public $includeCalendarScope = false;
    
    /**
     * @var string Graph API version
     */
    public $graphApiVersion = 'v1.0';
    
    /**
     * Initialize component from environment
     */
    public function init()
    {
        // Load from environment if not set
        if (empty($this->clientId)) {
            $this->clientId = getenv('MICROSOFT_CLIENT_ID') ?: Yii::$app->params['microsoft']['clientId'] ?? '';
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = getenv('MICROSOFT_CLIENT_SECRET') ?: Yii::$app->params['microsoft']['clientSecret'] ?? '';
        }
        if (empty($this->redirectUri)) {
            $this->redirectUri = getenv('MICROSOFT_REDIRECT_URI') ?: Yii::$app->params['microsoft']['redirectUri'] ?? '';
        }
        
        $envTenant = getenv('MICROSOFT_TENANT_ID');
        if (!empty($envTenant)) {
            $this->tenantId = $envTenant;
        } elseif (isset(Yii::$app->params['microsoft']['tenantId'])) {
            $this->tenantId = Yii::$app->params['microsoft']['tenantId'];
        }
        
        $this->enabled = filter_var(
            getenv('MICROSOFT_ENABLED') ?: Yii::$app->params['microsoft']['enabled'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
        
        // Add calendar scope if enabled
        if ($this->includeCalendarScope) {
            $this->scopes[] = 'Calendars.ReadWrite';
        }
        
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function getProviderName(): string
    {
        return 'microsoft';
    }
    
    /**
     * Get Azure AD base URL
     * @return string
     */
    protected function getAzureBaseUrl(): string
    {
        return "https://login.microsoftonline.com/{$this->tenantId}";
    }
    
    /**
     * @inheritdoc
     */
    protected function getAuthorizationUrl(): string
    {
        return $this->getAzureBaseUrl() . '/oauth2/v2.0/authorize';
    }
    
    /**
     * @inheritdoc
     */
    protected function getTokenUrl(): string
    {
        return $this->getAzureBaseUrl() . '/oauth2/v2.0/token';
    }
    
    /**
     * @inheritdoc
     */
    protected function getUserInfoUrl(): string
    {
        return "https://graph.microsoft.com/{$this->graphApiVersion}/me";
    }
    
    /**
     * @inheritdoc
     */
    protected function parseUserInfo(array $data): array
    {
        // Parse name - Microsoft returns displayName, givenName, surname
        $firstName = $data['givenName'] ?? '';
        $lastName = $data['surname'] ?? '';
        
        // If no givenName/surname, try to split displayName
        if (empty($firstName) && !empty($data['displayName'])) {
            $nameParts = explode(' ', $data['displayName'], 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
        }
        
        return [
            'provider_user_id' => $data['id'] ?? '',
            'email' => $data['mail'] ?? $data['userPrincipalName'] ?? null,
            'email_verified' => true, // Microsoft accounts are verified
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $data['displayName'] ?? '',
            'avatar' => null, // Need separate API call for photo
            'job_title' => $data['jobTitle'] ?? null,
            'department' => $data['department'] ?? null,
            'office_location' => $data['officeLocation'] ?? null,
            'mobile_phone' => $data['mobilePhone'] ?? null,
            'preferred_language' => $data['preferredLanguage'] ?? 'th',
        ];
    }
    
    /**
     * Get user profile photo
     * @param string $accessToken Access token
     * @return string|null Base64 encoded photo or null
     */
    public function getUserPhoto(string $accessToken): ?string
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('GET')
                ->setUrl("https://graph.microsoft.com/{$this->graphApiVersion}/me/photo/\$value")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->setOptions([
                    'timeout' => 10,
                ])
                ->send();
            
            if ($response->isOk) {
                $contentType = $response->headers->get('content-type');
                return 'data:' . $contentType . ';base64,' . base64_encode($response->content);
            }
            
            return null;
            
        } catch (\Exception $e) {
            Yii::debug('Microsoft photo fetch failed: ' . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Override authenticate to get photo
     * @inheritdoc
     */
    public function authenticate(string $code, string $state): ?\common\models\User
    {
        $user = parent::authenticate($code, $state);
        
        if ($user) {
            // Try to get user photo
            $oauth = $this->getConnection($user->id);
            if ($oauth && empty($user->avatar)) {
                $photo = $this->getUserPhoto($oauth->access_token);
                if ($photo) {
                    $user->avatar = $photo;
                    $user->save(false);
                }
            }
        }
        
        return $user;
    }
    
    /**
     * Create Outlook calendar event
     * @param string $accessToken User's access token
     * @param array $event Event data
     * @return array|null Created event or null on failure
     */
    public function createCalendarEvent(string $accessToken, array $event): ?array
    {
        $outlookEvent = [
            'subject' => $event['title'],
            'body' => [
                'contentType' => 'HTML',
                'content' => $event['description'] ?? '',
            ],
            'start' => [
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($event['start'])),
                'timeZone' => 'SE Asia Standard Time',
            ],
            'end' => [
                'dateTime' => date('Y-m-d\TH:i:s', strtotime($event['end'])),
                'timeZone' => 'SE Asia Standard Time',
            ],
            'location' => [
                'displayName' => $event['location'] ?? '',
            ],
            'reminderMinutesBeforeStart' => 30,
            'isReminderOn' => true,
        ];
        
        // Add attendees if provided
        if (!empty($event['attendees'])) {
            $outlookEvent['attendees'] = array_map(function($email) {
                return [
                    'emailAddress' => ['address' => $email],
                    'type' => 'required',
                ];
            }, $event['attendees']);
        }
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl("https://graph.microsoft.com/{$this->graphApiVersion}/me/events")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($outlookEvent))
                ->send();
            
            if (!$response->isOk) {
                Yii::error('Outlook Calendar event creation failed: ' . $response->content, 'oauth2');
                return null;
            }
            
            return $response->data;
            
        } catch (\Exception $e) {
            Yii::error('Outlook Calendar error: ' . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Delete Outlook calendar event
     * @param string $accessToken User's access token
     * @param string $eventId Outlook event ID
     * @return bool
     */
    public function deleteCalendarEvent(string $accessToken, string $eventId): bool
    {
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('DELETE')
                ->setUrl("https://graph.microsoft.com/{$this->graphApiVersion}/me/events/{$eventId}")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                ])
                ->send();
            
            return $response->isOk || $response->statusCode === 204;
            
        } catch (\Exception $e) {
            Yii::error('Outlook Calendar delete error: ' . $e->getMessage(), 'oauth2');
            return false;
        }
    }
    
    /**
     * Send email via Microsoft Graph
     * @param string $accessToken User's access token
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @return bool
     */
    public function sendEmail(string $accessToken, string $to, string $subject, string $body): bool
    {
        $message = [
            'message' => [
                'subject' => $subject,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $body,
                ],
                'toRecipients' => [
                    ['emailAddress' => ['address' => $to]],
                ],
            ],
            'saveToSentItems' => true,
        ];
        
        try {
            $response = $this->httpClient->createRequest()
                ->setMethod('POST')
                ->setUrl("https://graph.microsoft.com/{$this->graphApiVersion}/me/sendMail")
                ->setHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setContent(json_encode($message))
                ->send();
            
            return $response->isOk || $response->statusCode === 202;
            
        } catch (\Exception $e) {
            Yii::error('Microsoft Graph email error: ' . $e->getMessage(), 'oauth2');
            return false;
        }
    }
    
    /**
     * Get button HTML for Microsoft Sign-In
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
<a href="{$authUrl}" class="btn btn-microsoft d-flex align-items-center justify-content-center gap-2" style="background-color: #2f2f2f; color: #fff; border: none;">
    <svg width="18" height="18" viewBox="0 0 23 23">
        <path fill="#f35325" d="M1 1h10v10H1z"/>
        <path fill="#81bc06" d="M12 1h10v10H12z"/>
        <path fill="#05a6f0" d="M1 12h10v10H1z"/>
        <path fill="#ffba08" d="M12 12h10v10H12z"/>
    </svg>
    <span>เข้าสู่ระบบด้วย Microsoft</span>
</a>
HTML;
    }
}
