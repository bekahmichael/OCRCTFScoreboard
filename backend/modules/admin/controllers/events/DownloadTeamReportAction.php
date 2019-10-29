<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Events;
use app\models\Teams;
use app\components\media\Array2Xslsx;

use app\models\EventQuizzes;
use app\components\statistics\event\EventStatisticsTeams;
use app\components\statistics\event\QuestionsWithAnswersFromQuiz;
use app\components\statistics\event\TeamAnswersForQuiz;
use app\components\statistics\event\CheckTeamAnswer;
use app\components\statistics\event\TeamsWithRanks;

/**
 * @inheritdoc
 */
class DownloadTeamReportAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($event_id, $team_id)
    {
        $teamObj = Teams::findOne($team_id);
        $event = Events::findOne($event_id);
        $report_data = $this->processGetData($event_id);

        $sheats = [];

        foreach ($report_data['teams'] as $team) {
            if ($team['id'] != $team_id) {
                continue;
            }

            $data = [
                [
                    ['value' => '', 'style' => ['font-weight' => 'bold', 'width' => 5]],
                    ['value' => $team['name'], 'colspan' => 10, 'style' => ['font-size' => 16, 'font-weight' => 'bold', 'color' => '#2196f3', 'height' => 20]],
                ]
            ];

            $data[] = [
                ['value' => '', 'style' => ['font-weight' => 'bold']],
                ['value' => 'Scores: ' . number_format($team['scores']), 'colspan' => 10, 'style' => ['font-weight' => 'bold', 'width' => 5]],
            ];

            $data[] = [
                ['value' => '', 'style' => ['font-weight' => 'bold']],
                ['value' => 'Team Rank: ' . (trim($team['rank']) === '' ? 'N/A' : $team['rank']), 'colspan' => 10, 'style' => ['font-weight' => 'bold', 'width' => 5]],
            ];

            $data[] = [];

            $data[] = [
                ['value' => '',               'style' => ['font-weight' => 'bold']],
                ['value' => '',               'style' => ['font-weight' => 'bold', 'width' => 2]],
                ['value' => 'Question Title', 'style' => ['font-weight' => 'bold', 'width' => 50]],
                ['value' => 'Team Answers',   'style' => ['font-weight' => 'bold', 'width' => 50]],
                ['value' => 'Level',          'style' => ['font-weight' => 'bold', 'width' => 10, 'text-align' => 'right']],
                ['value' => 'Points',         'style' => ['font-weight' => 'bold', 'width' => 8, 'text-align' => 'right']],
                ['value' => 'Correct',        'style' => ['font-weight' => 'bold', 'width' => 8]],
                ['value' => 'Final Points',   'style' => ['font-weight' => 'bold', 'width' => 12, 'text-align' => 'right']],
                ['value' => 'Answered At',    'style' => ['font-weight' => 'bold', 'width' => 20]],
            ];

            foreach ($report_data['questions'] as $question) {
                $points_value = 0;
                $answered = [];
                $is_correct = [];
                $answered_at = [];

                if (array_key_exists($question['id'], $team['answers'])) {
                    $answered_at = ['value' => date('m/d/Y H:i:s', strtotime($team['answers'][$question['id']]['updated_at'])), 'style' => ['text-align' => 'left']];

                    $is_answered = ['value' => '✓', 'style' => ['text-align' => 'right', 'color' => '#4CAF50', 'font-weight' => 'bold', 'vertical-align' => 'top']];
                    if ($team['answers'][$question['id']]['is_correct']) {
                        $is_correct = ['value' => 'Yes', 'style' => ['text-align' => 'center', 'color' => '#4CAF50', 'font-weight' => 'bold']];

                        $points_value = $team['answers'][$question['id']]['change_points_to'];
                        if ($points_value === null) {
                            $points_value = $question['points'];
                        }
                    } else {
                        $is_correct = ['value' => 'No', 'style' => ['text-align' => 'center', 'color' => '#FF0000', 'font-weight' => 'bold']];
                    }
                } else {
                    $is_answered = ['value' => '⚪', 'style' => ['text-align' => 'right', 'color' => '#FF9800', 'font-weight' => 'bold', 'vertical-align' => 'top']];
                }

                $answers = '';
                if ($question['type'] == 3) {
                    if (array_key_exists($question['id'], $team['answers'])) {
                        $answers = $team['answers'][$question['id']]['answer_text'];
                    } else {
                        $answers = '';
                    }
                } else {
                    foreach ($question['answers'] as $answer) {
                        $is_checked = false;
                        if (array_key_exists($question['id'], $team['answers'])) {
                            if (in_array($answer['id'], $team['answers'][$question['id']]['answer_ids'])) {
                                $is_checked = true;
                            }
                        }

                        $answers .= ($is_checked ? '■' : '□') . ' ' . $answer['description'] . "\n";
                    }
                }

                $data[] = [
                    ['value' => ''],
                    $is_answered,
                    ['value' => $question['title'], 'wrap' => true, 'style' => ['height' => -1, 'vertical-align' => 'top']],
                    ['value' => trim($answers), 'wrap' => true, 'style' => ['text-align' => 'left']],
                    ['value' => $question['level']],
                    ['value' => $question['points'], 'format' => '#,##0.0'],
                    $is_correct,
                    ['value' => $points_value, 'style' => ['text-align' => 'right'], 'format' => '#,##0.0'],
                    $answered_at,
                ];
            }

            $sheats[$team['name']] = $data;
        }

        (new Array2Xslsx($sheats))->toDownload($teamObj->name . ' Report - ' . $event->name . ' - ' . date('m-d-Y') . '.xlsx');
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
            'teams'      => $teams,
            'event2quiz' => $event2quiz,
            'questions'  => array_values($questions),
        ];
    }
}