<?php

namespace api\controllers\v1;

use Yii;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\RateLimiter;
use yii\filters\Cors;
use yii\web\UnauthorizedHttpException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Base API Controller
 * Provides JWT authentication and common functionality
 */
class BaseController extends Controller
{
    /**
     * @var bool Whether authentication is required
     */
    public $authRequired = true;

    /**
     * @var array Actions that don't require authentication
     */
    public $authExcept = [];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Remove default authenticator
        unset($behaviors['authenticator']);

        // Add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

        // Add JWT authenticator
        if ($this->authRequired) {
            $behaviors['authenticator'] = [
                'class' => HttpBearerAuth::class,
                'except' => array_merge(['options'], $this->authExcept),
            ];
        }

        // Add rate limiter
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::class,
            'enableRateLimitHeaders' => true,
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Handle OPTIONS request for CORS
        if (Yii::$app->request->method === 'OPTIONS') {
            Yii::$app->response->statusCode = 200;
            return false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Generate JWT token for user
     *
     * @param \common\models\User $user
     * @param bool $isRefresh Whether to generate refresh token
     * @return string
     */
    protected function generateToken($user, $isRefresh = false)
    {
        $params = Yii::$app->params['jwt'];
        $time = time();
        $expire = $isRefresh ? $params['refreshExpire'] : $params['expire'];

        $payload = [
            'iss' => $params['issuer'],
            'aud' => $params['audience'],
            'iat' => $time,
            'exp' => $time + $expire,
            'sub' => $user->id,
            'type' => $isRefresh ? 'refresh' : 'access',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'fullname' => $user->fullname,
            ],
        ];

        return JWT::encode($payload, $params['secret'], $params['algorithm']);
    }

    /**
     * Validate and decode JWT token
     *
     * @param string $token
     * @return object|null
     */
    protected function validateToken($token)
    {
        try {
            $params = Yii::$app->params['jwt'];
            $decoded = JWT::decode($token, new Key($params['secret'], $params['algorithm']));

            // Check expiration
            if ($decoded->exp < time()) {
                return null;
            }

            return $decoded;
        } catch (\Exception $e) {
            Yii::warning('JWT validation failed: ' . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    /**
     * Get current authenticated user
     *
     * @return \common\models\User|null
     */
    protected function getCurrentUser()
    {
        return Yii::$app->user->identity;
    }

    /**
     * Standard success response
     *
     * @param mixed $data
     * @param string|null $message
     * @return array
     */
    protected function success($data = null, $message = null)
    {
        $response = ['success' => true];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return $response;
    }

    /**
     * Standard error response
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @return array
     */
    protected function error($message, $statusCode = 400, $errors = null)
    {
        Yii::$app->response->statusCode = $statusCode;

        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $response;
    }

    /**
     * Pagination helper
     *
     * @param \yii\data\ActiveDataProvider $dataProvider
     * @return array
     */
    protected function paginate($dataProvider)
    {
        $pagination = $dataProvider->pagination;

        return [
            'items' => $dataProvider->getModels(),
            'pagination' => [
                'total' => $dataProvider->getTotalCount(),
                'page' => $pagination->getPage() + 1,
                'pageSize' => $pagination->getPageSize(),
                'pageCount' => $pagination->getPageCount(),
            ],
        ];
    }

    /**
     * Validate model and return formatted errors
     *
     * @param \yii\base\Model $model
     * @return array|null Null if valid, array of errors if invalid
     */
    protected function validateModel($model)
    {
        if (!$model->validate()) {
            $errors = [];
            foreach ($model->errors as $attribute => $messages) {
                $errors[$attribute] = $messages[0];
            }
            return $errors;
        }
        return null;
    }
}
