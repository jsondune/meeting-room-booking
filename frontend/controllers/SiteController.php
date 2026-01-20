<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use common\models\MeetingRoom;
use common\models\Booking;
use common\models\LoginForm;
use common\models\SignupForm;
use common\models\PasswordResetRequestForm;
use common\models\ResetPasswordForm;
use common\models\ContactForm;
use common\models\Building;
use common\models\Holiday;

/**
 * Site controller - Frontend public pages
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'dashboard', 'profile'],
                'rules' => [
                    [
                        'actions' => ['logout', 'dashboard', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Get featured rooms
        $featuredRooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->andWhere(['is_featured' => 1])
            ->orderBy(['sort_order' => SORT_ASC])
            ->limit(6)
            ->all();

        // Get today's available rooms count
        $today = date('Y-m-d');
        $availableRoomsCount = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->count();

        // Get buildings list
        $buildings = Building::find()
            ->where(['is_active' => true])
            ->orderBy(['name_th' => SORT_ASC])
            ->all();

        // Get total bookings today
        $todayBookings = Booking::find()
            ->where(['booking_date' => $today])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]])
            ->count();

        return $this->render('index', [
            'featuredRooms' => $featuredRooms,
            'availableRoomsCount' => $availableRoomsCount,
            'buildings' => $buildings,
            'todayBookings' => $todayBookings,
        ]);
    }

    /**
     * User dashboard
     *
     * @return string
     */
    public function actionDashboard()
    {
        $user = Yii::$app->user->identity;
        $today = date('Y-m-d');

        // Get user's upcoming bookings
        $upcomingBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['>=', 'booking_date', $today])
            ->andWhere(['in', 'status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED]])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC])
            ->limit(10)
            ->all();

        // Get user's past bookings (last 30 days)
        $pastBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['<', 'booking_date', $today])
            ->orderBy(['booking_date' => SORT_DESC])
            ->limit(10)
            ->all();

        // Get user's booking statistics
        $totalBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->count();

        $completedBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => Booking::STATUS_COMPLETED])
            ->count();

        $pendingBookings = Booking::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['status' => Booking::STATUS_PENDING])
            ->count();

        // Monthly booking chart data (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $count = Booking::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['like', 'booking_date', $month . '%', false])
                ->count();
            $monthlyData[] = [
                'month' => date('M Y', strtotime($month . '-01')),
                'count' => (int)$count,
            ];
        }

        // Get available rooms for quick booking
        $quickBookingRooms = MeetingRoom::find()
            ->where(['status' => MeetingRoom::STATUS_ACTIVE])
            ->orderBy(['name_th' => SORT_ASC])
            ->limit(5)
            ->all();

        return $this->render('dashboard', [
            'user' => $user,
            'upcomingBookings' => $upcomingBookings,
            'pastBookings' => $pastBookings,
            'totalBookings' => $totalBookings,
            'completedBookings' => $completedBookings,
            'pendingBookings' => $pendingBookings,
            'monthlyData' => $monthlyData,
            'quickBookingRooms' => $quickBookingRooms,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard']);
        }

        $this->layout = 'auth';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Yii::$app->session->setFlash('success', 'ยินดีต้อนรับ ' . Yii::$app->user->identity->fullname);
            return $this->goBack(['dashboard']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->setFlash('info', 'คุณได้ออกจากระบบเรียบร้อยแล้ว');

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'ขอบคุณสำหรับการติดต่อ เราจะตอบกลับโดยเร็วที่สุด');
            } else {
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการส่งอีเมล กรุณาลองใหม่อีกครั้ง');
            }

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard']);
        }

        $this->layout = 'auth';

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'ลงทะเบียนสำเร็จ กรุณาตรวจสอบอีเมลเพื่อยืนยันบัญชี');
            return $this->redirect(['login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return Response|string
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'auth';

        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'กรุณาตรวจสอบอีเมลสำหรับคำแนะนำในการรีเซ็ตรหัสผ่าน');
                return $this->redirect(['login']);
            }

            Yii::$app->session->setFlash('error', 'ไม่พบบัญชีที่ใช้อีเมลนี้');
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return Response|string
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'auth';

        try {
            $model = new ResetPasswordForm($token);
        } catch (\yii\base\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', 'ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้องหรือหมดอายุ');
            return $this->redirect(['request-password-reset']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'รีเซ็ตรหัสผ่านสำเร็จ คุณสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้');
            return $this->redirect(['login']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @return Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new \common\models\VerifyEmailForm($token);
        } catch (\yii\base\InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', 'ลิงก์ยืนยันอีเมลไม่ถูกต้อง');
            return $this->redirect(['login']);
        }

        if ($model->verifyEmail()) {
            Yii::$app->session->setFlash('success', 'ยืนยันอีเมลสำเร็จ คุณสามารถเข้าสู่ระบบได้');
        } else {
            Yii::$app->session->setFlash('error', 'ไม่สามารถยืนยันอีเมลได้');
        }

        return $this->redirect(['login']);
    }

    /**
     * User profile page
     *
     * @return string
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;

        // Get booking statistics
        $stats = [
            'total' => Booking::find()->where(['user_id' => $user->id])->count(),
            'completed' => Booking::find()->where(['user_id' => $user->id, 'status' => Booking::STATUS_COMPLETED])->count(),
            'cancelled' => Booking::find()->where(['user_id' => $user->id, 'status' => Booking::STATUS_CANCELLED])->count(),
        ];

        return $this->render('profile', [
            'user' => $user,
            'stats' => $stats,
        ]);
    }

    /**
     * Calendar view - AJAX endpoint
     *
     * @return array
     */
    public function actionCalendarEvents()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $roomId = Yii::$app->request->get('room_id');

        $query = Booking::find()
            ->where(['between', 'booking_date', $start, $end])
            ->andWhere(['in', 'status', [Booking::STATUS_APPROVED, Booking::STATUS_PENDING]]);

        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }

        $bookings = $query->all();

        $events = [];
        foreach ($bookings as $booking) {
            $color = $booking->status === Booking::STATUS_APPROVED ? '#28a745' : '#ffc107';
            $events[] = [
                'id' => $booking->id,
                'title' => $booking->title,
                'start' => $booking->booking_date . 'T' . $booking->start_time,
                'end' => $booking->booking_date . 'T' . $booking->end_time,
                'color' => $color,
                'extendedProps' => [
                    'room' => $booking->room->name_th,
                    'status' => $booking->status,
                    'booking_code' => $booking->booking_code,
                ],
            ];
        }

        return $events;
    }

    /**
     * Get holidays for calendar
     *
     * @return array
     */
    public function actionHolidays()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $year = Yii::$app->request->get('year', date('Y'));

        $holidays = Holiday::find()
            ->where(['year' => $year])
            ->all();

        $events = [];
        foreach ($holidays as $holiday) {
            $events[] = [
                'id' => 'holiday-' . $holiday->id,
                'title' => $holiday->name_th,
                'start' => $holiday->holiday_date,
                'allDay' => true,
                'color' => '#dc3545',
                'display' => 'background',
            ];
        }

        return $events;
    }

    /**
     * Quick room availability check
     *
     * @return array
     */
    public function actionCheckAvailability()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $roomId = Yii::$app->request->get('room_id');
        $date = Yii::$app->request->get('date');
        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');

        if (!$roomId || !$date || !$startTime || !$endTime) {
            return [
                'success' => false,
                'message' => 'กรุณาระบุข้อมูลให้ครบถ้วน',
            ];
        }

        $room = MeetingRoom::findOne($roomId);
        if (!$room) {
            return [
                'success' => false,
                'message' => 'ไม่พบห้องประชุมที่ระบุ',
            ];
        }

        $isAvailable = $room->isAvailable($date, $startTime, $endTime);

        return [
            'success' => true,
            'available' => $isAvailable,
            'message' => $isAvailable ? 'ห้องว่าง สามารถจองได้' : 'ห้องไม่ว่างในช่วงเวลาที่เลือก',
            'room' => [
                'id' => $room->id,
                'name' => $room->name_th,
                'capacity' => $room->capacity,
            ],
        ];
    }

    /**
     * Displays help page.
     *
     * @return string
     */
    public function actionHelp()
    {
        return $this->render('help');
    }

    /**
     * Displays FAQ page.
     *
     * @return string
     */
    public function actionFaq()
    {
        return $this->render('faq');
    }
}
