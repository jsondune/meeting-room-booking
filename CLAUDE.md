# Thai Date Format Update - พ.ศ. (Buddhist Era)

## การปรับปรุง
- แปลงการแสดงผลวันที่ทั้งระบบเป็น พ.ศ. (Buddhist Era)
- รองรับ FullCalendar, Date Picker, และ Combo Box
- รองรับทั้ง PHP และ JavaScript

🎨 Features
┌─────────────────────────────────────────────────────────┐
│  ปฏิทินการจองห้องประชุม                    [ดูห้อง] [จอง] │
├───────────────────┬─────────────────────────────────────┤
│ กรองห้องประชุม    │                                       │
│ ○ ทุกห้อง (8)     │  < มกราคม 2569 >  [วันนี้][เดือน]...     │
│ ● VIP            │ ┌──────────────────────────────────┐ │
│ ○ กลาง A         │ │ อา.  จ.   อ.  พ.  พฤ.  ศ.   ส.  │ │
│ ○ กลาง B         │ │                                  │ │
│ ○ เล็ก 1          │ │      ████ ████                   │ │
│                  │ │     ประชุม  VIP                   │ │
├──────────────────┤ │                                  │ │
│ สถานะการจอง      │ │                  ████            │ │
│ ● อนุมัติแล้ว       │ │                 กลาง B           │ │
│ ● รออนุมัติ        │ └──────────────────────────────────┘ │
│ ● เสร็จสิ้น        │                                       │
├─────────────────┤                                       │
│ สถิติ             │                                      │
│ การจองทั้งหมด: 24 │                                      │
│ รออนุมัติ: 3       │                                      │
│ อนุมัติแล้ว: 21     │                                      │
└─────────────────┴──────────────────────────────────────┘


# 📋 Workflow การลงทะเบียนระบบจองห้องประชุม

## 🔄 Registration Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         REGISTRATION WORKFLOW                                │
└─────────────────────────────────────────────────────────────────────────────┘

                              ┌──────────────┐
                              │  หน้าแรก     │
                              │  (Homepage)  │
                              └──────┬───────┘
                                     │
                                     ▼
                         ┌───────────────────────┐
                         │   คลิก "ลงทะเบียน"    │
                         └───────────┬───────────┘
                                     │
                                     ▼
                    ┌────────────────────────────────┐
                    │      หน้าลงทะเบียน             │
                    │    (Signup Page)               │
                    └────────────────┬───────────────┘
                                     │
              ┌──────────────────────┼──────────────────────┐
              │                      │                      │
              ▼                      ▼                      ▼
    ┌─────────────────┐   ┌─────────────────┐   ┌─────────────────┐
    │  OAuth Login    │   │  OAuth Login    │   │  กรอกฟอร์ม      │
    │  Microsoft 365  │   │  Google         │   │  ลงทะเบียน      │
    │  (Azure AD)     │   │                 │   │  (Manual)       │
    └────────┬────────┘   └────────┬────────┘   └────────┬────────┘
             │                     │                      │
             └─────────────────────┼──────────────────────┘
                                   │
                                   ▼
                    ┌────────────────────────────────┐
                    │     Validation                 │
                    │  - ตรวจสอบข้อมูล               │
                    │  - ตรวจสอบ email ซ้ำ          │
                    │  - ตรวจสอบ username ซ้ำ       │
                    └────────────────┬───────────────┘
                                     │
                         ┌───────────┴───────────┐
                         │                       │
                    ผ่าน ▼                       ▼ ไม่ผ่าน
              ┌──────────────────┐     ┌──────────────────┐
              │  สร้างบัญชี      │     │  แสดง Error     │
              │  (Create User)   │     │  Message        │
              └────────┬─────────┘     └──────────────────┘
                       │
                       ▼
              ┌──────────────────┐
              │  ส่ง Email       │
              │  ยืนยันตัวตน    │
              │  (Verification)  │
              └────────┬─────────┘
                       │
                       ▼
              ┌──────────────────┐
              │  หน้าแจ้งเตือน   │
              │  "โปรดตรวจสอบ  │
              │   อีเมลของคุณ"   │
              └────────┬─────────┘
                       │
         ┌─────────────┴─────────────┐
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│  ผู้ใช้คลิก     │         │  ผู้ใช้ไม่คลิก  │
│  ลิงก์ใน Email  │         │  (หมดอายุ)      │
└────────┬────────┘         └────────┬────────┘
         │                           │
         ▼                           ▼
