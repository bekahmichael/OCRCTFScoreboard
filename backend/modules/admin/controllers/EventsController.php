<?php

namespace app\modules\admin\controllers;

/**
 * @inheritdoc
 */
class EventsController extends Controller
{
    public function actions()
    {
        return [
            'index'         => 'app\modules\admin\controllers\events\IndexAction',
            'delete'        => 'app\modules\admin\controllers\events\DeleteAction',
            'profile'       => 'app\modules\admin\controllers\events\ProfileAction',
            'teams'         => 'app\modules\admin\controllers\events\TeamsAction',
            'assign-team'   => 'app\modules\admin\controllers\events\AssignTeamAction',
            'scoreboard'    => 'app\modules\admin\controllers\events\ScoreboardAction',
            'event-quizzes' => 'app\modules\admin\controllers\events\EventQuizzesAction',
            'send-access-to-teams' => 'app\modules\admin\controllers\events\SendAccessToTeamsAction',
            'scoreboard-options' => 'app\modules\admin\controllers\events\ScoreboardOptionsAction',
            'results' => 'app\modules\admin\controllers\events\ResultsAction',
            'download-access-pins' => 'app\modules\admin\controllers\events\DownloadAccessPinsAction',
            'download-report' => 'app\modules\admin\controllers\events\DownloadReportAction',
            'download-team-report' => 'app\modules\admin\controllers\events\DownloadTeamReportAction',
        ];
    }
}