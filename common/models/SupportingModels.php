<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Equipment Model
 */
class Equipment extends ActiveRecord
{
    const STATUS_UNAVAILABLE = 0;
    const STATUS_AVAILABLE = 1;
    const STATUS_MAINTENANCE = 2;
    const STATUS_IN_USE = 3;
    const STATUS_RETIRED = 4;
    
    public $imageFile; // For file upload
    public $room_id; // For room assignment

    public static function tableName()
    {
        return '{{%equipment}}';
    }

    public function rules()
    {
        return [
            [['equipment_code', 'category_id', 'name_th', 'total_quantity'], 'required'],
            ['equipment_code', 'string', 'max' => 30],
            ['equipment_code', 'unique'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            [['brand', 'model', 'serial_number'], 'string', 'max' => 100],
            ['storage_location', 'string', 'max' => 255],
            [['category_id', 'building_id', 'total_quantity', 'available_quantity', 'status', 'created_by'], 'integer'],
            [['hourly_rate', 'daily_rate'], 'number', 'min' => 0],
            [['last_maintenance_date', 'next_maintenance_date'], 'date', 'format' => 'php:Y-m-d'],
            ['condition_status', 'in', 'range' => ['excellent', 'good', 'fair', 'poor']],
            [['description', 'usage_instructions', 'specifications'], 'string'],
            ['image', 'string', 'max' => 255],
            ['is_portable', 'boolean'],
            ['status', 'default', 'value' => self::STATUS_AVAILABLE],
            ['available_quantity', 'default', 'value' => 1],
            ['condition_status', 'default', 'value' => 'good'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_code' => 'รหัสอุปกรณ์',
            'category_id' => 'หมวดหมู่',
            'name_th' => 'ชื่ออุปกรณ์ (ไทย)',
            'name_en' => 'ชื่ออุปกรณ์ (English)',
            'brand' => 'ยี่ห้อ',
            'model' => 'รุ่น',
            'serial_number' => 'หมายเลขเครื่อง',
            'building_id' => 'อาคาร',
            'storage_location' => 'ตำแหน่งจัดเก็บ',
            'total_quantity' => 'จำนวนทั้งหมด',
            'available_quantity' => 'จำนวนที่พร้อมใช้',
            'is_portable' => 'เคลื่อนย้ายได้',
            'hourly_rate' => 'อัตรารายชั่วโมง',
            'daily_rate' => 'อัตรารายวัน',
            'condition_status' => 'สภาพอุปกรณ์',
            'status' => 'สถานะ',
            'description' => 'รายละเอียด',
        ];
    }

    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_UNAVAILABLE => '<span class="badge bg-secondary">ไม่พร้อมใช้</span>',
            self::STATUS_AVAILABLE => '<span class="badge bg-success">พร้อมใช้งาน</span>',
            self::STATUS_MAINTENANCE => '<span class="badge bg-warning text-dark">ซ่อมบำรุง</span>',
        ];
        return $labels[$this->status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    public function getCategory()
    {
        return $this->hasOne(EquipmentCategory::class, ['id' => 'category_id']);
    }

    public function getBuilding()
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_UNAVAILABLE => 'ไม่พร้อมใช้',
            self::STATUS_AVAILABLE => 'พร้อมใช้งาน',
            self::STATUS_MAINTENANCE => 'ซ่อมบำรุง',
        ];
    }

    public static function getConditionOptions()
    {
        return [
            'excellent' => 'ดีเยี่ยม',
            'good' => 'ดี',
            'fair' => 'พอใช้',
            'poor' => 'ต้องซ่อม',
        ];
    }
}

/**
 * Equipment Category Model
 */
