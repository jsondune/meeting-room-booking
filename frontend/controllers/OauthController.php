<?php
/**
 * OAuth Controller
 * 
 * Handles OAuth2 authentication callbacks for all providers
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\GoogleOAuth2Client;
use common\components\MicrosoftOAuth2Client;
use common\components\ThaiDOAuth2Client;

class OauthController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    // Anyone can access auth and callback actions
                    [
                        'actions' => ['auth', 'callback'],
                        'allow' => true,
                    ],
                    // Only authenticated users can connect/disconnect
                    [
                        'actions' => ['connect', 'disconnect'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'disconnect' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * Get OAuth client by provider name
     * @param string $provider
     * @return GoogleOAuth2Client|MicrosoftOAuth2Client|ThaiDOAuth2Client
     * @throws NotFoundHttpException
     */
    protected function getOAuthClient(string $provider)
    {
        $client = match($provider) {
            'google' => new GoogleOAuth2Client(),
            'microsoft' => new MicrosoftOAuth2Client(),
            'thaid' => new ThaiDOAuth2Client(),
            default => null,
        };
        
        if (!$client || !$client->isConfigured()) {
            throw new NotFoundHttpException('OAuth provider not available.');
        }
        
        return $client;
    }
    
    /**
     * Redirect to OAuth provider for authentication
     * GET /oauth/auth?provider=google
     * 
     * @param string $provider Provider name
     * @param string|null $return Return URL after login
     * @return \yii\web\Response
     */
    public function actionAuth(string $provider, ?string $return = null)
    {
        // Already logged in?
        if (!Yii::$app->user->isGuest) {
            return $this->redirect($return ?: ['/dashboard']);
        }
        
        $client = $this->getOAuthClient($provider);
        
        // Store return URL
        if ($return) {
            Yii::$app->session->set('oauth_return_url', $return);
        }
        
        // Redirect to provider
        return $this->redirect($client->getAuthUrl());
    }
    
    /**
     * Handle OAuth callback from provider
     * GET /oauth/callback/{provider}
     * 
     * @param string $provider Provider name
     * @return \yii\web\Response
     */
    public function actionCallback(string $provider)
    {
        $code = Yii::$app->request->get('code');
        $state = Yii::$app->request->get('state');
        $error = Yii::$app->request->get('error');
        $errorDescription = Yii::$app->request->get('error_description');
        
        // Handle OAuth errors
        if ($error) {
            Yii::warning("OAuth error from {$provider}: {$error} - {$errorDescription}", 'oauth');
            Yii::$app->session->setFlash('error', $this->getErrorMessage($error, $errorDescription));
            return $this->redirect(['/site/login']);
        }
        
        // Validate required parameters
        if (empty($code) || empty($state)) {
            throw new BadRequestHttpException('Invalid OAuth callback parameters.');
        }
        
        try {
            $client = $this->getOAuthClient($provider);
            
            // Check if this is a connect action (user already logged in)
            $isConnect = !Yii::$app->user->isGuest;
            
            if ($isConnect) {
                // Connect provider to existing account
                return $this->handleConnect($client, $code, $state);
            } else {
                // Authenticate/register user
                return $this->handleAuth($client, $code, $state);
            }
            
        } catch (\Exception $e) {
            Yii::error("OAuth callback error: " . $e->getMessage(), 'oauth');
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการเข้าสู่ระบบ โปรดลองใหม่อีกครั้ง');
            return $this->redirect(['/site/login']);
        }
    }
    
    /**
     * Handle authentication (login/register)
     */
    protected function handleAuth($client, string $code, string $state)
    {
        $user = $client->authenticate($code, $state);
        
        if (!$user) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยืนยันตัวตนได้ โปรดลองใหม่อีกครั้ง');
            return $this->redirect(['/site/login']);
        }
        
        // Check if user is active
        if ($user->status !== \common\models\User::STATUS_ACTIVE) {
            Yii::$app->session->setFlash('error', 'บัญชีของคุณถูกระงับการใช้งาน โปรดติดต่อผู้ดูแลระบบ');
            return $this->redirect(['/site/login']);
        }
        
        // Login user
        if (Yii::$app->user->login($user, 3600 * 24 * 30)) {
            // Update last login
            $user->updateLastLogin();
            
            // Log successful login
            Yii::info("OAuth login successful: user={$user->id}, provider={$client->getProviderName()}", 'oauth');
            
            // Flash success message
            Yii::$app->session->setFlash('success', 'เข้าสู่ระบบสำเร็จ ยินดีต้อนรับคุณ ' . $user->getDisplayName());
            
            // Redirect to return URL or dashboard
            $returnUrl = Yii::$app->session->get('oauth_return_url', ['/dashboard']);
            Yii::$app->session->remove('oauth_return_url');
            
            return $this->redirect($returnUrl);
        }
        
        Yii::$app->session->setFlash('error', 'ไม่สามารถเข้าสู่ระบบได้ โปรดลองใหม่อีกครั้ง');
        return $this->redirect(['/site/login']);
    }
    
    /**
     * Handle connecting provider to existing account
     */
    protected function handleConnect($client, string $code, string $state)
    {
        $userId = Yii::$app->user->id;
        
        // Get tokens and user info
        try {
            $tokenData = $client->getAccessToken($code, $state);
            $accessToken = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['expires_in'] ?? 3600;
            
            $userInfo = $client->getUserInfo($accessToken);
            
        } catch (\Exception $e) {
            Yii::error("OAuth connect error: " . $e->getMessage(), 'oauth');
            Yii::$app->session->setFlash('error', 'ไม่สามารถเชื่อมต่อบัญชีได้ โปรดลองใหม่อีกครั้ง');
            return $this->redirect(['/profile/connections']);
        }
        
        // Check if this OAuth account is already connected to another user
        $existing = \common\models\UserOauth::findByProvider(
            $client->getProviderName(),
            $userInfo['provider_user_id']
        );
        
        if ($existing && $existing->user_id !== $userId) {
            Yii::$app->session->setFlash('error', 'บัญชีนี้เชื่อมต่อกับผู้ใช้อื่นแล้ว');
            return $this->redirect(['/profile/connections']);
        }
        
        // Create or update OAuth connection
        $oauth = $existing ?: new \common\models\UserOauth();
        $oauth->user_id = $userId;
        $oauth->provider = $client->getProviderName();
        $oauth->provider_user_id = $userInfo['provider_user_id'];
        $oauth->access_token = $accessToken;
        $oauth->refresh_token = $refreshToken;
        $oauth->token_expires_at = date('Y-m-d H:i:s', time() + $expiresIn);
        $oauth->profile_data = json_encode($userInfo);
        
        if ($oauth->save()) {
            Yii::$app->session->setFlash('success', 'เชื่อมต่อบัญชี ' . $client->getProviderName() . ' สำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถบันทึกการเชื่อมต่อได้');
        }
        
        return $this->redirect(['/profile/connections']);
    }
    
    /**
     * Connect OAuth provider to current user
     * GET /oauth/connect?provider=google
     */
    public function actionConnect(string $provider)
    {
        $client = $this->getOAuthClient($provider);
        
        // Check if already connected
        if ($client->isConnected(Yii::$app->user->id)) {
            Yii::$app->session->setFlash('warning', 'บัญชีนี้เชื่อมต่อแล้ว');
            return $this->redirect(['/profile/connections']);
        }
        
        // Store return URL
        Yii::$app->session->set('oauth_return_url', Yii::$app->request->referrer ?: ['/profile/connections']);
        
        // Redirect to provider
        return $this->redirect($client->getAuthUrl());
    }
    
    /**
     * Disconnect OAuth provider from current user
     * POST /oauth/disconnect
     */
    public function actionDisconnect()
    {
        $provider = Yii::$app->request->post('provider');
        
        if (empty($provider)) {
            throw new BadRequestHttpException('Provider is required.');
        }
        
        $client = $this->getOAuthClient($provider);
        $userId = Yii::$app->user->id;
        
        // Check if user has other login methods
        $user = Yii::$app->user->identity;
        $connections = \common\models\UserOauth::findByUser($userId);
        
        // If only one connection and no password, don't allow disconnect
        if (count($connections) <= 1 && empty($user->password_hash)) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยกเลิกการเชื่อมต่อได้ เนื่องจากไม่มีวิธีเข้าสู่ระบบอื่น โปรดตั้งรหัสผ่านก่อน');
            return $this->redirect(['/profile/connections']);
        }
        
        if ($client->disconnect($userId)) {
            Yii::$app->session->setFlash('success', 'ยกเลิกการเชื่อมต่อ ' . $client->getProviderName() . ' สำเร็จ');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่พบการเชื่อมต่อที่ต้องการยกเลิก');
        }
        
        return $this->redirect(['/profile/connections']);
    }
    
    /**
     * Get user-friendly error message
     */
    protected function getErrorMessage(string $error, ?string $description): string
    {
        $messages = [
            'access_denied' => 'คุณปฏิเสธการเข้าถึง โปรดลองใหม่และอนุญาตการเข้าถึง',
            'invalid_request' => 'คำขอไม่ถูกต้อง โปรดลองใหม่อีกครั้ง',
            'unauthorized_client' => 'แอปพลิเคชันไม่ได้รับอนุญาต โปรดติดต่อผู้ดูแลระบบ',
            'invalid_scope' => 'สิทธิ์การเข้าถึงไม่ถูกต้อง โปรดติดต่อผู้ดูแลระบบ',
            'server_error' => 'เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ โปรดลองใหม่ภายหลัง',
            'temporarily_unavailable' => 'บริการไม่พร้อมใช้งานชั่วคราว โปรดลองใหม่ภายหลัง',
        ];
        
        return $messages[$error] ?? ($description ?: 'เกิดข้อผิดพลาด โปรดลองใหม่อีกครั้ง');
    }
}
