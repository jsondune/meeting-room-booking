<?php
/**
 * Migration for calendar sync and push notification tables
 * 
 * Creates:
 * - booking_calendar_sync: Stores external calendar event IDs for synced bookings
 * - user_push_tokens: Stores push notification tokens for users
 * - user_notification_settings: User notification preferences
 */

use yii\db\Migration;

class m241226_100001_create_calendar_sync_and_push_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        
        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Create booking_calendar_sync table
        $this->createTable('{{%booking_calendar_sync}}', [
            'id' => $this->primaryKey(),
            'booking_id' => $this->integer()->notNull(),
            'provider' => $this->string(50)->notNull()->comment('google, microsoft'),
            'external_event_id' => $this->string(500)->notNull(),
            'external_event_link' => $this->string(500)->null(),
            'sync_status' => $this->string(20)->notNull()->defaultValue('synced')->comment('synced, failed, pending'),
            'last_error' => $this->text()->null(),
            'synced_at' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);
        
        // Add indexes for booking_calendar_sync
        $this->createIndex(
            'idx-booking_calendar_sync-booking_id',
            '{{%booking_calendar_sync}}',
            'booking_id'
        );
        
        $this->createIndex(
            'idx-booking_calendar_sync-unique',
            '{{%booking_calendar_sync}}',
            ['booking_id', 'provider'],
            true
        );
        
        $this->createIndex(
            'idx-booking_calendar_sync-external_event_id',
            '{{%booking_calendar_sync}}',
            ['provider', 'external_event_id']
        );
        
        // Add foreign key
        $this->addForeignKey(
            'fk-booking_calendar_sync-booking_id',
            '{{%booking_calendar_sync}}',
            'booking_id',
            '{{%bookings}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();        

        // Create user_push_tokens table
        $this->createTable('{{%user_push_tokens}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'token' => $this->string(500)->notNull(),
            'provider' => $this->string(50)->notNull()->comment('fcm, onesignal'),
            'platform' => $this->string(20)->notNull()->comment('android, ios, web'),
            'device_id' => $this->string(255)->null()->comment('Unique device identifier'),
            'device_name' => $this->string(255)->null(),
            'app_version' => $this->string(50)->null(),
            'is_active' => $this->boolean()->notNull()->defaultValue(true),
            'last_used_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);
        
        // Add indexes for user_push_tokens
        $this->createIndex(
            'idx-user_push_tokens-user_id',
            '{{%user_push_tokens}}',
            'user_id'
        );
        
        $this->createIndex(
            'idx-user_push_tokens-token',
            '{{%user_push_tokens}}',
            'token',
            true
        );
        
        $this->createIndex(
            'idx-user_push_tokens-provider',
            '{{%user_push_tokens}}',
            ['user_id', 'provider']
        );
        
        $this->createIndex(
            'idx-user_push_tokens-device',
            '{{%user_push_tokens}}',
            ['user_id', 'device_id']
        );
        
        // Add foreign key
        $this->addForeignKey(
            'fk-user_push_tokens-user_id',
            '{{%user_push_tokens}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Disable foreign key checks
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0")->execute();                
        
        // Create user_notification_settings table
        $this->createTable('{{%user_notification_settings}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->unique(),
            
            // Email notifications
            'email_booking_created' => $this->boolean()->notNull()->defaultValue(true),
            'email_booking_approved' => $this->boolean()->notNull()->defaultValue(true),
            'email_booking_rejected' => $this->boolean()->notNull()->defaultValue(true),
            'email_booking_cancelled' => $this->boolean()->notNull()->defaultValue(true),
            'email_booking_reminder' => $this->boolean()->notNull()->defaultValue(true),
            'email_pending_approval' => $this->boolean()->notNull()->defaultValue(true),
            'email_daily_summary' => $this->boolean()->notNull()->defaultValue(false),
            
            // Push notifications
            'push_booking_created' => $this->boolean()->notNull()->defaultValue(true),
            'push_booking_approved' => $this->boolean()->notNull()->defaultValue(true),
            'push_booking_rejected' => $this->boolean()->notNull()->defaultValue(true),
            'push_booking_cancelled' => $this->boolean()->notNull()->defaultValue(true),
            'push_booking_reminder' => $this->boolean()->notNull()->defaultValue(true),
            'push_pending_approval' => $this->boolean()->notNull()->defaultValue(true),
            
            // WebSocket/Real-time notifications
            'realtime_enabled' => $this->boolean()->notNull()->defaultValue(true),
            
            // Calendar sync settings
            'calendar_sync_google' => $this->boolean()->notNull()->defaultValue(false),
            'calendar_sync_microsoft' => $this->boolean()->notNull()->defaultValue(false),
            'calendar_auto_sync' => $this->boolean()->notNull()->defaultValue(true)->comment('Auto sync on booking approval'),
            
            // Reminder settings
            'reminder_minutes_before' => $this->integer()->notNull()->defaultValue(30),
            'reminder_email' => $this->boolean()->notNull()->defaultValue(true),
            'reminder_push' => $this->boolean()->notNull()->defaultValue(true),
            
            // Quiet hours (no push notifications)
            'quiet_hours_enabled' => $this->boolean()->notNull()->defaultValue(false),
            'quiet_hours_start' => $this->time()->null()->comment('e.g., 22:00:00'),
            'quiet_hours_end' => $this->time()->null()->comment('e.g., 07:00:00'),
            
            'created_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ], $tableOptions);
        
        // Add foreign key for user_notification_settings
        $this->addForeignKey(
            'fk-user_notification_settings-user_id',
            '{{%user_notification_settings}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        
        // Add settings column to users table if not exists
        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%users}}');
        if (!isset($tableSchema->columns['settings'])) {
            $this->addColumn('{{%users}}', 'settings', $this->json()->null()->comment('User settings JSON'));
        }
        
        echo "    > created booking_calendar_sync table\n";
        echo "    > created user_push_tokens table\n";
        echo "    > created user_notification_settings table\n";
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys first
        $this->dropForeignKey('fk-user_notification_settings-user_id', '{{%user_notification_settings}}');
        $this->dropForeignKey('fk-user_push_tokens-user_id', '{{%user_push_tokens}}');
        $this->dropForeignKey('fk-booking_calendar_sync-booking_id', '{{%booking_calendar_sync}}');
        
        // Drop tables
        $this->dropTable('{{%user_notification_settings}}');
        $this->dropTable('{{%user_push_tokens}}');
        $this->dropTable('{{%booking_calendar_sync}}');
        
        // Remove settings column from users table
        $tableSchema = Yii::$app->db->schema->getTableSchema('{{%users}}');
        if (isset($tableSchema->columns['settings'])) {
            $this->dropColumn('{{%users}}', 'settings');
        }
        
        return true;
    }
}
