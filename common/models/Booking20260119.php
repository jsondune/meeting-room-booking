<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use common\models\BookingEquipment;
use common\models\BookingAttendee;

/**
 * Booking Model
 *
 * @property int $id
 * @property string $booking_code
 * @property int $room_id
 * @property int $user_id
 * @property int|null $department_id
 * @property string $booking_date
 * @property string $start_time
 * @property string $end_time
 * @property int|null $duration_minutes
 * @property string $meeting_title
 * @property string|null $meeting_description
 * @property string|null $meeting_type
 * @property int $attendees_count
 * @property string|null $external_attendees
 * @property string|null $contact_person
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property bool $is_recurring
 * @property string|null $recurrence_pattern
 * @property string|null $recurrence_end_date
 * @property int|null $parent_booking_id
 * @property string $status
 * @property int|null $approved_by
 * @property string|null $approved_at
 * @property string|null $rejection_reason
 * @property int|null $cancelled_by
 * @property string|null $cancelled_at
 * @property string|null $cancel_reason
 * @property float $total_room_cost
 * @property float $total_equipment_cost
 * @property float $total_cost
 * @property string|null $special_requests
 * @property string|null $internal_notes
 * @property string|null $check_in_at
 * @property string|null $check_out_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string $created_at
 * @property string $updated_at
 *
 * @property MeetingRoom $room
 * @property User $user
 * @property Department $department
 * @property User $approvedByUser
 * @property BookingAttendee[] $attendees
 * @property BookingEquipment[] $bookingEquipment
 */
