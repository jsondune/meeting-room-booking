<?php
/**
 * Equipment Category Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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
            ['sort_order', 'default', 'value' => 0],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัส',
            'name_th' => 'ชื่อหมวดหมู่ (ไทย)',
            'name_en' => 'ชื่อหมวดหมู่ (English)',
            'icon' => 'ไอคอน',
            'description' => 'รายละเอียด',
            'sort_order' => 'ลำดับ',
            'is_active' => 'ใช้งาน',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
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
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->select(['name_th', 'id'])
            ->indexBy('id')
            ->column();
    }
    
    public static function getAllForDropdown()
    {
        return ArrayHelper::map(
            self::find()
                ->where(['is_active' => true])
                ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
                ->all(),
            'id',
            'name_th'
        );
    }
    
    public function getDisplayName()
    {
        return $this->name_th . ($this->name_en ? " ({$this->name_en})" : '');
    }
    
    public function getEquipmentCount()
    {
        return $this->getEquipments()->count();
    }
}
