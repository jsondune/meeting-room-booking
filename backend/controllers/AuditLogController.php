<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;
use common\models\AuditLog;

/**
 * AuditLogController - View and search audit logs
 */
class AuditLogController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'matchCallback' => function($rule, $action) {
                            if (Yii::$app->user->isGuest) {
                                return false;
                            }
                            $user = Yii::$app->user->identity;
                            // Check role from user table column
                            if ($user && isset($user->role) && in_array($user->role, ['admin', 'superadmin'])) {
                                return true;
                            }
                            // Also check RBAC if available
                            $auth = Yii::$app->authManager;
                            if ($auth) {
                                return $auth->checkAccess($user->id, 'admin') || $auth->checkAccess($user->id, 'superadmin');
                            }
                            return false;
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuditLog models with search/filter
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = AuditLog::find()->orderBy(['created_at' => SORT_DESC]);

        // Apply filters from GET parameters
        $filterAction = Yii::$app->request->get('action');
        $filterUserId = Yii::$app->request->get('user_id');
        $filterDateFrom = Yii::$app->request->get('date_from');
        $filterDateTo = Yii::$app->request->get('date_to');
        $filterModelClass = Yii::$app->request->get('model_class');
        $filterIp = Yii::$app->request->get('ip_address');

        if (!empty($filterAction)) {
            $query->andWhere(['action' => $filterAction]);
        }

        if (!empty($filterUserId)) {
            $query->andWhere(['user_id' => $filterUserId]);
        }

        if (!empty($filterDateFrom)) {
            $query->andWhere(['>=', 'created_at', $filterDateFrom . ' 00:00:00']);
        }

        if (!empty($filterDateTo)) {
            $query->andWhere(['<=', 'created_at', $filterDateTo . ' 23:59:59']);
        }

        if (!empty($filterModelClass)) {
            $query->andWhere(['like', 'model_class', $filterModelClass]);
        }

        if (!empty($filterIp)) {
            $query->andWhere(['like', 'ip_address', $filterIp]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuditLog model
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // If AJAX request, return partial view
        if (Yii::$app->request->isAjax || Yii::$app->request->get('ajax')) {
            return $this->renderPartial('_view_detail', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the AuditLog model based on its primary key value
     *
     * @param int $id
     * @return AuditLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuditLog::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบข้อมูล Audit Log ที่ต้องการ');
    }
}
