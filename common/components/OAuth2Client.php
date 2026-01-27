<?php
/**
 * OAuth2 Client Base Component
 * 
 * Abstract base class for OAuth2 providers
 * Supports Google, Microsoft, and ThaiD authentication
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use common\models\User;
use common\models\UserOauth;

abstract class OAuth2Client extends Component
{
    /**
     * @var string OAuth2 client ID
     */
    public $clientId;
    
    /**
     * @var string OAuth2 client secret
     */
    public $clientSecret;
    
    /**
     * @var string OAuth2 redirect URI
     */
    public $redirectUri;
    
    /**
     * @var string[] OAuth2 scopes
     */
    public $scopes = [];
    
    /**
     * @var bool Whether this provider is enabled
     */
    public $enabled = false;
    
    /**
     * @var Client HTTP client instance
     */
    protected $httpClient;
    
    /**
     * @var string Session key for state parameter
     */
    protected const STATE_SESSION_KEY = 'oauth2_state';
    
    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
        
        $this->httpClient = new Client([
            'transport' => 'yii\httpclient\CurlTransport',
            'requestConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON,
            ],
        ]);
    }
    
    /**
     * Get provider name
     * @return string
     */
    abstract public function getProviderName(): string;
    
    /**
     * Get authorization URL
     * @return string
     */
    abstract protected function getAuthorizationUrl(): string;
    
    /**
     * Get token URL
     * @return string
     */
    abstract protected function getTokenUrl(): string;
    
    /**
     * Get user info URL
     * @return string
     */
    abstract protected function getUserInfoUrl(): string;
    
    /**
     * Parse user info response
     * @param array $data Response data
     * @return array Normalized user data
     */
    abstract protected function parseUserInfo(array $data): array;
    
    /**
     * Check if provider is properly configured
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->enabled 
            && !empty($this->clientId) 
            && !empty($this->clientSecret) 
            && !empty($this->redirectUri);
    }
    
    /**
     * Generate authorization URL with state
     * @param array $extraParams Extra URL parameters
     * @return string
     */
    public function getAuthUrl(array $extraParams = []): string
    {
        if (!$this->isConfigured()) {
            throw new InvalidConfigException("OAuth2 provider {$this->getProviderName()} is not configured");
        }
        
        // Generate state parameter for CSRF protection
        $state = Yii::$app->security->generateRandomString(32);
        Yii::$app->session->set(self::STATE_SESSION_KEY . '_' . $this->getProviderName(), $state);
        
        $params = array_merge([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $this->scopes),
            'state' => $state,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ], $extraParams);
        
        return $this->getAuthorizationUrl() . '?' . http_build_query($params);
    }
    
    /**
     * Validate state parameter
     * @param string $state State from callback
     * @return bool
     */
    protected function validateState(string $state): bool
    {
        $sessionKey = self::STATE_SESSION_KEY . '_' . $this->getProviderName();
        $expectedState = Yii::$app->session->get($sessionKey);
        
        // Remove state from session after validation
        Yii::$app->session->remove($sessionKey);
        
        return !empty($expectedState) && hash_equals($expectedState, $state);
    }
    
    /**
     * Exchange authorization code for access token
     * @param string $code Authorization code
     * @param string $state State parameter
     * @return array Token data
     * @throws Exception
     */
    public function getAccessToken(string $code, string $state): array
    {
        // Validate state
        if (!$this->validateState($state)) {
            throw new Exception('Invalid state parameter. Possible CSRF attack.');
        }
        
        $response = $this->httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($this->getTokenUrl())
            ->setData([
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
            ])
            ->setHeaders(['Accept' => 'application/json'])
            ->send();
        
        if (!$response->isOk) {
            Yii::error("OAuth2 token error: " . $response->content, 'oauth2');
            throw new Exception('Failed to get access token from ' . $this->getProviderName());
        }
        
        return $response->data;
    }
    
    /**
     * Refresh access token
     * @param string $refreshToken Refresh token
     * @return array New token data
     * @throws Exception
     */
    public function refreshAccessToken(string $refreshToken): array
    {
        $response = $this->httpClient->createRequest()
            ->setMethod('POST')
            ->setUrl($this->getTokenUrl())
            ->setData([
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ])
            ->setHeaders(['Accept' => 'application/json'])
            ->send();
        
        if (!$response->isOk) {
            Yii::error("OAuth2 refresh error: " . $response->content, 'oauth2');
            throw new Exception('Failed to refresh access token');
        }
        
        return $response->data;
    }
    
    /**
     * Get user info using access token
     * @param string $accessToken Access token
     * @return array User info
     * @throws Exception
     */
    public function getUserInfo(string $accessToken): array
    {
        $response = $this->httpClient->createRequest()
            ->setMethod('GET')
            ->setUrl($this->getUserInfoUrl())
            ->setHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ])
            ->send();
        
        if (!$response->isOk) {
            Yii::error("OAuth2 user info error: " . $response->content, 'oauth2');
            throw new Exception('Failed to get user info from ' . $this->getProviderName());
        }
        
        return $this->parseUserInfo($response->data);
    }
    
    /**
     * Handle OAuth callback and authenticate user
     * @param string $code Authorization code
     * @param string $state State parameter
     * @return User|null Authenticated user or null
     */
    public function authenticate(string $code, string $state): ?User
    {
        try {
            // Get access token
            $tokenData = $this->getAccessToken($code, $state);
            $accessToken = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['expires_in'] ?? 3600;
            
            if (!$accessToken) {
                Yii::error('No access token in response', 'oauth2');
                return null;
            }
            
            // Get user info
            $userInfo = $this->getUserInfo($accessToken);
            
            if (empty($userInfo['provider_user_id'])) {
                Yii::error('No provider user ID in response', 'oauth2');
                return null;
            }
            
            // Find or create user
            $user = $this->findOrCreateUser($userInfo, $accessToken, $refreshToken, $expiresIn);
            
            return $user;
            
        } catch (\Exception $e) {
            Yii::error("OAuth2 authentication failed: " . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Find existing user or create new one
     * @param array $userInfo User info from provider
     * @param string $accessToken Access token
     * @param string|null $refreshToken Refresh token
     * @param int $expiresIn Token expiry in seconds
     * @return User|null
     */
    protected function findOrCreateUser(array $userInfo, string $accessToken, ?string $refreshToken, int $expiresIn): ?User
    {
        $providerName = $this->getProviderName();
        $providerUserId = $userInfo['provider_user_id'];
        
        // Check for existing OAuth connection
        $oauth = UserOauth::find()
            ->where([
                'provider' => $providerName,
                'provider_user_id' => $providerUserId,
            ])
            ->one();
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            if ($oauth) {
                // Update existing OAuth record
                $oauth->access_token = $accessToken;
                $oauth->refresh_token = $refreshToken;
                $oauth->token_expires_at = date('Y-m-d H:i:s', time() + $expiresIn);
                $oauth->profile_data = json_encode($userInfo);
                $oauth->save(false);
                
                // Get associated user
                $user = $oauth->user;
                
                // Update user info if changed
                $this->updateUserInfo($user, $userInfo);
                
            } else {
                // Check if user exists with same email
                $user = null;
                if (!empty($userInfo['email'])) {
                    $user = User::findOne(['email' => $userInfo['email']]);
                }
                
                if (!$user) {
                    // Create new user
                    $user = $this->createUser($userInfo);
                    
                    if (!$user) {
                        $transaction->rollBack();
                        return null;
                    }
                }
                
                // Create OAuth connection
                $oauth = new UserOauth([
                    'user_id' => $user->id,
                    'provider' => $providerName,
                    'provider_user_id' => $providerUserId,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_expires_at' => date('Y-m-d H:i:s', time() + $expiresIn),
                    'profile_data' => json_encode($userInfo),
                ]);
                
                if (!$oauth->save()) {
                    Yii::error('Failed to save OAuth record: ' . json_encode($oauth->errors), 'oauth2');
                    $transaction->rollBack();
                    return null;
                }
            }
            
            $transaction->commit();
            return $user;
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('OAuth user creation failed: ' . $e->getMessage(), 'oauth2');
            return null;
        }
    }
    
    /**
     * Create new user from OAuth data
     * @param array $userInfo User info
     * @return User|null
     */
    protected function createUser(array $userInfo): ?User
    {
        $user = new User();
        $user->email = $userInfo['email'] ?? null;
        $user->username = $this->generateUsername($userInfo);
        $user->first_name = $userInfo['first_name'] ?? '';
        $user->last_name = $userInfo['last_name'] ?? '';
        $user->avatar = $userInfo['avatar'] ?? null;
        $user->status = User::STATUS_ACTIVE;
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
                'บัญชีของคุณถูกสร้างผ่าน ' . ucfirst($this->getProviderName()) . ' เรียบร้อยแล้ว'
            );
            
            return $user;
        }
        
        Yii::error('Failed to create user: ' . json_encode($user->errors), 'oauth2');
        return null;
    }
    
    /**
     * Update existing user info from OAuth data
     * @param User $user User to update
     * @param array $userInfo OAuth user info
     */
    protected function updateUserInfo(User $user, array $userInfo): void
    {
        $updated = false;
        
        // Update avatar if not set locally
        if (empty($user->avatar) && !empty($userInfo['avatar'])) {
            $user->avatar = $userInfo['avatar'];
            $updated = true;
        }
        
        // Update name if empty
        if (empty($user->first_name) && !empty($userInfo['first_name'])) {
            $user->first_name = $userInfo['first_name'];
            $updated = true;
        }
        
        if (empty($user->last_name) && !empty($userInfo['last_name'])) {
            $user->last_name = $userInfo['last_name'];
            $updated = true;
        }
        
        if ($updated) {
            $user->save(false);
        }
    }
    
    /**
     * Generate unique username from OAuth data
     * @param array $userInfo User info
     * @return string
     */
    protected function generateUsername(array $userInfo): string
    {
        // Try email prefix first
        if (!empty($userInfo['email'])) {
            $base = explode('@', $userInfo['email'])[0];
        } elseif (!empty($userInfo['first_name'])) {
            $base = strtolower($userInfo['first_name']);
        } else {
            $base = 'user';
        }
        
        // Clean username
        $base = preg_replace('/[^a-z0-9_]/', '', strtolower($base));
        $base = substr($base, 0, 20) ?: 'user';
        
        // Ensure uniqueness
        $username = $base;
        $counter = 1;
        
        while (User::find()->where(['username' => $username])->exists()) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * Disconnect OAuth provider from user
     * @param int $userId User ID
     * @return bool
     */
    public function disconnect(int $userId): bool
    {
        return UserOauth::deleteAll([
            'user_id' => $userId,
            'provider' => $this->getProviderName(),
        ]) > 0;
    }
    
    /**
     * Check if user has this provider connected
     * @param int $userId User ID
     * @return bool
     */
    public function isConnected(int $userId): bool
    {
        return UserOauth::find()
            ->where([
                'user_id' => $userId,
                'provider' => $this->getProviderName(),
            ])
            ->exists();
    }
    
    /**
     * Get OAuth connection for user
     * @param int $userId User ID
     * @return UserOauth|null
     */
    public function getConnection(int $userId): ?UserOauth
    {
        return UserOauth::find()
            ->where([
                'user_id' => $userId,
                'provider' => $this->getProviderName(),
            ])
            ->one();
    }
}
