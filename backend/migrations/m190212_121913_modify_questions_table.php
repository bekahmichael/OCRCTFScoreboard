<?php

use yii\db\Migration;

/**
 * Class m190212_121913_modify_questions_table
 */
class m190212_121913_modify_questions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('questions', 'points', $this->decimal(10,1)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('questions', 'points', $this->smallInteger()->notNull()->defaultValue(0));
    }
}
