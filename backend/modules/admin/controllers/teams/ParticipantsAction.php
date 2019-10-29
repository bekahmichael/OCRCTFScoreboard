<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use app\models\Users;
use app\models\TeamUsers;

/**
 * @inheritdoc
 */
class ParticipantsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($team_id)
    {
        $model = $this->getModel();
        $model->setAttributes(Yii::$app->request->post('filter'));


        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $query = Users::find()->asArray();

        $query->select([
            '`users`.*',
            '`team_users`.`id` as `team_user_id`',
        ]);

        $query->andFilterWhere([
            'or',
            ['like', 'username', $model->common],
            ['like', 'first_name', $model->common],
            ['like', 'last_name', $model->common],
            ['like', 'email', $model->common],
        ]);

        $query->andFilterWhere(['like', 'username', $model->username]);
        $query->andFilterWhere(['like', 'first_name', $model->first_name]);
        $query->andFilterWhere(['like', 'last_name', $model->last_name]);
        $query->andFilterWhere(['like', 'last_name', $model->last_name]);
        $query->andFilterWhere(['like', 'email', $model->email]);

        $query->leftJoin('auth_assignment', 'users.id = auth_assignment.user_id');
        $query->leftJoin(
            'team_users',
            implode(' AND ', [
                'users.id = team_users.user_id',
                'team_users.status = ' . TeamUsers::STATUS_ACTIVE,
                'team_users.team_id = ' . intval($team_id),
            ])
        );

        $query->andWhere(['auth_assignment.item_name' => 'public']);

        $query->andWhere(['users.status' => 'active']);

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'email',
                    'last_name',
                    'first_name',
                    'username',
                    'status',
                    'created_at',
                ]
            ]
        ]);


        $rows = [];
        foreach ($provider->getModels() as $row) {
            unset($row['password']);
            $row['is_assigned'] = $row['team_user_id'] > 0;
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
            'common'     => null,
            'username'   => null,
            'first_name' => null,
            'last_name'  => null,
            'email'      => null,
        ]);
        $model->addRule(['username', 'first_name', 'last_name', 'email', 'common'], 'trim');
        return $model;
    }
}
