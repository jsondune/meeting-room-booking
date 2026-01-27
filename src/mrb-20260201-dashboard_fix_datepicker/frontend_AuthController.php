<?php
/**
 * Auth Controller
 * 
 * Handles OAuth authentication redirects to proper providers
 * Routes: /auth/azure, /auth/google, /auth/thaid
 * 
 * @author BIzAI
 * @version 1.0
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class AuthController extends Controller
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
                    [
                        'actions' => ['azure', 'google', 'thaid', 'callback'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Check if OAuth provider is configured
     */
    protected function isProviderConfigured(string $provider): bool
    {
        $envKeys = [
            'microsoft' => 'MICROSOFT_CLIENT_ID',
            'google' => 'GOOGLE_CLIENT_ID',
            'thaid' => 'THAID_CLIENT_ID',
        ];
        
        $paramKeys = [
            'microsoft' => ['oauth', 'microsoft', 'clientId'],
            'google' => ['oauth', 'google', 'clientId'],
            'thaid' => ['oauth', 'thaid', 'clientId'],
        ];
        
        // Check environment variable
        if (isset($envKeys[$provider]) && getenv($envKeys[$provider])) {
            return true;
        }
        
        // Check params
        if (isset($paramKeys[$provider])) {
            $params = Yii::$app->params;
            foreach ($paramKeys[$provider] as $key) {
                if (!isset($params[$key])) {
                    return false;
                }
                $params = $params[$key];
            }
            return !empty($params);
        }
        
        return false;
    }

    /**
     * Show error when provider not configured
     */
    protected function showProviderNotConfigured(string $providerName): \yii\web\Response
    {
        Yii::$app->session->setFlash('warning', 
            "การเข้าสู่ระบบด้วย {$providerName} ยังไม่พร้อมใช้งาน กรุณาใช้วิธีอื่นหรือติดต่อผู้ดูแลระบบ"
        );
        return $this->redirect(['/site/login']);
    }

    /**
     * Microsoft Azure AD OAuth
     * GET /auth/azure
     */
    public function actionAzure()
    {
        if (!$this->isProviderConfigured('microsoft')) {
            return $this->showProviderNotConfigured('Microsoft 365');
        }
        return $this->redirect(['/oauth/auth', 'provider' => 'microsoft']);
    }

    /**
     * Google OAuth
     * GET /auth/google
     */
    public function actionGoogle()
    {
        if (!$this->isProviderConfigured('google')) {
            return $this->showProviderNotConfigured('Google');
        }
        return $this->redirect(['/oauth/auth', 'provider' => 'google']);
    }

    /**
     * ThaID OAuth
     * GET /auth/thaid
     */
    public function actionThaid()
    {
        if (!$this->isProviderConfigured('thaid')) {
            return $this->showProviderNotConfigured('ThaID');
        }
        return $this->redirect(['/oauth/auth', 'provider' => 'thaid']);
    }

    /**
     * OAuth Callback (redirects to OauthController)
     * GET /auth/callback/{provider}
     */
    public function actionCallback($provider)
    {
        // Forward all query parameters to oauth controller
        $params = Yii::$app->request->queryParams;
        $params[0] = '/oauth/callback';
        $params['provider'] = $provider;
        
        return $this->redirect($params);
    }
}
