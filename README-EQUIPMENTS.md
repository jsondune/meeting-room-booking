# Equipment Image Upload Fix

## ไฟล์ที่ต้อง Copy

| ไฟล์ใน ZIP | วางที่ |
|------------|--------|
| `Equipment.php` | `common/models/Equipment.php` |
| `EquipmentController.php` | `backend/controllers/EquipmentController.php` |
| `_form.php` | `backend/views/equipment/_form.php` |
| `index.php` | `backend/views/equipment/index.php` |
| `view.php` | `backend/views/equipment/view.php` |

## สร้าง Folder สำหรับ Upload

สร้าง folder นี้ด้วยตนเอง (หรือจะสร้างอัตโนมัติเมื่อ upload ครั้งแรก):
```
backend/web/uploads/equipment/
```

## ความสามารถที่เพิ่มมา

### 1. Upload รูปภาพ
- รองรับไฟล์ PNG, JPG, JPEG, GIF, WEBP
- ขนาดไม่เกิน 2MB
- Validation ทั้ง client-side และ server-side

### 2. Preview รูปภาพ
- แสดงรูปภาพปัจจุบัน (ถ้ามี)
- แสดง Preview รูปภาพใหม่ก่อน save

### 3. ลบรูปภาพ
- กดปุ่มถังขยะเพื่อลบรูปภาพ
- ลบได้ทั้งก่อนและหลัง save

### 4. เปลี่ยนรูปภาพ
- เลือกไฟล์ใหม่จะแทนที่รูปเดิมอัตโนมัติ
- ไฟล์เดิมจะถูกลบออกจาก server

### 5. แสดง Thumbnail
- หน้า Index แสดง thumbnail 60x45px
- หน้า View แสดงรูปใหญ่ 250px

## Methods ใหม่ใน Equipment Model

```php
// Upload image
$model->uploadImage($file);

// Delete image
$model->deleteImage();

// Get image URL
$model->imageUrl;

// Check if has image
$model->hasImage();
```

## โครงสร้าง Folder

```
backend/
├── web/
│   └── uploads/
│       └── equipment/
│           └── eq_timestamp_random.jpg (uploaded files)
```
