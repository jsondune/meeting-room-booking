<?php
/**
 * URL Rules for Frontend
 * Add these rules to frontend/config/main.php in the 'urlManager' => 'rules' array
 * 
 * This file is a reference - copy the rules to your main.php
 */

return [
    // Map /book/* to /booking/* controller
    'book' => 'booking/index',
    'book/<action:\w+>' => 'booking/<action>',
    'book/<action:\w+>/<id:\d+>' => 'booking/<action>',
    
    // Profile routes
    'profile' => 'profile/index',
    'profile/<action:\w+>' => 'profile/<action>',
    
    // Room routes  
    'room' => 'room/index',
    'room/<action:\w+>' => 'room/<action>',
    'room/<action:\w+>/<id:\d+>' => 'room/<action>',
    
    // Calendar
    'calendar' => 'site/calendar',
    
    // About
    'about' => 'site/about',
];

/**
 * Example of full urlManager configuration in frontend/config/main.php:
 * 
 * 'urlManager' => [
 *     'enablePrettyUrl' => true,
 *     'showScriptName' => false,
 *     'rules' => [
 *         'book' => 'booking/index',
 *         'book/<action:\w+>' => 'booking/<action>',
 *         'book/<action:\w+>/<id:\d+>' => 'booking/<action>',
 *         // ... other rules
 *     ],
 * ],
 */
