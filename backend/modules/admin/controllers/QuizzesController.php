<?php

namespace app\modules\admin\controllers;

/**
 * @inheritdoc
 */
class QuizzesController extends Controller
{
    public function actions()
    {
        return [
            'question'          => 'app\modules\admin\controllers\quizzes\QuestionAction',
            'questions'         => 'app\modules\admin\controllers\quizzes\QuestionsAction',
            'quiz-questions'    => 'app\modules\admin\controllers\quizzes\QuizQuestionsAction',
            'event-quiz-status' => 'app\modules\admin\controllers\quizzes\EventQuizStatusAction',
        ];
    }
}