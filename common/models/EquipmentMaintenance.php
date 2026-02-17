<?php
/**
 * Equipment Maintenance Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

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
            [['estimated_cost', 'actual_cost'], 'number', 'min' => 0],
            [['description', 'notes'], 'string'],
            ['status', 'default', 'value' => self::STATUS_PENDING],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment_id' => 'อุปกรณ์',
            'maintenance_type' => 'ประเภทการบำรุงรักษา',
            'scheduled_date' => 'วันที่กำหนด',
            'completed_date' => 'วันที่แล้วเสร็จ',
            'status' => 'สถานะ',
            'estimated_cost' => 'ค่าใช้จ่ายประมาณ',
            'actual_cost' => 'ค่าใช้จ่ายจริง',
            'performed_by' => 'ดำเนินการโดย',
            'created_by' => 'สร้างโดย',
            'description' => 'รายละเอียด',
            'notes' => 'หมายเหตุ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    public function getEquipment()
    {
        return $this->hasOne(Equipment::class, ['id' => 'equipment_id']);
    }

    public function getPerformedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'performed_by']);
    }
    
    public function getCreatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
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
    
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'รอดำเนินการ',
            self::STATUS_IN_PROGRESS => 'กำลังดำเนินการ',
            self::STATUS_COMPLETED => 'เสร็จสิ้น',
            self::STATUS_CANCELLED => 'ยกเลิก',
        ];
    }
    
    public static function getMaintenanceTypeOptions()
    {
        return [
            'routine' => 'บำรุงรักษาตามกำหนด',
            'repair' => 'ซ่อมแซม',
            'replacement' => 'เปลี่ยนอะไหล่',
            'inspection' => 'ตรวจสอบ',
            'calibration' => 'สอบเทียบ',
            'cleaning' => 'ทำความสะอาด',
            'upgrade' => 'อัพเกรด',
            'other' => 'อื่นๆ',
        ];
    }
    
    public function getMaintenanceTypeLabel()
    {
        $options = self::getMaintenanceTypeOptions();
        return $options[$this->maintenance_type] ?? $this->maintenance_type;
    }
    
    public static function getUpcoming($days = 7)
    {
        $endDate = date('Y-m-d', strtotime("+{$days} days"));
        return self::find()
            ->where(['status' => self::STATUS_PENDING])
            ->andWhere(['<=', 'scheduled_date', $endDate])
            ->orderBy(['scheduled_date' => SORT_ASC])
            ->all();
    }
    
    public static function getOverdue()
    {
        $today = date('Y-m-d');
        return self::find()
            ->where(['status' => [self::STATUS_PENDING, self::STATUS_IN_PROGRESS]])
            ->andWhere(['<', 'scheduled_date', $today])
            ->orderBy(['scheduled_date' => SORT_ASC])
            ->all();
    }
}
