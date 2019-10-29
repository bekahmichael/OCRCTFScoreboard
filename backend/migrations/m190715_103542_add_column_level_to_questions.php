<?php

use yii\db\Migration;

/**
 * Handles adding column `level` to table `questions`.
 */
class m190715_103542_add_column_level_to_questions extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('questions', 'level', $this->integer()->unsigned()->notNull()->defaultValue('1'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('questions', 'level');
    }
}
