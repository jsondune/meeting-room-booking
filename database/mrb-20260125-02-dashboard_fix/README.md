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

---

## 📁 ไฟล์ที่ต้อง Copy

### Backend Models
| ไฟล์ | วางที่ |
|------|--------|
| `ChangePasswordForm.php` | `backend/models/ChangePasswordForm.php` |

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
| `frontend_calendar.php` | `frontend/views/site/calendar.php` |
| `frontend_main.php` | `frontend/views/layouts/main.php` |
| `frontend_logo.svg` | `frontend/web/images/logo.svg` |
| `frontend_room_index.php` | `frontend/views/room/index.php` |

---

## ⚠️ สร้างโฟลเดอร์ก่อน Copy

```bash
mkdir -p backend/web/images
mkdir -p frontend/web/images
```

---

## 🧪 ทดสอบ

1. **Change Password:** `http://backend.mrb.test/site/change-password`
2. **Profile:** `http://backend.mrb.test/site/profile`
3. **Dashboard:** `http://backend.mrb.test/` - ดูว่าทุก section แสดงข้อมูลถูกต้อง
4. **Calendar:** `http://backend.mrb.test/booking/calendar` - ดูวันหยุด
5. **Frontend Calendar:** `http://frontend.mrb.test/site/calendar`
6. **Frontend Rooms:** `http://frontend.mrb.test/rooms` - ดูว่ารูปไม่ซ้อนแล้ว

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
