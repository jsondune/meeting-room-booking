<?php

use yii\db\Migration;

/**
 * Meeting Room Booking System - Database Migration
 * 
 * Creates all necessary tables for the meeting room booking system including:
 * - Users and authentication
 * - Rooms and equipment
 * - Bookings and equipment requests
 * - RBAC tables
 * - Audit logging
 * 
 * @author Digital Technology & AI Division, PBRI
 * @version 1.0.0
 */
class m240101_000001_create_meeting_room_booking_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        
        // =====================================================
        // 1. USER MANAGEMENT TABLES
        // =====================================================
        
        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Users table - main user account storage
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey()->unsigned(),
            'username' => $this->string(50)->notNull()->unique(),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'auth_key' => $this->string(32)->notNull(),
            'password_reset_token' => $this->string(255)->unique(),
            'verification_token' => $this->string(255)->unique(),
            'email_verified_at' => $this->timestamp()->null(),       
            
            // Profile information
            'first_name' => $this->string(100)->notNull(),
            'last_name' => $this->string(100)->notNull(),
            'phone' => $this->string(20),
            'avatar' => $this->string(255),
            'department_id' => $this->integer()->unsigned(),
            'position' => $this->string(100),
            
            // OAuth2 identifiers
            'azure_id' => $this->string(255)->unique(),
            'google_id' => $this->string(255)->unique(),
            'thaid_id' => $this->string(255)->unique(),
            'facebook_id' => $this->string(255)->unique(),
            
            // Two-Factor Authentication
            'two_factor_secret' => $this->string(255),
            'two_factor_enabled' => $this->boolean()->defaultValue(false),
            'backup_codes' => $this->text(),
            
            // Security fields
            'failed_login_attempts' => $this->smallInteger()->defaultValue(0),
            'locked_until' => $this->timestamp()->null(),
            'password_changed_at' => $this->timestamp()->null(),
            'last_login_at' => $this->timestamp()->null(),
            'last_login_ip' => $this->string(45),
            
            // Account status
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'role' => $this->string(20)->notNull()->defaultValue('user'),
            
            // Timestamps
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createIndex('idx_user_status', '{{%users}}', 'status');
        $this->createIndex('idx_user_role', '{{%users}}', 'role');
        $this->createIndex('idx_user_department', '{{%users}}', 'department_id');
        $this->createIndex('idx_user_deleted', '{{%users}}', 'deleted_at');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Departments table
        $this->createTable('{{%department}}', [
            'id' => $this->primaryKey()->unsigned(),
            'code' => $this->string(20)->notNull()->unique(),
            'name_th' => $this->string(255)->notNull(),
            'name_en' => $this->string(255),
            'parent_id' => $this->integer()->unsigned(),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_department_parent',
            '{{%department}}',
            'parent_id',
            '{{%department}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_user_department',
            '{{%users}}',
            'department_id',
            '{{%department}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();                

        // User sessions table for session management
        $this->createTable('{{%user_session}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'session_id' => $this->string(128)->notNull(),
            'ip_address' => $this->string(45)->notNull(),
            'user_agent' => $this->text(),
            'device_type' => $this->string(50),
            'browser' => $this->string(100),
            'platform' => $this->string(100),
            'location' => $this->string(255),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'last_activity_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'expired_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createIndex('idx_session_user', '{{%user_session}}', 'user_id');
        $this->createIndex('idx_session_id', '{{%user_session}}', 'session_id');
        $this->addForeignKey(
            'fk_session_user',
            '{{%user_session}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();                

        // Login history table
        $this->createTable('{{%login_history}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'username' => $this->string(50),
            'ip_address' => $this->string(45)->notNull(),
            'user_agent' => $this->text(),
            'login_method' => $this->string(20)->notNull()->comment('password, azure, google, thaid, facebook'),
            'login_status' => $this->string(20)->notNull()->comment('success, failed, locked, captcha_required'),
            'failure_reason' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_login_user', '{{%login_history}}', 'user_id');
        $this->createIndex('idx_login_ip', '{{%login_history}}', 'ip_address');
        $this->createIndex('idx_login_status', '{{%login_history}}', 'login_status');
        $this->createIndex('idx_login_created', '{{%login_history}}', 'created_at');

        // =====================================================
        // 2. MEETING ROOM TABLES
        // =====================================================

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Buildings table
        $this->createTable('{{%building}}', [
            'id' => $this->primaryKey()->unsigned(),
            'code' => $this->string(20)->notNull()->unique(),
            'name_th' => $this->string(255)->notNull(),
            'name_en' => $this->string(255),
            'address' => $this->text(),
            'latitude' => $this->decimal(10, 8),
            'longitude' => $this->decimal(11, 8),
            'floor_count' => $this->smallInteger()->defaultValue(1),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Meeting rooms table
        $this->createTable('{{%meeting_room}}', [
            'id' => $this->primaryKey()->unsigned(),
            'room_code' => $this->string(20)->notNull()->unique(),
            'name_th' => $this->string(255)->notNull(),
            'name_en' => $this->string(255),
            'building_id' => $this->integer()->unsigned()->notNull(),
            'floor' => $this->smallInteger()->notNull()->defaultValue(1),
            'room_number' => $this->string(20),
            
            // Capacity and type
            'capacity' => $this->integer()->notNull()->comment('Maximum number of attendees'),
            'room_type' => $this->string(50)->notNull()->comment('conference, training, boardroom, huddle, auditorium'),
            'room_layout' => $this->string(50)->comment('theater, classroom, u_shape, boardroom, banquet'),
            
            // Features
            'has_projector' => $this->boolean()->defaultValue(false),
            'has_video_conference' => $this->boolean()->defaultValue(false),
            'has_whiteboard' => $this->boolean()->defaultValue(false),
            'has_air_conditioning' => $this->boolean()->defaultValue(true),
            'has_wifi' => $this->boolean()->defaultValue(true),
            'has_audio_system' => $this->boolean()->defaultValue(false),
            'has_recording' => $this->boolean()->defaultValue(false),
            
            // Booking settings
            'min_booking_duration' => $this->smallInteger()->defaultValue(30)->comment('Minutes'),
            'max_booking_duration' => $this->smallInteger()->defaultValue(480)->comment('Minutes'),
            'advance_booking_days' => $this->smallInteger()->defaultValue(30)->comment('How many days in advance'),
            'requires_approval' => $this->boolean()->defaultValue(false),
            'allowed_departments' => $this->text()->comment('JSON array of department IDs, null = all'),
            
            // Pricing (if applicable)
            'hourly_rate' => $this->decimal(10, 2)->defaultValue(0),
            'half_day_rate' => $this->decimal(10, 2)->defaultValue(0),
            'full_day_rate' => $this->decimal(10, 2)->defaultValue(0),
            
            // Operating hours
            'operating_start_time' => $this->time()->defaultValue('08:00:00'),
            'operating_end_time' => $this->time()->defaultValue('18:00:00'),
            'available_days' => $this->string(20)->defaultValue('1,2,3,4,5')->comment('0=Sun, 1=Mon, etc.'),
            
            // Description and notes
            'description' => $this->text(),
            'usage_rules' => $this->text(),
            'contact_person' => $this->string(100),
            'contact_phone' => $this->string(20),
            
            // Status and display
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=active, 0=inactive, 2=maintenance'),
            'is_featured' => $this->boolean()->defaultValue(false)->comment('Show on homepage'),
            'sort_order' => $this->integer()->defaultValue(0),
            
            // Timestamps
            'created_by' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ], $tableOptions);

        $this->createIndex('idx_room_building', '{{%meeting_room}}', 'building_id');
        $this->createIndex('idx_room_status', '{{%meeting_room}}', 'status');
        $this->createIndex('idx_room_type', '{{%meeting_room}}', 'room_type');
        $this->createIndex('idx_room_capacity', '{{%meeting_room}}', 'capacity');
        $this->createIndex('idx_room_deleted', '{{%meeting_room}}', 'deleted_at');

        $this->addForeignKey(
            'fk_room_building',
            '{{%meeting_room}}',
            'building_id',
            '{{%building}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();                

        // Room images table
        $this->createTable('{{%room_image}}', [
            'id' => $this->primaryKey()->unsigned(),
            'room_id' => $this->integer()->unsigned()->notNull(),
            'filename' => $this->string(255)->notNull(),
            'original_name' => $this->string(255)->notNull(),
            'file_path' => $this->string(500)->notNull(),
            'file_size' => $this->integer()->unsigned(),
            'mime_type' => $this->string(100),
            'image_width' => $this->integer()->unsigned(),
            'image_height' => $this->integer()->unsigned(),
            'is_primary' => $this->boolean()->defaultValue(false),
            'sort_order' => $this->smallInteger()->defaultValue(0),
            'alt_text' => $this->string(255),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_image_room', '{{%room_image}}', 'room_id');
        $this->createIndex('idx_image_primary', '{{%room_image}}', 'is_primary');

        $this->addForeignKey(
            'fk_image_room',
            '{{%room_image}}',
            'room_id',
            '{{%meeting_room}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // =====================================================
        // 3. EQUIPMENT TABLES
        // =====================================================

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Equipment categories
        $this->createTable('{{%equipment_category}}', [
            'id' => $this->primaryKey()->unsigned(),
            'code' => $this->string(20)->notNull()->unique(),
            'name_th' => $this->string(100)->notNull(),
            'name_en' => $this->string(100),
            'icon' => $this->string(50)->comment('FontAwesome icon class'),
            'description' => $this->text(),
            'sort_order' => $this->smallInteger()->defaultValue(0),
            'is_active' => $this->boolean()->defaultValue(true),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Equipment items
        $this->createTable('{{%equipment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'equipment_code' => $this->string(30)->notNull()->unique(),
            'category_id' => $this->integer()->unsigned()->notNull(),
            'name_th' => $this->string(255)->notNull(),
            'name_en' => $this->string(255),
            'brand' => $this->string(100),
            'model' => $this->string(100),
            'serial_number' => $this->string(100),
            
            // Location
            'building_id' => $this->integer()->unsigned(),
            'storage_location' => $this->string(255),
            
            // Availability
            'total_quantity' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'available_quantity' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'is_portable' => $this->boolean()->defaultValue(true)->comment('Can be moved to different rooms'),
            
            // Pricing
            'hourly_rate' => $this->decimal(10, 2)->defaultValue(0),
            'daily_rate' => $this->decimal(10, 2)->defaultValue(0),
            
            // Maintenance
            'last_maintenance_date' => $this->date(),
            'next_maintenance_date' => $this->date(),
            'condition_status' => $this->string(20)->defaultValue('good')->comment('excellent, good, fair, poor'),
            
            // Description
            'description' => $this->text(),
            'usage_instructions' => $this->text(),
            'specifications' => $this->text()->comment('JSON format'),
            'image' => $this->string(255),
            
            // Status
            'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('1=available, 0=unavailable, 2=maintenance'),
            
            'created_by' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_equipment_category', '{{%equipment}}', 'category_id');
        $this->createIndex('idx_equipment_building', '{{%equipment}}', 'building_id');
        $this->createIndex('idx_equipment_status', '{{%equipment}}', 'status');

        $this->addForeignKey(
            'fk_equipment_category',
            '{{%equipment}}',
            'category_id',
            '{{%equipment_category}}',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_equipment_building',
            '{{%equipment}}',
            'building_id',
            '{{%building}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();                

        // Room default equipment (equipment that comes with the room)
        $this->createTable('{{%room_equipment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'room_id' => $this->integer()->unsigned()->notNull(),
            'equipment_id' => $this->integer()->unsigned()->notNull(),
            'quantity' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'is_included' => $this->boolean()->defaultValue(true)->comment('Included in room booking'),
            'notes' => $this->string(255),
        ], $tableOptions);

        $this->createIndex('idx_room_equip_room', '{{%room_equipment}}', 'room_id');
        $this->createIndex('idx_room_equip_equipment', '{{%room_equipment}}', 'equipment_id');
        $this->addForeignKey('fk_room_equip_room', '{{%room_equipment}}', 'room_id', '{{%meeting_room}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_room_equip_equipment', '{{%room_equipment}}', 'equipment_id', '{{%equipment}}', 'id', 'CASCADE', 'CASCADE');

        // =====================================================
        // 4. BOOKING TABLES
        // =====================================================

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Main bookings table
        $this->createTable('{{%booking}}', [
            'id' => $this->primaryKey()->unsigned(),
            'booking_code' => $this->string(20)->notNull()->unique(),
            'room_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'department_id' => $this->integer()->unsigned(),
            
            // Booking time
            'booking_date' => $this->date()->notNull(),
            'start_time' => $this->time()->notNull(),
            'end_time' => $this->time()->notNull(),
            'duration_minutes' => $this->smallInteger()->unsigned(),
            
            // Meeting details
            'meeting_title' => $this->string(255)->notNull(),
            'meeting_description' => $this->text(),
            'meeting_type' => $this->string(50)->comment('internal, external, training, interview, etc.'),
            'attendees_count' => $this->integer()->unsigned()->defaultValue(1),
            'external_attendees' => $this->text()->comment('JSON array of external attendee details'),
            
            // Contact
            'contact_person' => $this->string(100),
            'contact_phone' => $this->string(20),
            'contact_email' => $this->string(255),
            
            // Recurrence (for recurring meetings)
            'is_recurring' => $this->boolean()->defaultValue(false),
            'recurrence_pattern' => $this->string(20)->comment('daily, weekly, monthly'),
            'recurrence_end_date' => $this->date(),
            'parent_booking_id' => $this->integer()->unsigned()->comment('For recurring booking instances'),
            
            // Status and approval
            'status' => $this->string(20)->notNull()->defaultValue('pending')->comment('pending, approved, rejected, cancelled, completed'),
            'approved_by' => $this->integer()->unsigned(),
            'approved_at' => $this->timestamp()->null(),
            'rejection_reason' => $this->text(),
            'cancelled_by' => $this->integer()->unsigned(),
            'cancelled_at' => $this->timestamp()->null(),
            'cancellation_reason' => $this->text(),
            
            // Pricing
            'total_room_cost' => $this->decimal(10, 2)->defaultValue(0),
            'total_equipment_cost' => $this->decimal(10, 2)->defaultValue(0),
            'total_cost' => $this->decimal(10, 2)->defaultValue(0),
            
            // Additional info
            'special_requests' => $this->text(),
            'internal_notes' => $this->text(),
            'check_in_at' => $this->timestamp()->null(),
            'check_out_at' => $this->timestamp()->null(),
            
            // Timestamps
            'created_by' => $this->integer()->unsigned(),
            'updated_by' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_booking_room', '{{%booking}}', 'room_id');
        $this->createIndex('idx_booking_user', '{{%booking}}', 'user_id');
        $this->createIndex('idx_booking_date', '{{%booking}}', 'booking_date');
        $this->createIndex('idx_booking_status', '{{%booking}}', 'status');
        $this->createIndex('idx_booking_department', '{{%booking}}', 'department_id');
        $this->createIndex('idx_booking_parent', '{{%booking}}', 'parent_booking_id');
        // Composite index for availability check
        $this->createIndex('idx_booking_availability', '{{%booking}}', ['room_id', 'booking_date', 'start_time', 'end_time', 'status']);

        $this->addForeignKey('fk_booking_room', '{{%booking}}', 'room_id', '{{%meeting_room}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_booking_user', '{{%booking}}', 'user_id', '{{%users}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_booking_department', '{{%booking}}', 'department_id', '{{%department}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_booking_parent', '{{%booking}}', 'parent_booking_id', '{{%booking}}', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_booking_approved_by', '{{%booking}}', 'approved_by', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Booking attendees (internal users)
        $this->createTable('{{%booking_attendee}}', [
            'id' => $this->primaryKey()->unsigned(),
            'booking_id' => $this->integer()->unsigned()->notNull(),
            'user_id' => $this->integer()->unsigned(),
            'attendee_name' => $this->string(100),
            'attendee_email' => $this->string(255),
            'attendee_phone' => $this->string(20),
            'is_organizer' => $this->boolean()->defaultValue(false),
            'attendance_status' => $this->string(20)->defaultValue('pending')->comment('pending, accepted, declined, tentative'),
            'response_at' => $this->timestamp()->null(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_attendee_booking', '{{%booking_attendee}}', 'booking_id');
        $this->createIndex('idx_attendee_user', '{{%booking_attendee}}', 'user_id');
        $this->addForeignKey('fk_attendee_booking', '{{%booking_attendee}}', 'booking_id', '{{%booking}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_attendee_user', '{{%booking_attendee}}', 'user_id', '{{%users}}', 'id', 'SET NULL', 'CASCADE');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Equipment requests for bookings
        $this->createTable('{{%booking_equipment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'booking_id' => $this->integer()->unsigned()->notNull(),
            'equipment_id' => $this->integer()->unsigned()->notNull(),
            'quantity_requested' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'quantity_provided' => $this->integer()->unsigned(),
            'unit_price' => $this->decimal(10, 2)->defaultValue(0),
            'total_price' => $this->decimal(10, 2)->defaultValue(0),
            'status' => $this->string(20)->defaultValue('pending')->comment('pending, confirmed, delivered, returned'),
            'delivered_at' => $this->timestamp()->null(),
            'returned_at' => $this->timestamp()->null(),
            'condition_on_return' => $this->string(50),
            'notes' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_booking_equip_booking', '{{%booking_equipment}}', 'booking_id');
        $this->createIndex('idx_booking_equip_equipment', '{{%booking_equipment}}', 'equipment_id');
        $this->addForeignKey('fk_booking_equip_booking', '{{%booking_equipment}}', 'booking_id', '{{%booking}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_booking_equip_equipment', '{{%booking_equipment}}', 'equipment_id', '{{%equipment}}', 'id', 'RESTRICT', 'CASCADE');

        // =====================================================
        // 5. RBAC TABLES (Yii2 RBAC)
        // =====================================================

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Auth rules
        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Auth items (roles and permissions)
        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->createIndex('idx_auth_item_type', '{{%auth_item}}', 'type');
        $this->addForeignKey('fk_auth_item_rule', '{{%auth_item}}', 'rule_name', '{{%auth_rule}}', 'name', 'SET NULL', 'CASCADE');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Auth item children (hierarchy)
        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
        ], $tableOptions);

        $this->addForeignKey('fk_auth_child_parent', '{{%auth_item_child}}', 'parent', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_auth_child_child', '{{%auth_item_child}}', 'child', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Auth assignments
        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
        ], $tableOptions);

        $this->createIndex('idx_auth_assignment_user', '{{%auth_assignment}}', 'user_id');
        $this->addForeignKey('fk_auth_assignment_item', '{{%auth_assignment}}', 'item_name', '{{%auth_item}}', 'name', 'CASCADE', 'CASCADE');

        // =====================================================
        // 6. SYSTEM & AUDIT TABLES
        // =====================================================

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // System settings
        $this->createTable('{{%system_setting}}', [
            'id' => $this->primaryKey()->unsigned(),
            'setting_key' => $this->string(100)->notNull()->unique(),
            'setting_value' => $this->text(),
            'setting_type' => $this->string(20)->defaultValue('string')->comment('string, integer, boolean, json'),
            'category' => $this->string(50)->defaultValue('general'),
            'description' => $this->text(),
            'is_public' => $this->boolean()->defaultValue(false),
            'updated_by' => $this->integer()->unsigned(),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_setting_category', '{{%system_setting}}', 'category');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Audit log
        $this->createTable('{{%audit_log}}', [
            'id' => $this->bigPrimaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned(),
            'username' => $this->string(50),
            'ip_address' => $this->string(45),
            'user_agent' => $this->string(500),
            'action' => $this->string(50)->notNull(),
            'model_class' => $this->string(100),
            'model_id' => $this->string(50),
            'old_values' => $this->text()->comment('JSON'),
            'new_values' => $this->text()->comment('JSON'),
            'url' => $this->string(500),
            'description' => $this->text(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_audit_user', '{{%audit_log}}', 'user_id');
        $this->createIndex('idx_audit_action', '{{%audit_log}}', 'action');
        $this->createIndex('idx_audit_model', '{{%audit_log}}', ['model_class', 'model_id']);
        $this->createIndex('idx_audit_created', '{{%audit_log}}', 'created_at');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Notifications
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey()->unsigned(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'type' => $this->string(50)->notNull(),
            'title' => $this->string(255)->notNull(),
            'message' => $this->text(),
            'data' => $this->text()->comment('JSON additional data'),
            'link' => $this->string(500),
            'is_read' => $this->boolean()->defaultValue(false),
            'read_at' => $this->timestamp()->null(),
            'sent_email' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_notification_user', '{{%notification}}', 'user_id');
        $this->createIndex('idx_notification_read', '{{%notification}}', 'is_read');
        $this->addForeignKey('fk_notification_user', '{{%notification}}', 'user_id', '{{%users}}', 'id', 'CASCADE', 'CASCADE');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Holiday calendar
        $this->createTable('{{%holiday}}', [
            'id' => $this->primaryKey()->unsigned(),
            'holiday_date' => $this->date()->notNull(),
            'name_th' => $this->string(255)->notNull(),
            'name_en' => $this->string(255),
            'holiday_type' => $this->string(50)->defaultValue('public')->comment('public, organization, special'),
            'is_recurring' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_holiday_date', '{{%holiday}}', 'holiday_date');

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Email templates
        $this->createTable('{{%email_template}}', [
            'id' => $this->primaryKey()->unsigned(),
            'template_key' => $this->string(50)->notNull()->unique(),
            'name' => $this->string(100)->notNull(),
            'subject' => $this->string(255)->notNull(),
            'body_html' => $this->text(),
            'body_text' => $this->text(),
            'variables' => $this->text()->comment('JSON array of available variables'),
            'is_active' => $this->boolean()->defaultValue(true),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // File attachments (generic)
        $this->createTable('{{%attachment}}', [
            'id' => $this->primaryKey()->unsigned(),
            'model_class' => $this->string(100)->notNull(),
            'model_id' => $this->integer()->unsigned()->notNull(),
            'filename' => $this->string(255)->notNull(),
            'original_name' => $this->string(255)->notNull(),
            'file_path' => $this->string(500)->notNull(),
            'file_size' => $this->integer()->unsigned(),
            'mime_type' => $this->string(100),
            'uploaded_by' => $this->integer()->unsigned(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex('idx_attachment_model', '{{%attachment}}', ['model_class', 'model_id']);

        // =====================================================
        // 7. INSERT DEFAULT DATA
        // =====================================================
        
        $this->insertDefaultData();
    }

    /**
     * Insert default system data
     */
    private function insertDefaultData()
    {
        // Insert default departments
        $this->batchInsert('{{%department}}', ['code', 'name_th', 'name_en', 'sort_order'], [
            ['EXEC', 'ผู้บริหาร', 'Executive', 1],
            ['ADMIN', 'ฝ่ายบริหารงานทั่วไป', 'Administration', 2],
            ['IT', 'ฝ่ายเทคโนโลยีดิจิทัลและ AI', 'Digital Technology & AI', 3],
            ['HR', 'ฝ่ายทรัพยากรบุคคล', 'Human Resources', 4],
            ['FIN', 'ฝ่ายการเงิน', 'Finance', 5],
            ['ACAD', 'ฝ่ายวิชาการ', 'Academic Affairs', 6],
        ]);

        // Insert default building
        $this->insert('{{%building}}', [
            'code' => 'MAIN',
            'name_th' => 'อาคารสำนักงานหลัก',
            'name_en' => 'Main Office Building',
            'floor_count' => 5,
        ]);

        // Insert equipment categories
        $this->batchInsert('{{%equipment_category}}', ['code', 'name_th', 'name_en', 'icon', 'sort_order'], [
            ['PROJECTOR', 'เครื่องฉาย', 'Projector', 'fa-video', 1],
            ['DISPLAY', 'จอแสดงผล', 'Display', 'fa-tv', 2],
            ['COMPUTER', 'คอมพิวเตอร์', 'Computer', 'fa-laptop', 3],
            ['AUDIO', 'ระบบเสียง', 'Audio System', 'fa-volume-up', 4],
            ['VIDEO_CONF', 'ระบบประชุมทางไกล', 'Video Conference', 'fa-video-camera', 5],
            ['OTHER', 'อุปกรณ์อื่นๆ', 'Other Equipment', 'fa-cogs', 99],
        ]);

        // Insert system settings
        $this->batchInsert('{{%system_setting}}', ['setting_key', 'setting_value', 'setting_type', 'category', 'description'], [
            ['site_name', 'ระบบจองห้องประชุม', 'string', 'general', 'ชื่อระบบ'],
            ['site_name_en', 'Meeting Room Booking System', 'string', 'general', 'Site name (English)'],
            ['organization_name', 'BiZCO', 'string', 'general', 'ชื่อหน่วยงาน'],
            ['admin_email', 'admin@example.com', 'string', 'general', 'อีเมลผู้ดูแลระบบ'],
            ['timezone', 'Asia/Bangkok', 'string', 'general', 'เขตเวลา'],
            ['date_format', 'd/m/Y', 'string', 'general', 'รูปแบบวันที่'],
            ['time_format', 'H:i', 'string', 'general', 'รูปแบบเวลา'],
            ['default_booking_duration', '60', 'integer', 'booking', 'ระยะเวลาจองเริ่มต้น (นาที)'],
            ['max_advance_booking_days', '30', 'integer', 'booking', 'จองล่วงหน้าได้สูงสุด (วัน)'],
            ['allow_past_booking', '0', 'boolean', 'booking', 'อนุญาตให้จองย้อนหลัง'],
            ['require_approval', '0', 'boolean', 'booking', 'ต้องรออนุมัติทุกการจอง'],
            ['send_reminder_before', '30', 'integer', 'notification', 'ส่งแจ้งเตือนก่อนประชุม (นาที)'],
            ['password_min_length', '8', 'integer', 'security', 'ความยาวรหัสผ่านขั้นต่ำ'],
            ['password_require_uppercase', '1', 'boolean', 'security', 'ต้องมีตัวพิมพ์ใหญ่'],
            ['password_require_number', '1', 'boolean', 'security', 'ต้องมีตัวเลข'],
            ['password_require_special', '1', 'boolean', 'security', 'ต้องมีอักขระพิเศษ'],
            ['max_login_attempts', '5', 'integer', 'security', 'จำนวนครั้งที่ล็อกอินผิดได้'],
            ['lockout_duration', '30', 'integer', 'security', 'ระยะเวลาล็อค (นาที)'],
            ['session_timeout', '3600', 'integer', 'security', 'หมดเวลาเซสชัน (วินาที)'],
            ['enable_oauth_azure', '1', 'boolean', 'oauth', 'เปิดใช้ Azure AD Login'],
            ['enable_oauth_google', '1', 'boolean', 'oauth', 'เปิดใช้ Google Login'],
            ['enable_oauth_thaid', '1', 'boolean', 'oauth', 'เปิดใช้ ThaID Login'],
            ['enable_oauth_facebook', '0', 'boolean', 'oauth', 'เปิดใช้ Facebook Login'],
            ['enable_2fa', '1', 'boolean', 'security', 'เปิดใช้ Two-Factor Authentication'],
        ]);

        // Insert email templates
        $this->batchInsert('{{%email_template}}', ['template_key', 'name', 'subject', 'body_html'], [
            ['booking_confirmation', 'ยืนยันการจอง', 'ยืนยันการจองห้องประชุม: {{meeting_title}}', '<p>เรียน {{user_name}}</p><p>การจองห้องประชุมของท่านได้รับการยืนยันแล้ว</p><p>รายละเอียด:</p><ul><li>ห้อง: {{room_name}}</li><li>วันที่: {{booking_date}}</li><li>เวลา: {{start_time}} - {{end_time}}</li></ul>'],
            ['booking_reminder', 'แจ้งเตือนการประชุม', 'แจ้งเตือน: การประชุม {{meeting_title}} ใกล้เริ่มแล้ว', '<p>เรียน {{user_name}}</p><p>การประชุมของท่านจะเริ่มในอีก {{minutes}} นาที</p>'],
            ['booking_cancelled', 'ยกเลิกการจอง', 'การจองห้องประชุมถูกยกเลิก: {{meeting_title}}', '<p>เรียน {{user_name}}</p><p>การจองห้องประชุมของท่านถูกยกเลิกแล้ว</p>'],
            ['password_reset', 'รีเซ็ตรหัสผ่าน', 'รีเซ็ตรหัสผ่าน - ระบบจองห้องประชุม', '<p>เรียน {{user_name}}</p><p>คลิกลิงก์ด้านล่างเพื่อรีเซ็ตรหัสผ่านของท่าน:</p><p><a href="{{reset_link}}">รีเซ็ตรหัสผ่าน</a></p>'],
        ]);

        // Create super admin user
        $this->insert('{{%users}}', [
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password_hash' => Yii::$app->security->generatePasswordHash('Admin@123'),
            'auth_key' => Yii::$app->security->generateRandomString(),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'status' => 10,
            'role' => 'superadmin',
            'password_changed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop tables in reverse order of creation
        $this->dropTable('{{%attachment}}');
        $this->dropTable('{{%email_template}}');
        $this->dropTable('{{%holiday}}');
        $this->dropTable('{{%notification}}');
        $this->dropTable('{{%audit_log}}');
        $this->dropTable('{{%system_setting}}');
        $this->dropTable('{{%auth_assignment}}');
        $this->dropTable('{{%auth_item_child}}');
        $this->dropTable('{{%auth_item}}');
        $this->dropTable('{{%auth_rule}}');
        $this->dropTable('{{%booking_equipment}}');
        $this->dropTable('{{%booking_attendee}}');
        $this->dropTable('{{%booking}}');
        $this->dropTable('{{%room_equipment}}');
        $this->dropTable('{{%equipment}}');
        $this->dropTable('{{%equipment_category}}');
        $this->dropTable('{{%room_image}}');
        $this->dropTable('{{%meeting_room}}');
        $this->dropTable('{{%building}}');
        $this->dropTable('{{%login_history}}');
        $this->dropTable('{{%user_session}}');
        $this->dropTable('{{%users}}');
        $this->dropTable('{{%department}}');
    }
}
