<?php

namespace app\components\statistics\event;

use yii\helpers\ArrayHelper;

/**
 * Prepare methods for calculate ranks of teamlist.
 */
class TeamsWithRanks
{
    private $teams;

    /**
     * Constructor.
     */
    public function __construct($teams_with_scores)
    {
        $this->teams = $teams_with_scores;
    }

    /**
     * Return calculated result;
     */
    public function getTeams()
    {
        ArrayHelper::multisort($this->teams, ['scores', 'name'], [SORT_DESC, SORT_ASC], [SORT_NATURAL, SORT_STRING]);

        $cur_rank = 1;

        foreach ($this->teams as $k => $v) {
            if ($v['scores'] !== null) {
                $this->teams[$k]['rank'] = $cur_rank;
                $cur_rank++;
            } else {
                $this->teams[$k]['rank'] = null;
            }
        }

        return $this->teams;
    }
}