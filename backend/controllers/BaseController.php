<?php
/**
 * BaseController - Base controller for backend with common functionality
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use common\models\AuditLog;
use common\models\SystemSetting;
use common\models\User;

/**
 * BaseController provides common functionality for all backend controllers
 */
abstract class BaseController extends Controller
{
    /**
     * @var string Default layout for backend
     */
    public $layout = 'main';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            return $this->checkAccess($action->id);
                        },
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return $this->redirect(['/site/login']);
                    }
                    throw new ForbiddenHttpException(Yii::t('app', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้'));
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbActions(),
            ],
        ]);
    }

    /**
     * Check if current user has access to the action
     * @param string $actionId
     * @return bool
     */
    protected function checkAccess($actionId)
    {
        // Guest users are not allowed
        if (Yii::$app->user->isGuest) {
            return false;
        }

        $user = Yii::$app->user->identity;
        if (!$user) {
            return false;
        }

        // Get required roles for this action
        $accessRules = $this->accessRules();
        
        foreach ($accessRules as $rule) {
            if (!isset($rule['allow']) || !$rule['allow']) {
                continue;
            }
            
            // Check if action matches
            if (isset($rule['actions']) && !in_array($actionId, $rule['actions'])) {
                continue;
            }
            
            // Check if user has required role
            if (isset($rule['roles'])) {
                foreach ($rule['roles'] as $role) {
                    if ($user->hasRole($role)) {
                        return true;
                    }
                }
            } else {
                // No specific roles required, allow if rule matches
                return true;
            }
        }
        
        return false;
    }

    /**
     * Returns access rules for the controller
     * Override in child controllers for specific rules
     * @return array
     */
    protected function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Returns verb filter actions
     * Override in child controllers for specific verbs
     * @return array
     */
    protected function verbActions()
    {
        return [
            'delete' => ['POST'],
            'bulk-delete' => ['POST'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Check if system is in maintenance mode
        if ($this->isMaintenanceMode() && !$this->isMaintenanceExempt()) {
            return $this->redirect(['/site/maintenance']);
        }

        return true;
    }

    /**
     * Check if system is in maintenance mode
     * @return bool
     */
    protected function isMaintenanceMode()
    {
        return SystemSetting::getValue('maintenance_mode', false);
    }

    /**
     * Check if current user is exempt from maintenance mode
     * @return bool
     */
    protected function isMaintenanceExempt()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return Yii::$app->user->identity->hasRole('superadmin');
    }

    /**
     * Log action to audit log
     * @param string $action Action name
     * @param string $modelClass Model class name
     * @param int|null $modelId Model ID
     * @param array $oldValues Old values
     * @param array $newValues New values
     * @param string|null $description Description
     */
    protected function logAction($action, $modelClass = null, $modelId = null, $oldValues = [], $newValues = [], $description = null)
    {
        AuditLog::log($action, $modelClass, $modelId, $oldValues, $newValues, $description);
    }

    /**
     * Set flash message
     * @param string $type Message type (success, error, warning, info)
     * @param string $message Message content
     */
    protected function setFlash($type, $message)
    {
        Yii::$app->session->setFlash($type, $message);
    }

    /**
     * Get pagination params from request
     * @param int $defaultPageSize Default page size
     * @return array
     */
    protected function getPaginationParams($defaultPageSize = 20)
    {
        $request = Yii::$app->request;
        return [
            'page' => max(1, (int)$request->get('page', 1)),
            'pageSize' => min(100, max(10, (int)$request->get('per-page', $defaultPageSize))),
        ];
    }

    /**
     * Export data to CSV
     * @param array $data Data to export
     * @param array $columns Column definitions
     * @param string $filename Output filename
     */
    protected function exportToCsv($data, $columns, $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Add BOM for Excel UTF-8 support
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        
        // Write header
        $headers = array_map(function ($col) {
            return is_array($col) ? $col['label'] : $col;
        }, $columns);
        fputcsv($output, $headers);
        
        // Write data
        foreach ($data as $row) {
            $rowData = [];
            foreach ($columns as $key => $col) {
                $attribute = is_array($col) ? ($col['attribute'] ?? $key) : $key;
                $value = $row[$attribute] ?? '';
                if (is_array($col) && isset($col['value']) && is_callable($col['value'])) {
                    $value = call_user_func($col['value'], $row);
                }
                $rowData[] = $value;
            }
            fputcsv($output, $rowData);
        }
        
        fclose($output);
        Yii::$app->end();
    }

    /**
     * Handle AJAX request with JSON response
     * @param callable $callback Callback function
     * @return array JSON response
     */
    protected function handleAjaxRequest($callback)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            $result = call_user_func($callback);
            return ['success' => true, 'data' => $result];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if request is AJAX
     * @return bool
     */
    protected function isAjax()
    {
        return Yii::$app->request->isAjax;
    }

    /**
     * Get current user ID
     * @return int|null
     */
    protected function getUserId()
    {
        return Yii::$app->user->isGuest ? null : Yii::$app->user->id;
    }

    /**
     * Get current user
     * @return \common\models\User|null
     */
    protected function getUser()
    {
        return Yii::$app->user->identity;
    }

    /**
     * Check if current user has permission
     * @param string $permission Permission name
     * @return bool
     */
    protected function can($permission)
    {
        return Yii::$app->user->can($permission);
    }
}
