# Room Image Upload Feature

## ความสามารถ
1. ✅ อัปโหลดรูปภาพได้สูงสุด 5 ไฟล์
2. ✅ แก้ไข/ลบรูปภาพที่มีอยู่ได้
3. ✅ อัปโหลดเพิ่มเติมได้ (ถ้ายังไม่ครบ 5 รูป)
4. ✅ ลบรูปเดิมก่อนอัปเดต
5. ✅ ตั้งรูปหลัก (Primary) ใช้แสดงเป็น Thumbnail

## ไฟล์ที่ต้อง Copy

| ไฟล์ | วางที่ |
|------|--------|
| `RoomController.php` | `backend/controllers/RoomController.php` |
| `_form.php` | `backend/views/room/_form.php` |
| `MeetingRoom.php` | `common/models/MeetingRoom.php` |
| `RoomImage.php` | `common/models/RoomImage.php` |

## สร้าง Folder สำหรับ Upload

```
backend/web/uploads/rooms/
```

## ฟังก์ชันหลัก

### 1. อัปโหลดรูปภาพ (Max 5 รูป)
- รองรับ JPG, PNG, GIF, WEBP
- ขนาดไม่เกิน 2MB/รูป
- Preview ก่อนอัปโหลด

### 2. จัดการรูปภาพที่มีอยู่
- ลบรูปภาพ (กดปุ่มถังขยะ)
- ตั้งรูปหลัก (คลิกที่รูป หรือเลือก radio button)

### 3. รูปหลัก (Primary Image)
- ใช้แสดงเป็น Thumbnail ในรายการห้องประชุม
- มีป้าย "รูปหลัก" แสดงชัดเจน
- เปลี่ยนได้โดยคลิกที่รูปอื่น

## UI Features

```
┌─────────────────────────────────────────┐
│ 📷 รูปภาพห้องประชุม           [3/5 รูป] │
├─────────────────────────────────────────┤
│ รูปภาพปัจจุบัน (คลิกเพื่อตั้งเป็นรูปหลัก)│
│                                         │
│ ┌─────────┐ ┌─────────┐ ┌─────────┐    │
│ │ ⭐หลัก  │ │         │ │         │    │
│ │  [🗑️]   │ │  [🗑️]   │ │  [🗑️]   │    │
│ │  รูป 1  │ │  รูป 2  │ │  รูป 3  │    │
│ │ ○ รูปหลัก│ │ ○ รูปหลัก│ │ ○ รูปหลัก│    │
│ └─────────┘ └─────────┘ └─────────┘    │
│                                         │
│ อัปโหลดรูปภาพเพิ่มเติม (เหลืออีก 2 รูป) │
│ [Choose Files]                          │
│ รองรับ JPG, PNG, GIF, WEBP ≤ 2MB        │
└─────────────────────────────────────────┘
```

## Database Table: room_image

```sql
CREATE TABLE `room_image` (
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
  KEY `idx_room_image_room_id` (`room_id`),
  CONSTRAINT `fk_room_image_room` FOREIGN KEY (`room_id`) 
    REFERENCES `meeting_room` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
