<?php

namespace app\modules\admin\controllers\scoreboardTemplates;

use Yii;
use yii\base\Action;
use app\models\ScoreboardTemplates;
use app\models\Scoreboards;

/**
 * @inheritdoc
 */
class DeleteAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isPost) {
            Scoreboards::updateAll(['template_id' => 1], ['template_id' => $id]);
            $scoreboardTemplate = ScoreboardTemplates::findOne($id);
            $scoreboardTemplate->delete(false);

            return [
                'code' => 200
            ];
        } else {
            return [];
        }
    }
}
