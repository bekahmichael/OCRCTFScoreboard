<?php

namespace app\components\statistics\event;

use yii\helpers\ArrayHelper;
use app\models\AnswersQuiz;

/**
 * Return answers provided from team for the quiz.
 */
class TeamAnswersForQuiz
{
    private $team_id;
    private $quiz_id;
    private $event_id;

    /**
     * Constructor.
     */
    public function __construct($team_id, $quiz_id, $event_id)
    {
        $this->team_id = $team_id;
        $this->quiz_id = $quiz_id;
        $this->event_id = $event_id;
    }

    public function all()
    {
        $quiz_answers = ArrayHelper::toArray(
            AnswersQuiz::find()
                ->select([
                    'question_id',
                    'answer_ids',
                    'answer_text',
                    'change_points_to',
                    'updated_at',
                ])
                ->where([
                    'event_id' => $this->event_id,
                    'quiz_id'  => $this->quiz_id,
                    'team_id'  => $this->team_id,
                ])
                ->all()
        );

        foreach ($quiz_answers as $ka => $answer) {
            $ids = [];

            foreach ($answer['answer_ids'] as $id) {
                if (trim($id) !== '') {
                    $ids[] = (int) $id;
                }
            }

            $quiz_answers[$ka]['change_points_to'] = trim($answer['change_points_to']) === '' ? null : floatval($answer['change_points_to']);
            $quiz_answers[$ka]['answer_ids'] = $ids;
        }

        if (count($quiz_answers) === 0) {
            return (object) [];
        }

        return ArrayHelper::index($quiz_answers, 'question_id');
    }

    public function hasAnswers()
    {
        foreach ($this->all() as $answer) {
            if (count($answer['answer_ids']) > 0 || trim($answer['answer_text']) !== '') {
                return true;
            }
        }

        return false;
    }
}