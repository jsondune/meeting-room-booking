<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * ChangePasswordForm - แบบฟอร์มเปลี่ยนรหัสผ่าน
 */
class ChangePasswordForm extends Model
{
    public $current_password;
    public $new_password;
    public $confirm_password;

    /**
     * @var \common\models\User
     */
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['current_password', 'new_password', 'confirm_password'], 'required'],
            ['current_password', 'validateCurrentPassword'],
            ['new_password', 'string', 'min' => 6, 'max' => 72],
            ['new_password', 'match', 'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d).+$/', 
                'message' => 'รหัสผ่านต้องประกอบด้วยตัวอักษรและตัวเลข'],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 
                'message' => 'รหัสผ่านยืนยันไม่ตรงกัน'],
            ['new_password', 'compare', 'compareAttribute' => 'current_password', 'operator' => '!=',
                'message' => 'รหัสผ่านใหม่ต้องไม่ซ้ำกับรหัสผ่านเดิม'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'current_password' => 'รหัสผ่านปัจจุบัน',
            'new_password' => 'รหัสผ่านใหม่',
            'confirm_password' => 'ยืนยันรหัสผ่านใหม่',
        ];
    }

    /**
     * Validates the current password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateCurrentPassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->current_password)) {
                $this->addError($attribute, 'รหัสผ่านปัจจุบันไม่ถูกต้อง');
            }
        }
    }

    /**
     * Change password
     *
     * @return bool whether the password is changed successfully
     */
    public function changePassword()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if ($user) {
            $user->setPassword($this->new_password);
            return $user->save(false);
        }

        return false;
    }

    /**
     * Finds user by current logged in user
     *
     * @return \common\models\User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
        }

        return $this->_user;
    }
}
