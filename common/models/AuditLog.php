<?php
/**
 * Audit Log Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class AuditLog extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%audit_log}}';
    }

    public function rules()
    {
        return [
            [['action'], 'required'],
            ['user_id', 'integer'],
            ['username', 'string', 'max' => 50],
            ['ip_address', 'string', 'max' => 45],
            ['user_agent', 'string', 'max' => 500],
            ['action', 'string', 'max' => 50],
            ['model_class', 'string', 'max' => 100],
            ['model_id', 'string', 'max' => 50],
            [['old_values', 'new_values', 'description'], 'string'],
            ['url', 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ผู้ใช้',
            'username' => 'ชื่อผู้ใช้',
            'ip_address' => 'IP Address',
            'user_agent' => 'User Agent',
            'action' => 'การกระทำ',
            'model_class' => 'Model',
            'model_id' => 'Record ID',
            'old_values' => 'ค่าเดิม',
            'new_values' => 'ค่าใหม่',
            'url' => 'URL',
            'description' => 'รายละเอียด',
            'created_at' => 'เวลา',
        ];
    }

    /**
     * Log an action
     */
    public static function log($action, $modelClass = null, $modelId = null, $oldValues = [], $newValues = [], $description = null)
    {
        $log = new static();
        
        // Check if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $log->user_id = Yii::$app->user->id;
            $log->username = Yii::$app->user->identity->username ?? null;
        }
        
        // Get request info if available
        if (Yii::$app->has('request') && Yii::$app->request instanceof \yii\web\Request) {
            $log->ip_address = Yii::$app->request->userIP ?? '127.0.0.1';
            $log->user_agent = substr(Yii::$app->request->userAgent ?? '', 0, 500);
            $log->url = Yii::$app->request->absoluteUrl ?? null;
        } else {
            $log->ip_address = '127.0.0.1';
            $log->user_agent = 'Console';
            $log->url = null;
        }
        
        $log->action = $action;
        $log->model_class = $modelClass;
        $log->model_id = $modelId;
        $log->old_values = !empty($oldValues) ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null;
        $log->new_values = !empty($newValues) ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null;
        $log->description = $description;
        
        return $log->save(false);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getOldValuesArray()
    {
        return $this->old_values ? json_decode($this->old_values, true) : [];
    }
    
    public function getNewValuesArray()
    {
        return $this->new_values ? json_decode($this->new_values, true) : [];
    }

    public function getActionLabel()
    {
        $labels = [
            'create' => '<span class="badge bg-success">สร้าง</span>',
            'update' => '<span class="badge bg-info">แก้ไข</span>',
            'delete' => '<span class="badge bg-danger">ลบ</span>',
            'login' => '<span class="badge bg-primary">เข้าสู่ระบบ</span>',
            'logout' => '<span class="badge bg-secondary">ออกจากระบบ</span>',
            'approve' => '<span class="badge bg-success">อนุมัติ</span>',
            'reject' => '<span class="badge bg-warning text-dark">ปฏิเสธ</span>',
            'cancel' => '<span class="badge bg-danger">ยกเลิก</span>',
            'bulk_activate' => '<span class="badge bg-success">เปิดใช้งาน (หลายรายการ)</span>',
            'bulk_deactivate' => '<span class="badge bg-warning">ปิดใช้งาน (หลายรายการ)</span>',
            'bulk_delete' => '<span class="badge bg-danger">ลบ (หลายรายการ)</span>',
        ];
        return $labels[$this->action] ?? '<span class="badge bg-secondary">' . htmlspecialchars($this->action) . '</span>';
    }
    
    public static function getActionOptions()
    {
        return [
            'create' => 'สร้าง',
            'update' => 'แก้ไข',
            'delete' => 'ลบ',
            'login' => 'เข้าสู่ระบบ',
            'logout' => 'ออกจากระบบ',
            'approve' => 'อนุมัติ',
            'reject' => 'ปฏิเสธ',
            'cancel' => 'ยกเลิก',
        ];
    }
}
