<?php

use yii\db\Migration;

/**
 * Migration for creating user_oauth table
 * Stores OAuth2 provider connections for users (Google, Microsoft, ThaiD)
 */
class m240101_000002_create_user_oauth_table extends Migration
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

        // Create user_oauth table
        $this->createTable('{{%user_oauth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'provider' => $this->string(50)->notNull()->comment('OAuth provider: google, microsoft, thaid'),
            'provider_user_id' => $this->string(255)->notNull()->comment('Unique ID from the OAuth provider'),
            'access_token' => $this->text()->comment('OAuth access token'),
            'refresh_token' => $this->text()->comment('OAuth refresh token'),
            'token_expires_at' => $this->integer()->comment('Token expiration timestamp'),
            'profile_data' => $this->text()->comment('JSON-encoded profile data from provider'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Add foreign key to user table
        $this->addForeignKey(
            'fk-user_oauth-user_id',
            '{{%user_oauth}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Create unique index: one provider per user
        $this->createIndex(
            'idx-user_oauth-user_id-provider',
            '{{%user_oauth}}',
            ['user_id', 'provider'],
            true
        );

        // Create unique index: one provider_user_id per provider
        $this->createIndex(
            'idx-user_oauth-provider-provider_user_id',
            '{{%user_oauth}}',
            ['provider', 'provider_user_id'],
            true
        );

        // Index for finding user by provider
        $this->createIndex(
            'idx-user_oauth-provider',
            '{{%user_oauth}}',
            'provider'
        );

        // Add has_password column to user table if not exists
        // This helps track if user can disconnect all OAuth providers
        $this->addColumn('{{%users}}', 'has_password', $this->boolean()->defaultValue(true)->after('password_hash'));
        
        // Add oauth_avatar column to user table for storing OAuth profile pictures
        $this->addColumn('{{%users}}', 'oauth_avatar', $this->string(500)->after('avatar'));

        echo "    > user_oauth table created successfully.\n";
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove columns from user table
        $this->dropColumn('{{%users}}', 'oauth_avatar');
        $this->dropColumn('{{%users}}', 'has_password');

        // Drop foreign key first
        $this->dropForeignKey('fk-user_oauth-user_id', '{{%user_oauth}}');

        // Drop the table
        $this->dropTable('{{%user_oauth}}');

        echo "    > user_oauth table dropped.\n";

        return true;
    }
}
