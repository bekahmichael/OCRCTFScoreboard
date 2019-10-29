<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use app\models\Teams;
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


        $query = Teams::find()->asArray();
        $query->andFilterWhere([
            'or',
            ['like', 'name', $model->common],
        ]);
        $query->andFilterWhere(['like', 'name', $model->name]);

        $query->andWhere(['!=', 'status', Teams::STATUS_DELETED]);


        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'created_at'
                ]
            ]
        ]);

        // Cdate range
        if (!empty($model->created_at_from) && !empty($model->created_at_to)) {
            $created_at_from = \DateTime::createFromFormat('m/d/Y', $model->created_at_from);
            $created_at_to = \DateTime::createFromFormat('m/d/Y', $model->created_at_to);
            if ($created_at_from && $created_at_to) {
                $query->andWhere(['between', 'created_at', $created_at_from->format('Y-m-d 00:00:00'), $created_at_to->format('Y-m-d 23:59:59')]);
            }
        } else if (!empty($model->created_at_from)) {
            $created_at_from = \DateTime::createFromFormat('m/d/Y', $model->created_at_from);
            if ($created_at_from) {
                $query->andWhere(['>', 'created_at', $created_at_from->format('Y-m-d 00:00:00')]);
            }
        } else if (!empty($model->created_at_to)) {
            $created_at_to = \DateTime::createFromFormat('m/d/Y', $model->created_at_to);
            if ($created_at_to) {
                $query->andWhere(['<', 'created_at', $created_at_to->format('Y-m-d 23:59:59')]);
            }
        }

        $rows = [];
        foreach ($provider->getModels() as $row) {
            $row['status'] = (int) $row['status'];
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
            'name' => null,
            'created_at_from' => null,
            'created_at_to' => null,
        ]);
        $model->addRule(['name', 'common', 'created_at_from', 'created_at_to'], 'trim');
        return $model;
    }
    }