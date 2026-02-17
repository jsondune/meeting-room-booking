<?php
/**
 * BookingSearch Model - Search and filter bookings
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Booking;

/**
 * BookingSearch represents the model behind the search form of Booking.
 */
class BookingSearch extends Booking
{
    /**
     * @var string Date range start
     */
    public $date_from;

    /**
     * @var string Date range end
     */
    public $date_to;

    /**
     * @var string Search query
     */
    public $q;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'room_id', 'user_id', 'department_id', 'attendees_count', 'approved_by', 'created_by', 'updated_by'], 'integer'],
            [['booking_code', 'meeting_title', 'meeting_description', 'booking_date', 'start_time', 'end_time', 'status', 'meeting_type'], 'safe'],
            [['date_from', 'date_to', 'q'], 'safe'],
            [['total_cost', 'total_room_cost', 'total_equipment_cost'], 'number'],
            [['is_recurring'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Booking::find()
            ->with(['room', 'user', 'department']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'booking_date' => SORT_DESC,
                    'start_time' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // If validation fails, return empty results
            // $query->where('0=1');
            return $dataProvider;
        }

        // Grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'room_id' => $this->room_id,
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'attendees_count' => $this->attendees_count,
            'approved_by' => $this->approved_by,
            'is_recurring' => $this->is_recurring,
        ]);

        // Booking date filter
        if (!empty($this->booking_date)) {
            $query->andFilterWhere(['booking_date' => $this->booking_date]);
        }

        // Date range filter
        if (!empty($this->date_from)) {
            $query->andWhere(['>=', 'booking_date', $this->date_from]);
        }
        if (!empty($this->date_to)) {
            $query->andWhere(['<=', 'booking_date', $this->date_to]);
        }

        // Status filter
        if (!empty($this->status)) {
            $query->andFilterWhere(['status' => $this->status]);
        }

        // Meeting type filter
        if (!empty($this->meeting_type)) {
            $query->andFilterWhere(['meeting_type' => $this->meeting_type]);
        }

        // Text search filters
        $query->andFilterWhere(['like', 'booking_code', $this->booking_code])
            ->andFilterWhere(['like', 'meeting_title', $this->meeting_title])
            ->andFilterWhere(['like', 'meeting_description', $this->meeting_description]);

        // General search query
        if (!empty($this->q)) {
            $query->andWhere([
                'or',
                ['like', 'booking_code', $this->q],
                ['like', 'meeting_title', $this->q],
                ['like', 'meeting_description', $this->q],
            ]);
        }

        return $dataProvider;
    }

    /**
     * Search pending bookings for approval
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchPending($params)
    {
        $query = Booking::find()
            ->where(['status' => 'pending'])
            ->with(['room', 'user', 'department']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'booking_date' => SORT_ASC,
                    'created_at' => SORT_ASC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'room_id' => $this->room_id,
            'department_id' => $this->department_id,
        ]);

        $query->andFilterWhere(['like', 'booking_code', $this->booking_code])
            ->andFilterWhere(['like', 'meeting_title', $this->meeting_title]);

        // Date range
        if (!empty($this->date_from)) {
            $query->andWhere(['>=', 'booking_date', $this->date_from]);
        }
        if (!empty($this->date_to)) {
            $query->andWhere(['<=', 'booking_date', $this->date_to]);
        }

        return $dataProvider;
    }

    /**
     * Search bookings for today
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function searchToday($params)
    {
        $query = Booking::find()
            ->where(['booking_date' => date('Y-m-d')])
            ->andWhere(['in', 'status', ['pending', 'approved']])
            ->with(['room', 'user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'start_time' => SORT_ASC,
                ],
            ],
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'room_id' => $this->room_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }

    /**
     * Search user's own bookings
     *
     * @param array $params
     * @param int $userId
     * @return ActiveDataProvider
     */
    public function searchMyBookings($params, $userId)
    {
        $query = Booking::find()
            ->where(['user_id' => $userId])
            ->with(['room']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'booking_date' => SORT_DESC,
                    'start_time' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'room_id' => $this->room_id,
            'status' => $this->status,
        ]);

        if (!empty($this->booking_date)) {
            $query->andFilterWhere(['booking_date' => $this->booking_date]);
        }

        $query->andFilterWhere(['like', 'meeting_title', $this->meeting_title]);

        return $dataProvider;
    }

    /**
     * Get booking statistics for a date range
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @param int|null $roomId
     * @param int|null $departmentId
     * @return array
     */
    public static function getStatistics($dateFrom, $dateTo, $roomId = null, $departmentId = null)
    {
        $query = Booking::find()
            ->where(['>=', 'booking_date', $dateFrom])
            ->andWhere(['<=', 'booking_date', $dateTo]);

        if ($roomId) {
            $query->andWhere(['room_id' => $roomId]);
        }
        if ($departmentId) {
            $query->andWhere(['department_id' => $departmentId]);
        }

        return [
            'total' => (clone $query)->count(),
            'approved' => (clone $query)->andWhere(['status' => 'approved'])->count(),
            'pending' => (clone $query)->andWhere(['status' => 'pending'])->count(),
            'rejected' => (clone $query)->andWhere(['status' => 'rejected'])->count(),
            'cancelled' => (clone $query)->andWhere(['status' => 'cancelled'])->count(),
            'completed' => (clone $query)->andWhere(['status' => 'completed'])->count(),
            'totalHours' => (clone $query)
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->sum('duration_minutes') / 60 ?? 0,
            'totalCost' => (clone $query)
                ->andWhere(['in', 'status', ['approved', 'completed']])
                ->sum('total_cost') ?? 0,
        ];
    }
}
