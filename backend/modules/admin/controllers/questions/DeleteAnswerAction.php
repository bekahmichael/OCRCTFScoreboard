<?php

namespace app\modules\admin\controllers\questions;

use app\models\Answers;
use Yii;
use yii\base\Action;

/**
 * @inheritdoc
 */
class DeleteAnswerAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isPost) {
            $answer         = Answers::findOne($id);
            $answer->status = Answers::DELETED;
            $answer->save();
            return [
                'code' => 200,
            ];
        } else {
            return [];
        }
    }
}