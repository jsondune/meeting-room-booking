<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * BookingEquipment Model
 * 
 * @property int $id
 * @property int $booking_id
 * @property int $equipment_id
 * @property int $quantity_requested
 * @property int|null $quantity_approved
 * @property float|null $unit_price
 * @property float|null $total_price
 * @property string $status
 * @property string|null $notes
 * @property string|null $created_at
 * @property string|null $updated_at
 * 
 * @property Booking $booking
 * @property Equipment $equipment
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
            [['booking_id', 'equipment_id'], 'required'],
            [['booking_id', 'equipment_id', 'quantity_requested', 'quantity_approved'], 'integer'],
            ['quantity_requested', 'integer', 'min' => 1],
            ['quantity_requested', 'default', 'value' => 1],
            [['unit_price', 'total_price'], 'number'],
            ['status', 'string', 'max' => 20],
            ['status', 'default', 'value' => 'pending'],
            ['notes', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'การจอง',
            'equipment_id' => 'อุปกรณ์',
            'quantity_requested' => 'จำนวนที่ขอ',
            'quantity_approved' => 'จำนวนที่อนุมัติ',
            'unit_price' => 'ราคาต่อหน่วย',
            'total_price' => 'ราคารวม',
            'status' => 'สถานะ',
            'notes' => 'หมายเหตุ',
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
