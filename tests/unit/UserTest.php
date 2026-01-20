<?php
/**
 * User Model Unit Tests
 * 
 * @author PBRI Digital Technology & AI Division
 * @version 1.0
 */

namespace tests\unit;

use Codeception\Test\Unit;
use common\models\User;
use Yii;

class UserTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    /**
     * Test user creation
     */
    public function testCreateUser()
    {
        $user = new User();
        $user->username = 'testuser_' . time();
        $user->email = 'test_' . time() . '@example.com';
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->setPassword('TestPass123!');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        
        $this->assertTrue($user->save(), 'User should be saved');
        $this->assertNotNull($user->id, 'User ID should be set');
        $this->assertNotEmpty($user->password_hash, 'Password hash should be set');
        $this->assertNotEmpty($user->auth_key, 'Auth key should be set');
        
        // Cleanup
        $user->delete();
    }
    
    /**
     * Test password validation
     */
    public function testPasswordValidation()
    {
        $user = new User();
        $user->setPassword('MySecurePassword123');
        
        $this->assertTrue($user->validatePassword('MySecurePassword123'), 'Correct password should validate');
        $this->assertFalse($user->validatePassword('WrongPassword'), 'Wrong password should not validate');
        $this->assertFalse($user->validatePassword(''), 'Empty password should not validate');
    }
    
    /**
     * Test username validation
     */
    public function testUsernameValidation()
    {
        $user = new User();
        
        // Valid usernames
        $user->username = 'validuser123';
        $user->email = 'valid_' . time() . '@example.com';
        $user->setPassword('Test123!');
        $user->generateAuthKey();
        
        $this->assertTrue($user->validate(['username']), 'Valid username should pass validation');
        
        // Too short
        $user->username = 'ab';
        $this->assertFalse($user->validate(['username']), 'Username too short should fail');
        
        // Too long
        $user->username = str_repeat('a', 51);
        $this->assertFalse($user->validate(['username']), 'Username too long should fail');
    }
    
    /**
     * Test email validation
     */
    public function testEmailValidation()
    {
        $user = new User();
        $user->username = 'emailtest_' . time();
        $user->setPassword('Test123!');
        $user->generateAuthKey();
        
        // Valid email
        $user->email = 'valid@example.com';
        $this->assertTrue($user->validate(['email']), 'Valid email should pass');
        
        // Invalid email
        $user->email = 'invalid-email';
        $this->assertFalse($user->validate(['email']), 'Invalid email should fail');
        
        // Empty email
        $user->email = '';
        $this->assertFalse($user->validate(['email']), 'Empty email should fail');
    }
    
    /**
     * Test display name generation
     */
    public function testGetDisplayName()
    {
        $user = new User();
        
        // With first and last name
        $user->first_name = 'สมชาย';
        $user->last_name = 'ใจดี';
        $user->username = 'somchai';
        $this->assertEquals('สมชาย ใจดี', $user->getDisplayName(), 'Display name should be full name');
        
        // With only first name
        $user->first_name = 'สมชาย';
        $user->last_name = '';
        $this->assertEquals('สมชาย', $user->getDisplayName(), 'Display name should be first name');
        
        // With no names
        $user->first_name = '';
        $user->last_name = '';
        $this->assertEquals('somchai', $user->getDisplayName(), 'Display name should be username');
    }
    
    /**
     * Test user status constants
     */
    public function testStatusConstants()
    {
        $this->assertEquals(0, User::STATUS_INACTIVE, 'STATUS_INACTIVE should be 0');
        $this->assertEquals(1, User::STATUS_ACTIVE, 'STATUS_ACTIVE should be 1');
        $this->assertEquals(2, User::STATUS_SUSPENDED, 'STATUS_SUSPENDED should be 2');
    }
    
    /**
     * Test find by username
     */
    public function testFindByUsername()
    {
        // Create test user
        $user = new User();
        $user->username = 'findtest_' . time();
        $user->email = 'findtest_' . time() . '@example.com';
        $user->setPassword('Test123!');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);
        
        // Find by username
        $found = User::findByUsername($user->username);
        $this->assertNotNull($found, 'User should be found by username');
        $this->assertEquals($user->id, $found->id, 'Found user ID should match');
        
        // Find non-existent
        $notFound = User::findByUsername('nonexistent_user_xyz');
        $this->assertNull($notFound, 'Non-existent user should return null');
        
        // Cleanup
        $user->delete();
    }
    
    /**
     * Test find by email
     */
    public function testFindByEmail()
    {
        // Create test user
        $email = 'findemail_' . time() . '@example.com';
        $user = new User();
        $user->username = 'findemail_' . time();
        $user->email = $email;
        $user->setPassword('Test123!');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);
        
        // Find by email
        $found = User::findByEmail($email);
        $this->assertNotNull($found, 'User should be found by email');
        $this->assertEquals($user->id, $found->id, 'Found user ID should match');
        
        // Cleanup
        $user->delete();
    }
    
    /**
     * Test auth key generation
     */
    public function testAuthKeyGeneration()
    {
        $user = new User();
        $user->generateAuthKey();
        
        $this->assertNotEmpty($user->auth_key, 'Auth key should be generated');
        $this->assertEquals(32, strlen($user->auth_key), 'Auth key should be 32 characters');
    }
    
    /**
     * Test password reset token
     */
    public function testPasswordResetToken()
    {
        $user = new User();
        $user->generatePasswordResetToken();
        
        $this->assertNotEmpty($user->password_reset_token, 'Reset token should be generated');
        $this->assertStringContainsString('_', $user->password_reset_token, 'Reset token should contain timestamp separator');
        
        // Check if token is valid
        $this->assertTrue($user->isPasswordResetTokenValid($user->password_reset_token), 'Fresh token should be valid');
        
        // Check expired token
        $expiredToken = Yii::$app->security->generateRandomString() . '_' . (time() - 86400);
        $this->assertFalse($user->isPasswordResetTokenValid($expiredToken), 'Expired token should be invalid');
    }
    
    /**
     * Test unique username constraint
     */
    public function testUniqueUsername()
    {
        $uniqueUsername = 'unique_' . time();
        
        // First user
        $user1 = new User();
        $user1->username = $uniqueUsername;
        $user1->email = 'user1_' . time() . '@example.com';
        $user1->setPassword('Test123!');
        $user1->generateAuthKey();
        $user1->save(false);
        
        // Second user with same username
        $user2 = new User();
        $user2->username = $uniqueUsername;
        $user2->email = 'user2_' . time() . '@example.com';
        $user2->setPassword('Test123!');
        $user2->generateAuthKey();
        
        $this->assertFalse($user2->validate(['username']), 'Duplicate username should fail validation');
        
        // Cleanup
        $user1->delete();
    }
    
    /**
     * Test unique email constraint
     */
    public function testUniqueEmail()
    {
        $uniqueEmail = 'unique_' . time() . '@example.com';
        
        // First user
        $user1 = new User();
        $user1->username = 'user1_' . time();
        $user1->email = $uniqueEmail;
        $user1->setPassword('Test123!');
        $user1->generateAuthKey();
        $user1->save(false);
        
        // Second user with same email
        $user2 = new User();
        $user2->username = 'user2_' . time();
        $user2->email = $uniqueEmail;
        $user2->setPassword('Test123!');
        $user2->generateAuthKey();
        
        $this->assertFalse($user2->validate(['email']), 'Duplicate email should fail validation');
        
        // Cleanup
        $user1->delete();
    }
    
    /**
     * Test last login update
     */
    public function testUpdateLastLogin()
    {
        $user = new User();
        $user->username = 'logintest_' . time();
        $user->email = 'logintest_' . time() . '@example.com';
        $user->setPassword('Test123!');
        $user->generateAuthKey();
        $user->status = User::STATUS_ACTIVE;
        $user->save(false);
        
        $this->assertNull($user->last_login_at, 'Last login should be null initially');
        
        $user->updateLastLogin();
        
        $this->assertNotNull($user->last_login_at, 'Last login should be updated');
        
        // Cleanup
        $user->delete();
    }
}
