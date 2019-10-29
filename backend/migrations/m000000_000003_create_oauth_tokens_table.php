<?php

use yii\db\Migration;

/**
 * Handles the creation of table `oauth_tokens`.
 */
class m000000_000003_create_oauth_tokens_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('oauth_tokens', [
            'id'               => $this->primaryKey(),
            'user_id'          => $this->integer(11)->notNull(),
            'token'            => $this->string(128),
            'last_active_time' => $this->timestamp(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createIndex('u_token', 'oauth_tokens', 'token', true);
        $this->addForeignKey('fx_user_id', 'oauth_tokens', 'user_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('u_token', 'oauth_tokens');
        $this->dropForeignKey('fx_user_id', 'oauth_tokens');
        $this->dropTable('oauth_tokens');
    }
}
