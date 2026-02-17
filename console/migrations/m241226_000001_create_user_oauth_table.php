<?php

use yii\db\Migration;

/**
 * Migration: Create user_oauth table
 * 
 * Stores OAuth provider connections for users (Google, Microsoft, ThaiD)
 */
class m241226_000001_create_user_oauth_table extends Migration
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
                
        $this->createTable('{{%user_oauth}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'provider' => $this->string(50)->notNull()->comment('OAuth provider: google, microsoft, thaid'),
            'provider_user_id' => $this->string(255)->notNull()->comment('User ID from OAuth provider'),
            'access_token' => $this->text()->null()->comment('OAuth access token'),
            'refresh_token' => $this->text()->null()->comment('OAuth refresh token'),
            'token_expires_at' => $this->integer()->null()->comment('Token expiration timestamp'),
            'profile_data' => $this->text()->null()->comment('JSON encoded profile data from provider'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);

        // Foreign key to users table
        $this->addForeignKey(
            'fk-user_oauth-user_id',
            '{{%user_oauth}}',
            'user_id',
            '{{%users}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // Unique constraint: one provider account per user
        $this->createIndex(
            'idx-user_oauth-user_provider',
            '{{%user_oauth}}',
            ['user_id', 'provider'],
            true
        );

        // Unique constraint: one user per provider account
        $this->createIndex(
            'idx-user_oauth-provider_user',
            '{{%user_oauth}}',
            ['provider', 'provider_user_id'],
            true
        );

        // Index for provider lookup
        $this->createIndex(
            'idx-user_oauth-provider',
            '{{%user_oauth}}',
            'provider'
        );

        echo "    > Created user_oauth table with indexes and foreign keys.\n";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_oauth-user_id', '{{%user_oauth}}');
        $this->dropTable('{{%user_oauth}}');
        
        echo "    > Dropped user_oauth table.\n";
    }
}