class EquipmentCategory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%equipment_category}}';
    }

    public function rules()
    {
        return [
            [['code', 'name_th'], 'required'],
            ['code', 'string', 'max' => 20],
            ['code', 'unique'],
            [['name_th', 'name_en'], 'string', 'max' => 100],
            ['icon', 'string', 'max' => 50],
            ['description', 'string'],
            ['sort_order', 'integer'],
            ['is_active', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'รหัส',
            'name_th' => 'ชื่อหมวดหมู่ (ไทย)',
            'name_en' => 'ชื่อหมวดหมู่ (English)',
            'icon' => 'ไอคอน',
            'description' => 'รายละเอียด',
            'is_active' => 'ใช้งาน',
        ];
    }

    public function getEquipments()
    {
        return $this->hasMany(Equipment::class, ['category_id' => 'id']);
    }

    public static function getDropdownList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
}

/**
 * Building Model
 */
class Building extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%building}}';
    }

    public function rules()
    {
        return [
            [['code', 'name_th'], 'required'],
            ['code', 'string', 'max' => 20],
            ['code', 'unique'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            ['address', 'string'],
            [['latitude'], 'number', 'min' => -90, 'max' => 90],
            [['longitude'], 'number', 'min' => -180, 'max' => 180],
            ['floor_count', 'integer', 'min' => 1],
            ['is_active', 'boolean'],
            ['floor_count', 'default', 'value' => 1],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'รหัสอาคาร',
            'name_th' => 'ชื่ออาคาร (ไทย)',
            'name_en' => 'ชื่ออาคาร (English)',
            'address' => 'ที่อยู่',
            'latitude' => 'ละติจูด',
            'longitude' => 'ลองจิจูด',
            'floor_count' => 'จำนวนชั้น',
            'is_active' => 'ใช้งาน',
        ];
    }

    public function getRooms()
    {
        return $this->hasMany(MeetingRoom::class, ['building_id' => 'id']);
    }

    public static function getDropdownList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
}

/**
 * Department Model
 */
class Department extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%department}}';
    }

    public function rules()
    {
        return [
            [['code', 'name_th'], 'required'],
            ['code', 'string', 'max' => 20],
            ['code', 'unique'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            ['parent_id', 'integer'],
            ['parent_id', 'exist', 'targetClass' => self::class, 'targetAttribute' => 'id'],
            ['sort_order', 'integer'],
            ['is_active', 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'รหัส',
            'name_th' => 'ชื่อหน่วยงาน (ไทย)',
            'name_en' => 'ชื่อหน่วยงาน (English)',
            'parent_id' => 'หน่วยงานหลัก',
            'sort_order' => 'ลำดับ',
            'is_active' => 'ใช้งาน',
        ];
    }

    public function getParent()
    {
        return $this->hasOne(Department::class, ['id' => 'parent_id']);
    }

    public function getChildren()
    {
        return $this->hasMany(Department::class, ['parent_id' => 'id']);
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['department_id' => 'id']);
    }

    public static function getDropdownList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
}

/**
 * Room Image Model
 */
class RoomImage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%room_image}}';
    }

    public function rules()
    {
        return [
            [['room_id', 'filename', 'original_name', 'file_path'], 'required'],
            ['room_id', 'integer'],
            [['filename', 'original_name', 'alt_text'], 'string', 'max' => 255],
            ['file_path', 'string', 'max' => 500],
            ['mime_type', 'string', 'max' => 100],
            [['file_size', 'image_width', 'image_height', 'sort_order'], 'integer'],
            ['is_primary', 'boolean'],
        ];
    }

    public function getRoom()
    {
        return $this->hasOne(MeetingRoom::class, ['id' => 'room_id']);
    }

    public function getUrl()
    {
        return Yii::getAlias('@web/' . $this->file_path);
    }
}

/**
 * Room Equipment (Junction Table)
 */
class RoomEquipment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%room_equipment}}';
    }

    public function rules()
    {
        return [
            [['room_id', 'equipment_id'], 'required'],
            [['room_id', 'equipment_id', 'quantity'], 'integer'],
            ['is_included', 'boolean'],
            ['notes', 'string', 'max' => 255],
            ['quantity', 'default', 'value' => 1],
            ['is_included', 'default', 'value' => true],
        ];
    }

    public function getRoom()
    {
        return $this->hasOne(MeetingRoom::class, ['id' => 'room_id']);
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['id' => 'equipment_id']);
    }
}

