<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * BookingAttendee Model
 * 
 * @property int $id
 * @property int $booking_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $email
 * @property bool $is_external
 * @property bool $is_organizer
 * @property bool $is_confirmed
 * @property string|null $response_at
 * @property string|null $created_at
 * 
 * @property Booking $booking
 * @property User $user
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
            [['is_external', 'is_organizer', 'is_confirmed'], 'boolean'],
            [['is_external', 'is_organizer', 'is_confirmed'], 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_id' => 'การจอง',
            'user_id' => 'ผู้ใช้',
            'name' => 'ชื่อ',
            'email' => 'อีเมล',
            'is_external' => 'ผู้เข้าร่วมภายนอก',
            'is_organizer' => 'ผู้จัด',
            'is_confirmed' => 'ยืนยันแล้ว',
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
