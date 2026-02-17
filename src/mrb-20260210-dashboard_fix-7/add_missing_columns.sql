-- =======================================================================
-- SQL Script: Add missing columns to booking table
-- Run this if you get "Unknown property" errors
-- =======================================================================

-- Add cancellation_reason column if not exists
-- MySQL 8.0+
SET @dbname = DATABASE();
SET @tablename = 'booking';
SET @columnname = 'cancellation_reason';

SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = @dbname AND table_name = @tablename AND column_name = @columnname) > 0,
    'SELECT "Column cancellation_reason already exists"',
    'ALTER TABLE booking ADD COLUMN cancellation_reason TEXT NULL AFTER cancelled_at'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add cancelled_by column if not exists
SET @columnname = 'cancelled_by';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = @dbname AND table_name = @tablename AND column_name = @columnname) > 0,
    'SELECT "Column cancelled_by already exists"',
    'ALTER TABLE booking ADD COLUMN cancelled_by INT(11) NULL AFTER rejection_reason'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add cancelled_at column if not exists
SET @columnname = 'cancelled_at';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = @dbname AND table_name = @tablename AND column_name = @columnname) > 0,
    'SELECT "Column cancelled_at already exists"',
    'ALTER TABLE booking ADD COLUMN cancelled_at DATETIME NULL AFTER cancelled_by'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =======================================================================
-- Alternative: Simple ALTER TABLE statements (run manually if above fails)
-- Remove the comments and run if needed
-- =======================================================================

-- ALTER TABLE booking ADD COLUMN cancellation_reason TEXT NULL;
-- ALTER TABLE booking ADD COLUMN cancelled_by INT(11) NULL;
-- ALTER TABLE booking ADD COLUMN cancelled_at DATETIME NULL;

-- =======================================================================
-- Check existing columns
-- =======================================================================
-- SHOW COLUMNS FROM booking LIKE 'cancellation%';
-- SHOW COLUMNS FROM booking LIKE 'cancelled%';
