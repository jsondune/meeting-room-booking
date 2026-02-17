<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    $response->data = [
                        'success' => $response->isSuccessful,
                        'status' => $response->statusCode,
                        'data' => $response->data,
                    ];
                }
            },
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // Authentication
                'POST api/v1/auth/login' => 'v1/auth/login',
                'POST api/v1/auth/register' => 'v1/auth/register',
                'POST api/v1/auth/refresh' => 'v1/auth/refresh',
                'POST api/v1/auth/logout' => 'v1/auth/logout',
                'POST api/v1/auth/forgot-password' => 'v1/auth/forgot-password',
                'POST api/v1/auth/reset-password' => 'v1/auth/reset-password',
                
                // User Profile
                'GET api/v1/profile' => 'v1/profile/index',
                'PUT api/v1/profile' => 'v1/profile/update',
                'POST api/v1/profile/avatar' => 'v1/profile/avatar',
                'PUT api/v1/profile/password' => 'v1/profile/password',
                
                // Rooms
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/room'],
                    'prefix' => 'api',
                    'extraPatterns' => [
                        'GET search' => 'search',
                        'GET {id}/availability' => 'availability',
                        'GET {id}/time-slots' => 'time-slots',
                    ],
                ],
                
                // Bookings
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/booking'],
                    'prefix' => 'api',
                    'extraPatterns' => [
                        'GET my-bookings' => 'my-bookings',
                        'POST {id}/cancel' => 'cancel',
                        'POST {id}/check-in' => 'check-in',
                        'POST {id}/check-out' => 'check-out',
                        'GET statistics' => 'statistics',
                    ],
                ],
                
                // Buildings
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/building'],
                    'prefix' => 'api',
                    'only' => ['index', 'view'],
                    'extraPatterns' => [
                        'GET dropdown' => 'dropdown',
                        'GET {id}/rooms' => 'rooms',
                        'GET {id}/floors' => 'floors',
                    ],
                ],
                
                // Equipment
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['v1/equipment'],
                    'prefix' => 'api',
                    'only' => ['index', 'view'],
                    'extraPatterns' => [
                        'GET categories' => 'categories',
                        'GET available' => 'available',
                        'GET {id}/schedule' => 'schedule',
                        'POST {id}/request' => 'request',
                        'DELETE {id}/request' => 'remove-request',
                    ],
                ],
                
                // Notifications
                'GET api/v1/notifications' => 'v1/notification/index',
                'PUT api/v1/notifications/<id:\d+>/read' => 'v1/notification/read',
                'PUT api/v1/notifications/read-all' => 'v1/notification/read-all',
                'GET api/v1/notifications/unread-count' => 'v1/notification/unread-count',
                'DELETE api/v1/notifications/<id:\d+>' => 'v1/notification/delete',
                
                // Calendar
                'GET api/v1/calendar/events' => 'v1/calendar/events',
                'GET api/v1/calendar/my-events' => 'v1/calendar/my-events',
                'GET api/v1/calendar/holidays' => 'v1/calendar/holidays',
                'GET api/v1/calendar/day/<date>' => 'v1/calendar/day',
                'GET api/v1/calendar/week/<date>' => 'v1/calendar/week',
                'GET api/v1/calendar/month/<year:\d{4}>/<month:\d{1,2}>' => 'v1/calendar/month',
                'GET api/v1/calendar/room-schedule/<id:\d+>' => 'v1/calendar/room-schedule',
                
                // Profile additional endpoints
                'GET api/v1/profile/notifications' => 'v1/profile/notifications',
                'PUT api/v1/profile/notifications' => 'v1/profile/update-notifications',
                'POST api/v1/profile/enable-2fa' => 'v1/profile/enable-2fa',
                'POST api/v1/profile/verify-2fa' => 'v1/profile/verify-2fa',
                'POST api/v1/profile/disable-2fa' => 'v1/profile/disable-2fa',
                'DELETE api/v1/profile' => 'v1/profile/delete',
                
                // Auth additional endpoints
                'GET api/v1/auth/verify-email' => 'v1/auth/verify-email',
                
                // Dashboard
                'GET api/v1/dashboard' => 'v1/dashboard/index',
                
                // Health check
                'GET api/v1/health' => 'v1/default/health',
                'GET api/v1/version' => 'v1/default/version',
            ],
        ],
    ],
    'params' => $params,
];
