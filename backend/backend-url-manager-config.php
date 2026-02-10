<?php
/**
 * Backend URL Manager Configuration
 * Location: backend/config/main.php (update the 'components' section)
 */

return [
    // ... other config ...
    
    'components' => [
        // ... other components ...
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                // Default rule
                '' => 'site/index',
                
                // Basic routes
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                
                // Module routes (if you have modules)
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
            ],
        ],
        
        // ... other components ...
    ],
    
    // ... other config ...
];
