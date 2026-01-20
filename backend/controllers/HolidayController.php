<?php
/**
 * HolidayController - Backend controller for holiday management
 * Meeting Room Booking System
 */

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use common\models\Holiday;

/**
 * HolidayController implements the CRUD actions for Holiday model
 */
class HolidayController extends BaseController
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
                'roles' => ['manager', 'admin', 'superadmin'],
            ],
            [
                'actions' => ['create', 'update', 'delete', 'bulk-delete', 'toggle-status', 'import'],
                'allow' => true,
                'roles' => ['admin', 'superadmin'],
            ],
        ];
    }

    /**
     * Lists all Holiday models
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Holiday::find()->orderBy(['date' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Holiday model
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
     * Creates a new Holiday model
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Holiday();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'เพิ่มวันหยุดเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Holiday model
     * @param int $id
     * @return string|\yii\web\Response
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'แก้ไขวันหยุดเรียบร้อยแล้ว');
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Holiday model
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'ลบวันหยุดเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Toggle holiday status
     * @param int $id
     * @return \yii\web\Response
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = !$model->is_active;
        $model->save(false);
        
        Yii::$app->session->setFlash('success', $model->is_active ? 'เปิดใช้งานวันหยุดแล้ว' : 'ปิดใช้งานวันหยุดแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Import holidays (Thai government holidays)
     * @return string|\yii\web\Response
     */
    public function actionImport()
    {
        if (Yii::$app->request->isPost) {
            $year = Yii::$app->request->post('year', date('Y'));
            $count = $this->importThaiHolidays($year);
            Yii::$app->session->setFlash('success', "นำเข้าวันหยุดปี {$year} จำนวน {$count} รายการ");
            return $this->redirect(['index']);
        }

        return $this->render('import');
    }

    /**
     * Import Thai government holidays for a year
     * @param int $year
     * @return int Number of imported holidays
     */
    protected function importThaiHolidays($year)
    {
        $holidays = [
            ['date' => "{$year}-01-01", 'name_th' => 'วันขึ้นปีใหม่', 'name_en' => "New Year's Day", 'is_recurring' => true],
            ['date' => "{$year}-02-26", 'name_th' => 'วันมาฆบูชา', 'name_en' => 'Makha Bucha Day', 'is_recurring' => false],
            ['date' => "{$year}-04-06", 'name_th' => 'วันจักรี', 'name_en' => 'Chakri Memorial Day', 'is_recurring' => true],
            ['date' => "{$year}-04-13", 'name_th' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'is_recurring' => true],
            ['date' => "{$year}-04-14", 'name_th' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'is_recurring' => true],
            ['date' => "{$year}-04-15", 'name_th' => 'วันสงกรานต์', 'name_en' => 'Songkran Festival', 'is_recurring' => true],
            ['date' => "{$year}-05-01", 'name_th' => 'วันแรงงานแห่งชาติ', 'name_en' => 'National Labour Day', 'is_recurring' => true],
            ['date' => "{$year}-05-04", 'name_th' => 'วันฉัตรมงคล', 'name_en' => 'Coronation Day', 'is_recurring' => true],
            ['date' => "{$year}-05-22", 'name_th' => 'วันวิสาขบูชา', 'name_en' => 'Visakha Bucha Day', 'is_recurring' => false],
            ['date' => "{$year}-06-03", 'name_th' => 'วันเฉลิมพระชนมพรรษาสมเด็จพระราชินี', 'name_en' => "Queen's Birthday", 'is_recurring' => true],
            ['date' => "{$year}-07-20", 'name_th' => 'วันอาสาฬหบูชา', 'name_en' => 'Asalha Puja Day', 'is_recurring' => false],
            ['date' => "{$year}-07-21", 'name_th' => 'วันเข้าพรรษา', 'name_en' => 'Buddhist Lent Day', 'is_recurring' => false],
            ['date' => "{$year}-07-28", 'name_th' => 'วันเฉลิมพระชนมพรรษา ร.10', 'name_en' => "King's Birthday", 'is_recurring' => true],
            ['date' => "{$year}-08-12", 'name_th' => 'วันเฉลิมพระชนมพรรษา สมเด็จพระบรมราชชนนีพันปีหลวง', 'name_en' => "Queen Mother's Birthday", 'is_recurring' => true],
            ['date' => "{$year}-10-13", 'name_th' => 'วันคล้ายวันสวรรคต ร.9', 'name_en' => 'King Bhumibol Memorial Day', 'is_recurring' => true],
            ['date' => "{$year}-10-23", 'name_th' => 'วันปิยมหาราช', 'name_en' => 'Chulalongkorn Day', 'is_recurring' => true],
            ['date' => "{$year}-12-05", 'name_th' => 'วันคล้ายวันพระราชสมภพ ร.9', 'name_en' => "King Bhumibol's Birthday", 'is_recurring' => true],
            ['date' => "{$year}-12-10", 'name_th' => 'วันรัฐธรรมนูญ', 'name_en' => 'Constitution Day', 'is_recurring' => true],
            ['date' => "{$year}-12-31", 'name_th' => 'วันสิ้นปี', 'name_en' => "New Year's Eve", 'is_recurring' => true],
        ];

        $count = 0;
        foreach ($holidays as $data) {
            // Check if already exists
            $exists = Holiday::find()->where(['date' => $data['date']])->exists();
            if (!$exists) {
                $holiday = new Holiday();
                $holiday->date = $data['date'];
                $holiday->name_th = $data['name_th'];
                $holiday->name_en = $data['name_en'];
                $holiday->holiday_type = Holiday::TYPE_NATIONAL;
                $holiday->is_recurring = $data['is_recurring'];
                $holiday->is_active = true;
                if ($holiday->save()) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Finds the Holiday model based on its primary key value
     * @param int $id
     * @return Holiday
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Holiday::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบวันหยุดที่ต้องการ');
    }
}
