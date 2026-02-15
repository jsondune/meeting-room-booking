<?php
/**
 * Equipment Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
            // Image file upload validation
            ['imageFile', 'file', 
                'skipOnEmpty' => true, 
                'extensions' => 'png, jpg, jpeg, gif, webp',
                'mimeTypes' => 'image/*',
                'maxSize' => 2 * 1024 * 1024, // 2MB
                'tooBig' => 'ไฟล์ต้องมีขนาดไม่เกิน 2MB',
                'wrongExtension' => 'รองรับเฉพาะไฟล์ PNG, JPG, JPEG, GIF, WEBP',
            ],
            // Virtual properties for backward compatibility
            [['quantity', 'unit', 'purchase_price', 'rental_rate', 'purchase_date', 'warranty_expiry', 'notes'], 'safe'],
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
            'last_maintenance_date' => 'วันที่บำรุงรักษาล่าสุด',
            'next_maintenance_date' => 'วันที่บำรุงรักษาถัดไป',
            'status' => 'สถานะ',
            'description' => 'รายละเอียด',
            'usage_instructions' => 'วิธีใช้งาน',
            'specifications' => 'สเปค',
            'image' => 'รูปภาพ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_UNAVAILABLE => '<span class="badge bg-secondary">ไม่พร้อมใช้</span>',
            self::STATUS_AVAILABLE => '<span class="badge bg-success">พร้อมใช้งาน</span>',
            self::STATUS_MAINTENANCE => '<span class="badge bg-warning text-dark">ซ่อมบำรุง</span>',
            self::STATUS_IN_USE => '<span class="badge bg-info">กำลังใช้งาน</span>',
            self::STATUS_RETIRED => '<span class="badge bg-dark">ปลดระวาง</span>',
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

    /**
     * Get rooms this equipment is assigned to (many-to-many through room_equipment)
     */
    public function getRooms()
    {
        return $this->hasMany(MeetingRoom::class, ['id' => 'room_id'])
            ->viaTable('{{%room_equipment}}', ['equipment_id' => 'id']);
    }

    /**
     * Get room equipment assignments
     */
    public function getRoomEquipments()
    {
        return $this->hasMany(RoomEquipment::class, ['equipment_id' => 'id']);
    }

    public static function getStatusOptions()
    {
        return [
            self::STATUS_UNAVAILABLE => 'ไม่พร้อมใช้',
            self::STATUS_AVAILABLE => 'พร้อมใช้งาน',
            self::STATUS_MAINTENANCE => 'ซ่อมบำรุง',
            self::STATUS_IN_USE => 'กำลังใช้งาน',
            self::STATUS_RETIRED => 'ปลดระวาง',
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
    
    public function getConditionLabel()
    {
        $options = self::getConditionOptions();
        return $options[$this->condition_status] ?? $this->condition_status;
    }
    
    public static function getDropdownList()
    {
        return self::find()
            ->where(['status' => self::STATUS_AVAILABLE])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
    
    public static function getPortableEquipment()
    {
        return self::find()
            ->where(['is_portable' => true, 'status' => self::STATUS_AVAILABLE])
            ->all();
    }
    
    public function getDisplayName()
    {
        return $this->name_th . ($this->name_en ? " ({$this->name_en})" : '');
    }

    /**
     * Alias for total_quantity (backward compatibility for quantity)
     * @return int
     */
    public function getQuantity()
    {
        return $this->total_quantity;
    }

    /**
     * Setter for quantity (backward compatibility)
     * @param int $value
     */
    public function setQuantity($value)
    {
        $this->total_quantity = $value;
    }

    /**
     * Alias for category_id (backward compatibility for category)
     * @return int|null
     */
    public function getCategory_attr()
    {
        return $this->category_id;
    }

    /**
     * Setter for category (backward compatibility)
     * @param int $value
     */
    public function setCategory_attr($value)
    {
        $this->category_id = $value;
    }

    /**
     * Virtual property: unit
     * @return string
     */
    public function getUnit()
    {
        return 'ชิ้น';
    }

    /**
     * Setter for unit (no-op)
     * @param string $value
     */
    public function setUnit($value)
    {
        // No actual column
    }

    /**
     * Alias for daily_rate (backward compatibility for purchase_price)
     * @return float
     */
    public function getPurchase_price()
    {
        return $this->daily_rate ?? 0;
    }

    /**
     * Setter for purchase_price (backward compatibility)
     * @param float $value
     */
    public function setPurchase_price($value)
    {
        // No actual column
    }

    /**
     * Alias for hourly_rate (backward compatibility for rental_rate)
     * @return float
     */
    public function getRental_rate()
    {
        return $this->hourly_rate ?? 0;
    }

    /**
     * Setter for rental_rate (backward compatibility)
     * @param float $value
     */
    public function setRental_rate($value)
    {
        $this->hourly_rate = $value;
    }

    /**
     * Virtual property: purchase_date
     * @return string|null
     */
    public function getPurchase_date()
    {
        return $this->last_maintenance_date;
    }

    /**
     * Setter for purchase_date (no-op)
     * @param string $value
     */
    public function setPurchase_date($value)
    {
        // No actual column
    }

    /**
     * Virtual property: warranty_expiry
     * @return string|null
     */
    public function getWarranty_expiry()
    {
        return $this->next_maintenance_date;
    }

    /**
     * Setter for warranty_expiry (no-op)
     * @param string $value
     */
    public function setWarranty_expiry($value)
    {
        // No actual column
    }

    /**
     * Virtual property: notes (alias for description)
     * @return string|null
     */
    public function getNotes()
    {
        return $this->description;
    }

    /**
     * Setter for notes
     * @param string $value
     */
    public function setNotes($value)
    {
        $this->description = $value;
    }

    /**
     * Upload image file
     * @param \yii\web\UploadedFile $file
     * @return string|false Saved file path or false on failure
     */
    public function uploadImage($file)
    {
        if (!$file) {
            return false;
        }

        // Create upload directory using shared @uploads alias
        $uploadPath = Yii::getAlias('@uploads/equipment');
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Delete old image if exists
        $this->deleteImage();

        // Generate unique filename
        $fileName = 'eq_' . time() . '_' . Yii::$app->security->generateRandomString(8) . '.' . $file->extension;
        $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

        if ($file->saveAs($filePath)) {
            return 'equipment/' . $fileName;
        }

        return false;
    }

    /**
     * Delete current image file
     * @return bool
     */
    public function deleteImage()
    {
        if (!empty($this->image)) {
            $filePath = Yii::getAlias('@uploads') . '/' . ltrim($this->image, '/');
            if (file_exists($filePath) && is_file($filePath)) {
                @unlink($filePath);
            }
            $this->image = null;
            return true;
        }
        return false;
    }

    /**
     * Get full URL for image
     * @return string|null
     */
    public function getImageUrl()
    {
        if (!empty($this->image)) {
            // If it's already a full URL
            if (strpos($this->image, 'http') === 0) {
                return $this->image;
            }
            // Return web-accessible path using @uploadsUrl alias
            return Yii::getAlias('@uploadsUrl') . '/' . ltrim($this->image, '/');
        }
        return null;
    }

    /**
     * Get thumbnail URL (or placeholder)
     * @return string
     */
    public function getThumbnailUrl()
    {
        if ($this->imageUrl) {
            return $this->imageUrl;
        }
        // Return placeholder
        return Yii::$app->request->baseUrl . '/img/no-image.png';
    }

    /**
     * Check if equipment has image
     * @return bool
     */
    public function hasImage()
    {
        return !empty($this->image);
    }
}
