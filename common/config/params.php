<?php
/**
 * Common Parameters Configuration
 * Meeting Room Booking System
 */

return [
    // Application info
    'appName' => 'ระบบจองห้องประชุม',
    'appVersion' => '1.0.0',
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'dune2002@pb.ac.th',
    'supportEmail' => getenv('SUPPORT_EMAIL') ?: 'dune2002@pi.ac.th',
    'senderEmail' => getenv('SENDER_EMAIL') ?: 'noreply@pi.ac.th',
    'senderName' => getenv('SENDER_NAME') ?: 'ระบบจองห้องประชุม',
    
    // URLs
    'frontendUrl' => getenv('FRONTEND_URL') ?: 'http://mrb.test/frontend',
    'backendUrl' => getenv('BACKEND_URL') ?: 'http://mrb.test/backend',
    'apiUrl' => getenv('API_URL') ?: 'http://mrb.test:8082',
    
    // Booking settings
    'booking' => [
        'minAdvanceHours' => 2,           // Minimum hours in advance to book
        'maxAdvanceDays' => 90,           // Maximum days in advance to book
        'minDurationMinutes' => 30,       // Minimum booking duration
        'maxDurationHours' => 8,          // Maximum booking duration
        'workingHoursStart' => '08:00',   // Office hours start
        'workingHoursEnd' => '18:00',     // Office hours end
        'allowWeekends' => false,         // Allow weekend bookings
        'requireApproval' => true,        // Require admin approval
        'allowRecurring' => true,         // Allow recurring bookings
        'maxRecurrenceCount' => 52,       // Maximum recurrence count (1 year)
        'reminderHoursBefore' => [24, 1], // Send reminders at these hours before
        'autoCompleteAfterMinutes' => 30, // Auto-complete booking after end time
        'autoCancelNoShowMinutes' => 15,  // Auto-cancel if no check-in
    ],
    
    // User settings
    'user' => [
        'passwordMinLength' => 8,
        'passwordRequireUppercase' => true,
        'passwordRequireLowercase' => true,
        'passwordRequireNumber' => true,
        'passwordRequireSpecial' => false,
        'loginMaxAttempts' => 5,
        'loginLockoutMinutes' => 15,
        'sessionTimeout' => 3600,          // 1 hour
        'rememberMeDuration' => 2592000,   // 30 days
        'avatarMaxSize' => 2097152,        // 2MB
        'avatarAllowedTypes' => ['jpg', 'jpeg', 'png', 'gif'],
    ],
    
    // Room settings
    'room' => [
        'imageMaxSize' => 5242880,         // 5MB
        'imageAllowedTypes' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'imageMaxWidth' => 1920,
        'imageMaxHeight' => 1080,
    ],
    
    // Equipment settings
    'equipment' => [
        'imageMaxSize' => 2097152,         // 2MB
    ],
    
    // File upload settings
    'upload' => [
        'basePath' => '@webroot/uploads',
        'baseUrl' => '@web/uploads',
        'tempPath' => '@runtime/uploads',
    ],
    
    // Pagination
    'defaultPageSize' => 20,
    'maxPageSize' => 100,
    
    // API settings
    'api' => [
        'jwtSecretKey' => getenv('JWT_SECRET') ?: 'your-secret-key-change-in-production',
        'jwtExpiration' => 3600,           // 1 hour
        'jwtRefreshExpiration' => 604800,  // 7 days
        'rateLimitPerUser' => 100,         // Requests per minute
    ],
    
    // OAuth settings
    'oauth' => [
        'google' => [
            'clientId' => getenv('GOOGLE_CLIENT_ID'),
            'clientSecret' => getenv('GOOGLE_CLIENT_SECRET'),
        ],
        'azure' => [
            'clientId' => getenv('AZURE_CLIENT_ID'),
            'clientSecret' => getenv('AZURE_CLIENT_SECRET'),
            'tenantId' => getenv('AZURE_TENANT_ID') ?: 'common',
        ],
        'thaid' => [
            'clientId' => getenv('THAID_CLIENT_ID'),
            'clientSecret' => getenv('THAID_CLIENT_SECRET'),
        ],
    ],
    
    // Notification settings
    'notification' => [
        'enableEmail' => true,
        'enablePush' => false,
        'enableSms' => false,
    ],
    
    // Report settings
    'report' => [
        'defaultDateRange' => 30,          // Days for default reports
        'exportMaxRows' => 10000,          // Max rows for export
    ],
    
    // Maintenance
    'maintenanceMode' => false,
    'maintenanceAllowedIPs' => [],
];
