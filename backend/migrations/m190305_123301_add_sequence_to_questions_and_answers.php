<?php

use yii\db\Migration;

/**
 * Class m190305_123301_add_sequence_to_questions_and_answers
 */
class m190305_123301_add_sequence_to_questions_and_answers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('quiz2questions', 'sequence', $this->integer()->null());
        $this->addColumn('answers', 'sequence', $this->integer()->null());

        $this->execute("SET @r := 0; UPDATE quiz2questions SET sequence = (@r := @r + 1)");
        $this->execute("UPDATE answers SET sequence = id");
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('quiz2questions', 'sequence');
        $this->dropColumn('answers', 'sequence');
    }
}
