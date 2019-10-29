<?php

use yii\db\Migration;

/**
 * Class m190827_123945_questions_library
 */
class m190827_123945_questions_library extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('answers', 'library_created_from_id', $this->integer()->unsigned());
        $this->addColumn('questions', 'library_question_id', $this->integer()->unsigned());
        $this->addColumn('questions', 'library_created_from_id', $this->integer()->unsigned());
        $this->addColumn('questions', 'is_library_question', $this->integer()->unsigned()->notNull()->defaultValue('0'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190827_123945_questions_library cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190827_123945_questions_library cannot be reverted.\n";

        return false;
    }
    */
}
