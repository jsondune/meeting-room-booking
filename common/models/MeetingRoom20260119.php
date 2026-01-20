<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * Meeting Room Model
 *
 * @property int $id
 * @property string $room_code
 * @property string $name_th
 * @property string|null $name_en
 * @property int $building_id
 * @property int $floor
 * @property string|null $room_number
 * @property int $capacity
 * @property string $room_type
 * @property string|null $room_layout
 * @property bool $has_projector
 * @property bool $has_video_conference
 * @property bool $has_whiteboard
 * @property bool $has_air_conditioning
 * @property bool $has_wifi
 * @property bool $has_audio_system
 * @property bool $has_recording
 * @property int $min_booking_duration
 * @property int $max_booking_duration
 * @property int $advance_booking_days
 * @property bool $requires_approval
 * @property string|null $allowed_departments
 * @property float $hourly_rate
 * @property float $half_day_rate
 * @property float $full_day_rate
 * @property string $operating_start_time
 * @property string $operating_end_time
 * @property string $available_days
 * @property string|null $description
 * @property string|null $usage_rules
 * @property string|null $contact_person
 * @property string|null $contact_phone
 * @property int $status
 * @property bool $is_featured
 * @property int $sort_order
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property Building $building
 * @property RoomImage[] $images
 * @property RoomEquipment[] $equipment
 * @property Booking[] $bookings
 */
