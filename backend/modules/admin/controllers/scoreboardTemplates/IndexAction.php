<?php

namespace app\modules\admin\controllers\scoreboardTemplates;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use app\models\ScoreboardTemplates;
/**
 * @inheritdoc
 */
class IndexAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = $this->getModel();
        $model->setAttributes(Yii::$app->request->post('filter'));

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }


        $query = ScoreboardTemplates::find()->asArray();
        $query->andFilterWhere([
            'or',
            ['like', 'name', $model->common],
        ]);
        $query->andFilterWhere(['like', 'name', $model->name]);

        $query->andWhere(['is_system' => 0]);


        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name'
                ]
            ]
        ]);


        $rows = [];
        foreach ($provider->getModels() as $row) {
            // unset($row['refresh_token']);
            $rows[] = $row;
        }
        return [
            'rows' => $rows,
            'curr_page' => $provider->getPagination()->getPage() + 1,
            'last_page' => ceil($provider->getTotalCount() / $provider->getPagination()->getPageSize())
        ];
    }
    /**
     * @inheritdoc
     */
    public function getModel()
    {
        $model = new DynamicModel([
            'common' => null,
            'name' => null
        ]);
        $model->addRule(['name', 'common'], 'trim');
        return $model;
    }
}
