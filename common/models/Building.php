<?php
/**
 * Building Model
 * 
 * @property int $id
 * @property string $code
 * @property string $name_th
 * @property string|null $name_en
 * @property string|null $address
 * @property float|null $latitude
 * @property float|null $longitude
 * @property int $floor_count
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property MeetingRoom[] $rooms
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Building extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%building}}';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัสอาคาร',
            'name_th' => 'ชื่ออาคาร (ไทย)',
            'name_en' => 'ชื่ออาคาร (English)',
            'address' => 'ที่อยู่',
            'latitude' => 'ละติจูด',
            'longitude' => 'ลองจิจูด',
            'floor_count' => 'จำนวนชั้น',
            'is_active' => 'ใช้งาน',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    /**
     * Get rooms in this building
     * @return \yii\db\ActiveQuery
     */
    public function getRooms()
    {
        return $this->hasMany(MeetingRoom::class, ['building_id' => 'id']);
    }

    /**
     * Alias for getRooms (backward compatibility)
     * @return \yii\db\ActiveQuery
     */
    public function getMeetingRooms()
    {
        return $this->getRooms();
    }
    
    /**
     * Get active rooms count
     * @return int
     */
    public function getActiveRoomsCount()
    {
        return $this->getRooms()->where(['status' => MeetingRoom::STATUS_ACTIVE])->count();
    }

    /**
     * Get dropdown list for forms
     * @return array
     */
    public static function getDropdownList()
    {
        return self::find()
            ->where(['is_active' => true])
            ->orderBy(['name_th' => SORT_ASC])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
    
    /**
     * Get all buildings as array for dropdown
     * @return array
     */
    public static function getAllForDropdown()
    {
        return ArrayHelper::map(
            self::find()
                ->where(['is_active' => true])
                ->orderBy(['name_th' => SORT_ASC])
                ->all(),
            'id',
            'name_th'
        );
    }
    
    /**
     * Get display name
     * @return string
     */
    public function getDisplayName()
    {
        return $this->name_th . ($this->name_en ? " ({$this->name_en})" : '');
    }
    
    /**
     * Get status options
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_INACTIVE => 'ไม่ใช้งาน',
            self::STATUS_ACTIVE => 'ใช้งาน',
        ];
    }
    
    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $options = self::getStatusOptions();
        return $options[$this->is_active ? 1 : 0] ?? 'ไม่ทราบ';
    }
    
    /**
     * Get status badge HTML
     * @return string
     */
    public function getStatusBadge()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">ใช้งาน</span>';
        }
        return '<span class="badge bg-secondary">ไม่ใช้งาน</span>';
    }

    /**
     * Get name (alias for name_th)
     * @return string|null
     */
    public function getName()
    {
        return $this->name_th;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString()
    {
        return $this->name_th ?? '';
    }
}