/**
 * Booking Attendee Model
 */
class BookingAttendee extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%booking_attendee}}';
    }

    public function rules()
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id', 'user_id'], 'integer'],
            [['attendee_name', 'attendee_phone'], 'string', 'max' => 100],
            ['attendee_email', 'email'],
            ['attendee_email', 'string', 'max' => 255],
            ['is_organizer', 'boolean'],
            ['attendance_status', 'in', 'range' => ['pending', 'accepted', 'declined', 'tentative']],
            ['attendance_status', 'default', 'value' => 'pending'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'booking_id' => 'การจอง',
            'user_id' => 'ผู้ใช้',
            'attendee_name' => 'ชื่อ',
            'attendee_email' => 'อีเมล',
            'attendee_phone' => 'เบอร์โทร',
            'is_organizer' => 'ผู้จัด',
            'attendance_status' => 'สถานะ',
        ];
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

/**
 * Booking Equipment Model
 */
class BookingEquipment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%booking_equipment}}';
    }

    public function rules()
    {
        return [
            [['booking_id', 'equipment_id', 'quantity_requested'], 'required'],
            [['booking_id', 'equipment_id', 'quantity_requested', 'quantity_provided'], 'integer'],
            [['unit_price', 'total_price'], 'number', 'min' => 0],
            ['status', 'in', 'range' => ['pending', 'confirmed', 'delivered', 'returned']],
            ['condition_on_return', 'string', 'max' => 50],
            ['notes', 'string'],
            ['status', 'default', 'value' => 'pending'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'booking_id' => 'การจอง',
            'equipment_id' => 'อุปกรณ์',
            'quantity_requested' => 'จำนวนที่ขอ',
            'quantity_provided' => 'จำนวนที่จัดให้',
            'unit_price' => 'ราคาต่อหน่วย',
            'total_price' => 'ราคารวม',
            'status' => 'สถานะ',
        ];
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['id' => 'equipment_id']);
    }
}

/**
 * Audit Log Model
 */
class AuditLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%audit_log}}';
    }

    public function rules()
    {
        return [
            [['action'], 'required'],
            ['user_id', 'integer'],
            ['username', 'string', 'max' => 50],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string', 'max' => 500],
            ['action', 'string', 'max' => 50],
            ['model_class', 'string', 'max' => 100],
            ['model_id', 'string', 'max' => 50],
            [['old_values', 'new_values', 'description'], 'string'],
            ['url', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'ip_address' => 'IP Address',
            'action' => 'การกระทำ',
            'model_class' => 'Model',
            'model_id' => 'Record ID',
            'description' => 'รายละเอียด',
            'created_at' => 'เวลา',
        ];
    }

    /**
     * Log an action
     */
    public static function log($action, $modelClass = null, $modelId = null, $oldValues = [], $newValues = [], $description = null)
    {
        $log = new static();
        
        // Check if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $log->user_id = Yii::$app->user->id;
            $log->username = Yii::$app->user->identity->username ?? null;
        }
        
        // Get request info if available
        if (Yii::$app->has('request') && Yii::$app->request instanceof \yii\web\Request) {
            $log->ip_address = Yii::$app->request->userIP ?? '127.0.0.1';
            $log->user_agent = substr(Yii::$app->request->userAgent ?? '', 0, 500);
            $log->url = Yii::$app->request->absoluteUrl ?? null;
        } else {
            $log->ip_address = '127.0.0.1';
            $log->user_agent = 'Console';
            $log->url = null;
        }
        
        $log->action = $action;
        $log->model_class = $modelClass;
        $log->model_id = $modelId;
        $log->old_values = !empty($oldValues) ? json_encode($oldValues) : null;
        $log->new_values = !empty($newValues) ? json_encode($newValues) : null;
        $log->description = $description;
        
        return $log->save(false);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getActionLabel()
    {
        $labels = [
            'create' => '<span class="badge bg-success">สร้าง</span>',
            'update' => '<span class="badge bg-info">แก้ไข</span>',
            'delete' => '<span class="badge bg-danger">ลบ</span>',
            'login' => '<span class="badge bg-primary">เข้าสู่ระบบ</span>',
            'logout' => '<span class="badge bg-secondary">ออกจากระบบ</span>',
            'approve' => '<span class="badge bg-success">อนุมัติ</span>',
            'reject' => '<span class="badge bg-warning text-dark">ปฏิเสธ</span>',
        ];
        return $labels[$this->action] ?? '<span class="badge bg-secondary">' . $this->action . '</span>';
    }
}

