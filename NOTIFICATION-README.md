# Notification & User Dropdown Fix (v2)

## ปัญหาที่แก้ไข
- Bootstrap JS ไม่โหลด ทำให้ Dropdown ไม่ทำงาน
- เพิ่ม Bootstrap 5 CSS และ JS จาก CDN

## ไฟล์ที่ต้อง Copy

| ไฟล์ | วางที่ |
|------|--------|
| `main.php` | `backend/views/layouts/main.php` |
| `NotificationController.php` | `backend/controllers/NotificationController.php` |
| `notification_index.php` | `backend/views/notification/index.php` |
| `AppAsset.php` | `backend/assets/AppAsset.php` |

## สร้าง Folder

```
backend/views/notification/
backend/assets/
```

## สิ่งที่เพิ่มใน main.php

### 1. Bootstrap CSS (ใน <head>)
```html
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
```

### 2. Bootstrap JS (ก่อน </body>)
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
```

## ตรวจสอบว่าทำงานได้

หลังจาก copy ไฟล์แล้ว:
1. Clear browser cache (Ctrl+Shift+R)
2. คลิกที่ 🔔 (notification) - ควรแสดง dropdown
3. คลิกที่ชื่อ user - ควรแสดง dropdown menu

## UI Features

### Notification Dropdown
- โหลด notification ผ่าน AJAX
- แสดง badge จำนวนที่ยังไม่อ่าน
- กด "อ่านทั้งหมด" ได้

### User Dropdown
- แสดงชื่อและ email
- เมนู: โปรไฟล์, เปลี่ยนรหัสผ่าน, ออกจากระบบ
