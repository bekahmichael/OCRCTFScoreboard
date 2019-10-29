<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use app\models\TeamUsers;

/**
 * @inheritdoc
 */
class AssignParticipantAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $data = Yii::$app->request->post();

        $tu = TeamUsers::findOne(['user_id' => $data['user_id'], 'team_id' => $data['team_id']]);

        if ($data['is_assigned'] === true) {
            if ($tu === null) {
                $tu = new TeamUsers([
                    'user_id' => $data['user_id'],
                    'team_id' => $data['team_id'],
                ]);
                $tu->insert(false);
            }

            $tu->status = TeamUsers::STATUS_ACTIVE;
            $tu->update();
        } else {
            if ($tu !== null) {
                $tu->status = TeamUsers::STATUS_DELETED;
                $tu->update();
            }
        }

        return [
            'code'   => 200,
            'data'   => $data,
            'errors' => $tu === null ? [] : $tu->getErrors(),
            'record' => $tu,
        ];
    }
}