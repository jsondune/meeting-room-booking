# Thai Date Format Update - พ.ศ. (Buddhist Era)

## การปรับปรุง
- แปลงการแสดงผลวันที่ทั้งระบบเป็น พ.ศ. (Buddhist Era)
- รองรับ FullCalendar, Date Picker, และ Combo Box
- รองรับทั้ง PHP และ JavaScript

## ไฟล์ที่ต้อง Copy

| ไฟล์ใน ZIP | วางที่ |
|------------|--------|
| `thai-date.js` | `backend/web/js/thai-date.js` |
| `thai-date.js` | `frontend/web/js/thai-date.js` (copy เหมือนกัน) |
| `ThaiFormatter.php` | `common/components/ThaiFormatter.php` |
| `backend_calendar.php` | `backend/views/booking/calendar.php` |
| `backend_booking_form.php` | `backend/views/booking/_form.php` |
| `backend_main_layout.php` | `backend/views/layouts/main.php` |
| `frontend_main_layout.php` | `frontend/views/layouts/main.php` |
| `frontend_booking_create.php` | `frontend/views/booking/create.php` |

## การใช้งาน

### PHP (Yii2 Formatter)
```php
// ใน config/main.php (มีอยู่แล้ว)
'formatter' => [
    'class' => \common\components\ThaiFormatter::class,
],

// ใช้งาน
Yii::$app->formatter->asDate($date, 'long');     // 20 มกราคม พ.ศ. 2568
Yii::$app->formatter->asDate($date, 'medium');   // 20 ม.ค. 2568
Yii::$app->formatter->asDate($date, 'short');    // 20/1/68
Yii::$app->formatter->asDate($date, 'full');     // วันจันทร์ที่ 20 มกราคม พ.ศ. 2568
Yii::$app->formatter->asDatetime($datetime);     // 20 ม.ค. 2568 14:30 น.
```

### JavaScript
```javascript
// Basic formatting
ThaiDate.format('2025-01-20', 'long');      // 20 มกราคม 2568
ThaiDate.format('2025-01-20', 'medium');    // 20 ม.ค. 2568
ThaiDate.format('2025-01-20', 'short');     // 20/1/68
ThaiDate.format('2025-01-20', 'full');      // วันจันทร์ที่ 20 มกราคม พ.ศ. 2568

// Datetime formatting
ThaiDate.formatDatetime('2025-01-20 14:30:00', 'medium'); // 20 ม.ค. 2568 14:30 น.

// Year conversion
ThaiDate.toBuddhistYear(2025);   // 2568
ThaiDate.toChristianYear(2568);  // 2025

// Get current year
ThaiDate.currentYear();          // 2568

// Get today
ThaiDate.today('long');          // 20 มกราคม 2568
```

### HTML Data Attributes (Auto-format)
```html
<!-- วันที่จะถูก format อัตโนมัติเมื่อ DOM ready -->
<span data-thai-date="2025-01-20">2025-01-20</span>
<span data-thai-date="2025-01-20" data-format="long">2025-01-20</span>
<span data-thai-datetime="2025-01-20 14:30:00">2025-01-20 14:30:00</span>
```

### FullCalendar Thai Locale
```javascript
// Calendar title จะแสดง "มกราคม 2568" แทน "January 2025"
// ปฏิทินจะแสดงชื่อวันและเดือนเป็นภาษาไทย
```

## Thai Month Names
| เลข | ชื่อเต็ม | ชื่อย่อ |
|-----|---------|--------|
| 1 | มกราคม | ม.ค. |
| 2 | กุมภาพันธ์ | ก.พ. |
| 3 | มีนาคม | มี.ค. |
| 4 | เมษายน | เม.ย. |
| 5 | พฤษภาคม | พ.ค. |
| 6 | มิถุนายน | มิ.ย. |
| 7 | กรกฎาคม | ก.ค. |
| 8 | สิงหาคม | ส.ค. |
| 9 | กันยายน | ก.ย. |
| 10 | ตุลาคม | ต.ค. |
| 11 | พฤศจิกายน | พ.ย. |
| 12 | ธันวาคม | ธ.ค. |

## Thai Day Names
| เลข | ชื่อเต็ม | ชื่อย่อ |
|-----|---------|--------|
| 0 | อาทิตย์ | อา. |
| 1 | จันทร์ | จ. |
| 2 | อังคาร | อ. |
| 3 | พุธ | พ. |
| 4 | พฤหัสบดี | พฤ. |
| 5 | ศุกร์ | ศ. |
| 6 | เสาร์ | ส. |
