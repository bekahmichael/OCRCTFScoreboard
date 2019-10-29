<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/7/2019
 * Time: 12:07
 */

namespace app\modules\admin\controllers\quizzes;

use app\models\Questions;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;

class QuestionsAction extends Action
{
    public function run($quiz_id)
    {
        $model          = $this->getModel();
        $model->quiz_id = $quiz_id;

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $query = Questions::find()->where(['quiz_id' => $quiz_id])->asArray();

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
            'quiz_id' => null,
        ]);
        $model->addRule(['quiz_id'], 'required');
        $model->addRule(['quiz_id'], 'integer');
        return $model;
    }
}