<?php
/**
 * System Setting Model
 */

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class SystemSetting extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%system_setting}}';
    }

    public function rules()
    {
        return [
            [['setting_key'], 'required'],
            ['setting_key', 'string', 'max' => 100],
            ['setting_key', 'unique'],
            ['setting_value', 'string'],
            ['setting_type', 'in', 'range' => ['string', 'integer', 'boolean', 'json']],
            ['setting_type', 'default', 'value' => 'string'],
            ['category', 'string', 'max' => 50],
            ['description', 'string'],
            ['is_public', 'boolean'],
            ['is_public', 'default', 'value' => false],
            ['updated_by', 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'setting_key' => 'คีย์',
            'setting_value' => 'ค่า',
            'setting_type' => 'ประเภท',
            'category' => 'หมวดหมู่',
            'description' => 'คำอธิบาย',
            'is_public' => 'เปิดเผย',
            'updated_by' => 'แก้ไขโดย',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    /**
     * Get setting value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        try {
            $setting = static::find()->where(['setting_key' => $key])->one();
            
            if (!$setting) {
                return $default;
            }
            
            switch ($setting->setting_type) {
                case 'integer':
                    return (int) $setting->setting_value;
                case 'boolean':
                    return filter_var($setting->setting_value, FILTER_VALIDATE_BOOLEAN);
                case 'json':
                    return json_decode($setting->setting_value, true);
                default:
                    return $setting->setting_value;
            }
        } catch (\Exception $e) {
            // Table might not exist yet during migration
            return $default;
        }
    }

    /**
     * Set setting value
     * @param string $key
     * @param mixed $value
     * @param string $type
     * @return bool
     */
    public static function setValue($key, $value, $type = 'string')
    {
        $setting = static::find()->where(['setting_key' => $key])->one();
        
        if (!$setting) {
            $setting = new static();
            $setting->setting_key = $key;
        }
        
        $setting->setting_type = $type;
        
        if ($type === 'json') {
            $setting->setting_value = json_encode($value);
        } elseif ($type === 'boolean') {
            $setting->setting_value = $value ? '1' : '0';
        } else {
            $setting->setting_value = (string) $value;
        }
        
        // Check if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
            $setting->updated_by = Yii::$app->user->id;
        }
        
        return $setting->save();
    }
    
    /**
     * Get settings by category
     * @param string $category
     * @return array
     */
    public static function getByCategory($category)
    {
        try {
            return static::find()
                ->where(['category' => $category])
                ->orderBy(['setting_key' => SORT_ASC])
                ->all();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get all public settings
     * @return array
     */
    public static function getPublicSettings()
    {
        try {
            $settings = static::find()
                ->where(['is_public' => true])
                ->all();
            
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->setting_key] = static::getValue($setting->setting_key);
            }
            return $result;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Get updated by user
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
