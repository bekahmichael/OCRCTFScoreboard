<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\base\DynamicModel;
use app\models\Events;
use app\models\EventQuizzes;
use app\models\Scoreboards;

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

        $query = Events::find()->asArray();
        $query->andFilterWhere([
            'or',
            ['like', 'name', $model->common],
            ['like', 'description', $model->common],
        ]);
        $query->andFilterWhere(['like', 'name', $model->name]);
        $query->andFilterWhere(['like', 'description', $model->description]);

        // Cdate range
        if (!empty($model->event_date_from) && !empty($model->event_date_to)) {
            $event_date_from = \DateTime::createFromFormat('m/d/Y', $model->event_date_from);
            $event_date_to = \DateTime::createFromFormat('m/d/Y', $model->event_date_to);
            if ($event_date_from && $event_date_to) {
                $query->andWhere(['between', 'events.event_date', $event_date_from->format('Y-m-d 00:00:00'), $event_date_to->format('Y-m-d 23:59:59')]);
            }
        } else if (!empty($model->event_date_from)) {
            $event_date_from = \DateTime::createFromFormat('m/d/Y', $model->event_date_from);
            if ($event_date_from) {
                $query->andWhere(['>', 'events.event_date', $event_date_from->format('Y-m-d 00:00:00')]);
            }
        } else if (!empty($model->event_date_to)) {
            $event_date_to = \DateTime::createFromFormat('m/d/Y', $model->event_date_to);
            if ($event_date_to) {
                $query->andWhere(['<', 'events.event_date', $event_date_to->format('Y-m-d 23:59:59')]);
            }
        }

        if (!empty($model->status)) {
            $query->andWhere(['status' => $model->status]);
        } else {
            $query->andWhere(['!=', 'status', Events::STATUS_DELETED]);
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
            ],
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                    'event_date',
                    'event_time_start',
                    'event_time_end',
                    'status',
                    'description',
                ]
            ]
        ]);


        $rows = [];
        foreach ($provider->getModels() as $row) {
            $row['event_time_start'] = date('H:i', strtotime($row['event_time_start']));
            $row['event_time_end'] = date('H:i', strtotime($row['event_time_end']));

            $row['event2quiz'] = EventQuizzes::findOne(['event_id' => $row['id']]);
            $row['scoreboard'] = Scoreboards::findOne(['event_id' => $row['id']]);

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
            'description' => null,
            'event_date_from' => null,
            'event_date_to' => null,
            'status' => null,
        ]);
        $model->addRule(['name', 'common', 'description', 'event_date_from', 'event_date_to', 'status'], 'trim');
        return $model;
    }
}
