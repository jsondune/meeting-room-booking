# Dashboard & System Fix - แก้ไขหลายปัญหา

## 🐛 ปัญหาที่แก้ไข

### 1. Change Password Error
**Error:** `Class "backend\models\ChangePasswordForm" not found`
**แก้ไข:** สร้างไฟล์ `ChangePasswordForm.php` ใน backend/models/

### 2. Profile Department Error
**Error:** `Unknown Property - Getting unknown property: common\models\Department::name`
**แก้ไข:** เปลี่ยน `department->name` เป็น `department->name_th` ในทุกไฟล์

### 3. Dashboard ไม่แสดงข้อมูล
**ปัญหา:** 
- "รอการอนุมัติ" ไม่แสดงรายการ
- "การจองล่าสุด" ไม่แสดงรายการ  
- "ตารางการจองวันนี้" แสดง "กำลังโหลดข้อมูล..." ตลอด

**แก้ไข:** 
- ปรับ Controller ให้ส่งข้อมูลถูกต้อง
- ปรับ View ให้ใช้ Object properties แทน Array

### 4. Logo ขนาดเล็ก/ไม่ลงตัว
**แก้ไข:** ปรับ SVG Logo ใหม่ให้ขนาดพอเหมาะและมีภาษาไทย

### 5. Frontend Room List - รูปภาพซ้อน
**ปัญหา:** Badge room code ซ้อนทับกับรูปภาพ broken
**แก้ไข:** 
- เพิ่ม onerror handler แสดง placeholder สวยงามเมื่อรูปไม่โหลด
- ปรับ CSS ให้ badge อยู่ตำแหน่งถูกต้อง

### 6. Frontend Profile Connections Error
**Error:** `Undefined variable $connections`
**แก้ไข:** Controller ส่ง `oauthConnections` แต่ View ใช้ `connections` - แก้ให้ตรงกัน

### 7. Frontend Change Password - 404 Not Found
**ปัญหา:** ไม่มี action และ view สำหรับ change-password ใน frontend
**แก้ไข:** 
- สร้าง `ChangePasswordForm.php` ใน frontend/models/
- เพิ่ม `actionChangePassword()` ใน frontend/controllers/SiteController.php
- สร้าง view `change-password.php` ใน frontend/views/site/

### 8. Booking Create - ไม่แสดง Thumbnail ห้องประชุม
**ปัญหา:** หน้าจองห้องไม่แสดงรูป thumbnail และไม่มีลิงก์ดูรายละเอียด
**แก้ไข:** 
- เพิ่ม thumbnail รูปห้องประชุม พร้อม placeholder สวยงาม
- เพิ่มปุ่ม "ดูรายละเอียด" เปิดหน้าใหม่ดูข้อมูลห้อง
- ปรับ CSS ให้ card สวยงามขึ้น

### 9. Frontend Signup - Class SignupForm not found
**ปัญหา:** Autoloader ไม่พบ SignupForm class
**แก้ไข:** 
- ตรวจสอบว่าไฟล์ `common/models/SignupForm.php` มีอยู่
- รัน `composer dump-autoload` ที่ root ของโปรเจค

### 10. Frontend Signup - ขนาดฟอร์มไม่เหมาะสม
**ปัญหา:** หน้าลงทะเบียนแคบเกินไปเมื่อขยายหน้าจอ 100%
**แก้ไข:** 
- เพิ่ม CSS class `auth-card-wide` สำหรับหน้า signup
- ปรับ max-width เป็น 700px สำหรับหน้าจอใหญ่
- จัดฟอร์มเป็น 2 columns
- ปรับ OAuth buttons เป็นแถวเดียวแนวนอน

### 11. OAuth Routes - 404 Not Found
**ปัญหา:** `/auth/azure`, `/auth/google`, `/auth/thaid` แสดง 404
**แก้ไข:** 
- สร้าง `AuthController.php` ใหม่
- Redirect ไปยัง `OauthController` ที่มีอยู่แล้ว
- ตรวจสอบว่า OAuth configured หรือยังก่อน redirect
- แสดง warning message ถ้า OAuth ยังไม่พร้อมใช้งาน

