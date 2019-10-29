<?php

use yii\db\Migration;

/**
 * Handles the creation of table `teams`.
 */
class m190131_162122_create_teams_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('teams', [
            'id'             => $this->primaryKey(),
            'name'           => $this->string()->notNull(),
            'created_at'     => $this->dateTime(),
            'status'         => $this->smallInteger()->notNull()->defaultValue('0'),
            'avatar_file_id' => $this->integer(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_files_id', 'teams', 'avatar_file_id', 'files', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_files_id', 'teams');

        $this->dropTable('teams');
    }
}
