<?php
/**
 * RoomSearch Model - Search and filter meeting rooms
 */

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MeetingRoom;

class RoomSearch extends MeetingRoom
{
    public $building_name;
    public $q;

    public function rules()
    {
        return [
            [['id', 'building_id', 'floor', 'capacity', 'status'], 'integer'],
            [['name_th', 'name_en', 'room_code', 'description', 'building_name', 'q'], 'safe'],
            [['hourly_rate', 'half_day_rate', 'full_day_rate'], 'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = MeetingRoom::find()->with(['building']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'building_id' => SORT_ASC,
                    'name_th' => SORT_ASC,
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
            'id' => $this->id,
            'building_id' => $this->building_id,
            'floor' => $this->floor,
            'capacity' => $this->capacity,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name_th', $this->name_th])
            ->andFilterWhere(['like', 'name_en', $this->name_en])
            ->andFilterWhere(['like', 'room_code', $this->room_code]);

        // General search
        if (!empty($this->q)) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $this->q],
                ['like', 'name_en', $this->q],
                ['like', 'room_code', $this->q],
            ]);
        }

        return $dataProvider;
    }
}
