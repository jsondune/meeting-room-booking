<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'frontend\controllers',
    'bootstrap' => ['log'],
    'language' => 'th-TH',
    'sourceLanguage' => 'en-US',
    'name' => 'ระบบจองห้องประชุม',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'your-secret-key-here-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '' => 'site/index',
                'login' => 'site/login',
                'logout' => 'site/logout',
                'register' => 'site/register',
                'dashboard' => 'site/dashboard',
                'profile' => 'profile/index',
                'profile/edit' => 'profile/edit',
                'rooms' => 'room/index',
                'room/<id:\d+>' => 'room/view',
                'booking/create' => 'booking/create',
                'booking/<id:\d+>' => 'booking/view',            
                'my-bookings' => 'booking/index',
                // เพิ่มบรรทัดเหล่านี้
                'book' => 'booking/index',
                'book/<action:\w+>' => 'booking/<action>',
                'book/<action:\w+>/<id:\d+>' => 'booking/<action>',
                // ... other rules
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'css' => [],
                    'sourcePath' => null,
                    'baseUrl' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css',
                    'css' => ['bootstrap.min.css'],
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'js' => [],
                    'sourcePath' => null,
                    'baseUrl' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js',
                    'js' => ['bootstrap.bundle.min.js'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
