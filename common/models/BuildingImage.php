<?php
/**
 * Building Image Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class BuildingImage extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%building_image}}';
    }

    public function rules()
    {
        return [
            [['building_id', 'filename', 'original_name', 'file_path'], 'required'],
            ['building_id', 'integer'],
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
            'building_id' => 'อาคาร',
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

    public function getBuilding()
    {
        return $this->hasOne(Building::class, ['id' => 'building_id']);
    }

    public function getUrl()
    {
        // file_path is like: uploads/buildings/5/filename.jpg
        // Return relative URL from web root
        return '/' . $this->file_path;
    }
    
    public function getFullUrl()
    {
        return Yii::$app->request->hostInfo . $this->getUrl();
    }
    
    public function getThumbnailUrl($width = 200, $height = 150)
    {
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
