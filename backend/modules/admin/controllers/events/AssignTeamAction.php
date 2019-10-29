<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\EventTeams;

/**
 * @inheritdoc
 */
class AssignTeamAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $data = Yii::$app->request->post();

        $e2t = EventTeams::findOne(['event_id' => $data['event_id'], 'team_id' => $data['team_id']]);

        if ($data['is_assigned'] === true) {
            if ($e2t === null) {
                $e2t = new EventTeams([
                    'event_id' => $data['event_id'],
                    'team_id'  => $data['team_id'],
                ]);
                $e2t->insert(false);
            }

            $e2t->status = EventTeams::STATUS_ACTIVE;
            $e2t->update();
        } else {
            if ($e2t !== null) {
                $e2t->status = EventTeams::STATUS_DELETED;
                $e2t->update();
            }
        }

        return [
            'code'   => 200,
            'data'   => $data,
            'errors' => $e2t === null ? [] : $e2t->getErrors(),
            'record' => $e2t,
        ];
    }
}