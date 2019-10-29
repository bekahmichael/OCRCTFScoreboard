<?php

namespace app\modules\quiz\controllers\quiz;

use Yii;
use yii\base\Action;
use app\modules\quiz\models\forms\QuizAccessForm;
use app\models\EventTeams;

/**
 * @inheritdoc
 */
class AccessAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $model = new QuizAccessForm([
                'pin' => strtolower(Yii::$app->request->post('pin')),
            ]);

            if ($model->validate()) {
                $event_team = EventTeams::findOne(['pin' => $model->pin]);

                return [
                    'code'       => 200,
                    'access_key' => $event_team->access_key,
                    'event_id'   => $event_team->event_id,
                    'team_id'    => $event_team->team_id,
                ];
            } else {
                return [
                    'code'   => 400,
                    'errors' => $model->getFirstErrors(),
                ];
            }
        } else {
            return [];
        }
    }
}