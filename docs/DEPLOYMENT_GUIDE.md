# Deployment Guide - Meeting Room Booking System

## Table of Contents

1. [Prerequisites](#1-prerequisites)
2. [Quick Start with Docker](#2-quick-start-with-docker)
3. [Manual Installation](#3-manual-installation)
4. [Configuration](#4-configuration)
5. [Database Setup](#5-database-setup)
6. [Web Server Configuration](#6-web-server-configuration)
7. [SSL/TLS Setup](#7-ssltls-setup)
8. [Production Optimizations](#8-production-optimizations)
9. [Monitoring & Logging](#9-monitoring--logging)
10. [Backup & Recovery](#10-backup--recovery)
11. [Troubleshooting](#11-troubleshooting)

---

## 1. Prerequisites

### System Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| CPU | 2 cores | 4+ cores |
| RAM | 2 GB | 4+ GB |
| Storage | 20 GB SSD | 50+ GB SSD |
| OS | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |

### Software Requirements

| Software | Version | Purpose |
|----------|---------|---------|
| PHP | 8.1+ | Application runtime |
| MySQL | 8.0+ | Database |
| Redis | 6.0+ | Cache & sessions |
| Nginx | 1.18+ | Web server |
| Composer | 2.0+ | PHP dependencies |
| Node.js | 18+ | Asset compilation |
| Git | 2.0+ | Version control |

---

## 2. Quick Start with Docker

### 2.1 Clone Repository

```bash
git clone https://github.com/jsondune/MRB-System.git mrbapp
cd mrbapp
```

### 2.2 Configure Environment

```bash
cp .env.example .env
```

Edit `.env` file with your settings:

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://meeting.bizco.co.th

# Database
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=meeting_room_booking
DB_USERNAME=meeting_user
DB_PASSWORD=your_secure_password

# Redis
REDIS_HOST=redis
REDIS_PORT=6379

# Email
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
```

### 2.3 Build and Start

```bash
# Build containers
docker-compose build

# Start services
docker-compose up -d

# Run migrations
docker-compose exec php php yii migrate --interactive=0

# Seed demo data (optional)
docker-compose exec php php yii seeder/all

# Set permissions
docker-compose exec php chmod -R 777 runtime web/assets
```

### 2.4 Access Application

- **Frontend:** http://localhost
- **Backend:** http://localhost/backend

Default admin credentials:
- Username: `admin`
- Password: `admin123` (change immediately!)

---

## 3. Manual Installation

### 3.1 Install System Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install packages
sudo apt install -y \
    php8.2-fpm \
    php8.2-cli \
    php8.2-mysql \
    php8.2-redis \
    php8.2-curl \
    php8.2-gd \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-zip \
    php8.2-intl \
    php8.2-bcmath \
    nginx \
    mysql-server \
    redis-server \
    git \
    unzip \
    supervisor
```

### 3.2 Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3.3 Install Node.js

```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 3.4 Clone and Setup Application

```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/jsondune/MRB-System.git mrbapp
cd mrbapp

# Set ownership
sudo chown -R www-data:www-data /var/www/mrbapp

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# Setup environment
cp .env.example .env
# Edit .env with your settings

# Initialize Yii2
php init --env=Production --overwrite=All

# Set permissions
chmod -R 755 .
chmod -R 777 runtime web/assets
chmod -R 777 frontend/runtime frontend/web/assets
chmod -R 777 backend/runtime backend/web/assets
```

---

## 4. Configuration

### 4.1 Environment Variables

Create or edit `.env` file:

```env
#--------------------------------------------------------------------
# APPLICATION
#--------------------------------------------------------------------
APP_ENV=production
APP_DEBUG=false
APP_URL=https://meeting.bizco.co.th
FRONTEND_URL=https://meeting.bizco.co.th
BACKEND_URL=https://meeting.bizco.co.th/admin

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
DB_DSN="mysql:host=localhost;dbname=meeting_room_booking"
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=meeting_room_booking
DB_USERNAME=meeting_user
DB_PASSWORD=your_secure_password
DB_CHARSET=utf8mb4

#--------------------------------------------------------------------
# CACHE & SESSION (Redis)
#--------------------------------------------------------------------
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=
REDIS_DATABASE=0

CACHE_DRIVER=redis
SESSION_DRIVER=redis

#--------------------------------------------------------------------
# EMAIL (SMTP)
#--------------------------------------------------------------------
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=your_email@gmail.com
SMTP_PASSWORD=your_app_password
MAIL_FROM_ADDRESS=noreply@bizco.co.th
MAIL_FROM_NAME="ระบบจองห้องประชุม"

#--------------------------------------------------------------------
# JWT (API Authentication)
#--------------------------------------------------------------------
JWT_SECRET_KEY=your_very_long_random_secret_key_here
JWT_EXPIRE_HOURS=24

#--------------------------------------------------------------------
# OAUTH PROVIDERS
#--------------------------------------------------------------------
# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=${FRONTEND_URL}/oauth/callback?provider=google

# Microsoft OAuth
MICROSOFT_CLIENT_ID=your_microsoft_client_id
MICROSOFT_CLIENT_SECRET=your_microsoft_client_secret
MICROSOFT_TENANT_ID=common
MICROSOFT_REDIRECT_URI=${FRONTEND_URL}/oauth/callback?provider=microsoft

# ThaiD OAuth
THAID_CLIENT_ID=your_thaid_client_id
THAID_CLIENT_SECRET=your_thaid_client_secret
THAID_REDIRECT_URI=${FRONTEND_URL}/oauth/callback?provider=thaid
THAID_ENVIRONMENT=production

#--------------------------------------------------------------------
# WEBSOCKET
#--------------------------------------------------------------------
WEBSOCKET_HOST=localhost
WEBSOCKET_PORT=8080

#--------------------------------------------------------------------
# FILE STORAGE
#--------------------------------------------------------------------
UPLOAD_MAX_SIZE=10485760
UPLOAD_PATH=uploads
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,pdf,doc,docx
```

### 4.2 Yii2 Configuration

Edit `common/config/main-local.php`:

```php
<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DB_DSN'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => 'redis',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => getenv('REDIS_HOST'),
            'port' => getenv('REDIS_PORT'),
            'database' => 0,
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
            'transport' => [
                'scheme' => 'smtp',
                'host' => getenv('SMTP_HOST'),
                'port' => getenv('SMTP_PORT'),
                'encryption' => getenv('SMTP_ENCRYPTION'),
                'username' => getenv('SMTP_USERNAME'),
                'password' => getenv('SMTP_PASSWORD'),
            ],
        ],
    ],
];
```

---

## 5. Database Setup

### 5.1 Create Database and User

```bash
sudo mysql -u root
```

```sql
-- Create database
CREATE DATABASE meeting_room_booking 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'meeting_user'@'localhost' 
IDENTIFIED BY 'your_secure_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON meeting_room_booking.* 
TO 'meeting_user'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

### 5.2 Run Migrations

```bash
cd /var/www/meeting-room-booking

# Run all migrations
php yii migrate --interactive=0

# Seed demo data (optional, for testing)
php yii seeder/all
```

### 5.3 Create Admin User

```bash
# Using console command
php yii user/create-admin admin admin@bizco.co.th AdminPassword123!
```

Or via SQL:

```sql
INSERT INTO users (username, email, password_hash, role, status, created_at, updated_at)
VALUES (
    'admin',
    'admin@bizco.co.th',
    '$2y$13$...',  -- Use: php -r "echo password_hash('YourPassword', PASSWORD_DEFAULT);"
    'superadmin',
    10,
    UNIX_TIMESTAMP(),
    UNIX_TIMESTAMP()
);
```

---

## 6. Web Server Configuration

### 6.1 Nginx Configuration

Create `/etc/nginx/sites-available/meeting-room-booking`:

```nginx
# Upstream for PHP-FPM
upstream php-fpm {
    server unix:/var/run/php/php8.2-fpm.sock;
}

# HTTP redirect to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name meeting.bizco.co.th;
    return 301 https://$server_name$request_uri;
}

# Main HTTPS server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name meeting.bizco.co.th;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/meeting.bizco.co.th/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/meeting.bizco.co.th/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;

    # Document roots
    set $base_path /var/www/meeting-room-booking;
    root $base_path/frontend/web;
    index index.php;

    # Logging
    access_log /var/log/nginx/meeting-room-booking-access.log;
    error_log /var/log/nginx/meeting-room-booking-error.log;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    # Frontend
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    # Backend (admin panel)
    location /admin {
        alias $base_path/backend/web;
        try_files $uri $uri/ /admin/index.php$is_args$args;

        location ~ ^/admin/(.+\.php)$ {
            alias $base_path/backend/web/$1;
            fastcgi_pass php-fpm;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    # API
    location /api {
        alias $base_path/api/web;
        try_files $uri $uri/ /api/index.php$is_args$args;

        location ~ ^/api/(.+\.php)$ {
            alias $base_path/api/web/$1;
            fastcgi_pass php-fpm;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            include fastcgi_params;
        }
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass php-fpm;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(ht|git|env) {
        deny all;
    }

    # WebSocket proxy
    location /ws {
        proxy_pass http://localhost:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_read_timeout 86400;
    }
}
```

### 6.2 Enable Site

```bash
sudo ln -s /etc/nginx/sites-available/meeting-room-booking /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6.3 PHP-FPM Configuration

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data

pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500

php_admin_value[memory_limit] = 256M
php_admin_value[upload_max_filesize] = 20M
php_admin_value[post_max_size] = 25M
php_admin_value[max_execution_time] = 300
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

---

## 7. SSL/TLS Setup

### 7.1 Install Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
```

### 7.2 Obtain Certificate

```bash
sudo certbot --nginx -d meeting.bizco.co.th
```

### 7.3 Auto-renewal

```bash
# Test renewal
sudo certbot renew --dry-run

# Crontab entry (automatically added)
# 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 8. Production Optimizations

### 8.1 Enable OPcache

Edit `/etc/php/8.2/fpm/conf.d/10-opcache.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.fast_shutdown=1
```

### 8.2 Optimize Composer Autoloader

```bash
composer dump-autoload --optimize --classmap-authoritative
```

### 8.3 Setup Supervisor for Background Jobs

Create `/etc/supervisor/conf.d/meeting-room-booking.conf`:

```ini
[program:meeting-room-queue]
command=php /var/www/meeting-room-booking/yii queue/listen --verbose
process_name=%(program_name)s_%(process_num)02d
numprocs=2
directory=/var/www/meeting-room-booking
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/supervisor/meeting-queue.log
stderr_logfile=/var/log/supervisor/meeting-queue-error.log

[program:meeting-room-websocket]
command=php /var/www/meeting-room-booking/yii websocket/start
process_name=%(program_name)s
numprocs=1
directory=/var/www/meeting-room-booking
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/supervisor/meeting-websocket.log
stderr_logfile=/var/log/supervisor/meeting-websocket-error.log

[program:meeting-room-scheduler]
command=/bin/bash -c "while true; do php /var/www/meeting-room-booking/yii schedule/run; sleep 60; done"
process_name=%(program_name)s
numprocs=1
directory=/var/www/meeting-room-booking
autostart=true
autorestart=true
user=www-data
stdout_logfile=/var/log/supervisor/meeting-scheduler.log
stderr_logfile=/var/log/supervisor/meeting-scheduler-error.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 8.4 Setup Cron Jobs

```bash
sudo crontab -e -u www-data
```

Add:

```cron
# Send booking reminders
0 7 * * * php /var/www/meeting-room-booking/yii booking/send-reminders

# Auto-complete past bookings
0 * * * * php /var/www/meeting-room-booking/yii booking/auto-complete

# Auto-cancel expired pending bookings
30 * * * * php /var/www/meeting-room-booking/yii booking/auto-cancel

# Cleanup old files
0 3 * * 0 php /var/www/meeting-room-booking/yii cleanup/old-files

# Daily report
0 8 * * 1-5 php /var/www/meeting-room-booking/yii report/daily
```

---

## 9. Monitoring & Logging

### 9.1 Application Logs

Log locations:
- Frontend: `/var/www/meeting-room-booking/frontend/runtime/logs/`
- Backend: `/var/www/meeting-room-booking/backend/runtime/logs/`
- Console: `/var/www/meeting-room-booking/console/runtime/logs/`

### 9.2 Setup Log Rotation

Create `/etc/logrotate.d/meeting-room-booking`:

```
/var/www/meeting-room-booking/*/runtime/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    create 644 www-data www-data
}
```

### 9.3 Health Check Endpoint

Access: `https://meeting.bizco.co.th/api/health`

Response:
```json
{
  "status": "ok",
  "database": "connected",
  "cache": "connected",
  "timestamp": "2024-12-26T10:00:00+07:00"
}
```

---

## 10. Backup & Recovery

### 10.1 Database Backup Script

Create `/opt/scripts/backup-database.sh`:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/meeting-room-booking"
DB_NAME="meeting_room_booking"
DB_USER="meeting_user"
DB_PASS="your_password"

mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup uploads
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/www/meeting-room-booking/frontend/web/uploads

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
chmod +x /opt/scripts/backup-database.sh
```

### 10.2 Scheduled Backups

```bash
sudo crontab -e
```

Add:
```cron
# Daily backup at 2 AM
0 2 * * * /opt/scripts/backup-database.sh >> /var/log/backup.log 2>&1
```

### 10.3 Restore from Backup

```bash
# Restore database
gunzip < /var/backups/meeting-room-booking/db_YYYYMMDD_HHMMSS.sql.gz | mysql -u meeting_user -p meeting_room_booking

# Restore uploads
tar -xzf /var/backups/meeting-room-booking/uploads_YYYYMMDD_HHMMSS.tar.gz -C /
```

---

## 11. Troubleshooting

### Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| 500 Internal Server Error | Permission issues | `chmod -R 777 runtime web/assets` |
| Database connection error | Wrong credentials | Check `.env` DB settings |
| Assets not loading | Nginx config | Check alias paths in nginx config |
| Email not sending | SMTP config | Verify SMTP credentials and port |
| WebSocket not connecting | Firewall | Open port 8080 |

### Debug Mode

Enable temporarily for debugging:

```bash
# Edit .env
APP_DEBUG=true

# Check logs
tail -f /var/www/meeting-room-booking/frontend/runtime/logs/app.log
```

### Check Services

```bash
# Nginx
sudo systemctl status nginx

# PHP-FPM
sudo systemctl status php8.2-fpm

# MySQL
sudo systemctl status mysql

# Redis
sudo systemctl status redis

# Supervisor
sudo supervisorctl status
```

### Clear Cache

```bash
cd /var/www/meeting-room-booking

# Clear Yii cache
php yii cache/flush-all

# Clear Redis
redis-cli FLUSHALL

# Clear compiled assets
rm -rf frontend/web/assets/*
rm -rf backend/web/assets/*
```

---

## Support

For technical support:
- 📧 Email: dtai@bizco.co.th
- 📞 Phone: 02-XXX-XXXX

---

*Document Version: 1.0*  
*Last Updated: December 2024*
