<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Password Reset Request Form
 * ใช้สำหรับขอรีเซ็ตรหัสผ่าน (Frontend)
 */
class PasswordResetRequestForm extends Model
{
    public $email;

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
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        // Generate password reset token if not valid
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save(false)) {
                return false;
            }
        }

        // Try to send email, return true even if mail fails (for security - don't reveal if email exists)
        try {
            return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] ?? Yii::$app->params['adminEmail'] ?? 'noreply@example.com' => Yii::$app->name])
                ->setTo($this->email)
                ->setSubject('รีเซ็ตรหัสผ่าน - ' . Yii::$app->name)
                ->send();
        } catch (\Exception $e) {
            Yii::error('Failed to send password reset email: ' . $e->getMessage());
            // Return true for security (don't reveal if email sending failed)
            return true;
        }
    }
}
