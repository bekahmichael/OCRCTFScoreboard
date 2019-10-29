<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use app\models\Teams;
use app\models\EventTeams;
/**
 * @inheritdoc
 */
class TeamsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $id = intval($id);
        $model = $this->getModel();
        $model->setAttributes(Yii::$app->request->post('filter'));

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $query = Teams::find()
                    ->select([
                        'id'             => 'teams.id',
                        'name'           => 'teams.name',
                        'event_teams_id' => 'event_teams.id',
                        'access_key'     => 'event_teams.access_key',
                        'pin'            => 'event_teams.pin',
                    ])
                    ->asArray();

        $query->leftJoin(
            'event_teams',
            implode(' AND ', [
                'event_teams.team_id = teams.id',
                'event_teams.event_id = ' . $id,
                'event_teams.status = '   . EventTeams::STATUS_ACTIVE,
            ])
        );

        $query->andFilterWhere([
            'or',
            ['like', 'teams.name', $model->common],
        ]);
        $query->andFilterWhere(['like', 'name', $model->name]);

        if ($model->assigned == 1) {
            $query->andWhere(['not', ['event_teams.id' => null]]);
        }

        $query->andWhere(['teams.status' => Teams::STATUS_ACTIVE]);

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
            $row['is_assigned'] = intval($row['event_teams_id']) > 0;
            if (trim($row['pin']) !== '') {
                $row['pin'] = strtoupper($row['pin']);
            }
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
            'common'   => null,
            'name'     => null,
            'assigned' => null,
        ]);
        $model->addRule(['name', 'common', 'assigned'], 'trim');
        return $model;
    }
}
