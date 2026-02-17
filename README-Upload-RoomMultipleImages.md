🖼️ Room Image Upload Feature
ดาวน์โหลดและ Extract:
ไฟล์: room_image_feature.zip

📁 วิธี Copy ไฟล์
ไฟล์ใน ZIP               วางที่
RoomController.php      backend/controllers/RoomController.php
_form.php               backend/views/room/_form.php
MeetingRoom.php         common/models/MeetingRoom.php
RoomImage.php           common/models/RoomImage.php

📂 สร้าง Folder
backend/web/uploads/rooms/
🗄️ ตรวจสอบ Database
รัน SQL ใน room_image.sql ถ้ายังไม่มี table room_image

✅ ความสามารถ
Feature                 รายละเอียด
อัปโหลดสูงสุด 5 รูป        (JPG, PNG, GIF, WEBP ≤ 2MB)
Preview                 แสดงตัวอย่างก่อนอัปโหลด 
ลบรูป                    คลิกปุ่มถังขยะ ลบเมื่อกด Save
รูปหลัก                   คลิกที่รูปเพื่อตั้งเป็น Primary 
Thumbnail               รูปหลักแสดงในรายการห้อง

🎨 UI Preview
┌─────────────────────────────────────────┐
│ 📷 รูปภาพห้องประชุม           [3/5 รูป] │
├─────────────────────────────────────────┤
│ ┌─────────┐ ┌─────────┐ ┌─────────┐    │
│ │⭐รูปหลัก│ │         │ │          │    │
│ │  [🗑️]   │ │  [🗑️]   │ │  [🗑️]  │    │
│ │  รูป 1   │ │  รูป 2  │ │  รูป 3    │    │
│ │ ◉ รูปหลัก│ │ ○ รูปหลัก│ │ ○ รูปหลัก │    │
│ └─────────┘ └─────────┘ └─────────┘    │
│                                         │
│ อัปโหลดเพิ่มเติม (เหลืออีก 2 รูป)              │
│ [Choose Files]                          │
└─────────────────────────────────────────┘

หลังจาก copy ไฟล์แล้ว ลองเข้า http://backend.mrb.test/room/5/update อีกครั้ง