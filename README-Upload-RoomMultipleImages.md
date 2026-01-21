🖼️ Room Image Upload Feature
ดาวน์โหลดและ Extract:
ไฟล์: room_image_feature.zip

📁 วิธี Copy ไฟล์
ไฟล์ใน ZIPวางที่RoomController.phpbackend/controllers/RoomController.php_form.phpbackend/views/room/_form.phpMeetingRoom.phpcommon/models/MeetingRoom.phpRoomImage.phpcommon/models/RoomImage.php

📂 สร้าง Folder
backend/web/uploads/rooms/
🗄️ ตรวจสอบ Database
รัน SQL ใน room_image.sql ถ้ายังไม่มี table room_image

✅ ความสามารถ
Featureรายละเอียดอัปโหลดสูงสุด 5 รูป (JPG, PNG, GIF, WEBP ≤ 2MB)Previewแสดงตัวอย่างก่อนอัปโหลดลบรูปคลิกปุ่มถังขยะ ลบเมื่อกด Saveรูปหลักคลิกที่รูปเพื่อตั้งเป็น PrimaryThumbnailรูปหลักแสดงในรายการห้อง

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