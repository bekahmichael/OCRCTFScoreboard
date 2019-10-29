<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use app\models\Teams;

/**
 * @inheritdoc
 */
class BlockAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isPost) {
            $team =Teams::findOne($id);
            $team->status = Teams::STATUS_BLOCKED;
            $team->update(false);
            return [
                'code' => 200,
            ];
        } else {
            return [];
        }
    }
}