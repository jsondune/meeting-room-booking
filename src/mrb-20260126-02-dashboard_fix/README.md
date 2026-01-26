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

### เอกสาร Workflow
| ไฟล์ | รายละเอียด |
|------|-----------|
| `REGISTRATION_WORKFLOW.md` | เอกสาร workflow การลงทะเบียนระบบ |

---

## ⚠️ สร้างโฟลเดอร์ก่อน Copy

```bash
mkdir -p backend/web/images
mkdir -p frontend/web/images
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

รันคำสั่งนี้ที่ root ของโปรเจค:
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
