<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\User;
use common\models\MeetingRoom;
use common\models\Department;
use common\models\Equipment;
use common\models\RoomEquipment;
use common\models\Booking;
use common\models\BookingEquipment;
use common\models\Holiday;
use common\models\Setting;
use common\models\EmailTemplate;

/**
 * Database Seeder - Creates demo data for development and testing
 * 
 * Usage:
 *   yii seed              - Seed all data
 *   yii seed/users        - Seed users only
 *   yii seed/rooms        - Seed rooms only
 *   yii seed/bookings     - Seed bookings only
 *   yii seed/all          - Seed all data
 *   yii seed/reset        - Clear and reseed all data
 */
class SeedController extends Controller
{
    /**
     * @var bool Whether to skip confirmation prompts
     */
    public $force = false;
    
    /**
     * @var int Number of sample bookings to create
     */
    public $bookingCount = 50;
    
    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['force', 'bookingCount']);
    }
    
    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            'f' => 'force',
            'n' => 'bookingCount',
        ]);
    }
    
    /**
     * Seed all demo data
     */
    public function actionIndex()
    {
        return $this->actionAll();
    }
    
    /**
     * Seed all demo data
     */
    public function actionAll()
    {
        $this->stdout("\n=== Meeting Room Booking System - Database Seeder ===\n\n", Console::FG_CYAN);
        
        if (!$this->force && !$this->confirm('This will add demo data to the database. Continue?')) {
            return ExitCode::OK;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            $this->seedSettings();
            $this->seedDepartments();
            $this->seedUsers();
            $this->seedEquipment();
            $this->seedRooms();
            $this->seedHolidays();
            $this->seedEmailTemplates();
            $this->seedBookings();
            
            $transaction->commit();
            
            $this->stdout("\n✓ All demo data seeded successfully!\n\n", Console::FG_GREEN);
            
            return ExitCode::OK;
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stderr("Error: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    
    /**
     * Clear all data and reseed
     */
    public function actionReset()
    {
        $this->stdout("\n=== Database Reset and Reseed ===\n\n", Console::FG_YELLOW);
        
        if (!$this->force && !$this->confirm('WARNING: This will DELETE all existing data. Are you sure?')) {
            return ExitCode::OK;
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Disable foreign key checks
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
            
            // Truncate tables in reverse dependency order
            $tables = [
                'booking_equipment',
                'booking',
                'notification',
                'room_equipment',
                'equipment',
                'room',
                'user',
                'department',
                'holiday',
                'email_template',
                'setting',
            ];
            
            foreach ($tables as $table) {
                $this->stdout("  Clearing table: {$table}...", Console::FG_YELLOW);
                try {
                    Yii::$app->db->createCommand()->truncateTable($table)->execute();
                    $this->stdout(" Done\n", Console::FG_GREEN);
                } catch (\Exception $e) {
                    $this->stdout(" Table not found, skipping\n", Console::FG_GREY);
                }
            }
            
            // Re-enable foreign key checks
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
            
            $transaction->commit();
            
            // Now seed fresh data
            return $this->actionAll();
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
            $this->stderr("Error: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
    
    /**
     * Seed application settings
     */
    public function actionSettings()
    {
        $this->stdout("\n=== Seeding Settings ===\n", Console::FG_CYAN);
        $this->seedSettings();
        $this->stdout("✓ Settings seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedSettings()
    {
        $this->stdout("  Seeding settings...\n");
        
        $settings = [
            // General
            ['category' => 'general', 'key' => 'site_name', 'value' => 'ระบบจองห้องประชุม', 'type' => 'string'],
            ['category' => 'general', 'key' => 'site_name_en', 'value' => 'PBRI Meeting Room Booking', 'type' => 'string'],
            ['category' => 'general', 'key' => 'timezone', 'value' => 'Asia/Bangkok', 'type' => 'string'],
            ['category' => 'general', 'key' => 'date_format', 'value' => 'd/m/Y', 'type' => 'string'],
            ['category' => 'general', 'key' => 'time_format', 'value' => 'H:i', 'type' => 'string'],
            ['category' => 'general', 'key' => 'admin_email', 'value' => 'admin@bizco.co.th', 'type' => 'string'],
            
            // Booking
            ['category' => 'booking', 'key' => 'min_duration', 'value' => '30', 'type' => 'integer'],
            ['category' => 'booking', 'key' => 'max_duration', 'value' => '480', 'type' => 'integer'],
            ['category' => 'booking', 'key' => 'max_advance_days', 'value' => '90', 'type' => 'integer'],
            ['category' => 'booking', 'key' => 'min_advance_hours', 'value' => '1', 'type' => 'integer'],
            ['category' => 'booking', 'key' => 'working_start', 'value' => '08:00', 'type' => 'string'],
            ['category' => 'booking', 'key' => 'working_end', 'value' => '20:00', 'type' => 'string'],
            ['category' => 'booking', 'key' => 'allow_weekends', 'value' => '0', 'type' => 'boolean'],
            ['category' => 'booking', 'key' => 'require_approval', 'value' => '1', 'type' => 'boolean'],
            ['category' => 'booking', 'key' => 'auto_approve_same_dept', 'value' => '0', 'type' => 'boolean'],
            ['category' => 'booking', 'key' => 'cancel_before_hours', 'value' => '24', 'type' => 'integer'],
            
            // Notification
            ['category' => 'notification', 'key' => 'email_enabled', 'value' => '1', 'type' => 'boolean'],
            ['category' => 'notification', 'key' => 'reminder_enabled', 'value' => '1', 'type' => 'boolean'],
            ['category' => 'notification', 'key' => 'reminder_minutes', 'value' => '60,15', 'type' => 'string'],
            
            // System
            ['category' => 'system', 'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean'],
            ['category' => 'system', 'key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean'],
            ['category' => 'system', 'key' => 'oauth_enabled', 'value' => '0', 'type' => 'boolean'],
        ];
        
        foreach ($settings as $setting) {
            $model = Setting::findOne(['category' => $setting['category'], 'key' => $setting['key']]);
            if (!$model) {
                $model = new Setting();
                $model->category = $setting['category'];
                $model->key = $setting['key'];
            }
            $model->value = $setting['value'];
            $model->type = $setting['type'];
            $model->save(false);
        }
        
        $this->stdout("    ✓ " . count($settings) . " settings created\n", Console::FG_GREEN);
    }
    
    /**
     * Seed departments
     */
    public function actionDepartments()
    {
        $this->stdout("\n=== Seeding Departments ===\n", Console::FG_CYAN);
        $this->seedDepartments();
        $this->stdout("✓ Departments seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedDepartments()
    {
        $this->stdout("  Seeding departments...\n");
        
        $departments = [
            ['name' => 'กองเทคโนโลยีดิจิทัลและปัญญาประดิษฐ์', 'code' => 'DTAI', 'description' => 'Digital Technology & AI Division'],
            ['name' => 'กองนโยบายและแผน', 'code' => 'PLAN', 'description' => 'Policy and Planning Division'],
            ['name' => 'กองการเจ้าหน้าที่', 'code' => 'HR', 'description' => 'Human Resources Division'],
            ['name' => 'กองคลัง', 'code' => 'FIN', 'description' => 'Finance Division'],
            ['name' => 'กองบริหารงานทั่วไป', 'code' => 'ADMIN', 'description' => 'General Administration Division'],
            ['name' => 'สำนักงานเลขานุการ', 'code' => 'SEC', 'description' => 'Secretariat Office'],
            ['name' => 'กองพัฒนาการศึกษา', 'code' => 'EDU', 'description' => 'Educational Development Division'],
            ['name' => 'กองวิชาการ', 'code' => 'ACAD', 'description' => 'Academic Affairs Division'],
            ['name' => 'กองกิจการนักศึกษา', 'code' => 'STUD', 'description' => 'Student Affairs Division'],
            ['name' => 'กองวิจัยและพัฒนานวัตกรรม', 'code' => 'RIC', 'description' => 'Research and Innovation Division'],
        ];
        
        foreach ($departments as $dept) {
            $model = Department::findOne(['code' => $dept['code']]);
            if (!$model) {
                $model = new Department();
            }
            $model->name = $dept['name'];
            $model->code = $dept['code'];
            $model->description = $dept['description'];
            $model->is_active = true;
            $model->save(false);
        }
        
        $this->stdout("    ✓ " . count($departments) . " departments created\n", Console::FG_GREEN);
    }
    
    /**
     * Seed users
     */
    public function actionUsers()
    {
        $this->stdout("\n=== Seeding Users ===\n", Console::FG_CYAN);
        $this->seedUsers();
        $this->stdout("✓ Users seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedUsers()
    {
        $this->stdout("  Seeding users...\n");
        
        $dtaiDept = Department::findOne(['code' => 'DTAI']);
        $deptId = $dtaiDept ? $dtaiDept->id : 1;
        
        $users = [
            // Superadmin
            [
                'username' => 'superadmin',
                'email' => 'superadmin@bizco.co.th',
                'password' => 'SuperAdmin@123',
                'full_name' => 'ผู้ดูแลระบบสูงสุด',
                'role' => User::ROLE_SUPERADMIN,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
            // Admin
            [
                'username' => 'admin',
                'email' => 'admin@bizco.co.th',
                'password' => 'Admin@123',
                'full_name' => 'ผู้ดูแลระบบ',
                'role' => User::ROLE_ADMIN,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
            // Approver
            [
                'username' => 'approver',
                'email' => 'approver@bizco.co.th',
                'password' => 'Approver@123',
                'full_name' => 'ผู้อนุมัติ',
                'role' => User::ROLE_APPROVER,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
            // Regular users
            [
                'username' => 'user1',
                'email' => 'user1@bizco.co.th',
                'password' => 'User@123',
                'full_name' => 'สมชาย ใจดี',
                'role' => User::ROLE_USER,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
            [
                'username' => 'user2',
                'email' => 'user2@bizco.co.th',
                'password' => 'User@123',
                'full_name' => 'สมหญิง รักเรียน',
                'role' => User::ROLE_USER,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
            [
                'username' => 'user3',
                'email' => 'user3@bizco.co.th',
                'password' => 'User@123',
                'full_name' => 'วิชัย พัฒนา',
                'role' => User::ROLE_USER,
                'department_id' => $deptId,
                'phone' => '02-712-7000',
            ],
        ];
        
        foreach ($users as $userData) {
            $user = User::findOne(['username' => $userData['username']]);
            if (!$user) {
                $user = new User();
                $user->username = $userData['username'];
                $user->email = $userData['email'];
                $user->setPassword($userData['password']);
                $user->generateAuthKey();
            }
            $user->full_name = $userData['full_name'];
            $user->role = $userData['role'];
            $user->department_id = $userData['department_id'];
            $user->phone = $userData['phone'];
            $user->status = User::STATUS_ACTIVE;
            $user->email_verified = 1;
            $user->save(false);
        }
        
        $this->stdout("    ✓ " . count($users) . " users created\n", Console::FG_GREEN);
        $this->stdout("    Default passwords:\n", Console::FG_YELLOW);
        $this->stdout("      - superadmin: SuperAdmin@123\n");
        $this->stdout("      - admin: Admin@123\n");
        $this->stdout("      - approver: Approver@123\n");
        $this->stdout("      - user1/user2/user3: User@123\n");
    }
    
    /**
     * Seed equipment
     */
    public function actionEquipment()
    {
        $this->stdout("\n=== Seeding Equipment ===\n", Console::FG_CYAN);
        $this->seedEquipment();
        $this->stdout("✓ Equipment seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedEquipment()
    {
        $this->stdout("  Seeding equipment...\n");
        // 
        // category: 
        // `id`, `code`, `name_th`, `name_en`, `icon`, `description`, `sort_order`, `is_active`, `created_at`
        // 1, PROJECTOR, เครื่องฉาย, Projector, fa-video, NULL, 1, 1, 2026-01-17 23:25:04
        // 2, DISPLAY, จอแสดงผล, Display, fa-tv, NULL, 2, 1, 2026-01-17 23:25:04
        // 3, COMPUTER, คอมพิวเตอร์, Computer, fa-laptop, NULL, 3, 1, 2026-01-17, 23:25:04
        // 4, AUDIO, ระบบเสียง, Audio System, fa-volume-up, NULL, 4, 1, 2026-01-17, 23:25:04
        // 5, VIDEO_CONF, ระบบประชุมทางไกล, Video Conference, fa-video-camera, NULL, 5, 1, 2026-01-17, 23:25:04
        // 6, OTHER, อุปกรณ์อื่นๆ, Other Equipment, fa-cogs, NULL, 99, 1, 2026-01-17, 23:25:00        
        // equipments:
        // `id`, `equipment_code`, `category_id`, `name_th`, `name_en`, 
        // `brand`, `model`, `serial_number`, `building_id`, `storage_location`, 
        // `total_quantity`, `available_quantity`, `is_portable`, 
        // `hourly_rate`, `daily_rate`, `last_maintenance_date`, `next_maintenance_date`, 
        // `condition_status`, `description`, `usage_instructions`, `specifications`, 
        // `image`, `status`, `created_by`, `created_at`, `updated_at`
        $equipment = [
            ['equipment_code' => 'PRJEPS001', 'name_th' => 'โปรเจคเตอร์', 'name_en' => 'LCD Projector', 'category_id' => '1', 'icon' => 'bi-projector', 'description' => 'Projector EPSON EB-X51', 'total_quantity' => 20, 'available_quantity' => 10],
            ['equipment_code' => 'LED65001', 'name_th' => 'จอ LED 65 นิ้ว', 'name_en' => 'LED TV Display 65 inch', 'category_id' => '1', 'icon' => 'bi-display', 'description' => 'ไมโครโฟนไร้สาย Shure', 'total_quantity' => 20, 'available_quantity' => 5],
            ['equipment_code' => 'BRDWB001', 'name_th' => 'ไวท์บอร์ด ขนาด 120x180ซม.', 'name_en' => 'Whiteboard 120x180cm', 'category_id' => '1', 'icon' => 'bi-easel', 'description' => 'ไวท์บอร์ด 120x180 ซม.', 'total_quantity' => 15, 'available_quantity' => 15],
            ['equipment_code' => 'CAMVID001', 'name_th' => 'ระบบประชุมทางไกล Cisco WebEx', 'name_en' => 'Cisco WebEx Room Kit', 'category_id' => '3', 'icon' => 'bi-camera-video', 'description' => 'กล้อง Video Conference สำหรับประชุมออนไลน์', 'total_quantity' => 1, 'available_quantity' => 1],
            ['equipment_code' => 'MIC01', 'name_th' => 'ไมโครโฟนไร้สาย Shure', 'name_en' => 'Microphone,Shure Wireless', 'category_id' => '4', 'icon' => 'bi-mic', 'description' => 'ไมโครโฟนไร้สาย Shure', 'total_quantity' => 15, 'available_quantity' => 10],
            ['equipment_code' => 'SPK01', 'name_th' => 'ลำโพงห้องประชุม', 'name_en' => 'Conference Speaker', 'category_id' => '4', 'icon' => 'bi-speaker', 'description' => 'Sound Bar speaker', 'total_quantity' => 5, 'available_quantity' => 5],
            ['equipment_code' => 'LTP01', 'name_th' => 'โน้ตบุ๊ค', 'name_en' => 'Laptop', 'category_id' => '6', 'icon' => 'bi-laptop', 'description' => 'Notebook สำหรับนำเสนอ', 'total_quantity' => 35, 'available_quantity' => 35],
            ['equipment_code' => 'DCM01', 'name_th' => 'เครื่องฉายแผ่นใส', 'name_en' => 'Document Camera', 'category_id' => '6', 'icon' => 'bi-file-slides', 'description' => 'Projector EPSON EB-X51', 'total_quantity' => 20, 'available_quantity' => 10],
            ['equipment_code' => 'SND01', 'name_th' => 'ระบบเสียง', 'name_en' => 'Sound System', 'category_id' => '4', 'icon' => 'bi-volume-up', 'description' => 'ชุดเครื่องเสียงพร้อมลำโพง', 'total_quantity' => 5, 'available_quantity' => 2],
            ['equipment_code' => 'CAM01', 'name_th' => 'กล้องถ่ายรูป (Digital)', 'name_en' => 'Digital Camera', 'category_id' => '6', 'icon' => 'bi-camera', 'description' => 'Camera', 'total_quantity' => 15, 'available_quantity' => 10],
            ['equipment_code' => 'TEA01', 'name_th' => 'ชุดน้ำชา/กาแฟ', 'name_en' => 'Tea/Coffee Cup,Catering Set', 'category_id' => '6', 'icon' => 'bi-cup-hot', 'description' => 'ชุดน้ำชา/กาแฟ', 'total_quantity' => 20, 'available_quantity' => 10],
            ['equipment_code' => 'WF001', 'name_th' => 'WiFi', 'name_en' => 'WiFi', 'category_id' => '6', 'icon' => 'bi-wifi', 'description' => 'Pocket WiFI', 'total_quantity' => 30, 'available_quantity' => 10],     
            ['equipment_code' => 'FC001', 'name_th' => 'Flipchart', 'name_en' => 'Flipchart', 'category_id' => '6', 'icon' => 'bi-flipchart', 'description' => 'กระดาษ Flipchart พร้อมขาตั้ง', 'total_quantity' => 5, 'available_quantity' => 5],  
            ['equipment_code' => 'EO001', 'name_th' => 'ปลั๊กไฟพ่วง', 'name_en' => 'Electric Outlet', 'category_id' => '6', 'icon' => 'bi-wifi', 'description' => 'ปลั๊กพ่วง 6 ช่อง', 'total_quantity' => 40, 'available_quantity' => 40],   
        ];
        
        foreach ($equipment as $equip) {
            $model = Equipment::findOne(['name_th' => $equip['name_th']]);
            if (!$model) {
                $model = new Equipment();
            }
            $model->equipment_code = $equip['equipment_code'];
            $model->name_th = $equip['name_th'];
            $model->name_en = $equip['name_en'];
            $model->category_id = $equip['category_id'];
            $model->icon = $equip['icon'];
            $model->description = $equip['description'];
            $model->total_quantity = $equip['total_quantity'];
            $model->available_quantity = $equip['available_quantity'];
            $model->status = Equipment::STATUS_AVAILABLE;
            $model->save(false);
        }
        
        $this->stdout("    ✓ " . count($equipment) . " equipment items created\n", Console::FG_GREEN);
    }
    
    /**
     * Seed meeting rooms
     */
    public function actionRooms()
    {
        $this->stdout("\n=== Seeding Rooms ===\n", Console::FG_CYAN);
        $this->seedRooms();
        $this->stdout("✓ Rooms seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedRooms()
    {
        $this->stdout("  Seeding rooms...\n");
        
        // First, ensure buildings exist
        $this->seedBuildings();
        
        // Get building IDs
        $building1 = \common\models\Building::findOne(['code' => 'BLD-1']);
        $building2 = \common\models\Building::findOne(['code' => 'BLD-2']);
        $building3 = \common\models\Building::findOne(['code' => 'BLD-3']);
        $buildingAdmin = \common\models\Building::findOne(['code' => 'BLD-ADM']);
        
        $defaultBuildingId = $building1 ? $building1->id : 1;
        
        $rooms = [
            [
                'name_th' => 'ห้องประชุมใหญ่ 1',
                'name_en' => 'Large Meeting Room 1',
                'room_code' => 'CONF-L1',
                'building_id' => $building1 ? $building1->id : $defaultBuildingId,
                'floor' => 2,
                'room_number' => '201',
                'capacity' => 100,
                'room_type' => MeetingRoom::TYPE_CONFERENCE,
                'has_projector' => true,
                'has_video_conference' => true,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => true,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดใหญ่ รองรับ 100 คน มีระบบประชุมทางไกล',
            ],
            [
                'name_th' => 'ห้องประชุมใหญ่ 2',
                'name_en' => 'Large Meeting Room 2',
                'room_code' => 'CONF-L2',
                'building_id' => $building1 ? $building1->id : $defaultBuildingId,
                'floor' => 3,
                'room_number' => '301',
                'capacity' => 80,
                'room_type' => MeetingRoom::TYPE_CONFERENCE,
                'has_projector' => true,
                'has_video_conference' => false,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => true,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดใหญ่ รองรับ 80 คน',
            ],
            [
                'name_th' => 'ห้องประชุมกลาง A',
                'name_en' => 'Medium Meeting Room A',
                'room_code' => 'CONF-MA',
                'building_id' => $building2 ? $building2->id : $defaultBuildingId,
                'floor' => 1,
                'room_number' => '101',
                'capacity' => 40,
                'room_type' => MeetingRoom::TYPE_CONFERENCE,
                'has_projector' => true,
                'has_video_conference' => false,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => false,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดกลาง รองรับ 40 คน',
            ],
            [
                'name_th' => 'ห้องประชุมกลาง B',
                'name_en' => 'Medium Meeting Room B',
                'room_code' => 'CONF-MB',
                'building_id' => $building2 ? $building2->id : $defaultBuildingId,
                'floor' => 2,
                'room_number' => '201',
                'capacity' => 30,
                'room_type' => MeetingRoom::TYPE_CONFERENCE,
                'has_projector' => false,
                'has_video_conference' => false,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => false,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดกลาง รองรับ 30 คน พร้อมจอ LED',
            ],
            [
                'name_th' => 'ห้องประชุมเล็ก 1',
                'name_en' => 'Small Meeting Room 1',
                'room_code' => 'CONF-S1',
                'building_id' => $building1 ? $building1->id : $defaultBuildingId,
                'floor' => 4,
                'room_number' => '401',
                'capacity' => 15,
                'room_type' => MeetingRoom::TYPE_HUDDLE,
                'has_projector' => false,
                'has_video_conference' => false,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => false,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดเล็ก รองรับ 15 คน เหมาะสำหรับการประชุมทีมงาน',
            ],
            [
                'name_th' => 'ห้องประชุมเล็ก 2',
                'name_en' => 'Small Meeting Room 2',
                'room_code' => 'CONF-S2',
                'building_id' => $building1 ? $building1->id : $defaultBuildingId,
                'floor' => 4,
                'room_number' => '402',
                'capacity' => 12,
                'room_type' => MeetingRoom::TYPE_HUDDLE,
                'has_projector' => true,
                'has_video_conference' => false,
                'has_whiteboard' => false,
                'has_air_conditioning' => true,
                'has_audio_system' => false,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุมขนาดเล็ก รองรับ 12 คน',
            ],
            [
                'name_th' => 'ห้องประชุม VIP',
                'name_en' => 'VIP Meeting Room',
                'room_code' => 'CONF-VIP',
                'building_id' => $buildingAdmin ? $buildingAdmin->id : $defaultBuildingId,
                'floor' => 5,
                'room_number' => '501',
                'capacity' => 20,
                'room_type' => MeetingRoom::TYPE_BOARDROOM,
                'has_projector' => true,
                'has_video_conference' => true,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => true,
                'has_recording' => true,
                'hourly_rate' => 0,
                'description' => 'ห้องประชุม VIP สำหรับผู้บริหาร มีระบบประชุมทางไกลคุณภาพสูง',
            ],
            [
                'name_th' => 'ห้องฝึกอบรม 1',
                'name_en' => 'Training Room 1',
                'room_code' => 'TRAIN-1',
                'building_id' => $building3 ? $building3->id : $defaultBuildingId,
                'floor' => 1,
                'room_number' => '101',
                'capacity' => 50,
                'room_type' => MeetingRoom::TYPE_TRAINING,
                'has_projector' => true,
                'has_video_conference' => false,
                'has_whiteboard' => true,
                'has_air_conditioning' => true,
                'has_audio_system' => true,
                'hourly_rate' => 500,
                'description' => 'ห้องฝึกอบรม รองรับ 50 คน พร้อมคอมพิวเตอร์',
            ],
        ];
        
        foreach ($rooms as $roomData) {
            $room = MeetingRoom::findOne(['room_code' => $roomData['room_code']]);
            if (!$room) {
                $room = new MeetingRoom();
            }
            
            $room->setAttributes($roomData, false);
            $room->status = MeetingRoom::STATUS_ACTIVE;
            $room->operating_start_time = '08:00:00';
            $room->operating_end_time = '18:00:00';
            $room->available_days = json_encode([1, 2, 3, 4, 5]); // Monday to Friday
            $room->min_booking_duration = 30;
            $room->max_booking_duration = 480;
            $room->advance_booking_days = 30;
            $room->requires_approval = false;
            
            if (!$room->save(false)) {
                $this->stdout("  Error saving room: " . $roomData['room_code'] . "\n", Console::FG_RED);
            } else {
                $this->stdout("  ✓ Room: " . $roomData['name_th'] . "\n");
            }
        }
    }
    
    /**
     * Seed buildings
     */
    protected function seedBuildings()
    {
        $this->stdout("  Seeding buildings...\n");
        
        $buildings = [
            ['code' => 'BLD-1', 'name_th' => 'อาคาร 1', 'name_en' => 'Building 1', 'floor_count' => 5],
            ['code' => 'BLD-2', 'name_th' => 'อาคาร 2', 'name_en' => 'Building 2', 'floor_count' => 4],
            ['code' => 'BLD-3', 'name_th' => 'อาคาร 3', 'name_en' => 'Building 3', 'floor_count' => 3],
            ['code' => 'BLD-ADM', 'name_th' => 'อาคารบริหาร', 'name_en' => 'Administration Building', 'floor_count' => 6],
        ];
        
        foreach ($buildings as $buildingData) {
            $building = \common\models\Building::findOne(['code' => $buildingData['code']]);
            if (!$building) {
                $building = new \common\models\Building();
            }
            $building->code = $buildingData['code'];
            $building->name_th = $buildingData['name_th'];
            $building->name_en = $buildingData['name_en'];
            $building->floor_count = $buildingData['floor_count'];
            $building->is_active = true;
            $building->save(false);
            $this->stdout("  ✓ Building: " . $buildingData['name_th'] . "\n");
        }
    }
    
    /**
     * Seed holidays
     */
    public function actionHolidays()
    {
        $this->stdout("\n=== Seeding Holidays ===\n", Console::FG_CYAN);
        $this->seedHolidays();
        $this->stdout("✓ Holidays seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedHolidays()
    {
        $this->stdout("  Seeding holidays (2025)...\n");
        
        $year = date('Y');
        $holidays = [
            ['date' => "{$year}-01-01", 'name' => 'วันขึ้นปีใหม่'],
            ['date' => "{$year}-02-10", 'name' => 'วันมาฆบูชา'],
            ['date' => "{$year}-04-06", 'name' => 'วันจักรี'],
            ['date' => "{$year}-04-13", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-04-14", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-04-15", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-05-01", 'name' => 'วันแรงงานแห่งชาติ'],
            ['date' => "{$year}-05-04", 'name' => 'วันฉัตรมงคล'],
            ['date' => "{$year}-05-12", 'name' => 'วันวิสาขบูชา'],
            ['date' => "{$year}-06-03", 'name' => 'วันเฉลิมพระชนมพรรษาสมเด็จพระราชินี'],
            ['date' => "{$year}-07-10", 'name' => 'วันอาสาฬหบูชา'],
            ['date' => "{$year}-07-11", 'name' => 'วันเข้าพรรษา'],
            ['date' => "{$year}-07-28", 'name' => 'วันเฉลิมพระชนมพรรษา ร.10'],
            ['date' => "{$year}-08-12", 'name' => 'วันแม่แห่งชาติ'],
            ['date' => "{$year}-10-13", 'name' => 'วันคล้ายวันสวรรคต ร.9'],
            ['date' => "{$year}-10-23", 'name' => 'วันปิยมหาราช'],
            ['date' => "{$year}-12-05", 'name' => 'วันพ่อแห่งชาติ'],
            ['date' => "{$year}-12-10", 'name' => 'วันรัฐธรรมนูญ'],
            ['date' => "{$year}-12-31", 'name' => 'วันสิ้นปี'],
        ];
        
        foreach ($holidays as $holiday) {
            $model = Holiday::findOne(['date' => $holiday['date']]);
            if (!$model) {
                $model = new Holiday();
            }
            $model->date = $holiday['date'];
            $model->name = $holiday['name'];
            $model->is_recurring = false;
            $model->is_active = true;
            $model->save(false);
        }
        
        $this->stdout("    ✓ " . count($holidays) . " holidays created\n", Console::FG_GREEN);
    }
    
    /**
     * Seed email templates
     */
    protected function seedEmailTemplates()
    {
        $this->stdout("  Seeding email templates...\n");
        
        $templates = [
            [
                'type' => 'booking_confirmation',
                'name' => 'Booking Confirmation',
                'subject' => 'ยืนยันการจองห้องประชุม - {room_name}',
                'body' => '<p>เรียน {user_name}</p><p>การจองห้องประชุมของท่านได้รับการบันทึกเรียบร้อยแล้ว</p><p><strong>รายละเอียด:</strong><br>ห้อง: {room_name}<br>วันที่: {date}<br>เวลา: {start_time} - {end_time}<br>หัวข้อ: {title}</p><p>โปรดรอการอนุมัติจากผู้มีอำนาจ</p>',
            ],
            [
                'type' => 'booking_approved',
                'name' => 'Booking Approved',
                'subject' => 'อนุมัติการจองห้องประชุม - {room_name}',
                'body' => '<p>เรียน {user_name}</p><p>การจองห้องประชุมของท่านได้รับการ<strong>อนุมัติ</strong>แล้ว</p><p><strong>รายละเอียด:</strong><br>ห้อง: {room_name}<br>วันที่: {date}<br>เวลา: {start_time} - {end_time}</p>',
            ],
            [
                'type' => 'booking_rejected',
                'name' => 'Booking Rejected',
                'subject' => 'ปฏิเสธการจองห้องประชุม - {room_name}',
                'body' => '<p>เรียน {user_name}</p><p>การจองห้องประชุมของท่าน<strong>ไม่ได้รับการอนุมัติ</strong></p><p><strong>เหตุผล:</strong> {reject_reason}</p><p>โปรดติดต่อผู้ดูแลระบบหากมีข้อสงสัย</p>',
            ],
            [
                'type' => 'booking_reminder',
                'name' => 'Booking Reminder',
                'subject' => 'แจ้งเตือน: การประชุมใน {minutes} นาที - {room_name}',
                'body' => '<p>เรียน {user_name}</p><p>ขอแจ้งเตือนว่าการประชุมของท่านจะเริ่มใน <strong>{minutes} นาที</strong></p><p><strong>รายละเอียด:</strong><br>ห้อง: {room_name}<br>เวลา: {start_time}<br>หัวข้อ: {title}</p>',
            ],
            [
                'type' => 'welcome',
                'name' => 'Welcome Email',
                'subject' => 'ยินดีต้อนรับสู่ระบบจองห้องประชุม',
                'body' => '<p>เรียน {user_name}</p><p>ยินดีต้อนรับสู่ระบบจองห้องประชุม BiZCO</p><p>ท่านสามารถเข้าใช้งานระบบได้ที่: {login_url}</p><p>หากมีข้อสงสัยโปรดติดต่อ: {admin_email}</p>',
            ],
        ];
        
        foreach ($templates as $template) {
            $model = EmailTemplate::findOne(['type' => $template['type']]);
            if (!$model) {
                $model = new EmailTemplate();
            }
            $model->type = $template['type'];
            $model->name = $template['name'];
            $model->subject = $template['subject'];
            $model->body = $template['body'];
            $model->is_active = true;
            $model->save(false);
        }
        
        $this->stdout("    ✓ " . count($templates) . " email templates created\n", Console::FG_GREEN);
    }
    
    /**
     * Seed sample bookings
     */
    public function actionBookings()
    {
        $this->stdout("\n=== Seeding Bookings ===\n", Console::FG_CYAN);
        $this->seedBookings();
        $this->stdout("✓ Bookings seeded\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    protected function seedBookings()
    {
        $this->stdout("  Seeding sample bookings...\n");
        
        $users = User::find()->where(['role' => User::ROLE_USER])->all();
        $rooms = MeetingRoom::find()->where(['status' => MeetingRoom::STATUS_ACTIVE])->all();
        $approver = User::findOne(['role' => User::ROLE_APPROVER]);
        
        if (empty($users) || empty($rooms)) {
            $this->stdout("    ! No users or rooms found, skipping bookings\n", Console::FG_YELLOW);
            return;
        }
        
        $purposes = [
            'ประชุมทีมงาน',
            'ประชุมคณะกรรมการ',
            'ประชุมวิชาการ',
            'อบรมเชิงปฏิบัติการ',
            'ประชุมผู้บริหาร',
            'สัมมนาออนไลน์',
            'นำเสนอโครงการ',
            'ประชุมติดตามงาน',
            'ประชุมงบประมาณ',
            'ประชุมบุคลากร',
        ];
        
        $statuses = [
            Booking::STATUS_PENDING,
            Booking::STATUS_APPROVED,
            Booking::STATUS_COMPLETED,
            Booking::STATUS_CANCELLED,
        ];
        
        $created = 0;
        $startDate = strtotime('-7 days');
        $endDate = strtotime('+30 days');
        
        for ($i = 0; $i < $this->bookingCount; $i++) {
            $user = $users[array_rand($users)];
            $room = $rooms[array_rand($rooms)];
            
            // Random date and time
            $date = date('Y-m-d', rand($startDate, $endDate));
            $startHour = rand(8, 16);
            $duration = rand(1, 4);
            $endHour = min($startHour + $duration, 20);
            
            $startTime = "{$date} {$startHour}:00:00";
            $endTime = "{$date} {$endHour}:00:00";
            
            // Check for conflicts
            $conflict = Booking::find()
                ->where(['room_id' => $room->id])
                ->andWhere(['not', ['status' => [Booking::STATUS_CANCELLED, Booking::STATUS_REJECTED]]])
                ->andWhere(['<', 'start_time', $endTime])
                ->andWhere(['>', 'end_time', $startTime])
                ->exists();
            
            if ($conflict) {
                continue;
            }
            
            // Determine status based on date
            $isPast = strtotime($date) < strtotime('today');
            if ($isPast) {
                $status = rand(0, 100) > 20 ? Booking::STATUS_COMPLETED : Booking::STATUS_CANCELLED;
            } else {
                $status = $statuses[array_rand([Booking::STATUS_PENDING, Booking::STATUS_APPROVED])];
            }
            
            $booking = new Booking();
            $booking->user_id = $user->id;
            $booking->room_id = $room->id;
            $booking->meeting_title = $purposes[array_rand($purposes)];
            $booking->meeting_description = 'การจองตัวอย่างสำหรับทดสอบระบบ';
            $booking->start_time = $startTime;
            $booking->end_time = $endTime;
            $booking->attendees_count = rand(5, $room->capacity);
            $booking->status = $status;
            $booking->created_at = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
            
            if ($status === Booking::STATUS_APPROVED || $status === Booking::STATUS_COMPLETED) {
                $booking->approved_by = $approver ? $approver->id : null;
                $booking->approved_at = date('Y-m-d H:i:s', strtotime($booking->created_at . ' +1 hour'));
            }
            
            if ($status === Booking::STATUS_CANCELLED) {
                $booking->cancelled_at = date('Y-m-d H:i:s', strtotime($booking->created_at . ' +2 hours'));
                $booking->cancellation_reason = 'ยกเลิกเนื่องจากเปลี่ยนแปลงกำหนดการ';
            }
            
            if ($booking->save(false)) {
                $created++;
            }
        }
        
        $this->stdout("    ✓ {$created} sample bookings created\n", Console::FG_GREEN);
    }
}
