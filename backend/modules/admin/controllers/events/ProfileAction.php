<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Events;
use app\modules\admin\models\forms\EventForm;
use app\models\EventQuizzes;
use app\models\Scoreboards;
use yii\helpers\ArrayHelper;

/**
 * @inheritdoc
 */
class ProfileAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isGet) {
            $event = Events::findOne($id);

            $event->event_date = Yii::$app->formatter->format($event->event_date, 'date');
            $event->event_time_start = date('H:i', strtotime($event->event_time_start));
            $event->event_time_end = date('H:i', strtotime($event->event_time_end));

            $event = ArrayHelper::toArray($event);

            $event['event2quiz'] = EventQuizzes::findOne(['event_id' => $event['id']]);
            $event['scoreboard'] = Scoreboards::findOne(['event_id' => $event['id']]);

            $event['duration'] = $event['event2quiz']->time;

            return [
                'status' => 200,
                'data'   => $event,
            ];
        } else {
            $model = new EventForm([
                'id'               => $id,
                'name'             => Yii::$app->request->post('name'),
                'description'      => Yii::$app->request->post('description'),
                'status'           => Yii::$app->request->post('status'),
                'event_date'       => Yii::$app->request->post('event_date'),
                'event_time_start' => Yii::$app->request->post('event_time_start'),
                'event_time_end'   => Yii::$app->request->post('event_time_end'),
                'duration'         => Yii::$app->request->post('duration'),
                'group_by_level'   => Yii::$app->request->post('group_by_level'),
            ]);

            if ($model->validate()) {
                $event_date = \DateTime::createFromFormat(ltrim(Yii::$app->formatter->dateFormat, 'php:'), $model->event_date)->format('Y-m-d');
                if ($id == 0) {
                    $new = new Events([
                        'name'             => $model->name,
                        'description'      => $model->description,
                        'status'           => $model->status,
                        'event_date'       => $event_date,
                        'user_id'          => Yii::$app->user->id,
                        'event_time_start' => $model->event_time_start,
                        'event_time_end'   => $model->event_time_end,
                        'group_by_level'   => $model->group_by_level,
                    ]);
                    $new->insert(false);

                    $event2quiz = EventQuizzes::findOne(['event_id' => $new->id]);
                    $event2quiz->time = $model->duration;
                    $event2quiz->update(false);

                    return [
                        'status' => 200,
                        'data' => $new
                    ];
                } else {
                    $event = Events::findOne($id);
                    $event->name             = $model->name;
                    $event->description      = $model->description;
                    $event->status           = $model->status;
                    $event->event_date       = $event_date;
                    $event->event_time_start = $model->event_time_start;
                    $event->event_time_end   = $model->event_time_end;
                    $event->group_by_level   = $model->group_by_level;
                    $event->updater_id       = Yii::$app->user->id;
                    $event->updated_at       = date('Y-m-d H:i:s');
                    $event->update(false);

                    $event2quiz = EventQuizzes::findOne(['event_id' => $id]);
                    $event2quiz->time = $model->duration;
                    $event2quiz->update(false);

                    return [
                        'status' => 200,
                    ];
                }
            } else {
                return [
                    'status' => 400,
                    'errors' => $model->getFirstErrors()
                ];
            }
        }
    }
}