class Booking extends ActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

    const TYPE_INTERNAL = 'internal';
    const TYPE_EXTERNAL = 'external';
    const TYPE_TRAINING = 'training';
    const TYPE_INTERVIEW = 'interview';
    const TYPE_SEMINAR = 'seminar';
    const TYPE_OTHER = 'other';

    const RECURRENCE_DAILY = 'daily';
    const RECURRENCE_WEEKLY = 'weekly';
    const RECURRENCE_MONTHLY = 'monthly';

    /**
     * @var array Equipment IDs to request
     */
    public $equipmentRequests = [];

    /**
     * @var array Internal attendee user IDs
     */
    public $attendeeIds = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%booking}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
        
        // Only add BlameableBehavior if user component exists (not in console)
        if (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user')) {
            $behaviors[] = [
                'class' => BlameableBehavior::class,
            ];
        }
        
        return $behaviors;
    }

    /**
     * Override __get to support snake_case virtual attributes
     * Maps snake_case to camelCase getters
     */
    public function __get($name)
    {
        // Map snake_case virtual properties to getters
        $snakeCaseProperties = [
            'payment_status' => 'paymentStatus',
            'room_price' => 'roomPrice',
            'equipment_price' => 'equipmentPrice',
            'total_price' => 'totalPrice',
            'service_price' => 'servicePrice',
            'has_review' => 'hasReview',
        ];
        
        if (isset($snakeCaseProperties[$name])) {
            $getter = 'get' . ucfirst($snakeCaseProperties[$name]);
            if (method_exists($this, $getter)) {
                return $this->$getter();
            }
        }
        
        return parent::__get($name);
    }

    /**
     * Override __isset to support snake_case virtual attributes
     */
    public function __isset($name)
    {
        $snakeCaseProperties = [
            'payment_status' => 'paymentStatus',
            'room_price' => 'roomPrice',
            'equipment_price' => 'equipmentPrice',
            'total_price' => 'totalPrice',
            'service_price' => 'servicePrice',
            'has_review' => 'hasReview',
        ];
        
        if (isset($snakeCaseProperties[$name])) {
            $getter = 'get' . ucfirst($snakeCaseProperties[$name]);
            if (method_exists($this, $getter)) {
                return $this->$getter() !== null;
            }
        }
        
        return parent::__isset($name);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // Required fields
            [['room_id', 'booking_date', 'start_time', 'end_time', 'meeting_title'], 'required'],
            
            // Integer fields
            [['room_id', 'user_id', 'department_id', 'attendees_count', 'duration_minutes', 
              'parent_booking_id', 'approved_by', 'cancelled_by'], 'integer'],
            
            // Existence checks
            ['room_id', 'exist', 'targetClass' => MeetingRoom::class, 'targetAttribute' => 'id'],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
            ['department_id', 'exist', 'targetClass' => Department::class, 'targetAttribute' => 'id'],
            
            // String fields
            ['booking_code', 'string', 'max' => 20],
            ['meeting_title', 'string', 'max' => 255],
            ['meeting_type', 'string', 'max' => 50],
            ['meeting_type', 'in', 'range' => array_keys(self::getTypeOptions())],
            ['contact_person', 'string', 'max' => 100],
            ['contact_phone', 'string', 'max' => 20],
            ['contact_email', 'email'],
            ['status', 'string', 'max' => 20],
            ['status', 'in', 'range' => array_keys(self::getStatusOptions())],
            ['recurrence_pattern', 'string', 'max' => 20],
            ['recurrence_pattern', 'in', 'range' => array_keys(self::getRecurrenceOptions())],
            
            // Text fields
            [['meeting_description', 'external_attendees', 'rejection_reason', 
              'cancel_reason', 'special_requests', 'internal_notes'], 'string'],
            
            // Date and time fields
            ['booking_date', 'date', 'format' => 'php:Y-m-d'],
            ['booking_date', 'validateBookingDate'],
            [['start_time', 'end_time'], 'match', 'pattern' => '/^\d{2}:\d{2}(:\d{2})?$/', 'message' => 'รูปแบบเวลาไม่ถูกต้อง (HH:MM)'],
            ['end_time', 'validateEndTimeAfterStart'],
            ['recurrence_end_date', 'date', 'format' => 'php:Y-m-d'],
            
            // Boolean fields
            ['is_recurring', 'boolean'],
            
            // Numeric fields
            [['total_room_cost', 'total_equipment_cost', 'total_cost'], 'number', 'min' => 0],
            
            // Defaults
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['attendees_count', 'default', 'value' => 1],
            ['is_recurring', 'default', 'value' => false],
            [['total_room_cost', 'total_equipment_cost', 'total_cost'], 'default', 'value' => 0],
            
            // Custom validation
            ['room_id', 'validateRoomAvailability'],
            ['attendees_count', 'validateCapacity'],
            
            // Safe attributes
            [['equipmentRequests', 'attendeeIds'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'booking_code' => 'รหัสการจอง',
            'room_id' => 'ห้องประชุม',
            'user_id' => 'ผู้จอง',
            'department_id' => 'หน่วยงาน',
            'booking_date' => 'วันที่จอง',
            'start_time' => 'เวลาเริ่ม',
            'end_time' => 'เวลาสิ้นสุด',
            'duration_minutes' => 'ระยะเวลา (นาที)',
            'meeting_title' => 'หัวข้อการประชุม',
            'meeting_description' => 'รายละเอียด',
            'meeting_type' => 'ประเภทการประชุม',
            'attendees_count' => 'จำนวนผู้เข้าร่วม',
            'external_attendees' => 'ผู้เข้าร่วมภายนอก',
            'contact_person' => 'ผู้ติดต่อ',
            'contact_phone' => 'เบอร์ติดต่อ',
            'contact_email' => 'อีเมลติดต่อ',
            'is_recurring' => 'การจองซ้ำ',
            'recurrence_pattern' => 'รูปแบบการจองซ้ำ',
            'recurrence_end_date' => 'วันสิ้นสุดการจองซ้ำ',
            'status' => 'สถานะ',
            'approved_by' => 'อนุมัติโดย',
            'approved_at' => 'วันที่อนุมัติ',
            'rejection_reason' => 'เหตุผลที่ปฏิเสธ',
            'cancelled_by' => 'ยกเลิกโดย',
            'cancelled_at' => 'วันที่ยกเลิก',
            'cancel_reason' => 'เหตุผลที่ยกเลิก',
            'total_room_cost' => 'ค่าใช้จ่ายห้อง',
            'total_equipment_cost' => 'ค่าใช้จ่ายอุปกรณ์',
            'total_cost' => 'ค่าใช้จ่ายรวม',
            'special_requests' => 'คำขอพิเศษ',
            'internal_notes' => 'หมายเหตุภายใน',
            'check_in_at' => 'เวลาเช็คอิน',
            'check_out_at' => 'เวลาเช็คเอาท์',
            'equipmentRequests' => 'อุปกรณ์ที่ต้องการ',
            'attendeeIds' => 'ผู้เข้าร่วมประชุม',
            'created_at' => 'สร้างเมื่อ',
            'updated_at' => 'แก้ไขล่าสุด',
        ];
    }

    /**
     * Validate booking date
     */
    public function validateBookingDate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $bookingDate = strtotime($this->$attribute);
            $today = strtotime(date('Y-m-d'));
            
            // Check if past date
            if ($bookingDate < $today) {
                $allowPast = Yii::$app->params['booking.allowPastBooking'] ?? false;
                if (!$allowPast) {
                    $this->addError($attribute, 'ไม่สามารถจองวันที่ผ่านมาแล้วได้');
                }
            }
            
            // Check advance booking limit
            if ($this->room) {
                $maxAdvanceDays = $this->room->advance_booking_days;
                $maxDate = strtotime("+{$maxAdvanceDays} days");
                if ($bookingDate > $maxDate) {
                    $this->addError($attribute, "สามารถจองล่วงหน้าได้ไม่เกิน {$maxAdvanceDays} วัน");
                }
            }
        }
    }

    /**
     * Validate end time is after start time
     */
    public function validateEndTimeAfterStart($attribute, $params)
    {
        if (!$this->hasErrors() && $this->start_time && $this->end_time) {
            if (strtotime($this->end_time) <= strtotime($this->start_time)) {
                $this->addError($attribute, 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น');
            }
        }
    }

    /**
     * Validate room availability
     */
    public function validateRoomAvailability($attribute, $params)
    {
        if (!$this->hasErrors() && $this->room) {
            if (!$this->room->isAvailable(
                $this->booking_date,
                $this->start_time,
                $this->end_time,
                $this->isNewRecord ? null : $this->id
            )) {
                $this->addError($attribute, 'ห้องประชุมไม่ว่างในช่วงเวลาที่เลือก');
            }
        }
    }

    /**
     * Validate attendees count against room capacity
     */
    public function validateCapacity($attribute, $params)
    {
        if (!$this->hasErrors() && $this->room) {
            if ($this->$attribute > $this->room->capacity) {
                $this->addError($attribute, "ห้องนี้รองรับได้สูงสุด {$this->room->capacity} คน");
            }
        }
    }

    /**
     * Before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Generate booking code for new records
            if ($insert && empty($this->booking_code)) {
                $this->booking_code = $this->generateBookingCode();
            }
            
            // Set user_id if not set (only in web context)
            if (!$this->user_id && Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest) {
                $this->user_id = Yii::$app->user->id;
            }
            
            // Calculate duration
            if ($this->start_time && $this->end_time) {
                $start = strtotime($this->start_time);
                $end = strtotime($this->end_time);
                $this->duration_minutes = ($end - $start) / 60;
            }
            
            // Calculate costs (only if room is loaded)
            if ($this->room_id && $this->room) {
                $hours = ($this->duration_minutes ?? 60) / 60;
                $this->total_room_cost = ($this->room->hourly_rate ?? 0) * $hours;
            } else {
                $this->total_room_cost = 0;
            }
            $this->total_cost = ($this->total_room_cost ?? 0) + ($this->total_equipment_cost ?? 0);
            
            // Auto-approve if room doesn't require approval
            if ($insert && $this->status === self::STATUS_PENDING) {
                if ($this->room && !$this->room->requires_approval) {
                    $this->status = self::STATUS_APPROVED;
                    $this->approved_at = date('Y-m-d H:i:s');
                }
            }
            
            return true;
        }
        return false;
    }

    /**
     * After save
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Save equipment requests
        if (!empty($this->equipmentRequests)) {
            $this->saveEquipmentRequests();
        }
        
        // Save attendees
        if (!empty($this->attendeeIds)) {
            $this->saveAttendees();
        }
        
        // Create recurring bookings
        if ($insert && $this->is_recurring && $this->recurrence_pattern && $this->recurrence_end_date) {
            $this->createRecurringBookings();
        }
        
        // Send notifications
        if ($insert) {
            $this->sendBookingConfirmation();
        } elseif (isset($changedAttributes['status'])) {
            $this->sendStatusNotification($changedAttributes['status']);
        }
        
        // Log audit
        AuditLog::log(
            $insert ? 'create' : 'update',
            static::class,
            $this->id,
            $insert ? [] : $changedAttributes,
            $this->attributes,
            "Booking: {$this->booking_code}"
        );
    }

    /**
     * Generate unique booking code
     */
    protected function generateBookingCode()
    {
        $prefix = 'BK';
        $year = date('y');
        $month = date('m');
        
        // Find the last code for this month
        $lastBooking = static::find()
            ->where(['like', 'booking_code', "{$prefix}{$year}{$month}%", false])
            ->orderBy(['booking_code' => SORT_DESC])
            ->one();
        
        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate costs
     */
    public function calculateCosts()
    {
        // Room cost
        if ($this->room) {
            $this->total_room_cost = $this->room->calculateCost($this->start_time, $this->end_time);
        }
        
        // Equipment cost will be calculated in saveEquipmentRequests
        $this->total_cost = $this->total_room_cost + $this->total_equipment_cost;
    }

    /**
     * Save equipment requests
     */
    public function saveEquipmentRequests($equipmentRequests = null)
    {
        // Use passed parameter or property
        $requests = $equipmentRequests ?? $this->equipmentRequests ?? [];
        
        if (empty($requests)) {
            return;
        }
        
        // Delete existing requests
        BookingEquipment::deleteAll(['booking_id' => $this->id]);
        
        $totalEquipmentCost = 0;
        
        foreach ($requests as $request) {
            $equipmentId = $request['equipment_id'] ?? null;
            $quantity = $request['quantity'] ?? 1;
            
            if (!$equipmentId) continue;
            
            $equipment = Equipment::findOne($equipmentId);
            if (!$equipment) continue;
            
            $bookingEquipment = new BookingEquipment();
            $bookingEquipment->booking_id = $this->id;
            $bookingEquipment->equipment_id = $equipmentId;
            $bookingEquipment->quantity_requested = $quantity;
            
            // Calculate cost based on duration
            $durationHours = $this->duration_minutes / 60;
            if ($durationHours >= 8) {
                $bookingEquipment->unit_price = $equipment->daily_rate;
            } else {
                $bookingEquipment->unit_price = $equipment->hourly_rate * ceil($durationHours);
            }
            $bookingEquipment->total_price = $bookingEquipment->unit_price * $quantity;
            
            $bookingEquipment->status = 'pending';
            $bookingEquipment->save();
            
            $totalEquipmentCost += $bookingEquipment->total_price;
        }
        
        // Update equipment cost
        $this->total_equipment_cost = $totalEquipmentCost;
        $this->total_cost = $this->total_room_cost + $this->total_equipment_cost;
        $this->save(false, ['total_equipment_cost', 'total_cost']);
    }

    /**
     * Save attendees
     */
    public function saveAttendees($attendeeIds = null)
    {
        // Use passed parameter or property
        $ids = $attendeeIds ?? $this->attendeeIds ?? [];
        
        if (empty($ids)) {
            return;
        }
        
        // Delete existing attendees (except external)
        BookingAttendee::deleteAll(['and', ['booking_id' => $this->id], ['not', ['user_id' => null]]]);
        
        foreach ($ids as $userId) {
            $attendee = new BookingAttendee();
            $attendee->booking_id = $this->id;
            $attendee->user_id = $userId;
            $attendee->is_organizer = ($userId == $this->user_id);
            $attendee->save();
        }
    }

    /**
     * Create recurring bookings
     */
    public function createRecurringBookings()
    {
        $currentDate = strtotime($this->booking_date);
        $endDate = strtotime($this->recurrence_end_date);
        
        $interval = '+1 day';
        switch ($this->recurrence_pattern) {
            case self::RECURRENCE_DAILY:
                $interval = '+1 day';
                break;
            case self::RECURRENCE_WEEKLY:
                $interval = '+1 week';
                break;
            case self::RECURRENCE_MONTHLY:
                $interval = '+1 month';
                break;
        }
        
        $currentDate = strtotime($interval, $currentDate);
        
        while ($currentDate <= $endDate) {
            $bookingDate = date('Y-m-d', $currentDate);
            
            // Check room availability
            if ($this->room->isAvailable($bookingDate, $this->start_time, $this->end_time)) {
                $booking = new Booking();
                $booking->attributes = $this->attributes;
                $booking->id = null;
                $booking->booking_code = null;
                $booking->booking_date = $bookingDate;
                $booking->parent_booking_id = $this->id;
                $booking->is_recurring = false;
                $booking->recurrence_pattern = null;
                $booking->recurrence_end_date = null;
                $booking->save(false);
            }
            
            $currentDate = strtotime($interval, $currentDate);
        }
    }

    /**
     * Approve booking
     */
    public function approve($userId = null)
    {
        $this->status = self::STATUS_APPROVED;
        $this->approved_by = $userId ?? (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest ? Yii::$app->user->id : null);
        $this->approved_at = date('Y-m-d H:i:s');
        return $this->save(false, ['status', 'approved_by', 'approved_at']);
    }

    /**
     * Reject booking
     */
    public function reject($reason, $userId = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->rejection_reason = $reason;
        $this->approved_by = $userId ?? (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest ? Yii::$app->user->id : null);
        $this->approved_at = date('Y-m-d H:i:s');
        return $this->save(false, ['status', 'rejection_reason', 'approved_by', 'approved_at']);
    }

    /**
     * Cancel booking
     */
    public function cancel($reason, $userId = null)
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancel_reason = $reason;
        $this->cancelled_by = $userId ?? (Yii::$app instanceof \yii\web\Application && Yii::$app->has('user') && !Yii::$app->user->isGuest ? Yii::$app->user->id : null);
        $this->cancelled_at = date('Y-m-d H:i:s');
        return $this->save(false, ['status', 'cancel_reason', 'cancelled_by', 'cancelled_at']);
    }

    /**
     * Check in
     */
    public function checkIn()
    {
        $this->check_in_at = date('Y-m-d H:i:s');
        return $this->save(false, ['check_in_at']);
    }

    /**
     * Check out
     */
    public function checkOut()
    {
        $this->check_out_at = date('Y-m-d H:i:s');
        $this->status = self::STATUS_COMPLETED;
        return $this->save(false, ['check_out_at', 'status']);
    }

    /**
     * Send booking confirmation
     */
    public function sendBookingConfirmation()
    {
        // Send notification to user
        Notification::notify(
            $this->user_id,
            'booking_created',
            'การจองห้องประชุมสำเร็จ',
            "การจอง {$this->booking_code} - {$this->meeting_title} ได้รับการบันทึกแล้ว",
            ['booking_id' => $this->id],
            '/booking/view?id=' . $this->id
        );
        
        // TODO: Send email notification
    }

    /**
     * Send status notification
     */
    public function sendStatusNotification($oldStatus)
    {
        $messages = [
            self::STATUS_APPROVED => 'การจองได้รับการอนุมัติแล้ว',
            self::STATUS_REJECTED => 'การจองถูกปฏิเสธ',
            self::STATUS_CANCELLED => 'การจองถูกยกเลิก',
        ];
        
        if (isset($messages[$this->status])) {
            Notification::notify(
                $this->user_id,
                'booking_status_changed',
                $messages[$this->status],
                "การจอง {$this->booking_code} - {$this->meeting_title}",
                ['booking_id' => $this->id],
                '/booking/view?id=' . $this->id
            );
        }
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_REJECTED])) {
            return false;
        }
        
        // Can't cancel past bookings
        $bookingDateTime = strtotime($this->booking_date . ' ' . $this->start_time);
        if ($bookingDateTime < time()) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if booking can be edited
     */
    public function canBeEdited()
    {
        if (in_array($this->status, [self::STATUS_CANCELLED, self::STATUS_COMPLETED, self::STATUS_REJECTED])) {
            return false;
        }
        
        // Can't edit past bookings
        $bookingDateTime = strtotime($this->booking_date . ' ' . $this->start_time);
        if ($bookingDateTime < time()) {
            return false;
        }
        
        return true;
    }

    /**
     * Get status label with badge
     */
    public function getStatusLabel()
    {
        $labels = [
            self::STATUS_PENDING => '<span class="badge bg-warning text-dark">รอดำเนินการ</span>',
            self::STATUS_APPROVED => '<span class="badge bg-success">อนุมัติแล้ว</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">ปฏิเสธ</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">ยกเลิก</span>',
            self::STATUS_COMPLETED => '<span class="badge bg-info">เสร็จสิ้น</span>',
        ];
        return $labels[$this->status] ?? '<span class="badge bg-secondary">ไม่ทราบ</span>';
    }

    /**
     * Get type label
     */
    public function getTypeLabel()
    {
        $types = self::getTypeOptions();
        return $types[$this->meeting_type] ?? $this->meeting_type;
    }

    /**
     * Get duration formatted
     */
    public function getDurationFormatted()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        $parts = [];
        if ($hours > 0) $parts[] = "{$hours} ชั่วโมง";
        if ($minutes > 0) $parts[] = "{$minutes} นาที";
        
        return implode(' ', $parts);
    }

    /**
     * Get date time formatted
     */
    public function getDateTimeFormatted()
    {
        $date = Yii::$app->formatter->asDate($this->booking_date, 'php:d M Y');
        return "{$date} เวลา {$this->start_time} - {$this->end_time}";
    }

    /**
     * Get status options
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => 'รอดำเนินการ',
            self::STATUS_APPROVED => 'อนุมัติแล้ว',
            self::STATUS_REJECTED => 'ปฏิเสธ',
            self::STATUS_CANCELLED => 'ยกเลิก',
            self::STATUS_COMPLETED => 'เสร็จสิ้น',
        ];
    }

    /**
     * Get status labels (alias for getStatusOptions)
     */
    public static function getStatusLabels()
    {
        return self::getStatusOptions();
    }

    /**
     * Get type options
     */
    public static function getTypeOptions()
    {
        return [
            self::TYPE_INTERNAL => 'ประชุมภายใน',
            self::TYPE_EXTERNAL => 'ประชุมกับบุคคลภายนอก',
            self::TYPE_TRAINING => 'อบรม/สัมมนา',
            self::TYPE_INTERVIEW => 'สัมภาษณ์',
            self::TYPE_SEMINAR => 'ประชุมวิชาการ',
            self::TYPE_OTHER => 'อื่นๆ',
        ];
    }

    /**
     * Get recurrence options
     */
    public static function getRecurrenceOptions()
    {
        return [
            self::RECURRENCE_DAILY => 'ทุกวัน',
            self::RECURRENCE_WEEKLY => 'ทุกสัปดาห์',
            self::RECURRENCE_MONTHLY => 'ทุกเดือน',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(MeetingRoom::class, ['id' => 'room_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id' => 'department_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedByUser()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCancelledByUser()
    {
        return $this->hasOne(User::class, ['id' => 'cancelled_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentBooking()
    {
        return $this->hasOne(Booking::class, ['id' => 'parent_booking_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildBookings()
    {
        return $this->hasMany(Booking::class, ['parent_booking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendees()
    {
        return $this->hasMany(BookingAttendee::class, ['booking_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBookingEquipment()
    {
        return $this->hasMany(BookingEquipment::class, ['booking_id' => 'id']);
    }

    /**
     * Alias for meeting_title (backward compatibility)
     * @return string|null
     */
    public function getTitle()
    {
        return $this->meeting_title;
    }

    /**
     * Alias for meeting_description (backward compatibility)
     * @return string|null
     */
    public function getDescription()
    {
        return $this->meeting_description;
    }

    /**
     * Alias for meeting_type (backward compatibility)
     * @return string|null
     */
    public function getPurpose()
    {
        return $this->meeting_type;
    }

    /**
     * Alias for meeting_type (backward compatibility for booking_type)
     * @return string|null
     */
    public function getBooking_type()
    {
        return $this->meeting_type;
    }

    /**
     * Setter for booking_type (backward compatibility)
     * @param string|null $value
     */
    public function setBooking_type($value)
    {
        $this->meeting_type = $value;
    }

    /**
     * Alias for attendees_count (backward compatibility for attendee_count)
     * @return int
     */
    public function getAttendee_count()
    {
        return $this->attendees_count;
    }

    /**
     * Setter for attendee_count (backward compatibility)
     * @param int $value
     */
    public function setAttendee_count($value)
    {
        $this->attendees_count = $value;
    }

    /**
     * Alias for internal_notes (backward compatibility)
     * @return string|null
     */
    public function getNotes()
    {
        return $this->internal_notes;
    }

    /**
     * Alias for contact_person (backward compatibility for contact_name)
     * @return string|null
     */
    public function getContact_name()
    {
        return $this->contact_person;
    }

    /**
     * Setter for contact_name (backward compatibility)
     * @param string|null $value
     */
    public function setContact_name($value)
    {
        $this->contact_person = $value;
    }

    /**
     * Get service price (alias for total_equipment_cost)
     * @return float
     */
    public function getServicePrice()
    {
        return $this->total_equipment_cost ?? 0;
    }

    /**
     * Get payment status (calculated based on total_cost)
     * @return string|null
     */
    public function getPaymentStatus()
    {
        if ($this->total_cost <= 0) {
            return null; // Free booking
        }
        // For now, return 'pending' - can be enhanced with actual payment tracking
        return 'pending';
    }

    /**
     * Get room price (alias for total_room_cost)
     * @return float
     */
    public function getRoomPrice()
    {
        return $this->total_room_cost ?? 0;
    }

    /**
     * Get equipment price (alias for total_equipment_cost)
     * @return float
     */
    public function getEquipmentPrice()
    {
        return $this->total_equipment_cost ?? 0;
    }

    /**
     * Get total price (alias for total_cost)
     * @return float
     */
    public function getTotalPrice()
    {
        return $this->total_cost ?? 0;
    }

    /**
     * Check if booking has review
     * @return bool
     */
    public function getHasReview()
    {
        // For now, return false - can be enhanced with actual review tracking
        return false;
    }

    /**
     * Find today's bookings
     */
    public static function findTodayBookings()
    {
        return static::find()
            ->where(['booking_date' => date('Y-m-d')])
            ->andWhere(['not in', 'status', [self::STATUS_CANCELLED, self::STATUS_REJECTED]])
            ->orderBy(['start_time' => SORT_ASC]);
    }

    /**
     * Find pending bookings
     */
    public static function findPending()
    {
        return static::find()
            ->where(['status' => self::STATUS_PENDING])
            ->andWhere(['>=', 'booking_date', date('Y-m-d')])
            ->orderBy(['booking_date' => SORT_ASC, 'start_time' => SORT_ASC]);
    }

    /**
     * Find user's bookings
     */
    public static function findByUser($userId)
    {
        return static::find()
            ->where(['user_id' => $userId])
            ->orderBy(['booking_date' => SORT_DESC, 'start_time' => SORT_DESC]);
    }
}
