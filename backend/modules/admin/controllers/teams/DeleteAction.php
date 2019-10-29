<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use app\models\Teams;

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
            $team =Teams::findOne($id);
            $team->status = Teams::STATUS_DELETED;
            $team->update(false);
            return [
                'code' => 200,
            ];
        } else {
            return [];
        }
    }
}