class MeetingRoom extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_MAINTENANCE = 2;

    const TYPE_CONFERENCE = 'conference';
    const TYPE_TRAINING = 'training';
    const TYPE_BOARDROOM = 'boardroom';
    const TYPE_HUDDLE = 'huddle';
    const TYPE_AUDITORIUM = 'auditorium';

    const LAYOUT_THEATER = 'theater';
    const LAYOUT_CLASSROOM = 'classroom';
    const LAYOUT_USHAPE = 'u_shape';
    const LAYOUT_BOARDROOM = 'boardroom';
    const LAYOUT_BANQUET = 'banquet';

    /**
     * @var UploadedFile[] Image files for upload
     */
    public $imageFiles;

    /**
     * @var array Equipment IDs to attach
     */
    public $equipmentIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%meeting_room}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
        
        // Only add BlameableBehavior if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user')) {
            $behaviors[] = [
                'class' => BlameableBehavior::class,
            ];
        }
        
        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['name_th', 'building_id', 'capacity', 'room_type'], 'required'],
            
            // String fields
            ['room_code', 'string', 'max' => 20],
            ['room_code', 'unique', 'filter' => function ($query) {
                if (!$this->isNewRecord) {
                    $query->andWhere(['not', ['id' => $this->id]]);
                }
            }],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            ['room_number', 'string', 'max' => 20],
            ['room_type', 'string', 'max' => 50],
            ['room_type', 'in', 'range' => array_keys(self::getTypeOptions())],
            ['room_layout', 'string', 'max' => 50],
            ['room_layout', 'in', 'range' => array_keys(self::getLayoutOptions())],
            
            // Integer fields
            [['building_id', 'capacity', 'min_booking_duration', 'max_booking_duration', 'advance_booking_days', 'status', 'sort_order', 'floor'], 'integer'],
            ['building_id', 'exist', 'targetClass' => Building::class, 'targetAttribute' => 'id'],
            ['capacity', 'integer', 'min' => 1, 'max' => 1000],
            ['floor', 'integer', 'min' => -10, 'max' => 100],
            ['min_booking_duration', 'integer', 'min' => 15, 'max' => 480],
            ['max_booking_duration', 'integer', 'min' => 30, 'max' => 1440],
            ['advance_booking_days', 'integer', 'min' => 1, 'max' => 365],
            
            // Boolean fields
            [['has_projector', 'has_video_conference', 'has_whiteboard', 'has_air_conditioning', 
              'has_wifi', 'has_audio_system', 'has_recording', 'requires_approval', 'is_featured'], 'boolean'],
            
            // Decimal fields
            [['hourly_rate', 'half_day_rate', 'full_day_rate'], 'number', 'min' => 0],
            
            // Time fields
            [['operating_start_time', 'operating_end_time'], 'time', 'format' => 'php:H:i:s'],
            ['operating_end_time', 'compare', 'compareAttribute' => 'operating_start_time', 'operator' => '>', 'message' => 'End time must be after start time.'],
            
            // Text fields
            [['description', 'usage_rules', 'allowed_departments'], 'string'],
            ['contact_person', 'string', 'max' => 100],
            ['contact_phone', 'string', 'max' => 20],
            
            // Available days (comma-separated: 0=Sun, 1=Mon, etc.)
            ['available_days', 'string', 'max' => 20],
            ['available_days', 'match', 'pattern' => '/^[0-6](,[0-6])*$/', 'message' => 'Invalid days format.'],
            
            // Status
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE, self::STATUS_MAINTENANCE]],
            
            // Defaults
            ['floor', 'default', 'value' => 1],
            ['min_booking_duration', 'default', 'value' => 30],
            ['max_booking_duration', 'default', 'value' => 480],
            ['advance_booking_days', 'default', 'value' => 30],
            ['operating_start_time', 'default', 'value' => '08:00:00'],
            ['operating_end_time', 'default', 'value' => '18:00:00'],
            ['available_days', 'default', 'value' => '1,2,3,4,5'],
            ['sort_order', 'default', 'value' => 0],
            ['is_featured', 'default', 'value' => false],
            
            // File upload
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp', 'maxFiles' => 10, 'maxSize' => 5 * 1024 * 1024],
            
            // Equipment selection
            ['equipmentIds', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_code' => 'รหัสห้อง',
            'name_th' => 'ชื่อห้อง (ภาษาไทย)',
            'name_en' => 'ชื่อห้อง (English)',
            'building_id' => 'อาคาร',
            'floor' => 'ชั้น',
            'room_number' => 'เลขที่ห้อง',
            'capacity' => 'ความจุ (คน)',
            'room_type' => 'ประเภทห้อง',
            'room_layout' => 'รูปแบบการจัดห้อง',
            'has_projector' => 'มีเครื่องฉาย',
            'has_video_conference' => 'มีระบบ Video Conference',
            'has_whiteboard' => 'มีกระดานไวท์บอร์ด',
            'has_air_conditioning' => 'มีเครื่องปรับอากาศ',
            'has_wifi' => 'มี WiFi',
            'has_audio_system' => 'มีระบบเสียง',
            'has_recording' => 'มีระบบบันทึก',
            'min_booking_duration' => 'ระยะเวลาจองขั้นต่ำ (นาที)',
            'max_booking_duration' => 'ระยะเวลาจองสูงสุด (นาที)',
            'advance_booking_days' => 'จองล่วงหน้าได้ (วัน)',
            'requires_approval' => 'ต้องรออนุมัติ',
            'allowed_departments' => 'หน่วยงานที่อนุญาต',
            'hourly_rate' => 'อัตราค่าใช้จ่าย/ชั่วโมง',
            'half_day_rate' => 'อัตราค่าใช้จ่าย/ครึ่งวัน',
            'full_day_rate' => 'อัตราค่าใช้จ่าย/วัน',
            'operating_start_time' => 'เวลาเปิดให้บริการ',
            'operating_end_time' => 'เวลาปิดให้บริการ',
            'available_days' => 'วันที่เปิดให้บริการ',
            'description' => 'รายละเอียด',
            'usage_rules' => 'กฎการใช้งาน',
            'contact_person' => 'ผู้ติดต่อ',
            'contact_phone' => 'เบอร์ติดต่อ',
            'status' => 'สถานะ',
            'is_featured' => 'แสดงหน้าแรก',
            'sort_order' => 'ลำดับการแสดง',
            'imageFiles' => 'รูปภาพห้องประชุม',
            'equipmentIds' => 'อุปกรณ์ประจำห้อง',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    /**
     * Generate room code before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->room_code)) {
                $this->room_code = $this->generateRoomCode();
            }
            return true;
        }
        return false;
    }

    /**
     * Generate unique room code
     */
    protected function generateRoomCode()
    {
        $prefix = 'RM';
        $year = date('y');
        
        // Find the last code for this year
        $lastRoom = static::find()
            ->where(['like', 'room_code', "{$prefix}{$year}%", false])
            ->orderBy(['room_code' => SORT_DESC])
            ->one();
        
        if ($lastRoom) {
            $lastNumber = (int) substr($lastRoom->room_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * After save - handle images and equipment
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Handle image uploads
        if ($this->imageFiles) {
            $this->uploadImages();
        }
        
        // Handle equipment assignments
        if (!empty($this->equipmentIds)) {
            $this->saveEquipment();
        }
        
        // Log audit
        AuditLog::log(
            $insert ? 'create' : 'update',
            static::class,
            $this->id,
            $insert ? [] : $changedAttributes,
            $this->attributes
        );
    }

    /**
     * Upload room images
     */
    public function uploadImages()
    {
        $uploadPath = Yii::getAlias('@uploads/rooms/' . $this->id);
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $existingCount = RoomImage::find()->where(['room_id' => $this->id])->count();
        $maxImages = 10;
        
        foreach ($this->imageFiles as $index => $file) {
            if ($existingCount >= $maxImages) {
                break;
            }
            
            $filename = Yii::$app->security->generateRandomString(16) . '.' . $file->extension;
            $filePath = $uploadPath . '/' . $filename;
            
            if ($file->saveAs($filePath)) {
                $image = new RoomImage();
                $image->room_id = $this->id;
                $image->filename = $filename;
                $image->original_name = $file->baseName . '.' . $file->extension;
                $image->file_path = 'uploads/rooms/' . $this->id . '/' . $filename;
                $image->file_size = $file->size;
                $image->mime_type = $file->type;
                
                // Get image dimensions
                $imageInfo = getimagesize($filePath);
                if ($imageInfo) {
                    $image->image_width = $imageInfo[0];
                    $image->image_height = $imageInfo[1];
                }
                
                // Set first image as primary
                $image->is_primary = ($existingCount == 0 && $index == 0);
                $image->sort_order = $existingCount + $index;
                
                $image->save();
                $existingCount++;
            }
        }
    }

    /**
     * Save equipment assignments
     */
    public function saveEquipment()
    {
        // Delete existing assignments
        RoomEquipment::deleteAll(['room_id' => $this->id]);
        
        // Add new assignments
        foreach ($this->equipmentIds as $equipmentId) {
            $roomEquipment = new RoomEquipment();
            $roomEquipment->room_id = $this->id;
            $roomEquipment->equipment_id = $equipmentId;
            $roomEquipment->quantity = 1;
            $roomEquipment->is_included = true;
            $roomEquipment->save();
        }
    }

    /**
     * Soft delete
     */
    public function softDelete()
    {
        $this->deleted_at = date('Y-m-d H:i:s');
        $this->status = self::STATUS_INACTIVE;
        return $this->save(false, ['deleted_at', 'status']);
    }

    /**
     * Restore soft deleted
     */
    public function restore()
    {
        $this->deleted_at = null;
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false, ['deleted_at', 'status']);
    }

    /**
     * Check if room is available at given time
     */
    public function isAvailable($date, $startTime, $endTime, $excludeBookingId = null)
    {
        // Normalize time format to H:i for comparison
        $startTime = date('H:i', strtotime($startTime));
        $endTime = date('H:i', strtotime($endTime));
        
        // Check if date is in available days (skip if not configured)
        if (!empty($this->available_days)) {
            $dayOfWeek = (int) date('w', strtotime($date)); // 0=Sunday, 1=Monday, etc.
            
            // Parse available_days (could be JSON array or comma-separated)
            $availableDays = $this->available_days;
            if (is_string($availableDays)) {
                $decoded = json_decode($availableDays, true);
                if (is_array($decoded)) {
                    $availableDays = $decoded;
                } else {
                    $availableDays = array_map('intval', explode(',', $availableDays));
                }
            }
            
            if (!empty($availableDays) && !in_array($dayOfWeek, $availableDays)) {
                return false;
            }
        }
        
        // Check if time is within operating hours (skip if not configured)
        if (!empty($this->operating_start_time) && !empty($this->operating_end_time)) {
            $operatingStart = date('H:i', strtotime($this->operating_start_time));
            $operatingEnd = date('H:i', strtotime($this->operating_end_time));
            
            if ($startTime < $operatingStart || $endTime > $operatingEnd) {
                return false;
            }
        }
        
        // Check for conflicting bookings
        $query = Booking::find()
            ->where(['room_id' => $this->id])
            ->andWhere(['booking_date' => $date])
            ->andWhere(['not in', 'status', ['cancelled', 'rejected']])
            ->andWhere([
                'or',
                ['and', ['<', 'start_time', $endTime . ':00'], ['>', 'end_time', $startTime . ':00']],
                ['and', ['<', 'start_time', $endTime], ['>', 'end_time', $startTime]],
            ]);
        
        if ($excludeBookingId) {
            $query->andWhere(['not', ['id' => $excludeBookingId]]);
        }
        
        return !$query->exists();
    }

    /**
     * Get available time slots for a date
     */
    public function getAvailableSlots($date, $duration = 60)
    {
        $dayOfWeek = (int) date('w', strtotime($date));
        
        // Parse available_days
        $availableDays = $this->available_days;
        if (is_string($availableDays)) {
            $decoded = json_decode($availableDays, true);
            if (is_array($decoded)) {
                $availableDays = $decoded;
            } else {
                $availableDays = array_map('intval', explode(',', $availableDays));
            }
        }
        
        if (!empty($availableDays) && !in_array($dayOfWeek, $availableDays)) {
            return [];
        }
        
        // Get existing bookings for the date
        $bookings = Booking::find()
            ->where(['room_id' => $this->id, 'booking_date' => $date])
            ->andWhere(['not in', 'status', ['cancelled', 'rejected']])
            ->orderBy(['start_time' => SORT_ASC])
            ->all();
        
        $slots = [];
        $currentTime = strtotime($date . ' ' . $this->operating_start_time);
        $endOfDay = strtotime($date . ' ' . $this->operating_end_time);
        $durationSeconds = $duration * 60;
        
        foreach ($bookings as $booking) {
            $bookingStart = strtotime($date . ' ' . $booking->start_time);
            
            // Add slots before this booking
            while ($currentTime + $durationSeconds <= $bookingStart) {
                $slots[] = [
                    'start' => date('H:i', $currentTime),
                    'end' => date('H:i', $currentTime + $durationSeconds),
                ];
                $currentTime += $this->min_booking_duration * 60;
            }
            
            $currentTime = strtotime($date . ' ' . $booking->end_time);
        }
        
        // Add remaining slots after last booking
        while ($currentTime + $durationSeconds <= $endOfDay) {
            $slots[] = [
                'start' => date('H:i', $currentTime),
                'end' => date('H:i', $currentTime + $durationSeconds),
            ];
            $currentTime += $this->min_booking_duration * 60;
        }
        
        return $slots;
    }

    /**
     * Get bookings for a date range
     */
    public function getBookingsForDateRange($startDate, $endDate)
    {
        return Booking::find()
            ->where(['room_id' => $this->id])
            ->andWhere(['>=', 'booking_date', $startDate])
            ->andWhere(['<=', 'booking_date', $endDate])
            ->andWhere(['not in', 'status', ['cancelled', 'rejected']])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->all();
    }

    /**
     * Calculate booking cost
     */
    public function calculateCost($startTime, $endTime)
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $durationHours = ($end - $start) / 3600;
        
        if ($durationHours >= 8 && $this->full_day_rate > 0) {
            return $this->full_day_rate;
        }
        
        if ($durationHours >= 4 && $this->half_day_rate > 0) {
            return $this->half_day_rate;
        }
        
        return $this->hourly_rate * ceil($durationHours);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_INACTIVE => '<span class="badge bg-secondary">ไม่พร้อมใช้งาน</span>',
            self::STATUS_ACTIVE => '<span class="badge bg-success">พร้อมใช้งาน</span>',
            self::STATUS_MAINTENANCE => '<span class="badge bg-warning text-dark">ปิดปรับปรุง</span>',
        ];
        return $labels[$this->status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get room type label
     */
    public function getTypeLabel()
    {
        $types = self::getTypeOptions();
        return $types[$this->room_type] ?? $this->room_type;
    }

    /**
     * Get layout label
     */
    public function getLayoutLabel()
    {
        $layouts = self::getLayoutOptions();
        return $layouts[$this->room_layout] ?? $this->room_layout;
    }

    /**
     * Get display name
     */
    public function getDisplayName()
    {
        $name = $this->name_th;
        if ($this->room_number) {
            $name .= ' (' . $this->room_number . ')';
        }
        return $name;
    }

    /**
     * Get full location
     */
    public function getFullLocation()
    {
        $parts = [];
        if ($this->building) {
            $parts[] = $this->building->name_th;
        }
        $parts[] = 'ชั้น ' . $this->floor;
        if ($this->room_number) {
            $parts[] = 'ห้อง ' . $this->room_number;
        }
        return implode(' ', $parts);
    }

    /**
     * Get primary image URL
     */
    public function getPrimaryImageUrl()
    {
        $image = RoomImage::find()
            ->where(['room_id' => $this->id, 'is_primary' => true])
            ->one();
        
        if ($image) {
            return Yii::getAlias('@web/' . $image->file_path);
        }
        
        // Return default image
        return Yii::getAlias('@web/images/room-default.jpg');
    }

    /**
     * Get features list
     */
    public function getFeaturesList()
    {
        $features = [];
        
        if ($this->has_projector) $features[] = 'เครื่องฉาย';
        if ($this->has_video_conference) $features[] = 'Video Conference';
        if ($this->has_whiteboard) $features[] = 'ไวท์บอร์ด';
        if ($this->has_air_conditioning) $features[] = 'ปรับอากาศ';
        if ($this->has_wifi) $features[] = 'WiFi';
        if ($this->has_audio_system) $features[] = 'ระบบเสียง';
        if ($this->has_recording) $features[] = 'บันทึกการประชุม';
        
        return $features;
    }

    /**
     * Get type options for dropdown
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_CONFERENCE => 'ห้องประชุม (Conference)',
            self::TYPE_TRAINING => 'ห้องอบรม (Training)',
            self::TYPE_BOARDROOM => 'ห้องประชุมผู้บริหาร (Boardroom)',
            self::TYPE_HUDDLE => 'ห้องประชุมขนาดเล็ก (Huddle)',
            self::TYPE_AUDITORIUM => 'ห้องประชุมใหญ่ (Auditorium)',
        ];
    }

    /**
     * Get room types (alias for getTypeOptions)
     */
    public static function getRoomTypes()
    {
        return self::getTypeOptions();
    }

    /**
     * Get layout options for dropdown
     */
    public static function getLayoutOptions()
    {
        return [
            self::LAYOUT_THEATER => 'แบบโรงละคร (Theater)',
            self::LAYOUT_CLASSROOM => 'แบบห้องเรียน (Classroom)',
            self::LAYOUT_USHAPE => 'แบบตัว U (U-Shape)',
            self::LAYOUT_BOARDROOM => 'แบบห้องประชุม (Boardroom)',
            self::LAYOUT_BANQUET => 'แบบโต๊ะกลม (Banquet)',
        ];
    }

    /**
     * Get layout types (alias for getLayoutOptions)
     */
    public static function getLayoutTypes()
    {
        return self::getLayoutOptions();
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'ไม่พร้อมใช้งาน',
            self::STATUS_ACTIVE => 'พร้อมใช้งาน',
            self::STATUS_MAINTENANCE => 'ปิดปรับปรุง',
        ];
    }

    /**
     * Get status labels (alias for getStatusOptions)
     */
    public static function getStatusLabels()
    {
        return self::getStatusOptions();
    }

    /**
     * Get capacity ranges for filter
     */
    public static function getCapacityRanges()
    {
        return [
            '1-10' => '1-10 คน',
            '11-20' => '11-20 คน',
            '21-50' => '21-50 คน',
            '51-100' => '51-100 คน',
            '101+' => 'มากกว่า 100 คน',
        ];
    }

    /**
     * Get day options for available days
     */
    public static function getDayOptions()
    {
        return [
            '0' => 'อาทิตย์',
            '1' => 'จันทร์',
            '2' => 'อังคาร',
            '3' => 'พุธ',
            '4' => 'พฤหัสบดี',
            '5' => 'ศุกร์',
            '6' => 'เสาร์',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBuilding()
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(RoomImage::class, ['room_id' => 'id'])->orderBy(['sort_order' => SORT_ASC]);
    }

    /**
     * Get primary image filename
     * @return string|null
     */
    public function getImage()
    {
        $primaryImage = $this->getPrimaryImage();
        return $primaryImage ? $primaryImage->filename : null;
    }

    /**
     * Get primary RoomImage object
     * @return RoomImage|null
     */
    public function getPrimaryImage()
    {
        return RoomImage::find()
            ->where(['room_id' => $this->id])
            ->orderBy(['is_primary' => SORT_DESC, 'sort_order' => SORT_ASC])
            ->one();
    }

    /**
     * Get image URL with fallback
     * @return string
     */
    public function getImageUrl()
    {
        $primaryImage = $this->getPrimaryImage();
        if ($primaryImage && $primaryImage->file_path) {
            return Yii::getAlias('@web/' . $primaryImage->file_path);
        }
        // Fallback to placeholder
        $text = urlencode($this->name_th ?? 'Meeting Room');
        return "https://via.placeholder.com/400x250/4a90a4/ffffff?text={$text}";
    }

    /**
     * Get gallery images (JSON for compatibility)
     * @return string|null JSON array of image paths
     */
    public function getGalleryImages()
    {
        $images = RoomImage::find()
            ->where(['room_id' => $this->id])
            ->orderBy(['sort_order' => SORT_ASC])
            ->all();
        
        if (empty($images)) {
            return null;
        }
        
        $paths = array_map(function($img) {
            return $img->file_path;
        }, $images);
        
        return json_encode($paths);
    }

    /**
     * Get all room images as objects
     * @return RoomImage[]
     */
    public function getRoomImages()
    {
        return RoomImage::find()
            ->where(['room_id' => $this->id])
            ->orderBy(['is_primary' => SORT_DESC, 'sort_order' => SORT_ASC])
            ->all();
    }

    /**
     * Alias for name_th (backward compatibility)
     * @return string|null
     */
    public function getName()
    {
        return $this->name_th;
    }

    /**
     * Get location string (building + floor + room_number)
     * @return string
     */
    public function getLocation()
    {
        $parts = [];
        if ($this->building) {
            $parts[] = $this->building->name_th;
        }
        if ($this->floor) {
            $parts[] = 'ชั้น ' . $this->floor;
        }
        if ($this->room_number) {
            $parts[] = 'ห้อง ' . $this->room_number;
        }
        return implode(' ', $parts) ?: 'ไม่ระบุ';
    }

    /**
     * Get amenities as JSON string (built from has_* columns)
     * @return string JSON array of amenity codes
     */
    public function getAmenities()
    {
        $amenities = [];
        
        if ($this->has_projector) {
            $amenities[] = 'projector';
        }
        if ($this->has_video_conference) {
            $amenities[] = 'video_conference';
        }
        if ($this->has_whiteboard) {
            $amenities[] = 'whiteboard';
        }
        if ($this->has_air_conditioning) {
            $amenities[] = 'air_conditioning';
        }
        if ($this->has_wifi) {
            $amenities[] = 'wifi';
        }
        
        // Add from room equipment if available
        if ($this->roomEquipment) {
            foreach ($this->roomEquipment as $re) {
                if ($re->equipment) {
                    $code = strtolower(str_replace(' ', '_', $re->equipment->name_en ?? ''));
                    if (!empty($code) && !in_array($code, $amenities)) {
                        $amenities[] = $code;
                    }
                }
            }
        }
        
        return json_encode($amenities);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomEquipment()
    {
        return $this->hasMany(RoomEquipment::class, ['room_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipments()
    {
        return $this->hasMany(Equipment::class, ['id' => 'equipment_id'])
            ->via('roomEquipment');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookings()
    {
        return $this->hasMany(Booking::class, ['room_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Find active rooms only
     */
    public static function findActive()
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['deleted_at' => null]);
    }

    /**
     * Get rooms for dropdown
     */
    public static function getDropdownList()
    {
        $rooms = static::findActive()
            ->orderBy(['building_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();
        
        return ArrayHelper::map($rooms, 'id', function ($room) {
            return $room->getDisplayName() . ' (รองรับ ' . $room->capacity . ' คน)';
        });
    }
}
