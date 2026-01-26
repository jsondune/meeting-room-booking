<?php

namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Signup form model
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;
    public $first_name;
    public $last_name;
    public $phone;
    public $department_id;
    public $agree_terms;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['username', 'email', 'password', 'password_confirm', 'first_name', 'last_name'], 'required'],
            
            // Trim whitespace
            [['username', 'email', 'first_name', 'last_name'], 'trim'],
            
            // Username
            ['username', 'string', 'min' => 3, 'max' => 50],
            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 
                'message' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษร ตัวเลข และ _ เท่านั้น'],
            ['username', 'unique', 'targetClass' => User::class, 
                'message' => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว'],
            
            // Email
            ['email', 'email', 'message' => 'รูปแบบอีเมลไม่ถูกต้อง'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 
                'message' => 'อีเมลนี้ถูกใช้งานแล้ว'],
            
            // Password
            ['password', 'string', 'min' => 8, 
                'tooShort' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร'],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 
                'message' => 'รหัสผ่านไม่ตรงกัน'],
            
            // Name
            [['first_name', 'last_name'], 'string', 'max' => 100],
            
            // Phone (optional)
            ['phone', 'string', 'max' => 20],
            ['phone', 'match', 'pattern' => '/^[0-9\-\+\s]*$/', 
                'message' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง'],
            
            // Department (optional)
            ['department_id', 'integer'],
            ['department_id', 'exist', 'skipOnEmpty' => true, 
                'targetClass' => Department::class, 'targetAttribute' => 'id'],
            
            // Terms agreement
            ['agree_terms', 'required', 'requiredValue' => 1, 
                'message' => 'กรุณายอมรับเงื่อนไขการใช้งาน'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => 'ชื่อผู้ใช้',
            'email' => 'อีเมล',
            'password' => 'รหัสผ่าน',
            'password_confirm' => 'ยืนยันรหัสผ่าน',
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'phone' => 'เบอร์โทรศัพท์',
            'department_id' => 'หน่วยงาน',
            'agree_terms' => 'ยอมรับเงื่อนไขการใช้งาน',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->phone = $this->phone;
        $user->department_id = $this->department_id;
        $user->status = User::STATUS_INACTIVE; // Require email verification
        $user->role = User::ROLE_USER;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();

        if ($user->save()) {
            return $user;
        }

        // If save failed, copy errors to form
        foreach ($user->errors as $attribute => $errors) {
            foreach ($errors as $error) {
                $this->addError($attribute, $error);
            }
        }

        return null;
    }

    /**
     * Sends confirmation email to user
     *
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    public function sendEmail($user)
    {
        try {
            return Yii::$app->mailer
                ->compose(
                    ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] ?? 'noreply@example.com' => Yii::$app->name . ' - ยืนยันอีเมล'])
                ->setTo($this->email)
                ->setSubject('ยืนยันอีเมลสำหรับ ' . Yii::$app->name)
                ->send();
        } catch (\Exception $e) {
            Yii::error('Failed to send verification email: ' . $e->getMessage());
            return false;
        }
    }
}
