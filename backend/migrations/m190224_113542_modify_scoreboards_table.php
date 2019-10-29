<?php

use yii\db\Migration;

/**
 * Handles the modification of table `scoreboards`.
 */
class m190224_113542_modify_scoreboards_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_scoreboards_files_id', 'scoreboards');
        $this->addForeignKey('fk_scoreboards_files_id', 'scoreboards', 'background_image_file_id', 'files', 'id', 'SET NULL', 'CASCADE');

        $this->dropForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates');
        $this->addForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates', 'background_image_file_id', 'files', 'id', 'SET NULL', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_scoreboards_files_id', 'scoreboards');
        $this->addForeignKey('fk_scoreboards_files_id', 'scoreboards', 'background_image_file_id', 'files', 'id', 'CASCADE');

        $this->dropForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates');
        $this->addForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates', 'background_image_file_id', 'files', 'id', 'CASCADE');
    }
}
