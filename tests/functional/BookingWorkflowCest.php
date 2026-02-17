<?php

namespace tests\functional;

use tests\FunctionalTester;
use common\models\User;
use common\models\Room;
use common\models\Booking;
use common\fixtures\UserFixture;
use common\fixtures\RoomFixture;
use Codeception\Util\HttpCode;

/**
 * Functional Test: Booking Workflow
 * 
 * Tests the complete booking workflow from creation to completion
 */
class BookingWorkflowCest
{
    /**
     * @var User Test user
     */
    protected $user;

    /**
     * @var User Test approver
     */
    protected $approver;

    /**
     * @var Room Test room
     */
    protected $room;

    /**
     * Load fixtures before tests
     */
    public function _before(FunctionalTester $I)
    {
        // Create test user
        $this->user = new User();
        $this->user->username = 'testuser';
        $this->user->email = 'testuser@test.com';
        $this->user->setPassword('password123');
        $this->user->generateAuthKey();
        $this->user->role = 'user';
        $this->user->status = User::STATUS_ACTIVE;
        $this->user->first_name = 'Test';
        $this->user->last_name = 'User';
        $this->user->save();

        // Create test approver
        $this->approver = new User();
        $this->approver->username = 'testapprover';
        $this->approver->email = 'approver@test.com';
        $this->approver->setPassword('password123');
        $this->approver->generateAuthKey();
        $this->approver->role = 'approver';
        $this->approver->status = User::STATUS_ACTIVE;
        $this->approver->first_name = 'Test';
        $this->approver->last_name = 'Approver';
        $this->approver->save();

        // Create test room
        $this->room = new Room();
        $this->room->name = 'Test Conference Room';
        $this->room->description = 'A test room for functional testing';
        $this->room->location = 'Building A, Floor 1';
        $this->room->capacity = 20;
        $this->room->hourly_rate = 500;
        $this->room->status = Room::STATUS_ACTIVE;
        $this->room->save();
    }

    /**
     * Cleanup after tests
     */
    public function _after(FunctionalTester $I)
    {
        Booking::deleteAll(['user_id' => $this->user->id]);
        $this->room->delete();
        $this->approver->delete();
        $this->user->delete();
    }

    /**
     * Test: User can view available rooms
     */
    public function testUserCanViewAvailableRooms(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/room/index');
        
        $I->see('ห้องประชุม');
        $I->see($this->room->name);
        $I->see($this->room->location);
        $I->see($this->room->capacity . ' คน');
    }

    /**
     * Test: User can view room details
     */
    public function testUserCanViewRoomDetails(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/room/view?id=' . $this->room->id);
        
        $I->see($this->room->name);
        $I->see($this->room->description);
        $I->see($this->room->location);
        $I->seeElement('a', ['href' => '/booking/create?room_id=' . $this->room->id]);
    }

    /**
     * Test: User can create a booking
     */
    public function testUserCanCreateBooking(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/create?room_id=' . $this->room->id);
        
        $I->see('จองห้องประชุม');
        $I->see($this->room->name);
        
        // Fill booking form
        $startTime = date('Y-m-d H:i:s', strtotime('+1 day 09:00'));
        $endTime = date('Y-m-d H:i:s', strtotime('+1 day 11:00'));
        
        $I->fillField('Booking[subject]', 'ประชุมทดสอบระบบ');
        $I->fillField('Booking[description]', 'รายละเอียดการประชุม');
        $I->fillField('Booking[start_time]', $startTime);
        $I->fillField('Booking[end_time]', $endTime);
        $I->fillField('Booking[attendees_count]', 10);
        
        $I->click('ยืนยันการจอง');
        
        // Should redirect to booking view
        $I->see('การจองสำเร็จ');
        $I->see('ประชุมทดสอบระบบ');
        $I->see('รออนุมัติ');
        
        // Verify booking in database
        $I->seeRecord(Booking::class, [
            'user_id' => $this->user->id,
            'room_id' => $this->room->id,
            'subject' => 'ประชุมทดสอบระบบ',
            'status' => Booking::STATUS_PENDING,
        ]);
    }

    /**
     * Test: User cannot create booking with invalid data
     */
    public function testUserCannotCreateBookingWithInvalidData(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/create?room_id=' . $this->room->id);
        
        // Submit empty form
        $I->click('ยืนยันการจอง');
        
        // Should see validation errors
        $I->see('หัวข้อการประชุม ต้องไม่ว่าง');
        $I->see('วันเวลาเริ่ม ต้องไม่ว่าง');
        $I->see('วันเวลาสิ้นสุด ต้องไม่ว่าง');
        $I->see('จำนวนผู้เข้าร่วม ต้องไม่ว่าง');
    }

