<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Scoreboards;
use app\models\EventQuizzes;

/**
 * @inheritdoc
 */
class ScoreboardOptionsAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($event_id, $quiz_id)
    {
        $scoreboard = Scoreboards::findOne(['event_id' => $event_id]);

        $show_final_results = Yii::$app->request->post('show_final_results');

        if ($show_final_results !== null) {
            $event2quiz = EventQuizzes::findOne(['event_id' => $event_id, 'quiz_id' => $quiz_id]);

            if ($event2quiz !== null && $event2quiz->status === EventQuizzes::STATUS_FINISHED) {
                $scoreboard->show_final_results = (int) $show_final_results;
                $scoreboard->update(false);
            }
        }

        return [
            'event_id'   => $event_id,
            'scoreboard' => $scoreboard,
        ];
    }
}