# Meeting Room Booking System

ระบบจองห้องประชุมออนไลน์สำหรับองค์กร พัฒนาด้วย Yii2 Advanced Template

![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![Yii2](https://img.shields.io/badge/Yii2-2.0.50-blue?style=flat)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

## 📋 ภาพรวมระบบ

ระบบจองห้องประชุมที่ครบครันสำหรับองค์กร รองรับการจอง การอนุมัติ การแจ้งเตือน และการรายงาน

### ✨ คุณสมบัติหลัก

- 📅 **การจองห้องประชุม** - จองได้ง่ายผ่าน Calendar View
- ✅ **ระบบอนุมัติ** - Workflow การอนุมัติตามลำดับขั้น
- 🔔 **การแจ้งเตือน** - Email, Push Notification, WebSocket Real-time
- 📊 **รายงาน** - สถิติการใช้งาน, รายงานรายเดือน, Export Excel/PDF
- 👥 **การจัดการผู้ใช้** - RBAC, OAuth2 (Google, Microsoft, ThaiD)
- 📱 **Responsive Design** - รองรับทุกอุปกรณ์
- 🔗 **Calendar Sync** - ซิงค์กับ Google Calendar และ Outlook
- 🌐 **RESTful API** - สำหรับ Integration กับระบบอื่น

## 🏗 สถาปัตยกรรม

```
meeting-room-booking/
├── api/                    # RESTful API Module
│   ├── config/            # API configurations
│   ├── controllers/       # API endpoints
│   └── web/              # Entry point
├── backend/               # Admin Panel
│   ├── config/           # Backend configurations  
│   ├── controllers/      # Admin controllers
│   ├── models/           # Form models
│   └── views/            # Admin views
├── common/                # Shared Components
│   ├── components/       # Services (OAuth, Push, Calendar)
│   ├── config/           # Common configurations
│   ├── mail/             # Email templates
│   └── models/           # ActiveRecord models
├── console/               # CLI Commands
│   ├── components/       # Console services (WebSocket)
│   ├── controllers/      # Console commands
│   └── migrations/       # Database migrations
├── deploy/                # Deployment Configs
│   ├── mysql/            # MySQL configurations
│   ├── nginx.conf        # Nginx configuration
│   ├── php/              # PHP-FPM configurations
│   └── redis/            # Redis configuration
├── docs/                  # Documentation
│   ├── api/              # OpenAPI specification
│   ├── DEPLOYMENT_GUIDE.md
│   └── USER_MANUAL_TH.md
├── frontend/              # User Frontend
│   ├── config/           # Frontend configurations
│   ├── controllers/      # Frontend controllers
│   ├── views/            # Frontend views
│   └── web/              # Static assets (JS, CSS)
└── tests/                 # Test Suites
    ├── functional/       # Functional tests
    └── unit/             # Unit tests
```

## 🚀 Quick Start

### ด้วย Docker (แนะนำ)

```bash
# Clone repository
git clone https://github.com/your-org/meeting-room-booking.git
cd meeting-room-booking

# Copy environment file
cp .env.example .env

# Edit .env with your settings
nano .env

# Start services
docker-compose up -d

# Run migrations
docker-compose exec php yii migrate --interactive=0

# Create admin user
docker-compose exec php yii user/create-admin admin@example.com password123
```

เข้าใช้งาน:
- **Frontend**: http://localhost
- **Backend**: http://localhost/admin
- **API**: http://localhost/api

### Manual Installation

```bash
# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
# Edit .env file

# Run migrations
php yii migrate

# Create admin user
php yii user/create-admin admin@example.com password123

# Start development server
php yii serve
```

## ⚙️ Configuration

### Environment Variables

```bash
# Application
APP_ENV=production
APP_DEBUG=false

# Database
DB_DSN=mysql:host=localhost;dbname=meeting_room
DB_USERNAME=root
DB_PASSWORD=secret

# JWT for API
JWT_SECRET=your-256-bit-secret
JWT_ISSUER=meeting-room-api
JWT_TTL=3600

# OAuth Providers
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
MICROSOFT_CLIENT_ID=your-microsoft-client-id
MICROSOFT_CLIENT_SECRET=your-microsoft-client-secret
THAID_CLIENT_ID=your-thaid-client-id
THAID_CLIENT_SECRET=your-thaid-client-secret

# Push Notifications
FCM_ENABLED=true
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS=/path/to/service-account.json

# Email
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# WebSocket
WEBSOCKET_PORT=8080
WEBSOCKET_HOST=0.0.0.0
```

## 📖 API Documentation

API เอกสารอยู่ที่ `/docs/api/openapi.yaml`

### Authentication

```bash
# Login
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password123"
}

# Response
{
    "success": true,
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "refresh_token": "...",
    "expires_in": 3600
}
```

### Endpoints หลัก

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login` | เข้าสู่ระบบ |
| POST | `/api/auth/refresh` | Refresh token |
| GET | `/api/rooms` | รายการห้องประชุม |
| GET | `/api/rooms/{id}` | ข้อมูลห้องประชุม |
| GET | `/api/rooms/{id}/availability` | ตรวจสอบว่าง |
| POST | `/api/bookings` | สร้างการจอง |
| GET | `/api/bookings` | รายการจองของตัวเอง |
| PUT | `/api/bookings/{id}` | แก้ไขการจอง |
| DELETE | `/api/bookings/{id}` | ยกเลิกการจอง |
| POST | `/api/bookings/{id}/approve` | อนุมัติการจอง |
| POST | `/api/bookings/{id}/reject` | ปฏิเสธการจอง |

## 🔔 Real-time Notifications

### WebSocket Connection

```javascript
const ws = new MeetingRoomWS({
    url: 'ws://localhost:8080',
    authKey: 'your-auth-key',
    userId: 123
});

ws.connect();
ws.subscribe('user:123');
ws.subscribe('room:1');

ws.on('booking_update', (data) => {
    console.log('Booking updated:', data);
});
```

### Push Notifications

ระบบรองรับ Firebase Cloud Messaging (FCM) และ OneSignal

```javascript
// Initialize push notifications
const pushManager = new PushNotificationManager({
    firebaseConfig: FIREBASE_CONFIG,
    vapidKey: FIREBASE_VAPID_KEY
});

await pushManager.init();
```

## 📊 Reports

ระบบรายงานรองรับ:
- **รายงานการใช้งาน** - สถิติการใช้งานห้องประชุม
- **รายงานรายเดือน** - สรุปการจองรายเดือน
- **รายงานแผนก** - การใช้งานแยกตามแผนก
- **รายงานอุปกรณ์** - การใช้งานอุปกรณ์
- **Export** - Excel, PDF, CSV

## 🧪 Testing

```bash
# Run all tests
vendor/bin/codecept run

# Run unit tests
vendor/bin/codecept run unit

# Run functional tests
vendor/bin/codecept run functional

# Run specific test
vendor/bin/codecept run functional BookingWorkflowCest
```

## 🔒 Security

- RBAC (Role-Based Access Control)
- CSRF Protection
- XSS Prevention
- SQL Injection Prevention
- JWT Authentication สำหรับ API
- OAuth2 สำหรับ Social Login
- Rate Limiting

### Roles

| Role | Permissions |
|------|-------------|
| user | จองห้อง, ดูของตัวเอง |
| approver | อนุมัติ/ปฏิเสธการจอง |
| admin | จัดการห้อง, ผู้ใช้, รายงาน |
| super_admin | ทุกอย่าง + ตั้งค่าระบบ |

## 📱 Mobile Support

- Responsive Design ด้วย Bootstrap 5
- PWA Ready (Service Worker)
- Push Notifications บน Mobile
- Touch-friendly Calendar

## 🔧 Console Commands

```bash
# Send booking reminders
php yii notification/send-reminders

# Auto-complete bookings
php yii booking/auto-complete

# Auto-cancel no-show
php yii booking/auto-cancel

# Cleanup old data
php yii maintenance/cleanup

# WebSocket server
php yii websocket/start

# Queue worker
php yii queue/listen
```

## 📝 License

MIT License - ดู [LICENSE](LICENSE) สำหรับรายละเอียด

## 👥 Contributors

- **BIzAI**

## 📞 Support

- **Documentation**: [docs/](docs/)
- **Issues**: GitHub Issues
- **Email**: support@bizco.co.th

---

พัฒนาโดย ❤️ BIzAI
