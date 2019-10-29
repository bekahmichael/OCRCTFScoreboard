<?php

namespace app\modules\admin\controllers\questions;


use app\models\Questions;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\data\ActiveDataProvider;

/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/1/2019
 * Time: 16:05
 */

class IndexAction extends Action
{
    public function run()
    {
        $model = $this->getModel();
        $model->setAttributes(\Yii::$app->request->post('filter'));

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $query = Questions::find()->asArray();
        $query->andFilterWhere([
            'or',
            ['like', 'title', $model->common],
            ['like', 'description', $model->common],
        ]);
        $query->andFilterWhere(['like', 'title', $model->title]);

        // Cdate range
        if (!empty($model->created_at_from) && !empty($model->created_at_to)) {
            $created_at_from = \DateTime::createFromFormat('m/d/Y', $model->created_at_from);
            $created_at_to = \DateTime::createFromFormat('m/d/Y', $model->created_at_to);
            if ($created_at_from && $created_at_to) {
                $query->andWhere(['between', 'created_at', $created_at_from->format('Y-m-d 00:00:00'), $created_at_to->format('Y-m-d 23:59:59')]);
            }
        } elseif (!empty($model->created_at_from)) {
            $created_at_from = \DateTime::createFromFormat('m/d/Y', $model->created_at_from);
            if ($created_at_from) {
                $query->andWhere(['>', 'created_at', $created_at_from->format('Y-m-d 00:00:00')]);
            }
        } elseif (!empty($model->created_at_to)) {
            $created_at_to = \DateTime::createFromFormat('m/d/Y', $model->created_at_to);
            if ($created_at_to) {
                $query->andWhere(['<', 'created_at', $created_at_to->format('Y-m-d 23:59:59')]);
            }
        }

        if (in_array($model->status, array_keys(Questions::STATUSES))) {
            $query->andFilterWhere(['status' => $model->status]);
        }
        if ($model->status == '') {
            $query->andWhere(['<>', "status", Questions::DELETED]);
        }

        if (in_array($model->type, array_keys(Questions::TYPES))) {
            $query->andFilterWhere(['type' => $model->type]);
        }
        $query->andWhere(["is_library_question" => "1"]);
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
        ]);

        $rows = [];
        foreach ($provider->getModels() as $row) {
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
            'title' => null,
            'created_at_from' => null,
            'created_at_to' => null,
            'status' => null,
            'type' => null,
        ]);
        $model->addRule(['title', 'common', 'created_at_from', 'created_at_to', 'status', 'type'], 'trim');
        return $model;
    }
}