    /**
     * Test: User cannot create booking exceeding room capacity
     */
    public function testUserCannotCreateBookingExceedingCapacity(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/create?room_id=' . $this->room->id);
        
        $startTime = date('Y-m-d H:i:s', strtotime('+2 days 09:00'));
        $endTime = date('Y-m-d H:i:s', strtotime('+2 days 11:00'));
        
        $I->fillField('Booking[subject]', 'ประชุมทดสอบ');
        $I->fillField('Booking[start_time]', $startTime);
        $I->fillField('Booking[end_time]', $endTime);
        $I->fillField('Booking[attendees_count]', 100); // Exceeds capacity of 20
        
        $I->click('ยืนยันการจอง');
        
        $I->see('จำนวนผู้เข้าร่วมเกินความจุของห้อง');
    }

    /**
     * Test: User cannot create booking with time conflict
     */
    public function testUserCannotCreateBookingWithTimeConflict(FunctionalTester $I)
    {
        // Create existing booking
        $existingBooking = new Booking();
        $existingBooking->user_id = $this->approver->id;
        $existingBooking->room_id = $this->room->id;
        $existingBooking->subject = 'Existing Booking';
        $existingBooking->start_time = date('Y-m-d 09:00:00', strtotime('+3 days'));
        $existingBooking->end_time = date('Y-m-d 12:00:00', strtotime('+3 days'));
        $existingBooking->attendees_count = 5;
        $existingBooking->status = Booking::STATUS_APPROVED;
        $existingBooking->save();

        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/create?room_id=' . $this->room->id);
        
        // Try to book overlapping time
        $I->fillField('Booking[subject]', 'Conflicting Booking');
        $I->fillField('Booking[start_time]', date('Y-m-d 10:00:00', strtotime('+3 days')));
        $I->fillField('Booking[end_time]', date('Y-m-d 13:00:00', strtotime('+3 days')));
        $I->fillField('Booking[attendees_count]', 5);
        
        $I->click('ยืนยันการจอง');
        
        $I->see('ห้องประชุมถูกจองในช่วงเวลาดังกล่าวแล้ว');
        
        // Cleanup
        $existingBooking->delete();
    }

