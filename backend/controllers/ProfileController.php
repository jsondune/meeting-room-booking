<?php
/**
 * ProfileController - Backend user profile management
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;

/**
 * ProfileController - Admin profile and account management
 */
class ProfileController extends Controller
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
                    'disconnect-oauth' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Profile index - redirect to site/profile
     *
     * @return Response
     */
    public function actionIndex()
    {
        return $this->redirect(['/site/profile']);
    }

    /**
     * Manage OAuth connections
     *
     * @return string
     */
    public function actionConnections()
    {
        $user = Yii::$app->user->identity;

        // Get existing OAuth connections
        $oauthConnections = [];
        if (class_exists('\common\models\UserOauth')) {
            $oauthConnections = \common\models\UserOauth::find()
                ->where(['user_id' => $user->id])
                ->indexBy('provider')
                ->all();
        }

        // Check available providers
        $providers = [];
        
        if (getenv('GOOGLE_CLIENT_ID')) {
            $providers['google'] = [
                'name' => 'Google',
                'icon' => 'google',
                'color' => '#EA4335',
                'description' => 'เชื่อมต่อบัญชี Google เพื่อเข้าสู่ระบบและซิงค์ปฏิทิน',
                'features' => ['เข้าสู่ระบบด้วย Google', 'ซิงค์กับ Google Calendar'],
            ];
        }
        
        if (getenv('MICROSOFT_CLIENT_ID')) {
            $providers['microsoft'] = [
                'name' => 'Microsoft',
                'icon' => 'microsoft',
                'color' => '#00A4EF',
                'description' => 'เชื่อมต่อบัญชี Microsoft เพื่อเข้าสู่ระบบและซิงค์ปฏิทิน',
                'features' => ['เข้าสู่ระบบด้วย Microsoft', 'ซิงค์กับ Outlook Calendar'],
            ];
        }
        
        if (getenv('THAID_CLIENT_ID')) {
            $providers['thaid'] = [
                'name' => 'ThaiD',
                'icon' => 'thaid',
                'color' => '#1E3A8A',
                'description' => 'เชื่อมต่อบัญชี ThaiD เพื่อยืนยันตัวตนด้วยระบบภาครัฐ',
                'features' => ['ยืนยันตัวตนด้วย ThaiD', 'เข้าสู่ระบบด้วย ThaiD'],
            ];
        }

        // Check if user has password set
        $hasPassword = !empty($user->password_hash);

        return $this->render('connections', [
            'user' => $user,
            'connections' => $oauthConnections,
            'providers' => $providers,
            'hasPassword' => $hasPassword,
        ]);
    }

    /**
     * Disconnect OAuth provider
     *
     * @param string $provider
     * @return Response
     */
    public function actionDisconnectOauth($provider)
    {
        $user = Yii::$app->user->identity;

        // Check if user has password or other auth methods
        $hasPassword = !empty($user->password_hash);
        
        if (class_exists('\common\models\UserOauth')) {
            $oauthCount = \common\models\UserOauth::find()
                ->where(['user_id' => $user->id])
                ->count();

            if (!$hasPassword && $oauthCount <= 1) {
                Yii::$app->session->setFlash('error', 'ไม่สามารถยกเลิกการเชื่อมต่อได้ คุณต้องมีวิธีการเข้าสู่ระบบอย่างน้อย 1 วิธี โปรดตั้งรหัสผ่านก่อน');
                return $this->redirect(['connections']);
            }

            // Delete OAuth connection
            \common\models\UserOauth::deleteAll([
                'user_id' => $user->id,
                'provider' => $provider,
            ]);

            Yii::$app->session->setFlash('success', 'ยกเลิกการเชื่อมต่อเรียบร้อยแล้ว');
        }

        return $this->redirect(['connections']);
    }
}
