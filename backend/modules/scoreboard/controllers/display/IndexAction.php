<?php

namespace app\modules\scoreboard\controllers\display;

use Yii;
use yii\helpers\ArrayHelper;

use yii\base\Action;
use app\models\Scoreboards;
use app\models\Events;
use app\models\Teams;
use app\models\EventTeams;
use yii\helpers\Url;
use app\models\Quizzes;
use app\models\EventQuizzes;
use app\models\ScoreboardTemplates;

use app\models\AnswersQuiz;
use app\models\Questions;
use app\models\Answers;

use app\components\statistics\event\TeamsWithRanks;
use app\components\statistics\event\CheckTeamAnswer;

/**
 * @inheritdoc
 */
class IndexAction extends Action
{
    private $step = 1;
    private $maxSteps = 60;

    /**
     * @inheritdoc
     */
    public function run($hash)
    {
        session_write_close();
        $access_key = Yii::$app->request->post('access_key', Yii::$app->request->get('access_key'));

        $data = $this->getData($access_key);

        if ($data['scoreboard']) {
            $curStep = 0;

            while ($curStep < $this->maxSteps) {
                $calcHash = $this->calculateHash($data);
                if ($hash === $calcHash) {
                    sleep($this->step);
                    $curStep++;
                    $data = $this->getData($access_key);
                } else {
                    break;
                }
            }

            if ($data['scoreboard']['background_image_file_id'] !== null) {
                $data['background_image_url'] = strtr('{host}/{path}?id={id}', [
                    '{host}' => Url::base(env('HTTPS_ON') === true ? 'https' : 'http'),
                    '{path}' => '/admin/teams/avatar/',
                    '{id}'   => $data['scoreboard']['background_image_file_id'],
                ]);
            } else if ($data['scoreboard_template']['background_image_file_id'] !== null) {
                $data['background_image_url'] = strtr('{host}/{path}?id={id}', [
                    '{host}' => Url::base(env('HTTPS_ON') === true ? 'https' : 'http'),
                    '{path}' => '/admin/teams/avatar/',
                    '{id}'   => $data['scoreboard_template']['background_image_file_id'],
                ]);
            } else {
                $data['background_image_url'] = null;
            }

            if ($data['event2quiz']['status'] === EventQuizzes::STATUS_NOT_STARTED) {
                $data['time_left'] = $data['event2quiz']['time'] * 60;
            } else if ($data['event2quiz']['status'] === EventQuizzes::STATUS_ACTIVE) {
                $data['time_left'] = $data['event2quiz']['time'] * 60 - ((time() - strtotime($data['event2quiz']['time_start'])) + $data['event2quiz']['passed_time'] * 60);
            } else if ($data['event2quiz']['status'] === EventQuizzes::STATUS_PAUSED) {
                $data['time_left'] = $data['event2quiz']['time'] * 60 - $data['event2quiz']['passed_time'] * 60;
            } else {
                $data['time_left'] = 0;
            }

            return [
                'code'       => 200,
                'hash'       => $calcHash,
                'data'       => $data,
                'hasChanges' => $hash !== $calcHash,
            ];
        } else {
            return [
                'code' => 404,
            ];
        }
    }

    private function calculateHash($data = [])
    {
        return sha1(serialize($data));
    }

