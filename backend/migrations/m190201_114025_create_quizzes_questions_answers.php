<?php

use yii\db\Migration;

/**
 * Class m190201_114025_create_quizzes_questions_answers
 */
class m190201_114025_create_quizzes_questions_answers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('events', 'description', $this->text()->notNull());
        $this->addColumn('events', 'updated_at', $this->dateTime());
        $this->addColumn('events', 'user_id', $this->integer()->notNull()->comment('User which created event'));
        $this->addColumn('events', 'updater_id', $this->integer());

        $this->createTable('quizzes', [
            'id'          => $this->primaryKey(),
            'title'       => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'status'      => $this->smallInteger()->notNull(),
            'time'        => $this->integer()->notNull()->comment('Time in minutes'),
            'user_id'     => $this->integer()->notNull()->comment('User which created quiz'),
            'updater_id'  => $this->integer(),
            'created_at'  => $this->dateTime(),
            'updated_at'  => $this->dateTime(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('questions', [
            'id'          => $this->primaryKey(),
            'title'       => $this->string(255)->notNull(),
            'description' => $this->text()->notNull(),
            'status'      => $this->smallInteger()->notNull(),
            'user_id'     => $this->integer()->notNull()->comment('User which created question'),
            'updater_id'  => $this->integer(),
            'created_at'  => $this->dateTime(),
            'updated_at'  => $this->dateTime(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('answers', [
            'id'          => $this->primaryKey(),
            'question_id' => $this->integer()->notNull(),
            'description' => $this->text()->notNull(),
            'is_correct'  => $this->smallInteger()->notNull(),
            'status'      => $this->smallInteger()->notNull(),
            'user_id'     => $this->integer()->notNull()->comment('User which created question'),
            'updater_id'  => $this->integer(),
            'created_at'  => $this->dateTime(),
            'updated_at'  => $this->dateTime(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');

        $this->createTable('event2quizzes', [
            'event_id' => $this->integer()->notNull(),
            'quiz_id'  => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('event-quiz_pk', 'event2quizzes', ['event_id', 'quiz_id']);

        $this->createTable('quiz2questions', [
            'quiz_id'     => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB');
        $this->addPrimaryKey('quiz-question_pk', 'quiz2questions', ['quiz_id', 'question_id']);

        $this->addForeignKey('fk_events_user_id', 'events', 'user_id', 'users', 'id');
        $this->addForeignKey('fk_quizzes_user_id', 'quizzes', 'user_id', 'users', 'id');
        $this->addForeignKey('fk_questions_user_id', 'questions', 'user_id', 'users', 'id');
        $this->addForeignKey('fk_answers_user_id', 'answers', 'user_id', 'users', 'id');
        $this->addForeignKey('fk_answers_question_id', 'answers', 'question_id', 'questions', 'id', 'CASCADE');

        $this->addForeignKey('fk_event2quizzes_event_id', 'event2quizzes', 'event_id', 'events', 'id', 'CASCADE');
        $this->addForeignKey('fk_event2quizzes_quiz_id', 'event2quizzes', 'quiz_id', 'quizzes', 'id', 'CASCADE');

        $this->addForeignKey('fk_quiz2questions_quiz_id', 'quiz2questions', 'quiz_id', 'quizzes', 'id', 'CASCADE');
        $this->addForeignKey('fk_quiz2questions_question_id', 'quiz2questions', 'question_id', 'questions', 'id', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_quiz2questions_question_id', 'quiz2questions');
        $this->dropForeignKey('fk_quiz2questions_quiz_id', 'quiz2questions');

        $this->dropForeignKey('fk_event2quizzes_quiz_id', 'event2quizzes');
        $this->dropForeignKey('fk_event2quizzes_event_id', 'event2quizzes');

        $this->dropForeignKey('fk_answers_question_id', 'answers');
        $this->dropForeignKey('fk_answers_user_id', 'answers');
        $this->dropForeignKey('fk_questions_user_id', 'questions');
        $this->dropForeignKey('fk_quizzes_user_id', 'quizzes');
        $this->dropForeignKey('fk_events_user_id', 'events');

        $this->dropTable('quiz2questions');
        $this->dropTable('event2quizzes');

        $this->dropTable('answers');
        $this->dropTable('questions');
        $this->dropTable('quizzes');

        $this->dropColumn('events', 'description');
        $this->dropColumn('events', 'updated_at');
        $this->dropColumn('events', 'user_id');
        $this->dropColumn('events', 'updater_id');
    }
}
