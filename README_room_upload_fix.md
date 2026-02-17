# Room Image Upload Fix

## ปัญหาที่พบ
- `@webroot` alias ไม่ถูกต้องเมื่อเรียกจาก common model
- ไฟล์ไม่ถูก save และ record ไม่ถูกสร้างใน database

## การแก้ไข
เปลี่ยนจาก `@webroot` เป็น `@backend/web` ใน uploadImages() method

## ไฟล์ที่ต้อง Copy

| ไฟล์ | วางที่ |
|------|--------|
| `MeetingRoom.php` | `common/models/MeetingRoom.php` |
| `RoomImage.php` | `common/models/RoomImage.php` |

## สร้าง Folder

**สำคัญมาก!** ต้องสร้าง folder นี้ก่อน:

```
backend/web/uploads/rooms/
```

### วิธีสร้าง (Windows Command Prompt):
```cmd
cd C:\xampp\htdocs\mrbapp
mkdir backend\web\uploads\rooms
```

### หรือสร้างด้วย File Explorer:
1. ไปที่ `C:\xampp\htdocs\mrbapp\backend\web\`
2. สร้าง folder `uploads`
3. ภายใน uploads สร้าง folder `rooms`

## ตรวจสอบ Permission (Windows)
ปกติ Windows ไม่มีปัญหา permission แต่ถ้ายังไม่ได้:
- คลิกขวาที่ folder `uploads`
- Properties → Security
- ให้ IUSR และ IIS_IUSRS มีสิทธิ์ Write

## ทดสอบ
1. ไปที่ http://backend.mrb.test/room/5/update
2. เลือกรูปภาพ
3. กด Save
4. ตรวจสอบ:
   - folder `backend/web/uploads/rooms/5/` มีไฟล์รูปภาพ
   - table `room_image` มี record ใหม่
