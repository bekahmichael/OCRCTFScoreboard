<?php

use yii\db\Migration;

/**
 * Handles the modification of table `event2quizzes`.
 */
class m190211_145705_modify_event2quizzes_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event2quizzes', 'status', $this->smallInteger()->notNull()->defaultValue('0'));
        $this->addColumn('event2quizzes', 'time_start', $this->dateTime());
        $this->addColumn('event2quizzes', 'time_finish', $this->dateTime());
        $this->addColumn('event2quizzes', 'time', $this->integer()->notNull());
        $this->addColumn('event2quizzes', 'passed_time', $this->float(6, 4)->notNull()->defaultValue('0'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('event2quizzes', 'status');
        $this->dropColumn('event2quizzes', 'time_start');
        $this->dropColumn('event2quizzes', 'time_finish');
        $this->dropColumn('event2quizzes', 'time');
        $this->dropColumn('event2quizzes', 'passed_time');
    }
}
