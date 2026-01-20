<?php
/**
 * BuildingController - API controller for building information
 * Meeting Room Booking System
 * 
 * @author Digital Technology & AI Division
 * @version 1.0.0
 */

namespace api\controllers\v1;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Building;
use common\models\MeetingRoom;

/**
 * BuildingController provides RESTful API for buildings
 */
class BuildingController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Allow listing buildings without authentication
        $behaviors['authenticator']['optional'] = ['index', 'view', 'rooms', 'floors'];
        
        return $behaviors;
    }

    /**
     * GET /api/v1/buildings
     * List all active buildings
     * 
     * @return array
     */
    public function actionIndex()
    {
        $query = Building::find()
            ->where(['status' => Building::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC]);

        // Search filter
        $search = Yii::$app->request->get('search');
        if ($search) {
            $query->andWhere([
                'or',
                ['like', 'name_th', $search],
                ['like', 'name_en', $search],
                ['like', 'code', $search],
            ]);
        }

        // Include room count
        $withRooms = Yii::$app->request->get('with_rooms', false);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 20),
            ],
        ]);

        $buildings = [];
        foreach ($dataProvider->getModels() as $building) {
            $data = $this->formatBuilding($building);
            
            if ($withRooms) {
                $data['rooms'] = $this->getRoomSummary($building->id);
            }
            
            $buildings[] = $data;
        }

        return $this->paginate($buildings, $dataProvider->getPagination());
    }

    /**
     * GET /api/v1/buildings/{id}
     * Get building details
     * 
     * @param int $id
     * @return array
     */
    public function actionView($id)
    {
        $building = $this->findBuilding($id);

        $data = $this->formatBuilding($building, true);
        
        // Add floors with room counts
        $data['floors'] = $this->getFloorsWithRooms($building->id);
        
        // Add all rooms grouped by floor
        $data['rooms_by_floor'] = $this->getRoomsByFloor($building->id);
        
        // Statistics
        $data['statistics'] = $this->getBuildingStatistics($building->id);

        return $this->success($data);
    }

    /**
     * GET /api/v1/buildings/{id}/rooms
     * Get all rooms in a building
     * 
     * @param int $id
     * @return array
     */
    public function actionRooms($id)
    {
        $building = $this->findBuilding($id);
        
        $query = MeetingRoom::find()
            ->where([
                'building_id' => $building->id,
                'status' => MeetingRoom::STATUS_ACTIVE,
            ])
            ->orderBy(['floor' => SORT_ASC, 'room_number' => SORT_ASC]);

        // Filter by floor
        $floor = Yii::$app->request->get('floor');
        if ($floor !== null) {
            $query->andWhere(['floor' => $floor]);
        }

        // Filter by capacity
        $minCapacity = Yii::$app->request->get('min_capacity');
        if ($minCapacity) {
            $query->andWhere(['>=', 'capacity', $minCapacity]);
        }

        // Filter by room type
        $roomType = Yii::$app->request->get('room_type');
        if ($roomType) {
            $query->andWhere(['room_type' => $roomType]);
        }

        // Filter by features
        $features = Yii::$app->request->get('features');
        if ($features) {
            $featureList = is_array($features) ? $features : explode(',', $features);
            foreach ($featureList as $feature) {
                $query->andWhere(['like', 'features', trim($feature)]);
            }
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per_page', 50),
            ],
        ]);

        $rooms = [];
        foreach ($dataProvider->getModels() as $room) {
            $rooms[] = $this->formatRoom($room);
        }

        return $this->paginate($rooms, $dataProvider->getPagination());
    }

    /**
     * GET /api/v1/buildings/{id}/floors
     * Get floor information for a building
     * 
     * @param int $id
     * @return array
     */
    public function actionFloors($id)
    {
        $building = $this->findBuilding($id);
        
        $floors = $this->getFloorsWithRooms($building->id);
        
        return $this->success([
            'building' => [
                'id' => $building->id,
                'name_th' => $building->name_th,
                'name_en' => $building->name_en,
                'total_floors' => $building->total_floors,
            ],
            'floors' => $floors,
        ]);
    }

    /**
     * GET /api/v1/buildings/dropdown
     * Get buildings for dropdown selection
     * 
     * @return array
     */
    public function actionDropdown()
    {
        $buildings = Building::find()
            ->where(['status' => Building::STATUS_ACTIVE])
            ->orderBy(['sort_order' => SORT_ASC, 'name_th' => SORT_ASC])
            ->all();

        $data = [];
        foreach ($buildings as $building) {
            $data[] = [
                'id' => $building->id,
                'name' => $building->name_th,
                'name_en' => $building->name_en ?? $building->name_th,
                'code' => $building->code,
                'total_floors' => $building->total_floors,
            ];
        }

        return $this->success($data);
    }

    /**
     * Format building data
     * 
     * @param Building $building
     * @param bool $detailed
     * @return array
     */
    protected function formatBuilding($building, $detailed = false)
    {
        $data = [
            'id' => $building->id,
            'code' => $building->code,
            'name_th' => $building->name_th,
            'name_en' => $building->name_en,
            'total_floors' => $building->total_floors,
            'address' => $building->address,
            'status' => $building->status,
        ];

        if ($detailed) {
            $data['description'] = $building->description;
            $data['latitude'] = $building->latitude;
            $data['longitude'] = $building->longitude;
            $data['phone'] = $building->phone;
            $data['email'] = $building->email;
            $data['operating_hours'] = [
                'open' => $building->operating_hours_start,
                'close' => $building->operating_hours_end,
            ];
            $data['image_url'] = $building->getImageUrl();
            $data['created_at'] = $building->created_at;
            $data['updated_at'] = $building->updated_at;
        }

        return $data;
    }

    /**
     * Format room data
     * 
     * @param MeetingRoom $room
     * @return array
     */
    protected function formatRoom($room)
    {
        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'name_th' => $room->name_th,
            'name_en' => $room->name_en,
            'floor' => $room->floor,
            'capacity' => $room->capacity,
            'room_type' => $room->room_type,
            'features' => $room->getFeaturesList(),
            'hourly_rate' => (float) $room->hourly_rate,
            'requires_approval' => (bool) $room->requires_approval,
            'thumbnail_url' => $room->getThumbnailUrl(),
        ];
    }

    /**
     * Get room summary for building
     * 
     * @param int $buildingId
     * @return array
     */
    protected function getRoomSummary($buildingId)
    {
        $rooms = MeetingRoom::find()
            ->where([
                'building_id' => $buildingId,
                'status' => MeetingRoom::STATUS_ACTIVE,
            ])
            ->all();

        $totalCapacity = 0;
        $types = [];
        
        foreach ($rooms as $room) {
            $totalCapacity += $room->capacity;
            if (!isset($types[$room->room_type])) {
                $types[$room->room_type] = 0;
            }
            $types[$room->room_type]++;
        }

        return [
            'total' => count($rooms),
            'total_capacity' => $totalCapacity,
            'by_type' => $types,
        ];
    }

    /**
     * Get floors with room counts
     * 
     * @param int $buildingId
     * @return array
     */
    protected function getFloorsWithRooms($buildingId)
    {
        $floorData = MeetingRoom::find()
            ->select(['floor', 'COUNT(*) as room_count', 'SUM(capacity) as total_capacity'])
            ->where([
                'building_id' => $buildingId,
                'status' => MeetingRoom::STATUS_ACTIVE,
            ])
            ->groupBy('floor')
            ->orderBy(['floor' => SORT_ASC])
            ->asArray()
            ->all();

        $floors = [];
        foreach ($floorData as $data) {
            $floors[] = [
                'floor' => (int) $data['floor'],
                'room_count' => (int) $data['room_count'],
                'total_capacity' => (int) $data['total_capacity'],
            ];
        }

        return $floors;
    }

    /**
     * Get rooms grouped by floor
     * 
     * @param int $buildingId
     * @return array
     */
    protected function getRoomsByFloor($buildingId)
    {
        $rooms = MeetingRoom::find()
            ->where([
                'building_id' => $buildingId,
                'status' => MeetingRoom::STATUS_ACTIVE,
            ])
            ->orderBy(['floor' => SORT_ASC, 'room_number' => SORT_ASC])
            ->all();

        $grouped = [];
        foreach ($rooms as $room) {
            $floor = $room->floor;
            if (!isset($grouped[$floor])) {
                $grouped[$floor] = [];
            }
            $grouped[$floor][] = $this->formatRoom($room);
        }

        return $grouped;
    }

    /**
     * Get building statistics
     * 
     * @param int $buildingId
     * @return array
     */
    protected function getBuildingStatistics($buildingId)
    {
        $rooms = MeetingRoom::find()
            ->where([
                'building_id' => $buildingId,
                'status' => MeetingRoom::STATUS_ACTIVE,
            ])
            ->all();

        $roomIds = array_map(function($r) { return $r->id; }, $rooms);

        // Today's bookings
        $todayBookings = \common\models\Booking::find()
            ->where(['room_id' => $roomIds])
            ->andWhere(['booking_date' => date('Y-m-d')])
            ->andWhere(['in', 'status', ['approved', 'pending']])
            ->count();

        // This month bookings
        $monthBookings = \common\models\Booking::find()
            ->where(['room_id' => $roomIds])
            ->andWhere(['>=', 'booking_date', date('Y-m-01')])
            ->andWhere(['<=', 'booking_date', date('Y-m-t')])
            ->andWhere(['in', 'status', ['approved', 'completed']])
            ->count();

        return [
            'total_rooms' => count($rooms),
            'total_capacity' => array_sum(array_map(function($r) { return $r->capacity; }, $rooms)),
            'today_bookings' => (int) $todayBookings,
            'month_bookings' => (int) $monthBookings,
        ];
    }

    /**
     * Find building by ID
     * 
     * @param int $id
     * @return Building
     */
    protected function findBuilding($id)
    {
        $building = Building::findOne($id);
        
        if (!$building) {
            return $this->error('Building not found', 404);
        }
        
        if ($building->status !== Building::STATUS_ACTIVE) {
            return $this->error('Building is not available', 400);
        }
        
        return $building;
    }
}
