<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Events;

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
            $team =Events::findOne($id);
            $team->status = Events::STATUS_DELETED;
            $team->update(false);
            return [
                'code' => 200,
            ];
        } else {
            return [];
        }
    }
}