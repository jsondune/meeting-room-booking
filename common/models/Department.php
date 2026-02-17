<?php
/**
 * Department Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Department extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
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
            ['sort_order', 'default', 'value' => 0],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัส',
            'name_th' => 'ชื่อหน่วยงาน (ไทย)',
            'name_en' => 'ชื่อหน่วยงาน (English)',
            'parent_id' => 'หน่วยงานหลัก',
            'sort_order' => 'ลำดับ',
            'is_active' => 'ใช้งาน',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
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
    
    public function getStatusBadge()
    {
        if ($this->is_active) {
            return '<span class="badge bg-success">ใช้งาน</span>';
        }
        return '<span class="badge bg-secondary">ไม่ใช้งาน</span>';
    }
}
