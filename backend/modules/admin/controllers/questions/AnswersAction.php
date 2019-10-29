<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/5/2019
 * Time: 16:15
 */

namespace app\modules\admin\controllers\questions;

use app\models\Answers;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;

class AnswersAction extends Action
{
    public function run($question_id)
    {
        $model              = $this->getModel();
        $model->question_id = $question_id;

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $query = Answers::find()->where(['question_id' => $question_id])->getNotDeleted()->orderBy('sequence')->asArray();

        $provider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => false,
        ]);

        $rows = [];
        foreach ($provider->getModels() as $row) {
            $rows[] = $row;
        }
        return [
            'rows' => $rows,
        ];
    }


    /**
     * @inheritdoc
     */
    public function getModel()
    {
        $model = new DynamicModel([
            'question_id' => null,
        ]);
        $model->addRule(['question_id'], 'required');
        $model->addRule(['question_id'], 'integer');
        return $model;
    }
}