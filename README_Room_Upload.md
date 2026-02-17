# Room Image Upload - Final Version (v2)

## ✅ ความสามารถ

1. อัปโหลดรูปภาพได้สูงสุด **5 ไฟล์** ต่อห้อง
2. รองรับ JPG, PNG, GIF, WEBP ขนาดไม่เกิน 2MB/รูป
3. แก้ไข/ลบรูปภาพที่มีอยู่ได้
4. ตั้งรูปหลัก (Primary) ใช้แสดงเป็น Thumbnail
5. Preview รูปก่อนอัปโหลด
6. **ลบรูปหลักแล้วตั้งรูปแรกที่เหลือเป็นรูปหลักอัตโนมัติ**

## 📁 ไฟล์ที่ต้อง Copy

| ไฟล์ | วางที่ |
|------|--------|
| `MeetingRoom.php` | `common/models/MeetingRoom.php` |
| `RoomImage.php` | `common/models/RoomImage.php` |
| `RoomController.php` | `backend/controllers/RoomController.php` |
| `_form.php` | `backend/views/room/_form.php` |
| `view.php` | `backend/views/room/view.php` |

## 🔧 การแก้ไขใน v2

1. **Auto-set Primary** - หลังลบรูปหลัก จะตั้งรูปแรกที่เหลือเป็นรูปหลักอัตโนมัติ
2. **Filter Debug Messages** - ไม่แสดง debug messages ในหน้าเว็บ
3. **Flash Messages in View** - แสดง flash messages ในหน้า view ด้วย

## 📂 Folder Structure

```
backend/web/uploads/rooms/
├── 5/
│   ├── abc123.jpg
│   └── def456.png
├── 6/
│   ├── ghi789.jpg
│   └── ...
```

## 🗄️ Database Table

```sql
CREATE TABLE IF NOT EXISTS `room_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `image_width` int(11) DEFAULT NULL,
  `image_height` int(11) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_room_image_room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
