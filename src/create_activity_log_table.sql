-- =====================================================
-- Create activity_log table
-- Meeting Room Booking System
-- 
-- วิธี Import: 
-- 1. เปิด phpMyAdmin
-- 2. เลือก database mrb
-- 3. ไปที่ tab "SQL"
-- 4. Paste SQL นี้แล้วกด "Go"
-- =====================================================

CREATE TABLE IF NOT EXISTS `activity_log` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL,
    `type` VARCHAR(50) NOT NULL COMMENT 'Activity type',
    `description` VARCHAR(500) NOT NULL COMMENT 'Description in Thai',
    `data` TEXT NULL COMMENT 'Additional JSON data',
    `ip_address` VARCHAR(45) NULL COMMENT 'IP address',
    `user_agent` VARCHAR(500) NULL COMMENT 'Browser user agent',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_log_user` (`user_id`),
    INDEX `idx_activity_log_type` (`type`),
    INDEX `idx_activity_log_created` (`created_at`),
    CONSTRAINT `fk_activity_log_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `user` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verify
SELECT 'activity_log table created successfully!' as status;
DESCRIBE activity_log;