/**
 * Notification Model
 */
class Notification extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%notification}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'type', 'title'], 'required'],
            ['user_id', 'integer'],
            ['type', 'string', 'max' => 50],
            ['title', 'string', 'max' => 255],
            [['message', 'data'], 'string'],
            ['link', 'string', 'max' => 500],
            ['is_read', 'boolean'],
            ['sent_email', 'boolean'],
            ['is_read', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'type' => 'ประเภท',
            'title' => 'หัวข้อ',
            'message' => 'ข้อความ',
            'is_read' => 'อ่านแล้ว',
            'created_at' => 'เวลา',
        ];
    }

    /**
     * Create notification
     */
    public static function create($userId, $type, $title, $message = null, $data = [], $link = null)
    {
        $notification = new static();
        $notification->user_id = $userId;
        $notification->type = $type;
        $notification->title = $title;
        $notification->message = $message;
        $notification->data = !empty($data) ? json_encode($data) : null;
        $notification->link = $link;
        return $notification->save();
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = date('Y-m-d H:i:s');
        return $this->save(false, ['is_read', 'read_at']);
    }

    /**
     * Get unread count for user
     */
    public static function getUnreadCount($userId)
    {
        return static::find()
            ->where(['user_id' => $userId, 'is_read' => false])
            ->count();
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

/**
 * System Setting Model
 */
class SystemSetting extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%system_setting}}';
    }

    public function rules()
    {
        return [
            [['setting_key'], 'required'],
            ['setting_key', 'string', 'max' => 100],
            ['setting_key', 'unique'],
            ['setting_value', 'string'],
            ['setting_type', 'in', 'range' => ['string', 'integer', 'boolean', 'json']],
            ['category', 'string', 'max' => 50],
            ['description', 'string'],
            ['is_public', 'boolean'],
            ['updated_by', 'integer'],
        ];
    }

    /**
     * Get setting value
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::find()->where(['setting_key' => $key])->one();
        
        if (!$setting) {
            return $default;
        }
        
        switch ($setting->setting_type) {
            case 'integer':
                return (int) $setting->setting_value;
            case 'boolean':
                return (bool) $setting->setting_value;
            case 'json':
                return json_decode($setting->setting_value, true);
            default:
                return $setting->setting_value;
        }
    }

    /**
     * Set setting value
     */
    public static function setValue($key, $value, $type = 'string')
    {
        $setting = static::find()->where(['setting_key' => $key])->one();
        
        if (!$setting) {
            $setting = new static();
            $setting->setting_key = $key;
        }
        
        $setting->setting_type = $type;
        
        if ($type === 'json') {
            $setting->setting_value = json_encode($value);
        } elseif ($type === 'boolean') {
            $setting->setting_value = $value ? '1' : '0';
        } else {
            $setting->setting_value = (string) $value;
        }
        
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $setting->updated_by = Yii::$app->user->id;
        }
        
        return $setting->save();
    }
}

/**
 * Login History Model
 */
