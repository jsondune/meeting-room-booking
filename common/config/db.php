<?php
/**
 * Database Configuration
 * 
 * Copy this file to db-local.php and modify the values below
 * to match your local database settings.
 * 
 * For XAMPP default settings:
 * - host: localhost
 * - username: root
 * - password: (empty)
 * - database: meeting_room_booking
 */

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=mrbapp',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    
    // Schema caching for production
    'enableSchemaCache' => false,
    'schemaCacheDuration' => 3600,
    'schemaCache' => 'cache',
    
    // Table prefix (optional)
    // 'tablePrefix' => 'mrb_',
];
