<?php
/**
 * Room Image Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class RoomImage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%room_image}}';
    }

    public function rules()
    {
        return [
            [['room_id', 'filename', 'original_name', 'file_path'], 'required'],
            ['room_id', 'integer'],
            [['filename', 'original_name', 'alt_text'], 'string', 'max' => 255],
            ['file_path', 'string', 'max' => 500],
            ['mime_type', 'string', 'max' => 100],
            [['file_size', 'image_width', 'image_height', 'sort_order'], 'integer'],
            ['is_primary', 'boolean'],
            ['sort_order', 'default', 'value' => 0],
            ['is_primary', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'room_id' => 'ห้องประชุม',
            'filename' => 'ชื่อไฟล์',
            'original_name' => 'ชื่อไฟล์เดิม',
            'file_path' => 'ตำแหน่งไฟล์',
            'mime_type' => 'ประเภทไฟล์',
            'file_size' => 'ขนาดไฟล์',
            'image_width' => 'ความกว้าง',
            'image_height' => 'ความสูง',
            'alt_text' => 'คำอธิบายรูป',
            'is_primary' => 'รูปหลัก',
            'sort_order' => 'ลำดับ',
            'created_at' => 'สร้างเมื่อ',
        ];
    }

    public function getRoom()
    {
        return $this->hasOne(MeetingRoom::class, ['id' => 'room_id']);
    }

    public function getUrl()
    {
        return Yii::getAlias('@web/' . $this->file_path);
    }
    
    public function getFullUrl()
    {
        return Yii::$app->request->hostInfo . $this->getUrl();
    }
    
    public function getThumbnailUrl($width = 200, $height = 150)
    {
        // Return original URL - implement thumbnail generation if needed
        return $this->getUrl();
    }
    
    public function getFileSizeFormatted()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    public function getDimensions()
    {
        if ($this->image_width && $this->image_height) {
            return $this->image_width . 'x' . $this->image_height;
        }
        return null;
    }
}