### 12. Frontend Logo ขนาดเล็ก
**ปัญหา:** Logo ใน navbar ขนาดเล็กเกินไป (45px)
**แก้ไข:** เพิ่มขนาดเป็น 55px

### 13. การแสดงวันที่ภาษาไทย
**ปัญหา:** บางที่แสดงปี ค.ศ. แทนที่จะเป็น พ.ศ.
**แก้ไข:** 
- ThaiFormatter.php รองรับ format ต่างๆ แล้ว
- JavaScript fallback ใช้ `th-TH-u-ca-buddhist` calendar
- thai-date.js มี helper functions พร้อมใช้
- แก้ไข ICU pattern `'d MMM yyyy'` ให้ทำงานถูกต้อง

### 14. Booking Create - Unknown property cancellation_reason
**ปัญหา:** Database ไม่มี column `cancellation_reason`
**แก้ไข:** รัน SQL script เพื่อเพิ่ม column ที่หายไป

---

## 📁 ไฟล์ที่ต้อง Copy

### Backend Models
| ไฟล์ | วางที่ |
|------|--------|
| `ChangePasswordForm.php` | `backend/models/ChangePasswordForm.php` |

### Common Models
| ไฟล์ | วางที่ |
|------|--------|
| `common_SignupForm.php` | `common/models/SignupForm.php` |
| `common_ThaiFormatter.php` | `common/components/ThaiFormatter.php` |

### Backend Controllers
| ไฟล์ | วางที่ |
|------|--------|
| `backend_SiteController.php` | `backend/controllers/SiteController.php` |
| `BookingController.php` | `backend/controllers/BookingController.php` |

### Backend Views - Site
| ไฟล์ | วางที่ |
|------|--------|
| `dashboard.php` | `backend/views/site/dashboard.php` |
| `profile.php` | `backend/views/site/profile.php` |
| `change-password.php` | `backend/views/site/change-password.php` |

### Backend Views - Approval
| ไฟล์ | วางที่ |
|------|--------|
| `approval_pending.php` | `backend/views/approval/pending.php` |
| `approval_view.php` | `backend/views/approval/view.php` |
| `reassign.php` | `backend/views/approval/reassign.php` |

### Backend Views - Booking
| ไฟล์ | วางที่ |
|------|--------|
| `backend_calendar.php` | `backend/views/booking/calendar.php` |

### Backend Layout & Logo
| ไฟล์ | วางที่ |
|------|--------|
| `backend_main.php` | `backend/views/layouts/main.php` |
| `backend_logo.svg` | `backend/web/images/logo.svg` |

### Frontend
| ไฟล์ | วางที่ |
|------|--------|
| `frontend_SiteController.php` | `frontend/controllers/SiteController.php` |
| `frontend_ProfileController.php` | `frontend/controllers/ProfileController.php` |
| `frontend_AuthController.php` | `frontend/controllers/AuthController.php` |
| `frontend_ChangePasswordForm.php` | `frontend/models/ChangePasswordForm.php` |
| `frontend_auth_layout.php` | `frontend/views/layouts/auth.php` |
| `frontend_signup.php` | `frontend/views/site/signup.php` |
| `frontend_calendar.php` | `frontend/views/site/calendar.php` |
| `frontend_change-password.php` | `frontend/views/site/change-password.php` |
| `frontend_booking_create.php` | `frontend/views/booking/create.php` |
| `frontend_main.php` | `frontend/views/layouts/main.php` |
| `frontend_logo.svg` | `frontend/web/images/logo.svg` |
| `frontend_room_index.php` | `frontend/views/room/index.php` |
| `frontend_thai-date.js` | `frontend/web/js/thai-date.js` |

### เอกสาร Workflow
| ไฟล์ | รายละเอียด |
|------|-----------|
| `REGISTRATION_WORKFLOW.md` | เอกสาร workflow การลงทะเบียนระบบ |

### Database
| ไฟล์ | รายละเอียด |
|------|-----------|
| `add_missing_columns.sql` | SQL เพิ่ม column ที่หายไป (cancellation_reason, cancelled_by, cancelled_at) |

---

## ⚠️ สร้างโฟลเดอร์ก่อน Copy

