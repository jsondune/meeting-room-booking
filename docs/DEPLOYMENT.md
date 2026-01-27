# Meeting Room Booking System - Deployment Guide

## ระบบจองห้องประชุม BiZCO
### คู่มือการติดตั้งและ Deploy ระบบ

---

## สารบัญ

1. [ความต้องการของระบบ](#1-ความต้องการของระบบ)
2. [การติดตั้งแบบ Docker](#2-การติดตั้งแบบ-docker)
3. [การติดตั้งแบบ Manual](#3-การติดตั้งแบบ-manual)
4. [การตั้งค่า Environment](#4-การตั้งค่า-environment)
5. [การตั้งค่า Database](#5-การตั้งค่า-database)
6. [การตั้งค่า Web Server](#6-การตั้งค่า-web-server)
7. [การตั้งค่า SSL/TLS](#7-การตั้งค่า-ssltls)
8. [การ Seed ข้อมูลทดสอบ](#8-การ-seed-ข้อมูลทดสอบ)
9. [การ Monitor และ Logging](#9-การ-monitor-และ-logging)
10. [การ Backup และ Recovery](#10-การ-backup-และ-recovery)
11. [การแก้ไขปัญหา](#11-การแก้ไขปัญหา)

---

## 1. ความต้องการของระบบ

### Hardware Requirements (Minimum)
- CPU: 2 cores
- RAM: 4 GB
- Storage: 20 GB SSD
- Network: 100 Mbps

### Hardware Requirements (Recommended for 500+ users)
- CPU: 4 cores
- RAM: 8 GB
- Storage: 50 GB SSD
- Network: 1 Gbps

### Software Requirements
- **OS**: Ubuntu 22.04 LTS / 24.04 LTS
- **PHP**: 8.2+
- **MySQL**: 8.0+
- **Redis**: 7.0+
- **Nginx**: 1.24+
- **Composer**: 2.6+
- **Node.js**: 18+ (for asset building)

---

## 2. การติดตั้งแบบ Docker

### 2.1 ติดตั้ง Docker และ Docker Compose

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Add user to docker group
sudo usermod -aG docker $USER

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Verify installation
docker --version
docker compose version
```

### 2.2 Clone และตั้งค่า Project

```bash
# Clone repository
git clone https://github.com/jsondune/MRB-System.git mrbapp
cd mrbapp

# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env
```

### 2.3 Build และ Start Services

```bash
# Build images
docker compose build

# Start all services
docker compose up -d

# View logs
docker compose logs -f

# Check status
docker compose ps
```

### 2.4 Initialize Database

```bash
# Run migrations
docker compose exec php-fpm php yii migrate --interactive=0

# Seed demo data (optional)
docker compose exec php-fpm php yii seed/all

# Create admin user
docker compose exec php-fpm php yii user/create-admin
```

### 2.5 Development Mode

```bash
# Start with development services (phpMyAdmin, Mailhog, Redis Commander)
docker compose --profile dev up -d

# Access:
# - phpMyAdmin: http://localhost:8080
# - Mailhog: http://localhost:8025
# - Redis Commander: http://localhost:8081
```

---

## 3. การติดตั้งแบบ Manual

### 3.1 ติดตั้ง Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2 php8.2-fpm php8.2-cli php8.2-mysql php8.2-redis \
    php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip \
    php8.2-intl php8.2-bcmath php8.2-opcache php8.2-imagick -y

# Install MySQL 8.0
sudo apt install mysql-server -y

# Install Redis
sudo apt install redis-server -y

# Install Nginx
sudo apt install nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Supervisor
sudo apt install supervisor -y
```

### 3.2 Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p << EOF
CREATE DATABASE meeting_room_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'booking_app'@'localhost' IDENTIFIED BY 'your-secure-password';
GRANT ALL PRIVILEGES ON meeting_room_booking.* TO 'booking_app'@'localhost';
FLUSH PRIVILEGES;
EOF
```

### 3.3 Configure Redis

```bash
# Edit Redis configuration
sudo nano /etc/redis/redis.conf

# Set:
# bind 127.0.0.1
# maxmemory 256mb
# maxmemory-policy allkeys-lru
# appendonly yes

# Restart Redis
sudo systemctl restart redis-server
sudo systemctl enable redis-server
```

### 3.4 Deploy Application

```bash
# Create web directory
sudo mkdir -p /var/www/booking
sudo chown -R $USER:www-data /var/www/booking

# Clone repository
cd /var/www/booking
git clone https://github.com/jsondune/MRB-System.git .

# Install dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
sudo chown -R www-data:www-data /var/www/booking
sudo chmod -R 775 /var/www/booking/frontend/runtime
sudo chmod -R 775 /var/www/booking/frontend/web/assets
sudo chmod -R 775 /var/www/booking/backend/runtime
sudo chmod -R 775 /var/www/booking/backend/web/assets
sudo chmod -R 775 /var/www/booking/api/runtime

# Copy and configure environment
cp .env.example .env
nano .env

# Run migrations
php yii migrate --interactive=0

# Initialize RBAC
php yii rbac/init
```

### 3.5 Configure Nginx

```bash
# Copy Nginx configuration
sudo cp deploy/nginx.conf /etc/nginx/sites-available/booking
sudo ln -s /etc/nginx/sites-available/booking /etc/nginx/sites-enabled/

# Edit domain names
sudo nano /etc/nginx/sites-available/booking

# Test and reload
sudo nginx -t
sudo systemctl reload nginx
```

### 3.6 Configure Supervisor

```bash
# Copy supervisor configuration
sudo cp deploy/supervisor.conf /etc/supervisor/conf.d/booking.conf

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start booking:*
```

---

## 4. การตั้งค่า Environment

### 4.1 Essential Settings

```ini
# Application
APP_ENV=production
YII_DEBUG=false
APP_NAME="ระบบจองห้องประชุม"

# URLs (change to your domains)
FRONTEND_URL=https://booking.bizco.co.th
BACKEND_URL=https://admin.booking.bizco.co.th
API_URL=https://api.booking.bizco.co.th

# Security (generate unique keys)
COOKIE_VALIDATION_KEY=generate-unique-32-char-string
JWT_SECRET=generate-unique-32-char-string
ENCRYPTION_KEY=generate-unique-32-char-string
```

### 4.2 Generate Secure Keys

```bash
# Generate random key
php -r "echo base64_encode(random_bytes(32));"

# Or using openssl
openssl rand -base64 32
```

### 4.3 Database Settings

```ini
DB_HOST=localhost
DB_PORT=3306
DB_NAME=meeting_room_booking
DB_USER=booking_app
DB_PASS=your-secure-password
```

### 4.4 Email Settings (SMTP)

```ini
MAIL_TRANSPORT=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_EMAIL=noreply@example.com
MAIL_FROM_NAME="ระบบจองห้องประชุม"
```

---

## 5. การตั้งค่า Database

### 5.1 Run Migrations

```bash
# Apply all migrations
php yii migrate --interactive=0

# Check migration status
php yii migrate/history

# Rollback if needed
php yii migrate/down 3
```

### 5.2 Database Optimization

```sql
-- Add indexes for performance
ALTER TABLE booking ADD INDEX idx_status_date (status, start_time);
ALTER TABLE notification ADD INDEX idx_user_read (user_id, is_read);

-- Analyze tables
ANALYZE TABLE booking, room, user, notification;

-- Optimize tables
OPTIMIZE TABLE booking, room, user, notification;
```

---

## 6. การตั้งค่า Web Server

### 6.1 Nginx Configuration

Key settings in `/etc/nginx/sites-available/booking`:

```nginx
# Rate limiting
limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;

# PHP-FPM upstream
upstream php-fpm {
    server unix:/var/run/php/php8.2-fpm.sock;
}

# Frontend server
server {
    listen 80;
    server_name booking.example.com;
    root /var/www/booking/frontend/web;
    index index.php;
    
    # ... rest of configuration
}
```

### 6.2 PHP-FPM Optimization

Edit `/etc/php/8.2/fpm/pool.d/www.conf`:

```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 1000
```

---

## 7. การตั้งค่า SSL/TLS

### 7.1 Using Let's Encrypt (Certbot)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificates
sudo certbot --nginx -d booking.example.com -d admin.booking.example.com -d api.booking.example.com

# Auto-renewal (already set up by certbot)
sudo certbot renew --dry-run
```

### 7.2 Manual SSL Configuration

```nginx
server {
    listen 443 ssl http2;
    server_name booking.example.com;
    
    ssl_certificate /etc/letsencrypt/live/booking.example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/booking.example.com/privkey.pem;
    
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000" always;
}
```

---

## 8. การ Seed ข้อมูลทดสอบ

### 8.1 Seed All Demo Data

```bash
# Seed all tables
php yii seed/all

# Or seed specific tables
php yii seed/departments
php yii seed/users
php yii seed/rooms
php yii seed/equipment
php yii seed/bookings
php yii seed/holidays
```

### 8.2 Default Login Credentials

| Role | Username | Password |
|------|----------|----------|
| Super Admin | superadmin | SuperAdmin@123 |
| Admin | admin | Admin@123 |
| Approver | approver | Approver@123 |
| User | user1 | User@123 |

**⚠️ สำคัญ: เปลี่ยนรหัสผ่านทันทีหลังติดตั้ง!**

### 8.3 Reset Database (Development Only)

```bash
# Warning: This will DELETE all data!
php yii seed/reset --force
```

---

## 9. การ Monitor และ Logging

### 9.1 Application Logs

```bash
# View application logs
tail -f /var/www/booking/frontend/runtime/logs/app.log
tail -f /var/www/booking/backend/runtime/logs/app.log

# View Nginx logs
tail -f /var/log/nginx/booking-access.log
tail -f /var/log/nginx/booking-error.log

# View PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

### 9.2 Supervisor Status

```bash
# Check all processes
sudo supervisorctl status

# Check specific process
sudo supervisorctl status booking:booking-email-worker

# Restart workers
sudo supervisorctl restart booking:*
```

### 9.3 Health Check Endpoints

```bash
# Frontend health
curl https://booking.example.com/health

# API health
curl https://api.booking.example.com/health

# PHP-FPM status
curl http://localhost/fpm-status
```

---

## 10. การ Backup และ Recovery

### 10.1 Database Backup

```bash
#!/bin/bash
# backup-db.sh

BACKUP_DIR="/backup/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="meeting_room_booking"

mkdir -p $BACKUP_DIR
mysqldump -u root -p$DB_PASSWORD --single-transaction --routines --triggers \
    $DB_NAME > "$BACKUP_DIR/${DB_NAME}_${DATE}.sql"

# Compress
gzip "$BACKUP_DIR/${DB_NAME}_${DATE}.sql"

# Keep only last 30 days
find $BACKUP_DIR -name "*.gz" -mtime +30 -delete
```

### 10.2 Full Backup Script

```bash
#!/bin/bash
# full-backup.sh

BACKUP_DIR="/backup/booking"
DATE=$(date +%Y%m%d)

# Database
mysqldump meeting_room_booking | gzip > "$BACKUP_DIR/db_${DATE}.sql.gz"

# Uploaded files
tar -czf "$BACKUP_DIR/uploads_${DATE}.tar.gz" /var/www/booking/frontend/web/uploads

# Configuration
tar -czf "$BACKUP_DIR/config_${DATE}.tar.gz" /var/www/booking/.env
```

### 10.3 Recovery

```bash
# Restore database
gunzip < backup.sql.gz | mysql meeting_room_booking

# Restore uploads
tar -xzf uploads_backup.tar.gz -C /var/www/booking/frontend/web/

# Clear cache after restore
php yii cache/flush-all
```

---

## 11. การแก้ไขปัญหา

### 11.1 Common Issues

**Permission Denied**
```bash
sudo chown -R www-data:www-data /var/www/booking
sudo chmod -R 775 /var/www/booking/*/runtime
sudo chmod -R 775 /var/www/booking/*/web/assets
```

**Redis Connection Failed**
```bash
# Check Redis status
sudo systemctl status redis-server

# Test connection
redis-cli ping
```

**Database Connection Failed**
```bash
# Test MySQL connection
mysql -u booking_app -p meeting_room_booking -e "SELECT 1"

# Check MySQL status
sudo systemctl status mysql
```

**Emails Not Sending**
```bash
# Check queue worker
sudo supervisorctl status booking:booking-email-worker

# View email logs
tail -f /var/www/booking/common/runtime/logs/email.log

# Test SMTP connection
php yii email/test admin@example.com
```

### 11.2 Performance Issues

```bash
# Check slow queries
tail -f /var/log/mysql/slow.log

# Check OPcache status
php -r "var_dump(opcache_get_status());"

# Clear caches
php yii cache/flush-all
redis-cli FLUSHDB

# Rebuild assets
php yii asset/compress assets.php assets-prod.php
```

### 11.3 Debug Mode (Development Only)

```bash
# Enable debug temporarily
export YII_DEBUG=true
export YII_ENV=dev

# View detailed errors
tail -f /var/www/booking/frontend/runtime/logs/app.log
```

---

## Support

หากพบปัญหาหรือต้องการความช่วยเหลือ โปรดติดต่อ:

- **Email**: dtai@bizco.co.th
- **Phone**: 02-590-1000
- **GitHub Issues**: https://github.com/jsondune/Meeting-Room-Booking/issues

---

*Document Version: 1.0*
*Last Updated: December 2026*
*BIzAI*
