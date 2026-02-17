-- Meeting Room Booking System - Database Initialization
-- This script runs automatically when MySQL container starts

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `meeting_room_booking`
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Create application user (credentials from environment)
-- Note: In production, use environment variables or Docker secrets

-- Application user with limited privileges
CREATE USER IF NOT EXISTS 'booking_app'@'%' IDENTIFIED BY '${DB_APP_PASS}';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON `meeting_room_booking`.* TO 'booking_app'@'%';

-- Migration user with additional privileges (for migrations only)
CREATE USER IF NOT EXISTS 'booking_migrate'@'%' IDENTIFIED BY '${DB_MIGRATE_PASS}';
GRANT ALL PRIVILEGES ON `meeting_room_booking`.* TO 'booking_migrate'@'%';

-- Read-only user for reporting
CREATE USER IF NOT EXISTS 'booking_readonly'@'%' IDENTIFIED BY '${DB_READONLY_PASS}';
GRANT SELECT ON `meeting_room_booking`.* TO 'booking_readonly'@'%';

-- Apply privileges
FLUSH PRIVILEGES;

-- Switch to application database
USE `meeting_room_booking`;

-- Create audit log table for tracking database changes
CREATE TABLE IF NOT EXISTS `db_audit_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_name` VARCHAR(64) NOT NULL,
    `operation` ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    `record_id` INT UNSIGNED NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `user_id` INT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_table_operation` (`table_name`, `operation`),
    INDEX `idx_record` (`table_name`, `record_id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create session storage table (alternative to Redis for sessions)
CREATE TABLE IF NOT EXISTS `session` (
    `id` CHAR(64) NOT NULL,
    `expire` INT UNSIGNED NOT NULL,
    `data` LONGBLOB NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_expire` (`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create job queue table (alternative to Redis for queues)
CREATE TABLE IF NOT EXISTS `job_queue` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `channel` VARCHAR(64) NOT NULL DEFAULT 'default',
    `job` LONGBLOB NOT NULL,
    `pushed_at` INT UNSIGNED NOT NULL,
    `ttr` INT UNSIGNED NOT NULL DEFAULT 300,
    `delay` INT UNSIGNED NOT NULL DEFAULT 0,
    `priority` INT UNSIGNED NOT NULL DEFAULT 1024,
    `reserved_at` INT UNSIGNED NULL,
    `attempt` INT UNSIGNED NOT NULL DEFAULT 0,
    `done_at` INT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_channel` (`channel`),
    INDEX `idx_reserved` (`channel`, `reserved_at`),
    INDEX `idx_priority` (`channel`, `priority`, `pushed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cache table (alternative to Redis for caching)
CREATE TABLE IF NOT EXISTS `cache` (
    `id` VARCHAR(128) NOT NULL,
    `data` LONGBLOB NOT NULL,
    `expire` INT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_expire` (`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create stored procedures for common operations

-- Procedure to clean expired sessions
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_cleanup_sessions`()
BEGIN
    DELETE FROM `session` WHERE `expire` < UNIX_TIMESTAMP();
END //
DELIMITER ;

-- Procedure to clean expired cache
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_cleanup_cache`()
BEGIN
    DELETE FROM `cache` WHERE `expire` IS NOT NULL AND `expire` < UNIX_TIMESTAMP();
END //
DELIMITER ;

-- Procedure to clean old audit logs
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_cleanup_audit_logs`(IN days_to_keep INT)
BEGIN
    DELETE FROM `db_audit_log` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
END //
DELIMITER ;

-- Procedure to get booking statistics
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_booking_statistics`(
    IN p_start_date DATE,
    IN p_end_date DATE
)
BEGIN
    SELECT 
        COUNT(*) as total_bookings,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        COUNT(DISTINCT room_id) as rooms_used,
        COUNT(DISTINCT user_id) as unique_users,
        SUM(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as total_minutes
    FROM booking
    WHERE DATE(start_time) BETWEEN p_start_date AND p_end_date;
END //
DELIMITER ;

-- Procedure to check room availability
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_check_room_availability`(
    IN p_room_id INT,
    IN p_start_time DATETIME,
    IN p_end_time DATETIME,
    IN p_exclude_booking_id INT
)
BEGIN
    SELECT COUNT(*) as conflict_count
    FROM booking
    WHERE room_id = p_room_id
    AND status IN ('pending', 'approved')
    AND id != COALESCE(p_exclude_booking_id, 0)
    AND (
        (start_time <= p_start_time AND end_time > p_start_time)
        OR (start_time < p_end_time AND end_time >= p_end_time)
        OR (start_time >= p_start_time AND end_time <= p_end_time)
    );
END //
DELIMITER ;

-- Create scheduled events

-- Event to clean expired sessions every hour
CREATE EVENT IF NOT EXISTS `evt_cleanup_sessions`
ON SCHEDULE EVERY 1 HOUR
STARTS CURRENT_TIMESTAMP
DO
    CALL sp_cleanup_sessions();

-- Event to clean expired cache every 30 minutes
CREATE EVENT IF NOT EXISTS `evt_cleanup_cache`
ON SCHEDULE EVERY 30 MINUTE
STARTS CURRENT_TIMESTAMP
DO
    CALL sp_cleanup_cache();

-- Event to clean old audit logs monthly (keep 90 days)
CREATE EVENT IF NOT EXISTS `evt_cleanup_audit_logs`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    CALL sp_cleanup_audit_logs(90);

-- Enable event scheduler
SET GLOBAL event_scheduler = ON;

-- Display completion message
SELECT 'Database initialization completed successfully' AS status;
