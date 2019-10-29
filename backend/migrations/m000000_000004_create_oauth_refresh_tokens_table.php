<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oauth_refresh_tokens`.
 */
class m000000_000004_create_oauth_refresh_tokens_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('oauth_refresh_tokens', [
            'id'            => $this->primaryKey(),
            'user_id'       => $this->integer()->notNull(),
            'created_at'    => $this->dateTime(),
            'refresh_token' => $this->string(128)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createIndex('idx_oauth_refresh_tokens_user_id', 'oauth_refresh_tokens', 'user_id');
        $this->createIndex('idx_oauth_refresh_tokens_refresh_token', 'oauth_refresh_tokens', 'refresh_token');
        $this->addForeignKey('fk_users_id', 'oauth_refresh_tokens', 'user_id', 'users', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_users_id', 'oauth_refresh_tokens');
        $this->dropIndex('idx_oauth_refresh_tokens_user_id', 'oauth_refresh_tokens');
        $this->dropIndex('idx_oauth_refresh_tokens_refresh_token', 'oauth_refresh_tokens');
        $this->dropTable('oauth_refresh_tokens');
    }
}
