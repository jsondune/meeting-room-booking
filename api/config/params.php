<?php

return [
    'apiVersion' => '1.0.0',
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: 'meeting-room-booking-jwt-secret-key-2024',
        'expire' => 3600 * 24 * 7, // 7 days
        'refreshExpire' => 3600 * 24 * 30, // 30 days
        'algorithm' => 'HS256',
        'issuer' => 'meeting-room-booking',
        'audience' => 'meeting-room-app',
    ],
    'rateLimiting' => [
        'requests' => 100,
        'period' => 60, // seconds
    ],
];
