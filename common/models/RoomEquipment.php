<?php
/**
 * Room Equipment Model (Junction Table)
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

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
            [['room_id', 'equipment_id'], 'unique', 'targetAttribute' => ['room_id', 'equipment_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'ห้องประชุม',
            'equipment_id' => 'อุปกรณ์',
            'quantity' => 'จำนวน',
            'is_included' => 'รวมในห้อง',
            'notes' => 'หมายเหตุ',
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
