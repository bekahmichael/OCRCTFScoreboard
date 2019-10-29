<?php

namespace app\modules\quiz\controllers\quiz;


use app\models\AnswersQuiz;
use app\models\EventQuizzes;
use app\models\Events;
use app\models\EventTeams;
use app\models\Questions;
use app\models\Quizzes;
use app\models\Teams;
use Yii;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/8/2019
 * Time: 16:05
 */

class IndexAction extends Action
{
    public function run($hash)
    {
        list($teamId, $accessKey) = explode(':', $hash);
        /** @var Events $event */
        if($event = $this->checkAccess($teamId, $accessKey)) {
            $event->event_date = Yii::$app->formatter->format($event->event_date, 'date');
            $event->event_time_start = date('H:i', strtotime($event->event_time_start));
            $event->event_time_end = date('H:i', strtotime($event->event_time_end));
            /** @var Quizzes $quiz */
            $quiz  = $event->getQuizzes()->one();
            if($quiz) {
                $event2quiz = EventQuizzes::findOne(['quiz_id' => $quiz->id, 'event_id' => $event->id]);
                $questions = [];
                $answers = [];
                if($event2quiz->status === EventQuizzes::STATUS_ACTIVE) {
                    $questions = Questions::find()->getWithSequence()->joinWith(['quiz2questions'])->where([
                        'quiz_id' => $quiz->id
                    ])->with('activeAnswers')->getActive()->asArray()->all();
                    $answers = AnswersQuiz::findAll([
                        'event_id'    => $event->id,
                        'quiz_id'     => $quiz->id,
                        'team_id'     => $teamId
                    ]);
                }

                if ($event->group_by_level == 1) {
                    $prev_level = null;
                    ArrayHelper::multisort($questions, ['level'], [SORT_ASC], [SORT_NATURAL]);
                    foreach ($questions as $qk => $question) {
                        if ($question['level'] !== $prev_level) {
                            $questions[$qk]['first_of_level'] = true;
                            $prev_level = $question['level'];
                        } else {
                            $questions[$qk]['first_of_level'] = false;
                        }
                    }
                }

                return [
                    'code' => 200,
                    'status' => $event2quiz->status,
                    'team' => Teams::findOne(['id' => $teamId]),
                    'event' => $event,
                    'quiz' => $quiz,
                    'questions' => $questions,
                    'answers' => $answers,
                ];
            }
            return [
                'code' => 200,
                'event' => $event,
            ];
        }
        return [
            'code' => 404,
            'data' => "Not found",
        ];
    }

    /**
     * @param integer $teamId
     * @param string  $accessKey
     * @return array|bool|ActiveRecord
     */
    private function checkAccess($teamId, $accessKey)
    {
        /** @var EventTeams $e2t */
        $e2t = EventTeams::find()->where(['team_id' => $teamId, 'access_key' => $accessKey])->getActive()->one();
        if ($e2t) {
            return $e2t->getEvent()->one();
        }
        return false;
    }
}