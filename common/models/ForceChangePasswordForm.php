<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Force Change Password Form
 * Used when user must change password on first login
 * (No current password required)
 */
class ForceChangePasswordForm extends Model
{
    public $newPassword;
    public $confirmPassword;

    /**
     * @var User
     */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['newPassword', 'confirmPassword'], 'required'],
            [['newPassword'], 'string', 'min' => 8, 'max' => 72],
            [['newPassword'], 'match', 
                'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'message' => 'รหัสผ่านต้องประกอบด้วยตัวพิมพ์เล็ก ตัวพิมพ์ใหญ่ และตัวเลข'
            ],
            ['confirmPassword', 'compare', 
                'compareAttribute' => 'newPassword', 
                'message' => 'รหัสผ่านยืนยันไม่ตรงกัน'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'newPassword' => 'รหัสผ่านใหม่',
            'confirmPassword' => 'ยืนยันรหัสผ่านใหม่',
        ];
    }

    /**
     * Change password
     * @return bool
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if (!$user) {
            $this->addError('newPassword', 'ไม่พบข้อมูลผู้ใช้');
            return false;
        }

        $user->setPassword($this->newPassword);
        $user->must_change_password = 0;
        $user->password_changed_at = date('Y-m-d H:i:s');
        $user->generateAuthKey();

        if ($user->save(false)) {
            // Log audit
            AuditLog::log(
                'update',
                User::class,
                $user->id,
                ['must_change_password' => 1],
                ['must_change_password' => 0],
                'ผู้ใช้เปลี่ยนรหัสผ่านครั้งแรก'
            );
            return true;
        }

        return false;
    }

    /**
     * Get current user
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }
}
