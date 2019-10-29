<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/6/2019
 * Time: 20:09
 */

namespace app\modules\admin\controllers\events;

use app\models\Events;
use yii\base\Action;
use yii\base\DynamicModel;

class EventQuizzesAction extends Action
{
    public function run($event_id)
    {
        $model           = $this->getModel();
        $model->event_id = $event_id;

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        $event = Events::findOne($event_id);
        $quiz  = $event->getQuizzes()->one();

        return [
            'row' => $quiz,
        ];
    }


    /**
     * @inheritdoc
     */
    public function getModel()
    {
        $model = new DynamicModel([
            'event_id' => null,
        ]);
        $model->addRule(['event_id'], 'required');
        $model->addRule(['event_id'], 'integer');
        return $model;
    }
}