<?php
/**
 * Booking Model Unit Tests
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 */

namespace tests\unit;

use Codeception\Test\Unit;
use common\models\Booking;
use common\models\MeetingRoom;
use common\models\User;
use Yii;

class BookingTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    /**
     * @var User Test user
     */
    protected static $testUser;
    
    /**
     * @var MeetingRoom Test room
     */
    protected static $testRoom;
    
    /**
     * Set up test fixtures
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        
        // Create test user
        self::$testUser = new User();
        self::$testUser->username = 'bookingtest_' . time();
        self::$testUser->email = 'bookingtest_' . time() . '@example.com';
        self::$testUser->setPassword('Test123!');
        self::$testUser->generateAuthKey();
        self::$testUser->status = User::STATUS_ACTIVE;
        self::$testUser->save(false);
        
        // Create test room
        self::$testRoom = new MeetingRoom();
        self::$testRoom->name = 'Test Room ' . time();
        self::$testRoom->code = 'TEST-' . time();
        self::$testRoom->capacity = 20;
        self::$testRoom->hourly_rate = 100;
        self::$testRoom->is_active = 1;
        self::$testRoom->status = MeetingRoom::STATUS_AVAILABLE;
        self::$testRoom->save(false);
    }
    
    /**
     * Clean up test fixtures
     */
    public static function tearDownAfterClass(): void
    {
        if (self::$testUser) {
            self::$testUser->delete();
        }
        if (self::$testRoom) {
            self::$testRoom->delete();
        }
        
        parent::tearDownAfterClass();
    }
    
    /**
     * Test booking creation
     */
    public function testCreateBooking()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Test Meeting';
        $booking->description = 'Test description';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+1 day'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+1 day'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        
        $this->assertTrue($booking->save(), 'Booking should be saved');
        $this->assertNotNull($booking->id, 'Booking ID should be set');
        
        // Cleanup
        $booking->delete();
    }
    
    /**
     * Test status constants
     */
    public function testStatusConstants()
    {
        $this->assertEquals(0, Booking::STATUS_PENDING, 'STATUS_PENDING should be 0');
        $this->assertEquals(1, Booking::STATUS_APPROVED, 'STATUS_APPROVED should be 1');
        $this->assertEquals(2, Booking::STATUS_REJECTED, 'STATUS_REJECTED should be 2');
        $this->assertEquals(3, Booking::STATUS_CANCELLED, 'STATUS_CANCELLED should be 3');
        $this->assertEquals(4, Booking::STATUS_COMPLETED, 'STATUS_COMPLETED should be 4');
    }
    
    /**
     * Test duration calculation
     */
    public function testGetDuration()
    {
        $booking = new Booking();
        
        // 1 hour
        $booking->start_time = '2024-01-15 10:00:00';
        $booking->end_time = '2024-01-15 11:00:00';
        $this->assertEquals(60, $booking->getDuration(), 'Duration should be 60 minutes');
        
        // 2.5 hours
        $booking->start_time = '2024-01-15 09:00:00';
        $booking->end_time = '2024-01-15 11:30:00';
        $this->assertEquals(150, $booking->getDuration(), 'Duration should be 150 minutes');
        
        // 30 minutes
        $booking->start_time = '2024-01-15 14:00:00';
        $booking->end_time = '2024-01-15 14:30:00';
        $this->assertEquals(30, $booking->getDuration(), 'Duration should be 30 minutes');
    }
    
    /**
     * Test duration in hours
     */
    public function testGetDurationHours()
    {
        $booking = new Booking();
        
        $booking->start_time = '2024-01-15 10:00:00';
        $booking->end_time = '2024-01-15 12:30:00';
        $this->assertEquals(2.5, $booking->getDurationHours(), 'Duration should be 2.5 hours');
    }
    
    /**
     * Test total cost calculation
     */
    public function testGetTotalCost()
    {
        $booking = new Booking();
        $booking->room_id = self::$testRoom->id;
        $booking->start_time = '2024-01-15 10:00:00';
        $booking->end_time = '2024-01-15 12:00:00'; // 2 hours
        
        // Room rate is 100/hour, so 2 hours = 200
        $this->assertEquals(200.0, $booking->getTotalCost(), 'Total cost should be 200');
    }
    
    /**
     * Test time validation - end before start
     */
    public function testEndTimeBeforeStartTime()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Test Meeting';
        $booking->start_time = date('Y-m-d 11:00:00', strtotime('+1 day'));
        $booking->end_time = date('Y-m-d 10:00:00', strtotime('+1 day'));
        $booking->attendees_count = 5;
        
        $this->assertFalse($booking->validate(), 'End time before start time should fail');
        $this->assertTrue($booking->hasErrors('end_time'), 'Should have end_time error');
    }
    
    /**
     * Test booking in the past
     */
    public function testBookingInPast()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Test Meeting';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('-1 day'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('-1 day'));
        $booking->attendees_count = 5;
        
        $this->assertFalse($booking->validate(), 'Booking in past should fail');
        $this->assertTrue($booking->hasErrors('start_time'), 'Should have start_time error');
    }
    
    /**
     * Test attendees count validation
     */
    public function testAttendeesCountValidation()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Test Meeting';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+1 day'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+1 day'));
        
        // Zero attendees
        $booking->attendees_count = 0;
        $this->assertFalse($booking->validate(['attendees_count']), 'Zero attendees should fail');
        
        // Exceeds room capacity (room capacity is 20)
        $booking->attendees_count = 50;
        $this->assertFalse($booking->validate(), 'Exceeding capacity should fail');
        
        // Valid count
        $booking->attendees_count = 15;
        $booking->status = Booking::STATUS_PENDING;
        $this->assertTrue($booking->validate(['attendees_count', 'status']), 'Valid attendees count should pass');
    }
    
    /**
     * Test conflict detection
     */
    public function testConflictDetection()
    {
        // Create an existing booking
        $existing = new Booking();
        $existing->user_id = self::$testUser->id;
        $existing->room_id = self::$testRoom->id;
        $existing->subject = 'Existing Meeting';
        $existing->start_time = date('Y-m-d 10:00:00', strtotime('+2 days'));
        $existing->end_time = date('Y-m-d 12:00:00', strtotime('+2 days'));
        $existing->attendees_count = 5;
        $existing->status = Booking::STATUS_APPROVED;
        $existing->save(false);
        
        // Try to create overlapping booking
        $conflict = new Booking();
        $conflict->user_id = self::$testUser->id;
        $conflict->room_id = self::$testRoom->id;
        $conflict->subject = 'Conflicting Meeting';
        $conflict->start_time = date('Y-m-d 11:00:00', strtotime('+2 days')); // Overlaps
        $conflict->end_time = date('Y-m-d 13:00:00', strtotime('+2 days'));
        $conflict->attendees_count = 5;
        $conflict->status = Booking::STATUS_PENDING;
        
        // Check for conflicts
        $hasConflict = $conflict->hasTimeConflict();
        $this->assertTrue($hasConflict, 'Overlapping booking should detect conflict');
        
        // Non-overlapping booking
        $noConflict = new Booking();
        $noConflict->user_id = self::$testUser->id;
        $noConflict->room_id = self::$testRoom->id;
        $noConflict->subject = 'Non-conflicting Meeting';
        $noConflict->start_time = date('Y-m-d 14:00:00', strtotime('+2 days'));
        $noConflict->end_time = date('Y-m-d 15:00:00', strtotime('+2 days'));
        $noConflict->attendees_count = 5;
        $noConflict->status = Booking::STATUS_PENDING;
        
        $this->assertFalse($noConflict->hasTimeConflict(), 'Non-overlapping booking should not conflict');
        
        // Cleanup
        $existing->delete();
    }
    
    /**
     * Test status change methods
     */
    public function testStatusChange()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Status Test Meeting';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+3 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+3 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save(false);
        
        // Test approve
        $booking->approve(self::$testUser->id, 'Approved for testing');
        $this->assertEquals(Booking::STATUS_APPROVED, $booking->status, 'Status should be approved');
        $this->assertEquals(self::$testUser->id, $booking->approved_by, 'Approved by should be set');
        $this->assertNotNull($booking->approved_at, 'Approved at should be set');
        
        // Test cancel
        $booking->cancel('Cancelled for testing');
        $this->assertEquals(Booking::STATUS_CANCELLED, $booking->status, 'Status should be cancelled');
        $this->assertEquals('Cancelled for testing', $booking->cancel_reason, 'Cancel reason should be set');
        
        // Cleanup
        $booking->delete();
    }
    
    /**
     * Test can cancel logic
     */
    public function testCanCancel()
    {
        $booking = new Booking();
        $booking->status = Booking::STATUS_PENDING;
        $this->assertTrue($booking->canCancel(self::$testUser->id), 'Pending booking should be cancellable');
        
        $booking->status = Booking::STATUS_APPROVED;
        $booking->start_time = date('Y-m-d H:i:s', strtotime('+2 days'));
        $this->assertTrue($booking->canCancel(self::$testUser->id), 'Future approved booking should be cancellable');
        
        $booking->status = Booking::STATUS_COMPLETED;
        $this->assertFalse($booking->canCancel(self::$testUser->id), 'Completed booking should not be cancellable');
        
        $booking->status = Booking::STATUS_CANCELLED;
        $this->assertFalse($booking->canCancel(self::$testUser->id), 'Cancelled booking should not be cancellable');
    }
    
    /**
     * Test status label generation
     */
    public function testGetStatusLabel()
    {
        $booking = new Booking();
        
        $booking->status = Booking::STATUS_PENDING;
        $label = $booking->getStatusLabel();
        $this->assertStringContainsString('รอการอนุมัติ', $label, 'Pending label should contain Thai text');
        
        $booking->status = Booking::STATUS_APPROVED;
        $label = $booking->getStatusLabel();
        $this->assertStringContainsString('อนุมัติแล้ว', $label, 'Approved label should contain Thai text');
        
        $booking->status = Booking::STATUS_REJECTED;
        $label = $booking->getStatusLabel();
        $this->assertStringContainsString('ไม่อนุมัติ', $label, 'Rejected label should contain Thai text');
    }
    
    /**
     * Test relations
     */
    public function testRelations()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Relation Test';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+4 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+4 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save(false);
        
        // Test user relation
        $this->assertNotNull($booking->user, 'User relation should exist');
        $this->assertEquals(self::$testUser->id, $booking->user->id, 'User ID should match');
        
        // Test room relation
        $this->assertNotNull($booking->room, 'Room relation should exist');
        $this->assertEquals(self::$testRoom->id, $booking->room->id, 'Room ID should match');
        
        // Cleanup
        $booking->delete();
    }
    
    /**
     * Test booking code generation
     */
    public function testBookingCodeGeneration()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->subject = 'Code Test';
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+5 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+5 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save(false);
        
        // Booking code should be generated
        $this->assertNotEmpty($booking->booking_code, 'Booking code should be generated');
        $this->assertMatchesRegularExpression('/^BK\d{8}-\d{4}$/', $booking->booking_code, 'Booking code should match pattern');
        
        // Cleanup
        $booking->delete();
    }
    
    /**
     * Test subject validation
     */
    public function testSubjectValidation()
    {
        $booking = new Booking();
        $booking->user_id = self::$testUser->id;
        $booking->room_id = self::$testRoom->id;
        $booking->start_time = date('Y-m-d 10:00:00', strtotime('+1 day'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+1 day'));
        $booking->attendees_count = 5;
        
        // Empty subject
        $booking->subject = '';
        $this->assertFalse($booking->validate(['subject']), 'Empty subject should fail');
        
        // Subject too short
        $booking->subject = 'AB';
        $this->assertFalse($booking->validate(['subject']), 'Short subject should fail');
        
        // Valid subject
        $booking->subject = 'Valid Meeting Subject';
        $this->assertTrue($booking->validate(['subject']), 'Valid subject should pass');
    }
}
