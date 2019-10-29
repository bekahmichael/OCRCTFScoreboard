<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\EventQuizzes;

use app\models\AnswersQuiz;

use app\components\statistics\event\EventStatisticsTeams;
use app\components\statistics\event\QuestionsWithAnswersFromQuiz;
use app\components\statistics\event\TeamAnswersForQuiz;
use app\components\statistics\event\CheckTeamAnswer;
use app\components\statistics\event\TeamsWithRanks;


/**
 * @inheritdoc
 */
class ResultsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($event_id)
    {
        if (Yii::$app->request->isGet) {
            return $this->processGetData($event_id);
        } else if (Yii::$app->request->isPost) {
            if (Yii::$app->request->post('cmd') === 'update_points') {
                return $this->processUpdatePoint($event_id);
            }
        }

        return [];
    }

    private function processUpdatePoint($event_id)
    {
        $event2quiz = EventQuizzes::findOne(['event_id' => $event_id]);
        $data = Yii::$app->request->post('data');

        foreach ($data as $answer) {
            $answer_quiz = AnswersQuiz::findOne([
                'event_id'    => $event_id,
                'team_id'     => $answer['team_id'],
                'question_id' => $answer['question_id'],
                'quiz_id'     => $event2quiz->quiz_id,
            ]);

            if ($answer_quiz !== null) {
                unset($answer_quiz->answer_ids);
                $answer_quiz->change_points_to = trim($answer['change_points_to']) === '' ? null : floatval($answer['change_points_to']);
                $answer_quiz->update(false);
            } else {
                $answer_quiz = new AnswersQuiz([
                    'event_id'         => $event_id,
                    'team_id'          => $answer['team_id'],
                    'question_id'      => $answer['question_id'],
                    'quiz_id'          => $event2quiz->quiz_id,
                    'change_points_to' => trim($answer['change_points_to']) === '' ? null : floatval($answer['change_points_to']),
                ]);
                $answer_quiz->insert(false);
            }
        }

        return [
            'code' => 200,
            'data' => $data,
        ];
    }

    private function processGetData($event_id)
    {
        $teams = (new EventStatisticsTeams((int)$event_id))->all();
        $event2quiz = EventQuizzes::findOne(['event_id' => $event_id]);

        $questions = (new QuestionsWithAnswersFromQuiz($event2quiz->quiz_id))->all();

        foreach ($teams as $kt => $team) {
            $TeamAnswersForQuiz = (new TeamAnswersForQuiz($team['id'], $event2quiz->quiz_id, $event_id));
            $teams[$kt]['answers'] = $TeamAnswersForQuiz->all();
            $teams[$kt]['scores'] = $TeamAnswersForQuiz->hasAnswers() ? 0 : null;

            foreach($teams[$kt]['answers'] as $ak => $team_answer) {
                $teams[$kt]['answers'][$ak]['is_correct'] = (new CheckTeamAnswer(
                    $questions[$team_answer['question_id']],
                    $team_answer
                ))->isCorrect();

                if ($team_answer['change_points_to'] !== null) {
                    $teams[$kt]['scores'] += $team_answer['change_points_to'];
                } else if ($teams[$kt]['answers'][$ak]['is_correct'] === true) {
                    $teams[$kt]['scores'] += $questions[$team_answer['question_id']]['points'];
                }
            }
        }

        $teams = (new TeamsWithRanks($teams))->getTeams();

        return [
            'code'       => 200,
            'event_id'   => $event_id,
            'teams'      => $teams,
            'event2quiz' => $event2quiz,
            'questions'  => array_values($questions),
        ];
    }
}