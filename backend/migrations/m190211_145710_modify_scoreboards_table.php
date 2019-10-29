<?php

use yii\db\Migration;

/**
 * Handles the modification of table `scoreboards`.
 */
class m190211_145710_modify_scoreboards_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('scoreboards', 'background_color', $this->char(100)->notNull()->defaultValue(''));
        $this->addColumn('scoreboards', 'background_image_file_id', $this->integer());

        $this->addForeignKey('fk_scoreboards_files_id', 'scoreboards', 'background_image_file_id', 'files', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_scoreboards_files_id', 'scoreboards');

        $this->dropColumn('scoreboards', 'background_color');
        $this->dropColumn('scoreboards', 'background_image_file_id');
    }
}
