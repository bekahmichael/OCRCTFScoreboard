<?php

use yii\db\Migration;

/**
 * Handles the creation of table `event_teams`.
 */
class m190201_061037_create_event_teams_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('event_teams', [
            'id'       => $this->primaryKey(),
            'team_id'  => $this->integer()->notNull(),
            'event_id' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_event_teams_teams_id', 'event_teams', 'team_id', 'teams', 'id', 'CASCADE');

        $this->addForeignKey('fk_event_teams_events_id', 'event_teams', 'event_id', 'events', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_event_teams_teams_id', 'event_teams');

        $this->dropForeignKey('fk_event_teams_events_id', 'event_teams');

        $this->dropTable('event_teams');
    }
}
