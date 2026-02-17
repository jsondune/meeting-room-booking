<?php

namespace api\controllers\v1;

use Yii;
use yii\rest\Controller;
use yii\filters\Cors;

/**
 * DefaultController for API health checks and version info
 */
class DefaultController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
        ];

        return $behaviors;
    }

    /**
     * Health check endpoint
     * GET /api/v1/health
     *
     * @return array
     */
    public function actionHealth()
    {
        $status = 'healthy';
        $checks = [];

        // Check database connection
        try {
            Yii::$app->db->createCommand('SELECT 1')->execute();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error';
            $status = 'unhealthy';
        }

        // Check cache connection (if configured)
        try {
            Yii::$app->cache->get('health_check');
            $checks['cache'] = 'ok';
        } catch (\Exception $e) {
            $checks['cache'] = 'not_configured';
        }

        // Check disk space
        $freeSpace = disk_free_space('/');
        $totalSpace = disk_total_space('/');
        $usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
        
        if ($usedPercent > 90) {
            $checks['disk'] = 'warning';
            $status = $status === 'healthy' ? 'degraded' : $status;
        } else {
            $checks['disk'] = 'ok';
        }

        return [
            'status' => $status,
            'timestamp' => date('Y-m-d H:i:s'),
            'checks' => $checks,
            'system' => [
                'php_version' => PHP_VERSION,
                'yii_version' => Yii::getVersion(),
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                'disk_usage' => $usedPercent . '%',
            ],
        ];
    }

    /**
     * Version endpoint
     * GET /api/v1/version
     *
     * @return array
     */
    public function actionVersion()
    {
        return [
            'api_version' => Yii::$app->params['apiVersion'] ?? '1.0.0',
            'app_name' => 'Meeting Room Booking System',
            'app_version' => '1.0.0',
            'environment' => YII_ENV,
            'php_version' => PHP_VERSION,
            'yii_version' => Yii::getVersion(),
            'build_date' => '2024-12-25',
            'documentation' => '/api/v1/docs',
            'endpoints' => [
                'auth' => '/api/v1/auth',
                'rooms' => '/api/v1/rooms',
                'bookings' => '/api/v1/bookings',
                'profile' => '/api/v1/profile',
                'notifications' => '/api/v1/notifications',
                'calendar' => '/api/v1/calendar',
                'dashboard' => '/api/v1/dashboard',
            ],
        ];
    }

    /**
     * API documentation endpoint
     * GET /api/v1/docs
     *
     * @return array
     */
    public function actionDocs()
    {
        return [
            'title' => 'Meeting Room Booking API',
            'version' => '1.0.0',
            'description' => 'RESTful API for Meeting Room Booking System',
            'base_url' => Yii::$app->request->hostInfo . '/api/v1',
            'authentication' => [
                'type' => 'Bearer Token (JWT)',
                'header' => 'Authorization: Bearer {token}',
                'endpoints' => [
                    'login' => 'POST /api/v1/auth/login',
                    'register' => 'POST /api/v1/auth/register',
                    'refresh' => 'POST /api/v1/auth/refresh',
                    'logout' => 'POST /api/v1/auth/logout',
                ],
            ],
            'endpoints' => [
                'rooms' => [
                    'list' => 'GET /api/v1/rooms',
                    'view' => 'GET /api/v1/rooms/{id}',
                    'search' => 'GET /api/v1/rooms/search?term={term}',
                    'availability' => 'GET /api/v1/rooms/{id}/availability?date={date}',
                    'time_slots' => 'GET /api/v1/rooms/{id}/time-slots?date={date}',
                ],
                'bookings' => [
                    'list' => 'GET /api/v1/bookings',
                    'view' => 'GET /api/v1/bookings/{id}',
                    'create' => 'POST /api/v1/bookings',
                    'update' => 'PUT /api/v1/bookings/{id}',
                    'cancel' => 'POST /api/v1/bookings/{id}/cancel',
                    'check_in' => 'POST /api/v1/bookings/{id}/check-in',
                    'check_out' => 'POST /api/v1/bookings/{id}/check-out',
                    'statistics' => 'GET /api/v1/bookings/statistics',
                ],
                'profile' => [
                    'view' => 'GET /api/v1/profile',
                    'update' => 'PUT /api/v1/profile',
                    'avatar' => 'POST /api/v1/profile/avatar',
                    'password' => 'PUT /api/v1/profile/password',
                    'notifications' => 'GET /api/v1/profile/notifications',
                    'enable_2fa' => 'POST /api/v1/profile/enable-2fa',
                    'delete' => 'DELETE /api/v1/profile',
                ],
                'notifications' => [
                    'list' => 'GET /api/v1/notifications',
                    'mark_read' => 'PUT /api/v1/notifications/{id}/read',
                    'mark_all_read' => 'PUT /api/v1/notifications/read-all',
                    'unread_count' => 'GET /api/v1/notifications/unread-count',
                ],
            ],
            'rate_limiting' => [
                'requests_per_minute' => 100,
                'headers' => [
                    'X-Rate-Limit-Limit',
                    'X-Rate-Limit-Remaining',
                    'X-Rate-Limit-Reset',
                ],
            ],
            'response_format' => [
                'success' => [
                    'success' => true,
                    'status' => 200,
                    'data' => '...',
                    'message' => '...',
                ],
                'error' => [
                    'success' => false,
                    'status' => 400,
                    'message' => 'Error message',
                    'errors' => ['field' => 'error message'],
                ],
            ],
        ];
    }
}
