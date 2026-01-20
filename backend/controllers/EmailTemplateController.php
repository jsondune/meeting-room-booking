<?php
/**
 * EmailTemplateController - Backend email template management
 */

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\EmailTemplate;

class EmailTemplateController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function accessRules()
    {
        return [
            [
                'actions' => ['index', 'view'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 'toggle-status', 'preview', 'test-send'],
                'allow' => true,
                'roles' => ['superadmin'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbActions()
    {
        return [
            'delete' => ['post'],
            'toggle-status' => ['post'],
            'test-send' => ['post'],
        ];
    }

    /**
     * Lists all EmailTemplate models
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => EmailTemplate::find()->orderBy(['template_key' => SORT_ASC, 'name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EmailTemplate model
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
     * Creates a new EmailTemplate model
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new EmailTemplate();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'สร้างเทมเพลตอีเมลเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EmailTemplate model
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'อัปเดตเทมเพลตอีเมลเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EmailTemplate model
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'ลบเทมเพลตอีเมลเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Finds the EmailTemplate model based on its primary key value
     * @param int $id
     * @return EmailTemplate
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = EmailTemplate::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบเทมเพลตอีเมลที่ต้องการ');
    }
}