    /**
     * Test: User can view their bookings
     */
    public function testUserCanViewTheirBookings(FunctionalTester $I)
    {
        // Create a booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'My Test Booking';
        $booking->start_time = date('Y-m-d 14:00:00', strtotime('+4 days'));
        $booking->end_time = date('Y-m-d 16:00:00', strtotime('+4 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/my-bookings');
        
        $I->see('การจองของฉัน');
        $I->see('My Test Booking');
        $I->see($this->room->name);
        $I->see('รออนุมัติ');
        
        // Cleanup
        $booking->delete();
    }

    /**
     * Test: User can cancel their pending booking
     */
    public function testUserCanCancelPendingBooking(FunctionalTester $I)
    {
        // Create a booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'Booking to Cancel';
        $booking->start_time = date('Y-m-d 09:00:00', strtotime('+5 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+5 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/view?id=' . $booking->id);
        
        $I->see('Booking to Cancel');
        $I->click('ยกเลิกการจอง');
        
        // Confirm cancellation
        $I->see('ยืนยันการยกเลิก');
        $I->click('ยืนยัน');
        
        $I->see('ยกเลิกการจองสำเร็จ');
        
        // Verify in database
        $booking->refresh();
        $I->assertEquals(Booking::STATUS_CANCELLED, $booking->status);
    }

    /**
     * Test: Approver can view pending bookings
     */
    public function testApproverCanViewPendingBookings(FunctionalTester $I)
    {
        // Create a pending booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'Pending Approval';
        $booking->start_time = date('Y-m-d 09:00:00', strtotime('+6 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+6 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $I->amLoggedInAs($this->approver);
        $I->amOnPage('/booking/approval');
        
        $I->see('รออนุมัติ');
        $I->see('Pending Approval');
        $I->see($this->user->getDisplayName());
        
        // Cleanup
        $booking->delete();
    }

    /**
     * Test: Approver can approve a booking
     */
    public function testApproverCanApproveBooking(FunctionalTester $I)
    {
        // Create a pending booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'Needs Approval';
        $booking->start_time = date('Y-m-d 09:00:00', strtotime('+7 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+7 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $I->amLoggedInAs($this->approver);
        $I->amOnPage('/booking/view?id=' . $booking->id);
        
        $I->see('Needs Approval');
        $I->click('อนุมัติ');
        
        $I->see('อนุมัติการจองสำเร็จ');
        
        // Verify in database
        $booking->refresh();
        $I->assertEquals(Booking::STATUS_APPROVED, $booking->status);
        $I->assertEquals($this->approver->id, $booking->approved_by);
        $I->assertNotNull($booking->approved_at);
    }

    /**
     * Test: Approver can reject a booking with reason
     */
    public function testApproverCanRejectBookingWithReason(FunctionalTester $I)
    {
        // Create a pending booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'Will Be Rejected';
        $booking->start_time = date('Y-m-d 09:00:00', strtotime('+8 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+8 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        $I->amLoggedInAs($this->approver);
        $I->amOnPage('/booking/view?id=' . $booking->id);
        
        $I->see('Will Be Rejected');
        $I->click('ปฏิเสธ');
        
        // Fill rejection reason
        $I->fillField('rejection_reason', 'ห้องประชุมถูกจองสำหรับกิจกรรมพิเศษ');
        $I->click('ยืนยันการปฏิเสธ');
        
        $I->see('ปฏิเสธการจองสำเร็จ');
        
        // Verify in database
        $booking->refresh();
        $I->assertEquals(Booking::STATUS_REJECTED, $booking->status);
        $I->assertEquals('ห้องประชุมถูกจองสำหรับกิจกรรมพิเศษ', $booking->rejection_reason);
    }

    /**
     * Test: Regular user cannot access approval page
     */
    public function testRegularUserCannotAccessApprovalPage(FunctionalTester $I)
    {
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/approval');
        
        $I->seeResponseCodeIs(HttpCode::FORBIDDEN);
    }

    /**
     * Test: User receives notification after booking approval
     */
    public function testUserReceivesNotificationAfterApproval(FunctionalTester $I)
    {
        // Create and approve a booking
        $booking = new Booking();
        $booking->user_id = $this->user->id;
        $booking->room_id = $this->room->id;
        $booking->subject = 'Notification Test';
        $booking->start_time = date('Y-m-d 09:00:00', strtotime('+9 days'));
        $booking->end_time = date('Y-m-d 11:00:00', strtotime('+9 days'));
        $booking->attendees_count = 5;
        $booking->status = Booking::STATUS_PENDING;
        $booking->save();

        // Approve as approver
        $I->amLoggedInAs($this->approver);
        $I->amOnPage('/booking/approve?id=' . $booking->id);
        $I->click('ยืนยัน');

        // Check user notifications
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/notification/index');
        
        $I->see('การจองได้รับการอนุมัติ');
        $I->see('Notification Test');
    }

    /**
     * Test: Complete booking workflow from start to finish
     */
    public function testCompleteBookingWorkflow(FunctionalTester $I)
    {
        // Step 1: User creates booking
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/create?room_id=' . $this->room->id);
        
        $startTime = date('Y-m-d 09:00:00', strtotime('+10 days'));
        $endTime = date('Y-m-d 11:00:00', strtotime('+10 days'));
        
        $I->fillField('Booking[subject]', 'Complete Workflow Test');
        $I->fillField('Booking[description]', 'Testing complete workflow');
        $I->fillField('Booking[start_time]', $startTime);
        $I->fillField('Booking[end_time]', $endTime);
        $I->fillField('Booking[attendees_count]', 10);
        $I->click('ยืนยันการจอง');
        
        $I->see('การจองสำเร็จ');
        
        // Get booking ID from URL
        $bookingId = $I->grabFromCurrentUrl('~id=(\d+)~');
        
        // Step 2: Verify pending status
        $booking = Booking::findOne($bookingId);
        $I->assertEquals(Booking::STATUS_PENDING, $booking->status);
        
        // Step 3: Approver approves booking
        $I->amLoggedInAs($this->approver);
        $I->amOnPage('/booking/view?id=' . $bookingId);
        $I->click('อนุมัติ');
        
        $I->see('อนุมัติการจองสำเร็จ');
        
        // Step 4: Verify approved status
        $booking->refresh();
        $I->assertEquals(Booking::STATUS_APPROVED, $booking->status);
        
        // Step 5: User views approved booking
        $I->amLoggedInAs($this->user);
        $I->amOnPage('/booking/view?id=' . $bookingId);
        
        $I->see('Complete Workflow Test');
        $I->see('อนุมัติแล้ว');
        $I->see($this->room->name);
    }
}