┌─────────────────┐         ┌─────────────────┐
│  ยืนยันสำเร็จ   │         │  ต้องขอ         │
│  บัญชีเปิดใช้งาน│         │  Verification   │
│                 │         │  ใหม่           │
└────────┬────────┘         └─────────────────┘
         │
         ▼
┌──────────────────────────────────────────────┐
│              เข้าสู่ระบบได้                  │
│         (Ready to Login & Use)               │
└──────────────────────────────────────────────┘
```

---

## 📝 รายละเอียด Workflow

### Step 1: เข้าหน้าลงทะเบียน
**URL:** `/site/signup`

**ช่องทางการลงทะเบียน:**
1. **OAuth (Single Sign-On)**
   - Microsoft 365 (Azure AD) - สำหรับองค์กร
   - Google Account
   - ThaID (บัตรประชาชนดิจิทัล)
   
2. **ลงทะเบียนแบบกรอกฟอร์ม**
   - กรอกข้อมูลด้วยตนเอง

---

### Step 2: กรอกข้อมูล (Manual Registration)

**ข้อมูลที่ต้องกรอก:**

| ฟิลด์ | ประเภท | บังคับ | Validation |
|-------|--------|--------|------------|
| ชื่อ | Text | ✅ | สูงสุด 100 ตัวอักษร |
| นามสกุล | Text | ✅ | สูงสุด 100 ตัวอักษร |
| ชื่อผู้ใช้ | Text | ✅ | 3-50 ตัว, a-z, 0-9, _ เท่านั้น, ห้ามซ้ำ |
| อีเมล | Email | ✅ | รูปแบบ email ถูกต้อง, ห้ามซ้ำ |
| เบอร์โทรศัพท์ | Phone | ❌ | รูปแบบ 0-9, -, +, space |
| หน่วยงาน | Select | ❌ | เลือกจาก dropdown |
| รหัสผ่าน | Password | ✅ | อย่างน้อย 8 ตัวอักษร |
| ยืนยันรหัสผ่าน | Password | ✅ | ต้องตรงกับรหัสผ่าน |
| ยอมรับเงื่อนไข | Checkbox | ✅ | ต้องติ๊ก |

---

### Step 3: Validation

**Server-side Validation:**
```php
// ตรวจสอบ username ซ้ำ
['username', 'unique', 'targetClass' => User::class]

// ตรวจสอบ email ซ้ำ
['email', 'unique', 'targetClass' => User::class]

// ตรวจสอบรหัสผ่านตรงกัน
['password_confirm', 'compare', 'compareAttribute' => 'password']
```

**Error Messages (ภาษาไทย):**
- "ชื่อผู้ใช้นี้ถูกใช้งานแล้ว"
- "อีเมลนี้ถูกใช้งานแล้ว"
- "รหัสผ่านไม่ตรงกัน"
- "รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร"

---

### Step 4: สร้างบัญชีผู้ใช้

**Process:**
1. สร้าง record ใน `user` table
2. Set `status = INACTIVE` (รอยืนยัน email)
3. Set `role = USER` (ผู้ใช้ทั่วไป)
4. Generate `auth_key` สำหรับ remember login
5. Generate `verification_token` สำหรับยืนยัน email

**Database Insert:**
```sql
INSERT INTO user (
    username, email, password_hash, 
    full_name, phone, department_id,
    status, role, auth_key, verification_token,
    created_at, updated_at
) VALUES (...)
```

---

### Step 5: ส่ง Email ยืนยันตัวตน

**Email Template:** `emailVerify-html.php`

**เนื้อหา Email:**
```
เรียน [ชื่อผู้ใช้]