class LoginHistory extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%login_history}}';
    }

    public function rules()
    {
        return [
            [['ip_address', 'login_method', 'login_status'], 'required'],
            ['user_id', 'integer'],
            ['username', 'string', 'max' => 50],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string'],
            ['login_method', 'in', 'range' => ['password', 'azure', 'google', 'thaid', 'facebook']],
            ['login_status', 'in', 'range' => ['success', 'failed', 'locked', 'captcha_required']],
            ['failure_reason', 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'ผู้ใช้',
            'ip_address' => 'IP Address',
            'login_method' => 'วิธีเข้าสู่ระบบ',
            'login_status' => 'สถานะ',
            'failure_reason' => 'เหตุผล',
            'created_at' => 'เวลา',
        ];
    }

    /**
     * Log login attempt
     */
    public static function logAttempt($userId, $username, $method, $status, $failureReason = null)
    {
        $log = new static();
        $log->user_id = $userId;
        $log->username = $username;
        $log->ip_address = Yii::$app->request->userIP ?? '127.0.0.1';
        $log->user_agent = Yii::$app->request->userAgent;
        $log->login_method = $method;
        $log->login_status = $status;
        $log->failure_reason = $failureReason;
        return $log->save(false);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getStatusLabel()
    {
        $labels = [
            'success' => '<span class="badge bg-success">สำเร็จ</span>',
            'failed' => '<span class="badge bg-danger">ล้มเหลว</span>',
            'locked' => '<span class="badge bg-warning text-dark">ถูกล็อค</span>',
            'captcha_required' => '<span class="badge bg-info">ต้องใช้ CAPTCHA</span>',
        ];
        return $labels[$this->login_status] ?? '<span class="badge bg-secondary">' . $this->login_status . '</span>';
    }
}

/**
 * Holiday Model
 */
class Holiday extends ActiveRecord
{
    const STATUS_ACTIVE = 1;

    public static function tableName()
    {
        return '{{%holiday}}';
    }

    public function rules()
    {
        return [
            [['holiday_date', 'name_th'], 'required'],
            ['holiday_date', 'date', 'format' => 'php:Y-m-d'],
            ['holiday_date', 'unique', 'message' => 'วันที่นี้มีการกำหนดเป็นวันหยุดแล้ว'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            ['holiday_type', 'in', 'range' => ['public', 'special', 'substitute', 'regional']],
            ['year', 'integer'],
            ['is_recurring', 'boolean'],
            ['holiday_type', 'default', 'value' => 'public'],
            ['is_recurring', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'holiday_date' => 'วันที่',
            'name_th' => 'ชื่อวันหยุด (ไทย)',
            'name_en' => 'ชื่อวันหยุด (English)',
            'holiday_type' => 'ประเภท',
            'year' => 'ปี',
            'is_recurring' => 'เกิดซ้ำทุกปี',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->holiday_date) {
                $this->year = date('Y', strtotime($this->holiday_date));
            }
            return true;
        }
        return false;
    }

    public static function getTypeOptions()
    {
        return [
            'public' => 'วันหยุดราชการ',
            'special' => 'วันหยุดพิเศษ',
            'substitute' => 'วันหยุดชดเชย',
            'regional' => 'วันหยุดท้องถิ่น',
        ];
    }

    public function getTypeLabel()
    {
        $types = self::getTypeOptions();
        return $types[$this->holiday_type] ?? $this->holiday_type;
    }

    public function getTypeBadge()
    {
        $badges = [
            'public' => '<span class="badge bg-danger">วันหยุดราชการ</span>',
            'special' => '<span class="badge bg-warning text-dark">วันหยุดพิเศษ</span>',
            'substitute' => '<span class="badge bg-info">วันหยุดชดเชย</span>',
            'regional' => '<span class="badge bg-secondary">วันหยุดท้องถิ่น</span>',
        ];
        return $badges[$this->holiday_type] ?? '<span class="badge bg-secondary">' . $this->holiday_type . '</span>';
    }

    /**
     * Check if a date is a holiday
     * @param string $date Date in Y-m-d format
     * @return bool
     */
    public static function isHoliday($date)
    {
        return static::find()->where(['holiday_date' => $date])->exists();
    }

    /**
     * Get holidays for a specific year
     * @param int $year
     * @return array
     */
    public static function getHolidaysForYear($year)
    {
        return static::find()
            ->where(['year' => $year])
            ->orderBy(['holiday_date' => SORT_ASC])
            ->all();
    }

    /**
     * Get holidays between two dates
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getHolidaysBetween($startDate, $endDate)
    {
        return static::find()
            ->where(['between', 'holiday_date', $startDate, $endDate])
            ->orderBy(['holiday_date' => SORT_ASC])
            ->all();
    }
}

/**
 * Email Template Model
 */
class EmailTemplate extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%email_template}}';
    }

    public function rules()
    {
        return [
            [['template_key', 'name', 'subject', 'body_html'], 'required'],
            ['template_key', 'string', 'max' => 50],
            ['template_key', 'unique'],
            ['template_key', 'match', 'pattern' => '/^[a-z0-9_]+$/'],
            ['name', 'string', 'max' => 100],
            ['subject', 'string', 'max' => 255],
            [['body_html', 'body_text', 'description'], 'string'],
            ['category', 'string', 'max' => 50],
            ['is_active', 'boolean'],
            ['is_system', 'boolean'],
            ['is_active', 'default', 'value' => true],
            ['is_system', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_key' => 'รหัสเทมเพลต',
            'name' => 'ชื่อเทมเพลต',
            'subject' => 'หัวเรื่อง',
            'body_html' => 'เนื้อหา HTML',
            'body_text' => 'เนื้อหา Text',
            'category' => 'หมวดหมู่',
            'description' => 'คำอธิบาย',
            'is_active' => 'ใช้งาน',
            'is_system' => 'เทมเพลตระบบ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }

    /**
     * Render subject with variables
     *
     * @param array $data
     * @return string
     */
    public function renderSubject($data = [])
    {
        return $this->replaceVariables($this->subject, $data);
    }

    /**
     * Render HTML body with variables
     *
     * @param array $data
     * @return string
     */
    public function renderBodyHtml($data = [])
    {
        return $this->replaceVariables($this->body_html, $data);
    }

    /**
     * Render text body with variables
     *
     * @param array $data
     * @return string
     */
    public function renderBodyText($data = [])
    {
        if ($this->body_text) {
            return $this->replaceVariables($this->body_text, $data);
        }
        return strip_tags($this->renderBodyHtml($data));
    }

    /**
     * Replace variables in text
     *
     * @param string $text
     * @param array $data
     * @return string
     */
    protected function replaceVariables($text, $data)
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }

    /**
     * Get available variables
     *
     * @return array
     */
    public static function getAvailableVariables()
    {
        return [
            'user_name' => 'ชื่อผู้ใช้',
            'user_email' => 'อีเมลผู้ใช้',
            'user_phone' => 'เบอร์โทรผู้ใช้',
            'booking_code' => 'รหัสการจอง',
            'room_name' => 'ชื่อห้องประชุม',
            'room_code' => 'รหัสห้องประชุม',
            'building_name' => 'ชื่ออาคาร',
            'booking_date' => 'วันที่จอง',
            'start_time' => 'เวลาเริ่ม',
            'end_time' => 'เวลาสิ้นสุด',
            'booking_title' => 'หัวข้อการประชุม',
            'attendees_count' => 'จำนวนผู้เข้าร่วม',
            'total_cost' => 'ค่าใช้จ่ายรวม',
            'site_name' => 'ชื่อเว็บไซต์',
            'site_url' => 'URL เว็บไซต์',
            'approval_link' => 'ลิงก์อนุมัติ',
            'rejection_reason' => 'เหตุผลปฏิเสธ',
            'cancellation_reason' => 'เหตุผลยกเลิก',
            'qr_code_url' => 'URL QR Code',
        ];
    }

    public static function getCategoryOptions()
    {
        return [
            'booking' => 'การจอง',
            'approval' => 'การอนุมัติ',
            'notification' => 'แจ้งเตือน',
            'user' => 'ผู้ใช้',
            'system' => 'ระบบ',
        ];
    }

    /**
     * Get template by key
     *
     * @param string $key
     * @return static|null
     */
    public static function getByKey($key)
    {
        return static::find()
            ->where(['template_key' => $key, 'is_active' => true])
            ->one();
    }

    /**
     * Send email using this template
     *
     * @param string $to
     * @param array $data
     * @param array $attachments
     * @return bool
     */
    public function send($to, $data = [], $attachments = [])
    {
        $message = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setSubject($this->renderSubject($data))
            ->setHtmlBody($this->renderBodyHtml($data));

        if ($this->body_text) {
            $message->setTextBody($this->renderBodyText($data));
        }

        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                $message->attach($attachment);
            } elseif (is_array($attachment)) {
                $message->attach($attachment['path'], $attachment['options'] ?? []);
            }
        }

        return $message->send();
    }

    /**
     * Get category badge
     *
     * @return string
     */
    public function getCategoryBadge()
    {
        $badges = [
            'booking' => '<span class="badge bg-primary">การจอง</span>',
            'approval' => '<span class="badge bg-success">การอนุมัติ</span>',
            'notification' => '<span class="badge bg-info">แจ้งเตือน</span>',
            'user' => '<span class="badge bg-warning text-dark">ผู้ใช้</span>',
            'system' => '<span class="badge bg-secondary">ระบบ</span>',
        ];
        return $badges[$this->category] ?? '<span class="badge bg-secondary">' . $this->category . '</span>';
    }
}

