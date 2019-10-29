<?php

namespace app\modules\admin\controllers\questions;

use app\models\Questions;
use Yii;
use yii\base\Action;

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
            $question         = Questions::findOne($id);
            $question->status = Questions::DELETED;
            $question->save();
            return [
                'code' => 200,
            ];
        } else {
            return [];
        }
    }
}