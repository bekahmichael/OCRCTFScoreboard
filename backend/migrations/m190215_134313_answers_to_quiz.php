<?php

use yii\db\Migration;

/**
 * Class m190215_134313_answers_to_quiz
 */
class m190215_134313_answers_to_quiz extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('answers2quiz', [
            'id'               => $this->primaryKey(),
            'event_id'             => $this->integer()->notNull(),
            'quiz_id'             => $this->integer()->notNull(),
            'team_id'             => $this->integer()->notNull(),
            'question_id'             => $this->integer()->notNull(),
            'answer_ids'             => $this->string(255),
            'answer_text'             => $this->text(),
            'created_at'       => $this->dateTime(),
            'updated_at'       => $this->dateTime(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createIndex('idx_event_quiz_team', 'answers2quiz', ['event_id', 'quiz_id', 'team_id']);
        $this->createIndex('idx_event_quiz_team_question', 'answers2quiz', ['event_id', 'quiz_id', 'team_id', 'question_id'], true);

        $this->addForeignKey('fk_answers2quiz_event_id', 'answers2quiz', 'event_id', 'events', 'id');
        $this->addForeignKey('fk_answers2quiz_quiz_id', 'answers2quiz', 'quiz_id', 'quizzes', 'id');
        $this->addForeignKey('fk_answers2quiz_team_id', 'answers2quiz', 'team_id', 'teams', 'id');
        $this->addForeignKey('fk_answers2quiz_question_id', 'answers2quiz', 'question_id', 'questions', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_answers2quiz_question_id', 'answers2quiz');
        $this->dropForeignKey('fk_answers2quiz_team_id', 'answers2quiz');
        $this->dropForeignKey('fk_answers2quiz_quiz_id', 'answers2quiz');
        $this->dropForeignKey('fk_answers2quiz_event_id', 'answers2quiz');

        $this->dropIndex('idx_event_quiz_team_question', 'answers2quiz');
        $this->dropIndex('idx_event_quiz_team', 'answers2quiz');

        $this->dropTable('answers2quiz');
    }
}
