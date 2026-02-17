<?php
/**
 * Common Main Configuration
 * Meeting Room Booking System
 * 
 * This file contains configuration shared across all applications
 */

return [
    'name' => 'ระบบจองห้องประชุม',
    'language' => 'th',
    'sourceLanguage' => 'en-US',
    'timeZone' => 'Asia/Bangkok',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => \yii\caching\FileCache::class,
        ],
        'authManager' => [
            'class' => \yii\rbac\DbManager::class,
            'cache' => 'cache',
        ],
        // 
        'db' => require(__DIR__ . '/db.php'),
        // 
        // 'db' => [
        //     'class' => \yii\db\Connection::class,
        //     'dsn' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME'),
        //     'username' => getenv('DB_USER'),
        //     'password' => getenv('DB_PASS'),
        //     'charset' => 'utf8mb4',
        //     'enableSchemaCache' => !YII_DEBUG,
        //     'schemaCacheDuration' => 3600,
        //     'schemaCache' => 'cache',
        // ],
        // 
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => YII_DEBUG, // Use file for development
            'transport' => [
                'scheme' => getenv('MAIL_ENCRYPTION') ?: 'smtp',
                'host' => getenv('MAIL_HOST') ?: 'localhost',
                'username' => getenv('MAIL_USERNAME'),
                'password' => getenv('MAIL_PASSWORD'),
                'port' => (int)(getenv('MAIL_PORT') ?: 587),
            ],
        ],
        'formatter' => [
            'class' => \yii\i18n\Formatter::class,
            'locale' => 'th_TH',
            'timeZone' => 'Asia/Bangkok',
            'dateFormat' => 'php:d/m/Y',
            'datetimeFormat' => 'php:d/m/Y H:i',
            'timeFormat' => 'php:H:i',
            'currencyCode' => 'THB',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
            'nullDisplay' => '-',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => \yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'security' => [
            'class' => \yii\base\Security::class,
        ],
    ],
    'params' => require __DIR__ . '/params.php',
];
