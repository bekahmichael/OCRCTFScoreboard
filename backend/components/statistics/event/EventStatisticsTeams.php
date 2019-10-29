<?php

namespace app\components\statistics\event;

use app\models\EventTeams;
use app\models\Teams;

/**
 * Provide of all active events.
 */
class EventStatisticsTeams
{
    private $event_id;

    /**
     * Construct.
     */
    public function __construct($event_id)
    {
        $this->event_id = $event_id;
    }

    /**
     * Return all teams.
     */
    public function all()
    {
        $output = [];
        $teams = EventTeams::find()
            ->where([
                'event_id' => $this->event_id,
                'status' => EventTeams::STATUS_ACTIVE,
            ])
            ->with('team')
            ->all();

        foreach ($teams as $k => $team) {
            if ($team['team']['status'] === Teams::STATUS_ACTIVE) {
                $output[$k]['id'] = $team['team_id'];
                $output[$k]['name'] = $team['team']['name'];
            }
        }

        return $output;
    }
}