<?php

namespace app\modules\admin\controllers\quizzes;

use Yii;
use yii\base\Action;
use app\models\EventQuizzes;

/**
 * @inheritdoc
 */
class EventQuizStatusAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($quiz_id, $event_id)
    {
        $event2quiz = EventQuizzes::findOne(['quiz_id' => $quiz_id, 'event_id' => $event_id]);

        if (Yii::$app->request->isPost) {
            $status = Yii::$app->request->post('status');

            if ($event2quiz->status === 3) {
                return [
                    'code' => 400,
                    'errors' => ['The quiz was finished.'],
                ];
            } else if (
                $event2quiz->status === EventQuizzes::STATUS_NOT_STARTED &&
                !in_array($status, [EventQuizzes::STATUS_ACTIVE, EventQuizzes::STATUS_FINISHED])
            ) {
                return [
                    'code' => 400,
                    'errors' => ['Provide incorrect status.'],
                ];
            } else if (
                $event2quiz->status === EventQuizzes::STATUS_ACTIVE &&
                !in_array($status, [
                    EventQuizzes::STATUS_NOT_STARTED,
                    EventQuizzes::STATUS_PAUSED,
                    EventQuizzes::STATUS_FINISHED,
                ])
            ) {
                return [
                    'code' => 400,
                    'errors' => ['Provide incorrect status.'],
                ];
            } else if (
                $event2quiz->status === EventQuizzes::STATUS_PAUSED &&
                !in_array($status, [
                    EventQuizzes::STATUS_NOT_STARTED,
                    EventQuizzes::STATUS_ACTIVE,
                    EventQuizzes::STATUS_FINISHED,
                ])
            ) {
                return [
                    'code' => 400,
                    'errors' => ['Provide incorrect status.'],
                ];
            }

            if ($status === EventQuizzes::STATUS_ACTIVE) {
                $event2quiz->time_start = gmdate('Y-m-d H:i:s');
            } else if ($status === EventQuizzes::STATUS_PAUSED) {
                $event2quiz->passed_time += (strtotime(gmdate('Y-m-d H:i:s')) - strtotime($event2quiz->time_start)) / 60;
            } else if ($status === EventQuizzes::STATUS_NOT_STARTED) {
                $event2quiz->time_start = null;
                $event2quiz->passed_time = 0;
            } else if ($status === EventQuizzes::STATUS_FINISHED) {
                $event2quiz->time_finish = gmdate('Y-m-d H:i:s');
                $event2quiz->passed_time += (strtotime(gmdate('Y-m-d H:i:s')) - strtotime($event2quiz->time_start)) / 60;
            }

            $event2quiz->changeStatus($status);

            return [
                'event2quiz' => $event2quiz,
                'errors'     => $event2quiz->getErrors(),
            ];
        } else {
            return [
                'event2quiz' => $event2quiz,
            ];
        }
    }
}