/**
 * Activity Log Model
 */
class ActivityLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%activity_log}}';
    }

    public function rules()
    {
        return [
            [['activity_type'], 'required'],
            ['user_id', 'integer'],
            [['activity_type', 'action'], 'string', 'max' => 50],
            ['model_class', 'string', 'max' => 100],
            ['model_id', 'string', 'max' => 50],
            [['description', 'old_values', 'new_values'], 'string'],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'activity_type' => 'ประเภท',
            'action' => 'การกระทำ',
            'model_class' => 'Model',
            'model_id' => 'Record ID',
            'description' => 'รายละเอียด',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'created_at' => 'เวลา',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Log user activity
     */
    public static function log($activityType, $action = null, $modelClass = null, $modelId = null, $description = null, $oldValues = [], $newValues = [])
    {
        $log = new static();
        
        // Check if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $log->user_id = Yii::$app->user->id;
        }
        
        $log->activity_type = $activityType;
        $log->action = $action;
        $log->model_class = $modelClass;
        $log->model_id = $modelId;
        $log->description = $description;
        $log->old_values = !empty($oldValues) ? json_encode($oldValues) : null;
        $log->new_values = !empty($newValues) ? json_encode($newValues) : null;
        
        // Get request info if available
        if (Yii::$app->has('request') && Yii::$app->request instanceof \yii\web\Request) {
            $log->ip_address = Yii::$app->request->userIP ?? '127.0.0.1';
            $log->user_agent = substr(Yii::$app->request->userAgent ?? '', 0, 500);
        } else {
            $log->ip_address = '127.0.0.1';
            $log->user_agent = 'Console';
        }
        
        return $log->save(false);
    }
}

