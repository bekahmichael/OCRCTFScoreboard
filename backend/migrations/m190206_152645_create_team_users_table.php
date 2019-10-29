<?php

use yii\db\Migration;

/**
 * Handles the creation of table `team_users`.
 */
class m190206_152645_create_team_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('team_users', [
            'id'         => $this->primaryKey(),
            'team_id'    => $this->integer()->notNull(),
            'user_id'    => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
            'status'     => $this->smallInteger()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_team_users_teams_id', 'team_users', 'team_id', 'teams', 'id', 'CASCADE');

        $this->addForeignKey('fk_team_users_users_id', 'team_users', 'user_id', 'users', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_team_users_teams_id', 'team_users');

        $this->dropForeignKey('fk_team_users_users_id', 'team_users');

        $this->dropTable('team_users');
    }
}
