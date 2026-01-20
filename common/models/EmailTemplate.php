<?php
/**
 * Email Template Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class EmailTemplate extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%email_template}}';
    }

    public function rules()
    {
        return [
            [['template_key', 'name', 'subject', 'body_html'], 'required'],
            ['template_key', 'string', 'max' => 50],
            ['template_key', 'unique'],
            ['template_key', 'match', 'pattern' => '/^[a-z0-9_]+$/'],
            ['name', 'string', 'max' => 100],
            ['subject', 'string', 'max' => 255],
            [['body_html', 'body_text', 'description'], 'string'],
            ['category', 'string', 'max' => 50],
            ['is_active', 'boolean'],
            ['is_system', 'boolean'],
            ['is_active', 'default', 'value' => true],
            ['is_system', 'default', 'value' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_key' => 'รหัสเทมเพลต',
            'name' => 'ชื่อเทมเพลต',
            'subject' => 'หัวเรื่อง',
            'body_html' => 'เนื้อหา HTML',
            'body_text' => 'เนื้อหา Text',
            'category' => 'หมวดหมู่',
            'description' => 'คำอธิบาย',
            'is_active' => 'ใช้งาน',
            'is_system' => 'เทมเพลตระบบ',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขเมื่อ',
        ];
    }

    /**
     * Render subject with variables
     */
    public function renderSubject($data = [])
    {
        return $this->replaceVariables($this->subject, $data);
    }

    /**
     * Render HTML body with variables
     */
    public function renderBodyHtml($data = [])
    {
        return $this->replaceVariables($this->body_html, $data);
    }

    /**
     * Render text body with variables
     */
    public function renderBodyText($data = [])
    {
        if ($this->body_text) {
            return $this->replaceVariables($this->body_text, $data);
        }
        return strip_tags($this->renderBodyHtml($data));
    }

    /**
     * Replace variables in text
     */
    protected function replaceVariables($text, $data)
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{{' . $key . '}}', $value, $text);
        }
        return $text;
    }

    /**
     * Get available variables
     */
    public static function getAvailableVariables()
    {
        return [
            'user_name' => 'ชื่อผู้ใช้',
            'user_email' => 'อีเมลผู้ใช้',
            'user_phone' => 'เบอร์โทรผู้ใช้',
            'booking_code' => 'รหัสการจอง',
            'room_name' => 'ชื่อห้องประชุม',
            'room_code' => 'รหัสห้องประชุม',
            'building_name' => 'ชื่ออาคาร',
            'booking_date' => 'วันที่จอง',
            'start_time' => 'เวลาเริ่ม',
            'end_time' => 'เวลาสิ้นสุด',
            'booking_title' => 'หัวข้อการประชุม',
            'attendees_count' => 'จำนวนผู้เข้าร่วม',
            'total_cost' => 'ค่าใช้จ่ายรวม',
            'site_name' => 'ชื่อเว็บไซต์',
            'site_url' => 'URL เว็บไซต์',
            'approval_link' => 'ลิงก์อนุมัติ',
            'rejection_reason' => 'เหตุผลปฏิเสธ',
            'cancel_reason' => 'เหตุผลยกเลิก',
            'qr_code_url' => 'URL QR Code',
        ];
    }

    public static function getCategoryOptions()
    {
        return [
            'booking' => 'การจอง',
            'approval' => 'การอนุมัติ',
            'notification' => 'แจ้งเตือน',
            'user' => 'ผู้ใช้',
            'system' => 'ระบบ',
        ];
    }

    /**
     * Get template by key
     */
    public static function getByKey($key)
    {
        return static::find()
            ->where(['template_key' => $key, 'is_active' => true])
            ->one();
    }

    /**
     * Send email using this template
     */
    public function send($to, $data = [], $attachments = [])
    {
        $message = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setSubject($this->renderSubject($data))
            ->setHtmlBody($this->renderBodyHtml($data));

        if ($this->body_text) {
            $message->setTextBody($this->renderBodyText($data));
        }

        foreach ($attachments as $attachment) {
            if (is_string($attachment)) {
                $message->attach($attachment);
            } elseif (is_array($attachment)) {
                $message->attach($attachment['path'], $attachment['options'] ?? []);
            }
        }

        return $message->send();
    }

    /**
     * Get category badge
     */
    public function getCategoryBadge()
    {
        $badges = [
            'booking' => '<span class="badge bg-primary">การจอง</span>',
            'approval' => '<span class="badge bg-success">การอนุมัติ</span>',
            'notification' => '<span class="badge bg-info">แจ้งเตือน</span>',
            'user' => '<span class="badge bg-warning text-dark">ผู้ใช้</span>',
            'system' => '<span class="badge bg-secondary">ระบบ</span>',
        ];
        return $badges[$this->category] ?? '<span class="badge bg-secondary">' . $this->category . '</span>';
    }
}
