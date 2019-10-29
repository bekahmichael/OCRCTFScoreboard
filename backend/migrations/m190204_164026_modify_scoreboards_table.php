<?php

use yii\db\Migration;

/**
 * Handles the modification of table `scoreboards`.
 */
class m190204_164026_modify_scoreboards_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('scoreboards', 'show_countdown_clock', $this->boolean()->notNull()->defaultValue('0'));
        $this->addColumn('scoreboards', 'show_quiz_title', $this->boolean()->notNull()->defaultValue('0'));
        $this->addColumn('scoreboards', 'show_teams_avatars', $this->boolean()->notNull()->defaultValue('0'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('scoreboards', 'show_countdown_clock');
        $this->dropColumn('scoreboards', 'show_quiz_title');
        $this->dropColumn('scoreboards', 'show_teams_avatars');
    }
}
