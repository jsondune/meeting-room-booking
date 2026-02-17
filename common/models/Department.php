<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Department Model
 *
 * @property int $id
 * @property string $name_th
 * @property string $name_en
 * @property string $code
 * @property int $parent_id
 * @property int $sort_order
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class Department extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%department}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name_th'], 'required'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
            [['parent_id', 'sort_order', 'is_active'], 'integer'],
            [['is_active'], 'default', 'value' => self::STATUS_ACTIVE],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_th' => 'ชื่อหน่วยงาน (ไทย)',
            'name_en' => 'ชื่อหน่วยงาน (อังกฤษ)',
            'code' => 'รหัส',
            'parent_id' => 'หน่วยงานหลัก',
            'sort_order' => 'ลำดับ',
            'is_active' => 'สถานะ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }

    /**
     * Get dropdown list for select input
     * @param bool $activeOnly
     * @return array
     */
    public static function getDropdownList($activeOnly = true)
    {
        $query = static::find()
            ->select(['id', 'name_th'])
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC]);
        
        if ($activeOnly) {
            $query->where(['is_active' => self::STATUS_ACTIVE]);
        }
        
        return ArrayHelper::map($query->all(), 'id', 'name_th');
    }

    /**
     * Get department name (alias for name_th)
     * @return string
     */
    public function getName()
    {
        return $this->name_th;
    }

    /**
     * Get parent department
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Department::class, ['id' => 'parent_id']);
    }

    /**
     * Get child departments
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Department::class, ['parent_id' => 'id']);
    }

    /**
     * Get users in this department
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['department_id' => 'id']);
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_INACTIVE => '<span class="badge bg-secondary">ปิดใช้งาน</span>',
            self::STATUS_ACTIVE => '<span class="badge bg-success">ใช้งาน</span>',
        ];
        return $statuses[$this->is_active] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get active departments count
     * @return int
     */
    public static function getActiveCount()
    {
        return static::find()->where(['is_active' => self::STATUS_ACTIVE])->count();
    }
}
