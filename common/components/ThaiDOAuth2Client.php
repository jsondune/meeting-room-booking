<?php
/**
 * ThaiD OAuth2 Client Component
 * 
 * Handles authentication via Thailand Digital ID (ThaiD) OAuth2
 * ThaiD is managed by DGA (Digital Government Development Agency)
 * 
 * @see https://api.thaid.dga.or.th
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\components;

use Yii;

class ThaiDOAuth2Client extends OAuth2Client
{
    /**
     * @var string ThaiD API base URL
     */
    public $apiBaseUrl = 'https://api.thaid.dga.or.th';
    
    /**
     * @var string Environment: sandbox, production
     */
    public $environment = 'sandbox';
    
    /**
     * @var string[] Default scopes for ThaiD
     */
    public $scopes = [
        'openid',
        'profile',
        'pid', // Personal ID (Citizen ID)
        'name',
        'email',
        'address',
    ];
    
    /**
     * @var string API version
     */
    public $apiVersion = 'v1';
    
    /**
     * @var bool Request address information
     */
    public $requestAddress = false;
    
    /**
     * Initialize component from environment
     */
    public function init()
    {
        // Load from environment if not set
        if (empty($this->clientId)) {
            $this->clientId = getenv('THAID_CLIENT_ID') ?: Yii::$app->params['thaid']['clientId'] ?? '';
        }
        if (empty($this->clientSecret)) {
            $this->clientSecret = getenv('THAID_CLIENT_SECRET') ?: Yii::$app->params['thaid']['clientSecret'] ?? '';
        }
        if (empty($this->redirectUri)) {
            $this->redirectUri = getenv('THAID_REDIRECT_URI') ?: Yii::$app->params['thaid']['redirectUri'] ?? '';
        }
        
        $envUrl = getenv('THAID_API_URL');
        if (!empty($envUrl)) {
            $this->apiBaseUrl = $envUrl;
        } elseif (isset(Yii::$app->params['thaid']['apiUrl'])) {
            $this->apiBaseUrl = Yii::$app->params['thaid']['apiUrl'];
        }
        
        $envMode = getenv('THAID_ENV');
        if (!empty($envMode)) {
            $this->environment = $envMode;
        } elseif (isset(Yii::$app->params['thaid']['environment'])) {
            $this->environment = Yii::$app->params['thaid']['environment'];
        }
        
        $this->enabled = filter_var(
            getenv('THAID_ENABLED') ?: Yii::$app->params['thaid']['enabled'] ?? false,
            FILTER_VALIDATE_BOOLEAN
        );
        
        // Use sandbox URL in sandbox mode
        if ($this->environment === 'sandbox') {
            $this->apiBaseUrl = 'https://api-sandbox.thaid.dga.or.th';
        }
        
        parent::init();
    }
    
    /**
     * @inheritdoc
     */
    public function getProviderName(): string
    {
        return 'thaid';
    }
    
    /**
     * @inheritdoc
     */
    protected function getAuthorizationUrl(): string
    {
        return "{$this->apiBaseUrl}/oauth2/{$this->apiVersion}/authorize";
    }
    
    /**
     * @inheritdoc
     */
    protected function getTokenUrl(): string
    {
        return "{$this->apiBaseUrl}/oauth2/{$this->apiVersion}/token";
    }
    
    /**
     * @inheritdoc
     */
    protected function getUserInfoUrl(): string
    {
        return "{$this->apiBaseUrl}/api/{$this->apiVersion}/userinfo";
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthUrl(array $extraParams = []): string
    {
        // ThaiD specific parameters
        $extraParams['response_type'] = 'code';
        
        // Display language
        $extraParams['ui_locales'] = 'th';
        
        // Authentication level (if required)
        // $extraParams['acr_values'] = 'ial2'; // Identity assurance level
        
        return parent::getAuthUrl($extraParams);
    }
    
    /**
     * @inheritdoc
     */
    public function getAccessToken(string $code, string $state): array
    {
        // Validate state
        if (!$this->validateState($state)) {
            throw new \yii\httpclient\Exception('Invalid state parameter. Possible CSRF attack.');
        }
        
        // ThaiD may require Basic Auth for token endpoint
        $credentials = base64_encode($this->clientId . ':' . $this->clientSecret);
        
        $response = $this->httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($this->getTokenUrl())
            ->setHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])
            ->setData([
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
            ])
            ->send();
        
        if (!$response->isOk) {
            Yii::error("ThaiD token error: " . $response->content, 'oauth2');
            throw new \yii\httpclient\Exception('Failed to get access token from ThaiD');
        }
        
        return $response->data;
    }
    
    /**
     * @inheritdoc
     */
    protected function parseUserInfo(array $data): array
    {
        // ThaiD returns Thai names in different format
        // Name structure may be: prefix + first_name + middle_name + last_name
        
        $firstName = $data['given_name'] ?? $data['first_name_th'] ?? '';
        $lastName = $data['family_name'] ?? $data['last_name_th'] ?? '';
        $fullName = $data['name'] ?? trim("$firstName $lastName");
        
        // Thai title/prefix
        $prefix = $data['prefix_th'] ?? $data['title_th'] ?? '';
        
        // Parse address if available
        $address = [];
        if (!empty($data['address'])) {
            $address = [
                'house_no' => $data['address']['house_no'] ?? '',
                'village' => $data['address']['village'] ?? '',
                'lane' => $data['address']['lane'] ?? '',
                'road' => $data['address']['road'] ?? '',
                'subdistrict' => $data['address']['sub_district'] ?? '',
                'district' => $data['address']['district'] ?? '',
                'province' => $data['address']['province'] ?? '',
                'postal_code' => $data['address']['postal_code'] ?? '',
            ];
        }
        
        return [
            'provider_user_id' => $data['sub'] ?? $data['pid'] ?? '',
            'citizen_id' => $this->maskCitizenId($data['pid'] ?? ''),
            'citizen_id_hash' => hash('sha256', $data['pid'] ?? ''),
            'email' => $data['email'] ?? null,
            'email_verified' => $data['email_verified'] ?? false,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'full_name' => $fullName,
            'prefix' => $prefix,
            'first_name_en' => $data['first_name_en'] ?? '',
            'last_name_en' => $data['last_name_en'] ?? '',
            'gender' => $data['gender'] ?? null,
            'birthdate' => $data['birthdate'] ?? null,
            'phone' => $data['phone_number'] ?? null,
            'avatar' => $data['picture'] ?? null,
            'address' => $address,
            'ial' => $data['ial'] ?? null, // Identity Assurance Level
            'verified_at' => $data['updated_at'] ?? null,
        ];
    }
    
    /**
     * Mask citizen ID for display (show only first 3 and last 4 digits)
     * @param string $citizenId 13-digit Thai citizen ID
     * @return string Masked ID
     */
    protected function maskCitizenId(string $citizenId): string
    {
        if (strlen($citizenId) !== 13) {
            return '***********';
        }
        
        return substr($citizenId, 0, 3) . '-****-***' . substr($citizenId, -4);
    }
    
    /**
     * Override user creation for ThaiD specific fields
     * @inheritdoc
     */
    protected function createUser(array $userInfo): ?\common\models\User
    {
        $user = new \common\models\User();
        $user->email = $userInfo['email'] ?? null;
        $user->username = $this->generateUsername($userInfo);
        $user->full_name = $userInfo['full_name'] ?? '';
        $user->phone = $userInfo['phone'] ?? null;
        $user->avatar = $userInfo['avatar'] ?? null;
        $user->status = \common\models\User::STATUS_ACTIVE;
        
        // Generate secure random password (user can reset if needed)
        $user->setPassword(Yii::$app->security->generateRandomString(16));
        $user->generateAuthKey();
        
        if ($user->save()) {
            // Assign default role
            $auth = Yii::$app->authManager;
            $role = $auth->getRole('user');
            if ($role) {
                $auth->assign($role, $user->id);
            }
            
            // Send welcome notification
            NotificationHelper::create(
                $user->id,
                NotificationHelper::TYPE_ACCOUNT_ALERT,
                'ยินดีต้อนรับสู่ระบบจองห้องประชุม',
                'บัญชีของคุณถูกยืนยันตัวตนผ่าน ThaiD เรียบร้อยแล้ว ท่านได้รับการยืนยันตัวตนระดับสูงสุด'
            );
            
            return $user;
        }
        
        Yii::error('Failed to create ThaiD user: ' . json_encode($user->errors), 'oauth2');
        return null;
    }
    
    /**
     * Verify citizen ID format
     * @param string $citizenId 13-digit Thai citizen ID
     * @return bool
     */
    public function validateCitizenId(string $citizenId): bool
    {
        // Must be 13 digits
        if (!preg_match('/^\d{13}$/', $citizenId)) {
            return false;
        }
        
        // Checksum validation (Thai citizen ID algorithm)
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$citizenId[$i] * (13 - $i);
        }
        
        $checkDigit = (11 - ($sum % 11)) % 10;
        
        return $checkDigit === (int)$citizenId[12];
    }
    
    /**
     * Get identity assurance level description
     * @param string|null $ial IAL value
     * @return string
     */
    public function getIalDescription(?string $ial): string
    {
        $levels = [
            'ial1' => 'ระดับ 1 - ยืนยันตัวตนพื้นฐาน',
            'ial1.5' => 'ระดับ 1.5 - ยืนยันตัวตนระยะไกล',
            'ial2' => 'ระดับ 2 - ยืนยันตัวตนด้วยเอกสาร',
            'ial2.5' => 'ระดับ 2.5 - ยืนยันตัวตนแบบพิสูจน์',
            'ial3' => 'ระดับ 3 - ยืนยันตัวตนต่อหน้า',
        ];
        
        return $levels[$ial] ?? 'ไม่ระบุระดับการยืนยันตัวตน';
    }
    
    /**
     * Check if user meets minimum IAL requirement
     * @param array $userInfo User info from ThaiD
     * @param string $minimumIal Minimum required IAL
     * @return bool
     */
    public function meetsIalRequirement(array $userInfo, string $minimumIal = 'ial2'): bool
    {
        $ialOrder = ['ial1', 'ial1.5', 'ial2', 'ial2.5', 'ial3'];
        
        $userIal = $userInfo['ial'] ?? 'ial1';
        $userLevel = array_search($userIal, $ialOrder);
        $requiredLevel = array_search($minimumIal, $ialOrder);
        
        if ($userLevel === false || $requiredLevel === false) {
            return false;
        }
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Format Thai address
     * @param array $address Address data
     * @return string
     */
    public function formatAddress(array $address): string
    {
        $parts = [];
        
        if (!empty($address['house_no'])) {
            $parts[] = 'บ้านเลขที่ ' . $address['house_no'];
        }
        if (!empty($address['village'])) {
            $parts[] = 'หมู่ ' . $address['village'];
        }
        if (!empty($address['lane'])) {
            $parts[] = 'ซอย ' . $address['lane'];
        }
        if (!empty($address['road'])) {
            $parts[] = 'ถนน ' . $address['road'];
        }
        if (!empty($address['subdistrict'])) {
            $parts[] = 'ตำบล/แขวง ' . $address['subdistrict'];
        }
        if (!empty($address['district'])) {
            $parts[] = 'อำเภอ/เขต ' . $address['district'];
        }
        if (!empty($address['province'])) {
            $parts[] = 'จังหวัด ' . $address['province'];
        }
        if (!empty($address['postal_code'])) {
            $parts[] = $address['postal_code'];
        }
        
        return implode(' ', $parts);
    }
    
    /**
     * Get button HTML for ThaiD Sign-In
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
        
        // ThaiD official colors: Blue #0066CC
        return <<<HTML
<a href="{$authUrl}" class="btn btn-thaid d-flex align-items-center justify-content-center gap-2" style="background-color: #0066CC; color: #fff; border: none;">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
    </svg>
    <span>เข้าสู่ระบบด้วย ThaiD</span>
</a>
HTML;
    }
    
    /**
     * Get ThaiD badge HTML to show user is verified
     * @param string $ial Identity Assurance Level
     * @return string HTML
     */
    public function getVerifiedBadge(string $ial = 'ial2'): string
    {
        $badgeColor = match($ial) {
            'ial3' => 'success',
            'ial2.5', 'ial2' => 'primary',
            'ial1.5' => 'info',
            default => 'secondary',
        };
        
        return <<<HTML
<span class="badge bg-{$badgeColor}">
    <i class="bi bi-shield-check me-1"></i>
    ยืนยันด้วย ThaiD
</span>
HTML;
    }
}
