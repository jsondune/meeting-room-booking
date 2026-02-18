<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Forgot Password Form
 * ใช้สำหรับขอรีเซ็ตรหัสผ่าน
 */
class ForgotPasswordForm extends Model
{
    public $email;

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
            ['email', 'trim'],
            ['email', 'required', 'message' => 'กรุณากรอกอีเมล'],
            ['email', 'email', 'message' => 'รูปแบบอีเมลไม่ถูกต้อง'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'ไม่พบอีเมลนี้ในระบบ หรือบัญชีถูกระงับ'
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'อีเมล',
        ];
    }

    /**
     * Sends an email with a link for resetting password.
     *
     * @return bool whether the email was sent
     */
    public function sendEmail()
    {
        $user = $this->getUser();

        if (!$user) {
            return false;
        }

        // Generate password reset token
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save(false)) {
                return false;
            }
        }

        // Send email
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'] ?? 'noreply@example.com' => Yii::$app->name . ' Robot'])
            ->setTo($this->email)
            ->setSubject('รีเซ็ตรหัสผ่าน - ' . Yii::$app->name)
            ->send();
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne([
                'status' => User::STATUS_ACTIVE,
                'email' => $this->email,
            ]);
        }

        return $this->_user;
    }
}
