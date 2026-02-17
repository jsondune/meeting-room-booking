<?php
/**
 * Backend Main Configuration
 * Meeting Room Booking System - Admin Panel
 */

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'meeting-room-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'cookieValidationKey' => getenv('BACKEND_COOKIE_KEY') ?: 'your-backend-cookie-validation-key',
        ],
        'user' => [
            'identityClass' => \common\models\User::class,
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'name' => 'mrb-backend',
            'timeout' => 3600,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'logFile' => '@runtime/logs/error.log',
                ],
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['info'],
                    'categories' => ['application'],
                    'logFile' => '@runtime/logs/app.log',
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
                'profile' => 'site/profile',
                'dashboard' => 'site/dashboard',
                
                // CRUD resources
                '<controller:(room|booking|user|equipment|department)>' => '<controller>/index',
                '<controller:(room|booking|user|equipment|department)>/create' => '<controller>/create',
                '<controller:(room|booking|user|equipment|department)>/<id:\d+>' => '<controller>/view',
                '<controller:(room|booking|user|equipment|department)>/<id:\d+>/update' => '<controller>/update',
                '<controller:(room|booking|user|equipment|department)>/<id:\d+>/delete' => '<controller>/delete',
                
                // Approval
                'approval' => 'approval/index',
                'approval/pending' => 'approval/pending',
                'approval/history' => 'approval/history',
                'approval/<id:\d+>' => 'approval/view',
                
                // Reports
                'reports' => 'report/index',
                'reports/<action>' => 'report/<action>',
                
                // Settings
                'settings' => 'setting/index',
                'settings/<action>' => 'setting/<action>',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                \yii\web\JqueryAsset::class => [
                    'sourcePath' => null,
                    'js' => ['https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js'],
                ],
                \yii\bootstrap5\BootstrapAsset::class => [
                    'sourcePath' => null,
                    'css' => ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css'],
                ],
                \yii\bootstrap5\BootstrapPluginAsset::class => [
                    'sourcePath' => null,
                    'js' => ['https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
