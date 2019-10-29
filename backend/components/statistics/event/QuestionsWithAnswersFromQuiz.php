<?php

namespace app\components\statistics\event;

use yii\helpers\ArrayHelper;
use app\models\Questions;
use app\models\Answers;
use app\models\QuizQuestions;

/**
 * Prepare list of question with answers.
 */
class QuestionsWithAnswersFromQuiz
{
    private $quiz_id;

    /**
     * Constructor.
     */
    public function __construct($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function all()
    {
        $questions_ids = $this->getAllquestionsIds();

        $questions = ArrayHelper::toArray(
            Questions::find()
                ->select([
                    'id',
                    'title',
                    'type',
                    'points',
                    'level',
                ])
                ->where(['id' => $questions_ids])
                ->andWhere(['status' => Answers::ACTIVE])
                ->all()
        );

        $answers = ArrayHelper::index(
            ArrayHelper::toArray(
                Answers::find()
                    ->select([
                        'id',
                        'description',
                        'question_id',
                        'is_correct',
                    ])
                    ->where(['question_id' => $questions_ids])
                    ->andWhere(['status' => Answers::ACTIVE])
                    ->all()
            )
        , null, 'question_id');

        foreach ($questions as $qkey => $qvalue) {
            $questions[$qkey]['points'] = floatval($questions[$qkey]['points']);
            if (array_key_exists($qvalue['id'], $answers)) {
                $questions[$qkey]['answers'] = $answers[$qvalue['id']];
            }
        }

        return ArrayHelper::index($questions, 'id');
    }

    private function getAllquestionsIds()
    {
        return QuizQuestions::find()->select('question_id')->where(['quiz_id' => $this->quiz_id])->column();
    }
}