<?php
/**
 * Seeder Controller
 * 
 * Database seeding commands for demo/test data
 * 
 * Usage:
 *   php yii seeder/all           - Seed all data
 *   php yii seeder/users         - Seed users only
 *   php yii seeder/rooms         - Seed meeting rooms
 *   php yii seeder/bookings      - Seed bookings
 *   php yii seeder/reset         - Reset and reseed all
 * 
 * @author BIzCO
 * @version 1.0
 */

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use common\models\User;
use common\models\MeetingRoom;
use common\models\Booking;

class SeederController extends Controller
{
    /**
     * @var bool Whether to use Thai locale for Faker
     */
    public $thai = true;
    
    /**
     * @var \Faker\Generator
     */
    protected $faker;
    
    /**
     * Initialize faker
     */
    public function init()
    {
        parent::init();
        $locale = $this->thai ? 'th_TH' : 'en_US';
        $this->faker = \Faker\Factory::create($locale);
    }
    
    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), ['thai']);
    }
    
    /**
     * Seed all demo data
     */
    public function actionAll(): int
    {
        $this->stdout("Starting full database seeding...\n\n", Console::FG_CYAN);
        
        $this->actionDepartments();
        $this->actionUsers();
        $this->actionEquipment();
        $this->actionRooms();
        $this->actionBookings();
        $this->actionSettings();
        $this->actionHolidays();
        
        $this->stdout("\n✓ All seeding completed!\n", Console::FG_GREEN);
        
        return ExitCode::OK;
    }
    
    /**
     * Seed departments
     */
    public function actionDepartments(): int
    {
        $this->stdout("Seeding departments...\n", Console::FG_YELLOW);
        
        $departments = [
            ['name' => 'กลุ่มเทคโนโลยีดิจิทัลและปัญญาประดิษฐ์', 'code' => 'DTAI', 'name_en' => 'Digital Technology & AI Division'],
            ['name' => 'กลุ่มบริหารงานกลาง', 'code' => 'ADMIN', 'name_en' => 'General Administration Division'],
            ['name' => 'กลุ่มนโยบายและแผน', 'code' => 'PLAN', 'name_en' => 'Policy and Planning Division'],
            ['name' => 'กลุ่มพัฒนาบุคลากร', 'code' => 'HRD', 'name_en' => 'Human Resource Development Division'],
            ['name' => 'กลุ่มวิชาการและวิจัย', 'code' => 'ACAD', 'name_en' => 'Academic and Research Division'],
            ['name' => 'กลุ่มการเงินและบัญชี', 'code' => 'FIN', 'name_en' => 'Finance and Accounting Division'],
            ['name' => 'กลุ่มพัสดุและครุภัณฑ์', 'code' => 'PROC', 'name_en' => 'Procurement Division'],
            ['name' => 'กลุ่มกฎหมายและนิติการ', 'code' => 'LEGAL', 'name_en' => 'Legal Division'],
            ['name' => 'กลุ่มประชาสัมพันธ์', 'code' => 'PR', 'name_en' => 'Public Relations Division'],
            ['name' => 'กลุ่มตรวจสอบภายใน', 'code' => 'AUDIT', 'name_en' => 'Internal Audit Division'],
        ];
        
        $count = 0;
        foreach ($departments as $data) {
            $exists = Yii::$app->db->createCommand("SELECT id FROM {{%department}} WHERE code = :code")
                ->bindValue(':code', $data['code'])
                ->queryScalar();
            
            if (!$exists) {
                Yii::$app->db->createCommand()->insert('{{%department}}', array_merge($data, [
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]))->execute();
                $count++;
            }
        }
        
        $this->stdout("  ✓ Created {$count} departments\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed users with different roles
     */
    public function actionUsers(): int
    {
        $this->stdout("Seeding users...\n", Console::FG_YELLOW);
        
        $auth = Yii::$app->authManager;
        
        // Create roles if not exists
        $roles = ['superadmin', 'admin', 'approver', 'user'];
        foreach ($roles as $roleName) {
            if (!$auth->getRole($roleName)) {
                $role = $auth->createRole($roleName);
                $auth->add($role);
            }
        }
        
        // Default users
        $users = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@bizco.co.th',
                'password' => 'Admin@123',
                'full_name' => 'ผู้ดูแลระบบ',
                'role' => 'superadmin',
                'department_id' => 1,
            ],
            [
                'username' => 'admin',
                'email' => 'admin@bizco.co.th',
                'password' => 'Admin@123',
                'full_name' => 'แอดมินทั่วไป',
                'role' => 'admin',
                'department_id' => 1,
            ],
            [
                'username' => 'approver',
                'email' => 'approver@bizco.co.th',
                'password' => 'User@123',
                'full_name' => 'ผู้อนุมัติการจอง',
                'role' => 'approver',
                'department_id' => 2,
            ],
        ];
        
        // Add random users
        $thaiFirstNames = ['สมชาย', 'สมหญิง', 'ประเสริฐ', 'สุภาพร', 'วิชัย', 'อรุณ', 'พิมพ์ใจ', 'กิตติ', 'นภา', 'ธนา'];
        $thaiLastNames = ['ใจดี', 'มีสุข', 'รักษา', 'พัฒนา', 'สวัสดิ์', 'ศรีสุข', 'ทองคำ', 'แก้วมณี', 'วงศ์ไทย', 'จันทร์ดี'];
        
        for ($i = 1; $i <= 20; $i++) {
            $firstName = $thaiFirstNames[array_rand($thaiFirstNames)];
            $lastName = $thaiLastNames[array_rand($thaiLastNames)];
            $users[] = [
                'username' => 'user' . $i,
                'email' => "user{$i}@bizco.co.th",
                'password' => 'User@123',
                'full_name' => $firstName . " " . $lastName,
                'role' => 'user',
                'department_id' => rand(1, 10),
            ];
        }
        
        $count = 0;
        foreach ($users as $data) {
            $exists = User::findOne(['username' => $data['username']]);
            
            if (!$exists) {
                $user = new User();
                $user->username = $data['username'];
                $user->email = $data['email'];
                $user->setPassword($data['password']);
                $user->generateAuthKey();
                $user->full_name = $data['full_name'];
                $user->department_id = $data['department_id'];
                $user->status = User::STATUS_ACTIVE;
                
                if ($user->save(false)) {
                    $role = $auth->getRole($data['role']);
                    if ($role) {
                        $auth->assign($role, $user->id);
                    }
                    $count++;
                }
            }
        }
        
        $this->stdout("  ✓ Created {$count} users\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed equipment
     */
    public function actionEquipment(): int
    {
        $this->stdout("Seeding equipment...\n", Console::FG_YELLOW);
        
        // `id`, `equipment_code`, `category_id`, `name_th`, `name_en`, 
        // `brand`, `model`, `serial_number`, `building_id`, `storage_location`, 
        // `total_quantity`, `available_quantity`, `is_portable`, 
        // `hourly_rate`, `daily_rate`, `last_maintenance_date`, `next_maintenance_date`, 
        // `condition_status`, `description`, `usage_instructions`, `specifications`, 
        // `image`, `status`, `created_by`, `created_at`, `updated_at`        
        $equipment = [
            ['name_th' => 'โปรเจคเตอร์', 'name_en' => 'Projector', 'icon' => 'bi-projector', 'description' => 'Projector EPSON EB-X51', 'total_quantity' => 20, 'available_quantity' => 10],
            ['name_th' => 'จอ LED 65 นิ้ว', 'name_en' => 'LED Screen', 'icon' => 'bi-display', 'description' => 'ไมโครโฟนไร้สาย Shure', 'total_quantity' => 20, 'available_quantity' => 5],
            ['name_th' => 'ไวท์บอร์ด', 'name_en' => 'Whiteboard', 'icon' => 'bi-easel', 'description' => 'ไวท์บอร์ด 120x180 ซม.', 'total_quantity' => 15, 'available_quantity' => 15],
            ['name_th' => 'ระบบประชุมทางไกล', 'name_en' => 'Cisco WebEx Room Kit', 'icon' => 'bi-camera-video', 'description' => 'กล้อง Video Conference สำหรับประชุมออนไลน์', 'total_quantity' => 1, 'quantavailable_quantityity' => 1],
            ['name_th' => 'ไมโครโฟน', 'name_en' => 'Microphone', 'icon' => 'bi-mic', 'description' => 'ไมโครโฟนไร้สาย Shure', 'total_quantity' => 15, 'available_quantity' => 10],
            ['name_th' => 'ลำโพง', 'name_en' => 'Speaker', 'icon' => 'bi-speaker', 'description' => 'Sound Bar speaker', 'total_quantity' => 5, 'available_quantity' => 5],
            ['name_th' => 'โน้ตบุ๊ค', 'name_en' => 'Laptop', 'icon' => 'bi-laptop', 'description' => 'Notebook สำหรับนำเสนอ', 'total_quantity' => 35, 'available_quantity' => 35],
            ['name_th' => 'เครื่องฉายแผ่นใส', 'name_en' => 'Document Camera', 'icon' => 'bi-file-slides', 'description' => 'Projector EPSON EB-X51', 'total_quantity' => 20, 'available_quantity' => 10],
            ['name_th' => 'ระบบเสียง', 'name_en' => 'Sound System', 'icon' => 'bi-volume-up', 'description' => 'ชุดเครื่องเสียงพร้อมลำโพง', 'total_quantity' => 5, 'available_quantity' => 2],
            ['name_th' => 'กล้องถ่ายภาพ', 'name_en' => 'Camera', 'icon' => 'bi-camera', 'description' => 'Camera', 'total_quantity' => 15, 'available_quantity' => 10],
            ['name_th' => 'ชุดน้ำชา/กาแฟ', 'name_en' => 'Tea/Coffee Set', 'icon' => 'bi-cup-hot', 'description' => 'ชุดน้ำชา/กาแฟ', 'total_quantity' => 20, 'available_quantity' => 10],
            ['name_th' => 'WiFi', 'name_en' => 'WiFi', 'icon' => 'bi-wifi', 'description' => 'Pocket WiFI', 'total_quantity' => 30, 'available_quantity' => 10],     
            ['name_th' => 'Flipchart', 'name_en' => 'Flipchart', 'icon' => 'bi-flipchart', 'description' => 'กระดาษ Flipchart พร้อมขาตั้ง', 'total_quantity' => 5, 'available_quantity' => 5],  
            ['name_th' => 'ปลั๊กไฟพ่วง', 'name_en' => 'Electric Outlet', 'icon' => 'bi-wifi', 'description' => 'ปลั๊กพ่วง 6 ช่อง', 'total_quantity' => 40, 'available_quantity' => 40],   
        ];
        
        $count = 0;
        foreach ($equipment as $data) {
            $exists = Yii::$app->db->createCommand("SELECT id FROM {{%equipment}} WHERE name_th = :name_th")
                ->bindValue(':name', $data['name_th'])
                ->queryScalar();
            
            if (!$exists) {
                Yii::$app->db->createCommand()->insert('{{%equipment}}', array_merge($data, [
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]))->execute();
                $count++;
            }
        }
        
        $this->stdout("  ✓ Created {$count} equipment items\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed meeting rooms
     */
    public function actionRooms(): int
    {
        $this->stdout("Seeding meeting rooms...\n", Console::FG_YELLOW);
        
        $rooms = [
            [
                'name' => 'ห้องประชุมใหญ่ ชั้น 1',
                'code' => 'CONF-101',
                'building' => 'อาคารอำนวยการ',
                'floor' => '1',
                'capacity' => 100,
                'hourly_rate' => 500,
                'description' => 'ห้องประชุมใหญ่ รองรับได้ 100 ที่นั่ง พร้อมระบบเสียงและโปรเจคเตอร์',
                'amenities' => json_encode(['projector', 'sound_system', 'microphone', 'wifi', 'air_con']),
            ],
            [
                'name' => 'ห้องประชุม VIP',
                'code' => 'VIP-201',
                'building' => 'อาคารอำนวยการ',
                'floor' => '2',
                'capacity' => 20,
                'hourly_rate' => 800,
                'description' => 'ห้องประชุม VIP สำหรับผู้บริหาร พร้อมระบบ Video Conference',
                'amenities' => json_encode(['projector', 'video_conference', 'led_screen', 'wifi', 'air_con', 'coffee']),
            ],
            [
                'name' => 'ห้องประชุมย่อย A',
                'code' => 'MEET-301A',
                'building' => 'อาคารอำนวยการ',
                'floor' => '3',
                'capacity' => 10,
                'hourly_rate' => 200,
                'description' => 'ห้องประชุมย่อยสำหรับทีมงาน',
                'amenities' => json_encode(['whiteboard', 'projector', 'wifi', 'air_con']),
            ],
            [
                'name' => 'ห้องประชุมย่อย B',
                'code' => 'MEET-301B',
                'building' => 'อาคารอำนวยการ',
                'floor' => '3',
                'capacity' => 10,
                'hourly_rate' => 200,
                'description' => 'ห้องประชุมย่อยสำหรับทีมงาน',
                'amenities' => json_encode(['whiteboard', 'projector', 'wifi', 'air_con']),
            ],
            [
                'name' => 'ห้องอบรม ชั้น 4',
                'code' => 'TRAIN-401',
                'building' => 'อาคารอำนวยการ',
                'floor' => '4',
                'capacity' => 50,
                'hourly_rate' => 400,
                'description' => 'ห้องอบรมขนาดกลาง พร้อมคอมพิวเตอร์ 25 ชุด',
                'amenities' => json_encode(['projector', 'computers', 'wifi', 'air_con', 'whiteboard']),
            ],
            [
                'name' => 'ห้องประชุมออนไลน์',
                'code' => 'ONLINE-501',
                'building' => 'อาคารเทคโนโลยี',
                'floor' => '5',
                'capacity' => 15,
                'hourly_rate' => 350,
                'description' => 'ห้องประชุมสำหรับ Video Conference พร้อมกล้องและไมค์คุณภาพสูง',
                'amenities' => json_encode(['video_conference', 'led_screen', 'microphone', 'camera', 'wifi', 'air_con']),
            ],
            [
                'name' => 'ห้องประชุมบอร์ด',
                'code' => 'BOARD-601',
                'building' => 'อาคารอำนวยการ',
                'floor' => '6',
                'capacity' => 25,
                'hourly_rate' => 1000,
                'description' => 'ห้องประชุมคณะกรรมการ พร้อมระบบครบครัน',
                'amenities' => json_encode(['projector', 'video_conference', 'led_screen', 'sound_system', 'microphone', 'wifi', 'air_con', 'coffee']),
            ],
            [
                'name' => 'ห้องสัมมนา',
                'code' => 'SEM-701',
                'building' => 'อาคารวิชาการ',
                'floor' => '7',
                'capacity' => 200,
                'hourly_rate' => 1500,
                'description' => 'ห้องสัมมนาขนาดใหญ่ รองรับ 200 คน',
                'amenities' => json_encode(['projector', 'sound_system', 'microphone', 'stage', 'wifi', 'air_con']),
            ],
        ];
        
        $count = 0;
        foreach ($rooms as $data) {
            $exists = MeetingRoom::findOne(['code' => $data['code']]);
            
            if (!$exists) {
                $room = new MeetingRoom();
                $room->attributes = $data;
                $room->status = MeetingRoom::STATUS_AVAILABLE;
                $room->is_active = 1;
                
                if ($room->save(false)) {
                    $count++;
                }
            }
        }
        
        $this->stdout("  ✓ Created {$count} meeting rooms\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed sample bookings
     */
    public function actionBookings(): int
    {
        $this->stdout("Seeding bookings...\n", Console::FG_YELLOW);
        
        $users = User::find()->where(['status' => User::STATUS_ACTIVE])->all();
        $rooms = MeetingRoom::find()->where(['is_active' => 1])->all();
        
        if (empty($users) || empty($rooms)) {
            $this->stdout("  ⚠ No users or rooms found. Run seeder/users and seeder/rooms first.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        $subjects = [
            'ประชุมประจำเดือน',
            'อบรมพัฒนาบุคลากร',
            'ประชุมคณะกรรมการ',
            'สัมมนาวิชาการ',
            'ประชุมทีมงาน',
            'ประชุมหารือโครงการ',
            'อบรมระบบสารสนเทศ',
            'ประชุมติดตามงาน',
            'Workshop พัฒนาทักษะ',
            'ประชุมวางแผนงบประมาณ',
        ];
        
        $count = 0;
        
        // Create bookings for the past week
        for ($dayOffset = -7; $dayOffset <= 14; $dayOffset++) {
            $date = date('Y-m-d', strtotime("{$dayOffset} days"));
            
            // Skip weekends
            $dayOfWeek = date('N', strtotime($date));
            if ($dayOfWeek >= 6) continue;
            
            // Random number of bookings per day
            $bookingsPerDay = rand(2, 5);
            
            for ($i = 0; $i < $bookingsPerDay; $i++) {
                $user = $users[array_rand($users)];
                $room = $rooms[array_rand($rooms)];
                
                $startHour = rand(8, 15);
                $duration = rand(1, 3);
                $endHour = min($startHour + $duration, 18);
                
                $startTime = sprintf('%s %02d:00:00', $date, $startHour);
                $endTime = sprintf('%s %02d:00:00', $date, $endHour);
                
                // Check for conflicts
                $conflict = Booking::find()
                    ->where(['room_id' => $room->id])
                    ->andWhere(['<', 'start_time', $endTime])
                    ->andWhere(['>', 'end_time', $startTime])
                    ->exists();
                
                if ($conflict) continue;
                
                // Determine status based on date
                if ($dayOffset < 0) {
                    $status = rand(0, 10) > 1 ? Booking::STATUS_COMPLETED : Booking::STATUS_CANCELLED;
                } elseif ($dayOffset == 0) {
                    $status = Booking::STATUS_APPROVED;
                } else {
                    $status = rand(0, 10) > 2 ? Booking::STATUS_APPROVED : Booking::STATUS_PENDING;
                }
                
                $booking = new Booking();
                $booking->user_id = $user->id;
                $booking->room_id = $room->id;
                $booking->subject = $subjects[array_rand($subjects)];
                $booking->description = 'รายละเอียดการจองห้องประชุม';
                $booking->start_time = $startTime;
                $booking->end_time = $endTime;
                $booking->attendees_count = rand(5, $room->capacity);
                $booking->status = $status;
                $booking->approved_by = $status === Booking::STATUS_APPROVED ? 1 : null;
                $booking->approved_at = $status === Booking::STATUS_APPROVED ? date('Y-m-d H:i:s', strtotime($startTime . ' -1 day')) : null;
                
                if ($booking->save(false)) {
                    $count++;
                }
            }
        }
        
        $this->stdout("  ✓ Created {$count} bookings\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed system settings
     */
    public function actionSettings(): int
    {
        $this->stdout("Seeding settings...\n", Console::FG_YELLOW);
        
        $settings = [
            ['category' => 'general', 'key' => 'site_name', 'value' => 'ระบบจองห้องประชุม BiZCO'],
            ['category' => 'general', 'key' => 'site_name_en', 'value' => 'Meeting Room Booking System'],
            ['category' => 'general', 'key' => 'contact_email', 'value' => 'booking@pi.ac.th'],
            ['category' => 'general', 'key' => 'contact_phone', 'value' => '02-590-1000'],
            ['category' => 'booking', 'key' => 'working_hours_start', 'value' => '08:00'],
            ['category' => 'booking', 'key' => 'working_hours_end', 'value' => '18:00'],
            ['category' => 'booking', 'key' => 'min_duration', 'value' => '30'],
            ['category' => 'booking', 'key' => 'max_duration', 'value' => '480'],
            ['category' => 'booking', 'key' => 'max_advance_days', 'value' => '90'],
            ['category' => 'booking', 'key' => 'auto_approve', 'value' => '0'],
            ['category' => 'booking', 'key' => 'allow_weekend', 'value' => '0'],
            ['category' => 'notification', 'key' => 'email_enabled', 'value' => '1'],
            ['category' => 'notification', 'key' => 'reminder_before', 'value' => '60,15'],
        ];
        
        $count = 0;
        foreach ($settings as $data) {
            $exists = Yii::$app->db->createCommand("SELECT id FROM {{%setting}} WHERE `key` = :key")
                ->bindValue(':key', $data['key'])
                ->queryScalar();
            
            if (!$exists) {
                Yii::$app->db->createCommand()->insert('{{%setting}}', array_merge($data, [
                    'created_at' => date('Y-m-d H:i:s'),
                ]))->execute();
                $count++;
            }
        }
        
        $this->stdout("  ✓ Created {$count} settings\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Seed Thai holidays
     */
    public function actionHolidays(): int
    {
        $this->stdout("Seeding holidays...\n", Console::FG_YELLOW);
        
        $year = date('Y');
        
        $holidays = [
            ['date' => "{$year}-01-01", 'name' => 'วันขึ้นปีใหม่'],
            ['date' => "{$year}-02-26", 'name' => 'วันมาฆบูชา'],
            ['date' => "{$year}-04-06", 'name' => 'วันจักรี'],
            ['date' => "{$year}-04-13", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-04-14", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-04-15", 'name' => 'วันสงกรานต์'],
            ['date' => "{$year}-05-01", 'name' => 'วันแรงงานแห่งชาติ'],
            ['date' => "{$year}-05-04", 'name' => 'วันฉัตรมงคล'],
            ['date' => "{$year}-05-22", 'name' => 'วันวิสาขบูชา'],
            ['date' => "{$year}-06-03", 'name' => 'วันเฉลิมพระชนมพรรษาสมเด็จพระราชินี'],
            ['date' => "{$year}-07-20", 'name' => 'วันอาสาฬหบูชา'],
            ['date' => "{$year}-07-21", 'name' => 'วันเข้าพรรษา'],
            ['date' => "{$year}-07-28", 'name' => 'วันเฉลิมพระชนมพรรษา ร.10'],
            ['date' => "{$year}-08-12", 'name' => 'วันแม่แห่งชาติ'],
            ['date' => "{$year}-10-13", 'name' => 'วันคล้ายวันสวรรคต ร.9'],
            ['date' => "{$year}-10-23", 'name' => 'วันปิยมหาราช'],
            ['date' => "{$year}-12-05", 'name' => 'วันพ่อแห่งชาติ'],
            ['date' => "{$year}-12-10", 'name' => 'วันรัฐธรรมนูญ'],
            ['date' => "{$year}-12-31", 'name' => 'วันสิ้นปี'],
        ];
        
        $count = 0;
        foreach ($holidays as $data) {
            $exists = Yii::$app->db->createCommand("SELECT id FROM {{%holiday}} WHERE date = :date")
                ->bindValue(':date', $data['date'])
                ->queryScalar();
            
            if (!$exists) {
                Yii::$app->db->createCommand()->insert('{{%holiday}}', array_merge($data, [
                    'type' => 'national',
                    'is_recurring' => 0,
                    'status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                ]))->execute();
                $count++;
            }
        }
        
        $this->stdout("  ✓ Created {$count} holidays\n", Console::FG_GREEN);
        return ExitCode::OK;
    }
    
    /**
     * Reset and reseed all data
     * WARNING: This will delete all existing data!
     */
    public function actionReset(): int
    {
        if (!$this->confirm('This will DELETE all existing data. Are you sure?')) {
            return ExitCode::OK;
        }
        
        $this->stdout("Resetting database...\n", Console::FG_RED);
        
        // Disable foreign key checks
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        
        // Truncate tables
        $tables = [
            'booking_equipment',
            'booking',
            'room_equipment',
            'meeting_room',
            'equipment',
            'user_oauth',
            'notification',
            'auth_assignment',
            'user',
            'department',
            'setting',
            'holiday',
            'email_log',
            'audit_log',
        ];
        
        foreach ($tables as $table) {
            try {
                Yii::$app->db->createCommand("TRUNCATE TABLE {{%{$table}}}")->execute();
                $this->stdout("  Truncated {$table}\n");
            } catch (\Exception $e) {
                $this->stdout("  Skipped {$table} (may not exist)\n", Console::FG_YELLOW);
            }
        }
        
        // Re-enable foreign key checks
        Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS = 1')->execute();
        
        $this->stdout("\n", Console::FG_GREEN);
        
        // Re-seed all
        return $this->actionAll();
    }
}