ขอบคุณที่ลงทะเบียนใช้งานระบบจองห้องประชุม

โปรดคลิกลิงก์ด้านล่างเพื่อยืนยันอีเมลของคุณ:
[ลิงก์ยืนยัน]

ลิงก์นี้จะหมดอายุภายใน 24 ชั่วโมง

หากคุณไม่ได้ลงทะเบียน โปรดเพิกเฉยอีเมลนี้
```

**Verification URL:**
```
/site/verify-email?token={verification_token}
```

---

### Step 6: ยืนยัน Email

**Process:**
1. ผู้ใช้คลิกลิงก์ใน email
2. ระบบตรวจสอบ token
3. ถ้า token ถูกต้องและยังไม่หมดอายุ:
   - Set `status = ACTIVE`
   - ลบ `verification_token`
4. Redirect ไปหน้า Login พร้อม message สำเร็จ

**Success Message:**
"ยืนยันอีเมลเรียบร้อยแล้ว คุณสามารถเข้าสู่ระบบได้"

---

## 🔐 OAuth Registration Flow

### Microsoft 365 (Azure AD)
```
User → Click "Microsoft" → Azure AD Login Page → 
Authorize → Callback with token → 
Create/Link User → Auto-verify → Ready to use
```

### Google
```
User → Click "Google" → Google Login Page → 
Consent → Callback with profile → 
Create/Link User → Auto-verify → Ready to use
```

### ThaID
```
User → Click "ThaID" → ThaID App/QR → 
Verify with PIN → Callback with citizen data → 
Create/Link User → Auto-verify → Ready to use
```

**OAuth ข้อดี:**
- ไม่ต้องจำรหัสผ่าน
- ยืนยันตัวตนอัตโนมัติ
- เชื่อมต่อกับปฏิทินได้

---

## 📊 User Status Flow

```
┌─────────────┐    ยืนยัน Email    ┌─────────────┐
│   INACTIVE  │ ─────────────────► │   ACTIVE    │
│  (สถานะ 9)  │                    │  (สถานะ 10) │
└─────────────┘                    └──────┬──────┘
                                          │
                                   Admin ระงับ
                                          │
                                          ▼
                                   ┌─────────────┐
                                   │  SUSPENDED  │
                                   │  (สถานะ 8)  │
                                   └─────────────┘
```

---

## 🛡️ Security Considerations

1. **Password Security**
   - Hash ด้วย bcrypt (cost 13)
   - ไม่เก็บ plain text

2. **Token Security**
   - Verification token หมดอายุใน 24 ชม.
   - Token เป็น random string 32 ตัว

3. **Rate Limiting**
   - จำกัดการลงทะเบียนจาก IP เดียวกัน
   - ป้องกัน bot registration

4. **Input Validation**
   - Server-side validation เสมอ
   - XSS protection
   - SQL injection protection (Prepared statements)

---

## 📱 Mobile Responsive

หน้าลงทะเบียนรองรับการใช้งานบน:
- Desktop (≥992px) - แสดงแบบ 2 columns
- Tablet (768-991px) - แสดงแบบ 2 columns
- Mobile (<768px) - แสดงแบบ 1 column

---

## 🔗 Related URLs

| หน้า | URL | Description |
|------|-----|-------------|
| ลงทะเบียน | `/site/signup` | หน้าสมัครสมาชิก |
| เข้าสู่ระบบ | `/site/login` | หน้า login |
| ยืนยัน email | `/site/verify-email?token=xxx` | ยืนยันอีเมล |
| ลืมรหัสผ่าน | `/site/request-password-reset` | ขอ reset password |
| OAuth Microsoft | `/auth/azure` | Login ด้วย Microsoft |
| OAuth Google | `/auth/google` | Login ด้วย Google |
| OAuth ThaID | `/auth/thaid` | Login ด้วย ThaID |


