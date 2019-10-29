<?php

use yii\db\Migration;

/**
 * Handles the creation of table `scoreboard_templates`.
 */
class m190201_122526_create_scoreboard_templates_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('scoreboard_templates', [
            'id'   => $this->primaryKey(),
            'name' => $this->string()->unique()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->insert('scoreboard_templates', [
            'id'   => 1,
            'name' => 'Default',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('scoreboard_templates');
    }
}