/**
 * BookingEquipment Model
 */
class BookingEquipment extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%booking_equipment}}';
    }

    public function rules()
    {
        return [
            [['booking_id', 'equipment_id', 'quantity'], 'required'],
            [['booking_id', 'equipment_id', 'quantity'], 'integer'],
            ['quantity', 'integer', 'min' => 1],
            [['total_cost'], 'number'],
            ['notes', 'string'],
        ];
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['id' => 'equipment_id']);
    }
}

/**
 * BookingAttendee Model
 */
class BookingAttendee extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%booking_attendee}}';
    }

    public function rules()
    {
        return [
            [['booking_id'], 'required'],
            [['booking_id', 'user_id'], 'integer'],
            [['email'], 'email'],
            [['name'], 'string', 'max' => 255],
            ['is_external', 'boolean'],
            ['is_confirmed', 'boolean'],
        ];
    }

    public function getBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'booking_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

/**
 * EquipmentMaintenance Model
 */
class EquipmentMaintenance extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public static function tableName()
    {
        return '{{%equipment_maintenance}}';
    }

    public function rules()
    {
        return [
            [['equipment_id', 'maintenance_type', 'scheduled_date'], 'required'],
            [['equipment_id', 'performed_by', 'created_by'], 'integer'],
            ['maintenance_type', 'string', 'max' => 50],
            ['status', 'in', 'range' => [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED, self::STATUS_CANCELLED]],
            [['scheduled_date', 'completed_date'], 'date', 'format' => 'php:Y-m-d'],
            [['estimated_cost', 'actual_cost'], 'number'],
            [['description', 'notes'], 'string'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
        ];
    }

    public function attributeLabels()
    {
        return [
            'equipment_id' => 'อุปกรณ์',
            'maintenance_type' => 'ประเภทการบำรุงรักษา',
            'scheduled_date' => 'วันที่กำหนด',
            'completed_date' => 'วันที่แล้วเสร็จ',
            'status' => 'สถานะ',
            'estimated_cost' => 'ค่าใช้จ่ายประมาณ',
            'actual_cost' => 'ค่าใช้จ่ายจริง',
            'performed_by' => 'ดำเนินการโดย',
        ];
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['id' => 'equipment_id']);
    }

    public function getPerformedBy()
    {
        return $this->hasOne(User::class, ['id' => 'performed_by']);
    }

    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING => '<span class="badge bg-warning text-dark">รอดำเนินการ</span>',
            self::STATUS_IN_PROGRESS => '<span class="badge bg-info">กำลังดำเนินการ</span>',
            self::STATUS_COMPLETED => '<span class="badge bg-success">เสร็จสิ้น</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">ยกเลิก</span>',
        ];
        return $labels[$this->status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }
}

