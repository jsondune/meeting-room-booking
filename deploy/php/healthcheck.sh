#!/bin/sh
# Meeting Room Booking System - PHP-FPM Health Check
# Returns exit code 0 if healthy, 1 if unhealthy

set -e

# Check if PHP-FPM is running
if ! pgrep -x "php-fpm" > /dev/null; then
    echo "PHP-FPM process not running"
    exit 1
fi

# Check PHP-FPM ping endpoint
PING_RESPONSE=$(SCRIPT_NAME=/fpm-ping \
    SCRIPT_FILENAME=/fpm-ping \
    REQUEST_METHOD=GET \
    cgi-fcgi -bind -connect 127.0.0.1:9001 2>/dev/null || echo "failed")

if [ "$PING_RESPONSE" != "pong" ] && ! echo "$PING_RESPONSE" | grep -q "pong"; then
    echo "PHP-FPM ping failed"
    exit 1
fi

# Optional: Check if we can execute PHP
PHP_CHECK=$(php -r "echo 'ok';" 2>/dev/null || echo "failed")
if [ "$PHP_CHECK" != "ok" ]; then
    echo "PHP execution failed"
    exit 1
fi

# Optional: Check Redis connection
if [ -n "$REDIS_HOST" ]; then
    REDIS_CHECK=$(php -r "
        try {
            \$redis = new Redis();
            \$redis->connect('$REDIS_HOST', 6379, 2);
            echo \$redis->ping() ? 'ok' : 'failed';
        } catch (Exception \$e) {
            echo 'failed';
        }
    " 2>/dev/null || echo "failed")
    
    if [ "$REDIS_CHECK" != "ok" ]; then
        echo "Redis connection failed (warning only)"
        # Don't fail on Redis - it might be temporarily unavailable
    fi
fi

# Optional: Check MySQL connection
if [ -n "$DB_HOST" ] && [ -n "$DB_USER" ]; then
    DB_CHECK=$(php -r "
        try {
            \$pdo = new PDO('mysql:host=$DB_HOST;dbname=$DB_NAME', '$DB_USER', '$DB_PASS');
            \$pdo->query('SELECT 1');
            echo 'ok';
        } catch (PDOException \$e) {
            echo 'failed';
        }
    " 2>/dev/null || echo "failed")
    
    if [ "$DB_CHECK" != "ok" ]; then
        echo "Database connection failed (warning only)"
        # Don't fail on DB - it might be temporarily unavailable
    fi
fi

echo "PHP-FPM is healthy"
exit 0
