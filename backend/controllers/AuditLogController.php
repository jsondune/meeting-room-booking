<?php
/**
 * AuditLogController - Backend audit log viewer
 */

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\AuditLog;

class AuditLogController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view', 'export'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Lists all AuditLog models
     * @return string
     */
    public function actionIndex()
    {
        $query = AuditLog::find()->orderBy(['created_at' => SORT_DESC]);

        // Filters
        $action = Yii::$app->request->get('action');
        $modelType = Yii::$app->request->get('model_type');
        $userId = Yii::$app->request->get('user_id');
        $dateFrom = Yii::$app->request->get('date_from');
        $dateTo = Yii::$app->request->get('date_to');

        if ($action) {
            $query->andWhere(['action' => $action]);
        }
        if ($modelType) {
            $query->andWhere(['model_type' => $modelType]);
        }
        if ($userId) {
            $query->andWhere(['user_id' => $userId]);
        }
        if ($dateFrom) {
            $query->andWhere(['>=', 'created_at', $dateFrom . ' 00:00:00']);
        }
        if ($dateTo) {
            $query->andWhere(['<=', 'created_at', $dateTo . ' 23:59:59']);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AuditLog model
     * @param int $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the AuditLog model based on its primary key value
     * @param int $id
     * @return AuditLog
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = AuditLog::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบ Audit Log ที่ต้องการ');
    }
}
