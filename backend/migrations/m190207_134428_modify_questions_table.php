<?php

use yii\db\Migration;

/**
 * Class m190207_134428_modify_questions_table
 */
class m190207_134428_modify_questions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('questions', 'title', $this->text()->notNull());
        $this->alterColumn('questions', 'description', $this->text()->null());
        $this->addColumn('questions', 'show_description', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('questions', 'points', $this->smallInteger()->notNull()->defaultValue(0));
        $this->addColumn('questions', 'type', $this->smallInteger()->notNull()->defaultValue(0)->comment('multiple, radio, opened'));
        $this->addColumn('questions', 'file_id', $this->integer()->null());

        $this->addForeignKey('fk_questions_file_id', 'questions', 'file_id', 'files', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_questions_file_id', 'questions');

        $this->dropColumn('questions', 'show_description');
        $this->dropColumn('questions', 'type');
        $this->dropColumn('questions', 'points');
        $this->dropColumn('questions', 'file_id');
    }
}
