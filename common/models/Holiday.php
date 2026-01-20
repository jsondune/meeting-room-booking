<?php
/**
 * Holiday Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class Holiday extends ActiveRecord
{
    const TYPE_NATIONAL = 'national';
    const TYPE_REGIONAL = 'regional';
    const TYPE_ORGANIZATION = 'organization';
    const TYPE_SPECIAL = 'special';
    
    public static function tableName()
    {
        return '{{%holiday}}';
    }

    public function rules()
    {
        return [
            [['date', 'name_th'], 'required'],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            [['name_th', 'name_en'], 'string', 'max' => 255],
            ['description', 'string'],
            ['holiday_type', 'string', 'max' => 50],
            ['holiday_type', 'in', 'range' => array_keys(self::getTypeOptions())],
            ['is_recurring', 'boolean'],
            ['is_active', 'boolean'],
            ['holiday_type', 'default', 'value' => self::TYPE_NATIONAL],
            ['is_recurring', 'default', 'value' => false],
            ['is_active', 'default', 'value' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'วันที่',
            'name_th' => 'ชื่อวันหยุด (ไทย)',
            'name_en' => 'ชื่อวันหยุด (English)',
            'description' => 'รายละเอียด',
            'holiday_type' => 'ประเภท',
            'is_recurring' => 'เกิดซ้ำทุกปี',
            'is_active' => 'ใช้งาน',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    public static function getTypeOptions()
    {
        return [
            self::TYPE_NATIONAL => 'วันหยุดราชการ',
            self::TYPE_REGIONAL => 'วันหยุดภูมิภาค',
            self::TYPE_ORGANIZATION => 'วันหยุดองค์กร',
            self::TYPE_SPECIAL => 'วันพิเศษ',
        ];
    }
    
    public function getTypeLabel()
    {
        $options = self::getTypeOptions();
        return $options[$this->holiday_type] ?? $this->holiday_type;
    }

    public static function isHoliday($date)
    {
        $dateStr = is_string($date) ? $date : date('Y-m-d', strtotime($date));
        
        return self::find()
            ->where(['date' => $dateStr, 'is_active' => true])
            ->exists();
    }
    
    public static function getHolidaysInRange($startDate, $endDate)
    {
        return self::find()
            ->where(['between', 'date', $startDate, $endDate])
            ->andWhere(['is_active' => true])
            ->orderBy(['date' => SORT_ASC])
            ->all();
    }
    
    public static function getHolidayDatesInRange($startDate, $endDate)
    {
        return self::find()
            ->select('date')
            ->where(['between', 'date', $startDate, $endDate])
            ->andWhere(['is_active' => true])
            ->column();
    }
    
    public static function getUpcoming($limit = 10)
    {
        return self::find()
            ->where(['>=', 'date', date('Y-m-d')])
            ->andWhere(['is_active' => true])
            ->orderBy(['date' => SORT_ASC])
            ->limit($limit)
            ->all();
    }
    
    public function getDisplayName()
    {
        return $this->name_th . ($this->name_en ? " ({$this->name_en})" : '');
    }
}
