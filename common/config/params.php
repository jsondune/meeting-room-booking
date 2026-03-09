<?php
/**
 * Common Application Parameters
 * 
 * This file contains shared configuration parameters used across
 * frontend, backend, and console applications.
 * 
 * Location: common/config/params.php
 * 
 * Usage: Yii::$app->params['paramName']
 */

return [
    // ============================================================
    // Admin & Support Contact
    // ============================================================
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Meeting Room Booking System',
    
    // ============================================================
    // Application Settings
    // ============================================================
    'appName' => 'ระบบจองห้องประชุม',
    'appNameEn' => 'Meeting Room Booking System',
    'appVersion' => '1.0.0',
    'organization' => 'สถาบันพระบรมราชชนก',
    'organizationEn' => 'Praboromarajchanok Institute',
    
    // ============================================================
    // Calendar & Booking Settings
    // ============================================================
    'calendar' => [
        // จำนวนวันที่แสดงย้อนหลัง (Past days to display)
        'pastDays' => 30,
        
        // จำนวนวันที่แสดงล่วงหน้า (Future days to display)
        'futureDays' => 180,
        
        // เวลาเริ่มต้นของตารางปฏิทิน (Calendar start time)
        'slotMinTime' => '07:00:00',
        
        // เวลาสิ้นสุดของตารางปฏิทิน (Calendar end time)
        'slotMaxTime' => '20:00:00',
        
        // ช่วงเวลาแต่ละ slot (Slot duration in minutes)
        'slotDuration' => 30,
        
        // แสดงวันเสาร์-อาทิตย์ (Show weekends)
        'showWeekends' => true,
        
        // Default view: timeGridWeek, dayGridMonth, timeGridDay
        'defaultView' => 'timeGridWeek',
    ],
    
    // ============================================================
    // Booking Rules
    // ============================================================
    'booking' => [
        // จำนวนวันที่จองล่วงหน้าได้สูงสุด (Maximum advance booking days)
        'maxAdvanceDays' => 180,
        
        // จำนวนวันที่จองล่วงหน้าได้ต่ำสุด (Minimum advance booking days)
        'minAdvanceDays' => 0,
        
        // ระยะเวลาจองขั้นต่ำ (นาที)
        'minDuration' => 30,
        
        // ระยะเวลาจองสูงสุด (นาที)
        'maxDuration' => 480,
        
        // จำนวนวันสูงสุดสำหรับ Date Range Booking
        'maxDateRangeDays' => 30,
        
        // อนุญาตให้จองข้ามวัน (Allow overnight booking)
        'allowOvernight' => false,
        
        // ต้องอนุมัติก่อนใช้งาน (Require approval)
        'requireApproval' => true,
        
        // ส่ง email แจ้งเตือนเมื่อจอง
        'sendConfirmationEmail' => true,
        
        // ส่ง email แจ้งเตือนก่อนถึงเวลาประชุม (ชั่วโมง)
        'reminderHoursBefore' => 24,
    ],
    
    // ============================================================
    // Upload Settings
    // ============================================================
    'upload' => [
        // ขนาดไฟล์สูงสุด (bytes) - 2MB
        'maxFileSize' => 2 * 1024 * 1024,
        
        // นามสกุลไฟล์ที่อนุญาต (รูปภาพ)
        'allowedImageExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        
        // นามสกุลไฟล์ที่อนุญาต (เอกสาร)
        'allowedDocumentExtensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
        
        // ขนาด Avatar (pixels)
        'avatarSize' => 200,
    ],
    
    // ============================================================
    // Pagination Settings
    // ============================================================
    'pagination' => [
        'defaultPageSize' => 20,
        'maxPageSize' => 100,
    ],
    
    // ============================================================
    // Security Settings
    // ============================================================
    'security' => [
        // ความยาวรหัสผ่านขั้นต่ำ
        'minPasswordLength' => 8,
        
        // ต้องมีตัวพิมพ์ใหญ่
        'requireUppercase' => true,
        
        // ต้องมีตัวพิมพ์เล็ก
        'requireLowercase' => true,
        
        // ต้องมีตัวเลข
        'requireDigit' => true,
        
        // Session timeout (seconds) - 30 minutes
        'sessionTimeout' => 1800,
        
        // จำนวนครั้งที่ login ผิดก่อนล็อค
        'maxLoginAttempts' => 5,
        
        // ระยะเวลาล็อค (seconds) - 15 minutes
        'lockoutDuration' => 900,
    ],
    
    // ============================================================
    // User Registration
    // ============================================================
    'user' => [
        'passwordResetTokenExpire' => 3600,
        'emailVerificationTokenExpire' => 86400,
    ],
    
    // ============================================================
    // Date/Time Format (Thai)
    // ============================================================
    'dateFormat' => [
        'php' => 'Y-m-d',
        'phpDateTime' => 'Y-m-d H:i:s',
        'phpTime' => 'H:i',
        'display' => 'd/m/Y',
        'displayDateTime' => 'd/m/Y H:i',
        'displayTime' => 'H:i น.',
    ],
];
