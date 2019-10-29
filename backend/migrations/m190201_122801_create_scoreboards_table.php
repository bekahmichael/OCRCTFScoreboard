<?php

use yii\db\Migration;

/**
 * Handles the creation of table `scoreboards`.
 */
class m190201_122801_create_scoreboards_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('scoreboards', [
            'id'          => $this->primaryKey(),
            'template_id' => $this->integer()->notNull(),
            'event_id'    => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_scoreboards_scoreboard_templates_id', 'scoreboards', 'template_id', 'scoreboard_templates', 'id', 'CASCADE');

        $this->addForeignKey('fk_scoreboards_events_id', 'scoreboards', 'event_id', 'events', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_scoreboards_scoreboard_templates_id', 'scoreboards');

        $this->dropForeignKey('fk_scoreboards_events_id', 'scoreboards');

        $this->dropTable('scoreboards');
    }
}