```bash
mkdir -p backend/web/images
mkdir -p frontend/web/images
mkdir -p frontend/web/js
mkdir -p frontend/models
```

---

## 🧪 ทดสอบ

1. **Change Password:** `http://backend.mrb.test/site/change-password`
2. **Profile:** `http://backend.mrb.test/site/profile`
3. **Dashboard:** `http://backend.mrb.test/` - ดูว่าทุก section แสดงข้อมูลถูกต้อง
4. **Calendar:** `http://backend.mrb.test/booking/calendar` - ดูวันหยุด
5. **Frontend Calendar:** `http://frontend.mrb.test/site/calendar`
6. **Frontend Rooms:** `http://frontend.mrb.test/rooms` - ดูว่ารูปไม่ซ้อนแล้ว
7. **Frontend Connections:** `http://frontend.mrb.test/profile/connections` - ไม่มี error
8. **Frontend Change Password:** `http://frontend.mrb.test/site/change-password` - หน้าเปลี่ยนรหัสผ่าน
9. **Frontend Booking Create:** `http://frontend.mrb.test/booking/create` - ดู thumbnail และปุ่มดูรายละเอียด
10. **Frontend Signup:** `http://frontend.mrb.test/site/signup` - หน้าลงทะเบียน

---

## ⚠️ หลัง Copy ไฟล์แล้ว

### 1. รัน SQL เพิ่ม Column (ถ้าเกิด error "Unknown property")
```bash
mysql -u root -p your_database < add_missing_columns.sql
```

หรือรัน SQL นี้ใน phpMyAdmin:
```sql
ALTER TABLE booking ADD COLUMN cancellation_reason TEXT NULL;
ALTER TABLE booking ADD COLUMN cancelled_by INT(11) NULL;
ALTER TABLE booking ADD COLUMN cancelled_at DATETIME NULL;
```

### 2. รัน Composer dump-autoload
```bash
composer dump-autoload
```

---

## 🎨 Logo ใหม่

### Backend (Sidebar)
```
┌─────────────────────────┐
│  📅✓  MeetingRoom       │
│       ระบบจองห้องประชุม  │
└─────────────────────────┘
```

### Frontend (Navbar)
```
┌─────────────────────────────────┐
│  📅✓  ระบบจองห้องประชุม         │
│       Meeting Room Booking      │
└─────────────────────────────────┘
```

---

## 📅 การใช้งานวันที่ภาษาไทย (พ.ศ.)

### ใน PHP (Server-side)
```php
// ใช้ Yii::$app->formatter (ThaiFormatter)
echo Yii::$app->formatter->asDate($date, 'long');    // 26 มกราคม พ.ศ. 2569
echo Yii::$app->formatter->asDate($date, 'medium');  // 26 ม.ค. 2569
echo Yii::$app->formatter->asDate($date, 'short');   // 26/1/69
echo Yii::$app->formatter->asDate($date, 'full');    // วันอาทิตย์ ที่ 26 มกราคม พ.ศ. 2569
echo Yii::$app->formatter->asDatetime($date, 'long'); // 26 มกราคม พ.ศ. 2569 14:30 น.
```

### ใน JavaScript (Client-side)
```javascript
// ใช้ ThaiDate helper (frontend/web/js/thai-date.js)
ThaiDate.format('2026-01-26', 'long');    // 26 มกราคม 2569
ThaiDate.format('2026-01-26', 'medium');  // 26 ม.ค. 2569
ThaiDate.format('2026-01-26', 'full');    // วันอาทิตย์ที่ 26 มกราคม พ.ศ. 2569
ThaiDate.formatDatetime('2026-01-26 14:30', 'long'); // 26 มกราคม 2569 14:30 น.
ThaiDate.today('long');                   // วันที่ปัจจุบัน
ThaiDate.currentYear();                   // 2569
```

### ใน HTML (Auto-format)
```html
<!-- จะแปลงอัตโนมัติเมื่อโหลดหน้า -->
<span data-thai-date="2026-01-26" data-format="long"></span>
<span data-thai-datetime="2026-01-26 14:30" data-format="medium"></span>
```