/**
 * Setting Model (alias for system settings)
 */
class Setting extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%system_setting}}';
    }

    public function rules()
    {
        return [
            [['key'], 'required'],
            ['key', 'string', 'max' => 100],
            ['key', 'unique'],
            ['value', 'string'],
            ['category', 'string', 'max' => 50],
            ['description', 'string'],
        ];
    }

    public static function get($key, $default = null)
    {
        $setting = static::find()->where(['key' => $key])->one();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $category = 'general')
    {
        $setting = static::find()->where(['key' => $key])->one();
        if (!$setting) {
            $setting = new static();
            $setting->key = $key;
            $setting->category = $category;
        }
        $setting->value = is_array($value) ? json_encode($value) : $value;
        return $setting->save();
    }
}

/**
 * RoomImage Model
 */
class RoomImage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%room_image}}';
    }

    public function rules()
    {
        return [
            [['room_id', 'image_path'], 'required'],
            ['room_id', 'integer'],
            [['image_path', 'caption'], 'string', 'max' => 255],
            ['is_primary', 'boolean'],
            ['sort_order', 'integer'],
            ['is_primary', 'default', 'value' => false],
            ['sort_order', 'default', 'value' => 0],
        ];
    }

    public function getRoom()
    {
        return $this->hasOne(MeetingRoom::class, ['id' => 'room_id']);
    }

    public function getUrl()
    {
        return $this->image_path ? Yii::$app->urlManager->createAbsoluteUrl(['/uploads/rooms/' . $this->image_path]) : null;
    }
}
