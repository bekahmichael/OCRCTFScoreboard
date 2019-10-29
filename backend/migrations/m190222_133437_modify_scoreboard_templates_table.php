<?php

use yii\db\Migration;

/**
 * Handles the modification of table `scoreboard_templates`.
 */
class m190222_133437_modify_scoreboard_templates_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('scoreboard_templates', 'is_system', $this->boolean()->notNull()->defaultValue('0'));
        $this->update('scoreboard_templates', ['is_system' => '1'], ['id' => 1]);

        $this->addColumn('scoreboard_templates', 'background_color', $this->char(50)->notNull());
        $this->addColumn('scoreboard_templates', 'foreground_color', $this->char(50)->notNull());
        $this->addColumn('scoreboard_templates', 'background_image_file_id', $this->integer());
        $this->addColumn('scoreboard_templates', 'text_color', $this->char(50)->notNull());
        $this->addColumn('scoreboard_templates', 'title_color', $this->char(50)->notNull());
        $this->addColumn('scoreboard_templates', 'column_color', $this->char(50)->notNull());

        $this->addForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates', 'background_image_file_id', 'files', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_scoreboard_templates_files_id', 'scoreboard_templates');

        $this->dropColumn('scoreboard_templates', 'background_color');
        $this->dropColumn('scoreboard_templates', 'foreground_color');
        $this->dropColumn('scoreboard_templates', 'background_image_file_id');
        $this->dropColumn('scoreboard_templates', 'text_color');
        $this->dropColumn('scoreboard_templates', 'title_color');
        $this->dropColumn('scoreboard_templates', 'column_color');
        $this->dropColumn('scoreboard_templates', 'is_system');
    }
}
