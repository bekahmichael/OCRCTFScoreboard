<?php

use yii\db\Migration;

/**
 * Class m190207_150555_modify_answers_table
 */
class m190207_150555_modify_answers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('answers', 'file_id', $this->integer()->null());

        $this->addForeignKey('fk_answers_file_id', 'answers', 'file_id', 'files', 'id', 'SET NULL');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_answers_file_id', 'answers');
        
        $this->dropColumn('answers', 'file_id');
    }
}
