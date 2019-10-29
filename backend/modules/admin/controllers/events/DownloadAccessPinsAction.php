<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\EventTeams;
use app\models\TeamUsers;
use app\models\Teams;
use app\models\Events;

use app\components\media\Array2Xslsx;

/**
 * @inheritdoc
 */
class DownloadAccessPinsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($event_id)
    {
        $teams = $this->getAssignedTeams($event_id);
        $event = Events::findOne($event_id);

        $data = [
            [
                ['value' => $event->name, 'colspan' => 10, 'style' => ['font-size' => 16, 'font-weight' => 'bold', 'color' => '#2196f3', 'height' => 20]],
            ]
        ];

        $data[] = [];

        $data[] = [
            ['value' => 'Team Name',  'style' => ['font-weight' => 'bold', 'width' => 25]],
            ['value' => 'Access PIN', 'style' => ['font-weight' => 'bold', 'width' => 14]],
        ];

        foreach ($teams as $team) {
            $data[] = [
                ['value' => $team['name']],
                ['value' => strtoupper($team['pin'])],
            ];
        }

        (new Array2Xslsx(['AccessPINs' => $data]))->toDownload($event->name . '.xlsx');
    }

    private function getAssignedTeams($event_id)
    {
        $query = Teams::find()
                    ->select([
                        'id'             => 'teams.id',
                        'name'           => 'teams.name',
                        'event_teams_id' => 'event_teams.id',
                        'access_key'     => 'event_teams.access_key',
                        'pin'            => 'event_teams.pin',
                    ])
                    ->asArray();

        $query->leftJoin(
            'event_teams',
            implode(' AND ', [
                'event_teams.team_id = teams.id',
                'event_teams.event_id = ' . $event_id,
                'event_teams.status = ' . EventTeams::STATUS_ACTIVE,
            ])
        );

        $query->andWhere(['not', ['event_teams.id' => null]]);
        $query->andWhere(['teams.status' => Teams::STATUS_ACTIVE]);
        $query->orderBy(['teams.name' => SORT_ASC]);

        return $query->asArray()->all();
    }
}