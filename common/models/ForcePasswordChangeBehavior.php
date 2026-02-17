<?php

namespace common\components;

use Yii;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

/**
 * ForcePasswordChangeBehavior
 * 
 * บังคับให้ user เปลี่ยนรหัสผ่านก่อนใช้งานระบบ
 * 
 * Usage in controller:
 * public function behaviors()
 * {
 *     return [
 *         'forcePasswordChange' => [
 *             'class' => \common\components\ForcePasswordChangeBehavior::class,
 *         ],
 *     ];
 * }
 */
class ForcePasswordChangeBehavior extends Behavior
{
    /**
     * @var array Actions that are allowed even when password change is required
     */
    public $allowedActions = ['change-password', 'logout', 'force-change-password'];
    
    /**
     * @var string The route to redirect to for password change
     */
    public $changePasswordRoute = '/site/force-change-password';

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    /**
     * Check if user must change password before accessing any action
     * 
     * @param \yii\base\ActionEvent $event
     * @return bool
     */
    public function beforeAction($event)
    {
        // Skip for guests
        if (Yii::$app->user->isGuest) {
            return true;
        }

        // Skip for allowed actions
        $actionId = $event->action->id;
        if (in_array($actionId, $this->allowedActions)) {
            return true;
        }

        // Check if user must change password
        $user = Yii::$app->user->identity;
        if ($user && $this->mustChangePassword($user)) {
            Yii::$app->session->setFlash('warning', 'กรุณาเปลี่ยนรหัสผ่านก่อนใช้งานระบบ');
            Yii::$app->response->redirect([$this->changePasswordRoute]);
            $event->isValid = false;
            return false;
        }

        return true;
    }

    /**
     * Check if user must change password
     * 
     * @param \common\models\User $user
     * @return bool
     */
    protected function mustChangePassword($user)
    {
        // Check must_change_password flag
        if (isset($user->must_change_password) && $user->must_change_password) {
            return true;
        }
        
        return false;
    }
}
