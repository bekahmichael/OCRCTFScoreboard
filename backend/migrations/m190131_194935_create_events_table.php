<?php

use yii\db\Migration;

/**
 * Handles the creation of table `events`.
 */
class m190131_194935_create_events_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('events', [
            'id'               => $this->primaryKey(),
            'name'             => $this->string()->notNull(),
            'created_at'       => $this->dateTime(),
            'event_date'       => $this->date(),
            'event_time_start' => $this->time(),
            'event_time_end'   => $this->time(),
            'status'           => $this->smallInteger()->notNull()->defaultValue('0')->comment('0 - active, 1 - deleted'),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('events');
    }
}