    private function getData($access_key)
    {
        $scoreboard = Scoreboards::findOne(['access_key' => $access_key]);
        if ($scoreboard) {
            $event = Events::findOne(['id' => $scoreboard->event_id, 'status' => Events::STATUS_ACTIVE]);

            if ($event === null) {
                return [
                    'code' => 404,
                ];
            }

            $scoreboard_template = ScoreboardTemplates::findOne($scoreboard->template_id);

            $teams = EventTeams::find()
                    ->where([
                        'event_id' => $scoreboard->event_id,
                        'status' => EventTeams::STATUS_ACTIVE,
                    ])
                    ->with('team')
                    ->asArray()
                    ->all();


            foreach ($teams as $kt => $kv) {
                if (intval($teams[$kt]['team']['status']) !== Teams::STATUS_ACTIVE) {
                    unset($teams[$kt]);
                }
            }

            $event2quiz = EventQuizzes::findOne(['event_id' => $event->id]);
            $team_scores = $this->getScoresForAllTeamsWithCache($event->id, $event2quiz->quiz_id);

            foreach ($teams as $k => $team) {
                $teams[$k]['scores'] = null;
                if (array_key_exists($team['team_id'], $team_scores)) {
                    $teams[$k]['scores'] = $team_scores[$team['team_id']];
                }

                $teams[$k]['id'] = (int) $teams[$k]['id'];
                $teams[$k]['event_id'] = (int) $teams[$k]['event_id'];
                $teams[$k]['status'] = (int) $teams[$k]['status'];
                $teams[$k]['team_id'] = (int) $teams[$k]['status'];

                unset($teams[$k]['access_key']);
                if (!empty($team['team']['avatar_file_id'])) {
                    $teams[$k]['avatar_url'] = Url::base(env('HTTPS_ON') === true ? 'https' : 'http') . '/admin/teams/avatar/?id=' . $team['team']['avatar_file_id'];
                } else {
                    $teams[$k]['avatar_url'] = null;
                }
            }

            $teams = (new TeamsWithRanks($teams))->getTeams();

            $quiz = Quizzes::findOne(['id' => $event2quiz->quiz_id])->toArray();

            return [
                'scoreboard'          => $scoreboard,
                'event'               => $event,
                'teams'               => $teams,
                'quiz'                => $quiz,
                'event2quiz'          => $event2quiz,
                'scoreboard_template' => $scoreboard_template,
            ];
        }
        return [
            'scoreboard'          => null,
            'event'               => null,
            'teams'               => null,
            'quiz'                => null,
            'event2quiz'          => null,
            'scoreboard_template' => null,
        ];
    }

    /**
     * Return list of scores of teams with delay.
     *
     * @param integer $event_id
     * @param integer $quiz_id
     * @return array
     */
    private function getScoresForAllTeamsWithCache($event_id, $quiz_id)
    {
        $cache = Yii::$app->cache;
        $team_scores = $cache->get(['getScoresForAllTeams', $event_id, $quiz_id]);

        if ($team_scores === false) {
            $team_scores = $this->getScoresForAllTeams($event_id, $quiz_id);
            $cache->set(['getScoresForAllTeams', $event_id, $quiz_id], $team_scores, 1 * 60);
        }

        return $team_scores;
    }

    /**
     * Calculate and return scores of teams.
     *
     * @param integer $event_id
     * @param integer $quiz_id
     * @return array the results
     */
    private function getScoresForAllTeams($event_id, $quiz_id)
    {
        $team_scores = [];

        $quiz_answers = ArrayHelper::toArray(
            AnswersQuiz::find()
                ->select([
                    'quiz_id',
                    'team_id',
                    'question_id',
                    'answer_ids',
                    'answer_text',
                    'change_points_to',
                ])
                ->where(['event_id' => $event_id, 'quiz_id' => $quiz_id])
                ->all()
        );

        $questions_ids = ArrayHelper::getColumn($quiz_answers, 'question_id');

        $questions = $this->getQuestions($questions_ids);

        foreach ($quiz_answers as $kqa => $vqa) {
            if (!array_key_exists($vqa['team_id'], $team_scores)) {
                $team_scores[$vqa['team_id']] = 0;
            }

            $checkIsCorrect = new CheckTeamAnswer($questions[$vqa['question_id']], $vqa);

            if ($vqa['change_points_to'] !== null) {
                $team_scores[$vqa['team_id']] += floatval($vqa['change_points_to']);
            } else if ($checkIsCorrect->isCorrect() === true) {
                $team_scores[$vqa['team_id']] += floatval($questions[$vqa['question_id']]['points']);
            }
        }

        return $team_scores;
    }

    /**
     * Prepare list of question with answers.
     *
     * @param array $questions_ids
     * @return array
     */
    private function getQuestions($questions_ids)
    {
        $questions = ArrayHelper::toArray(
            Questions::find()
                ->select([
                    'id',
                    'title',
                    'type',
                    'points',
                ])
                ->where(['id' => $questions_ids])
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
            if (array_key_exists($qvalue['id'], $answers)) {
                $questions[$qkey]['answers'] = $answers[$qvalue['id']];
            }
        }

        return ArrayHelper::index($questions, 'id');
    }
}