<?php

namespace app\modules\admin\controllers\events;

use Yii;
use yii\base\Action;
use app\models\Scoreboards;
use app\modules\admin\models\forms\ScoreboardForm;
use app\models\ScoreboardTemplates;

/**
 * @inheritdoc
 */
class ScoreboardAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        $scoreboard = Scoreboards::findOne(['event_id' => $id]);

        if (Yii::$app->request->isPost) {
            $model = new ScoreboardForm([
                'show_countdown_clock'     => Yii::$app->request->post('show_countdown_clock'),
                'show_quiz_title'          => Yii::$app->request->post('show_quiz_title'),
                'show_teams_avatars'       => Yii::$app->request->post('show_teams_avatars'),
                'background_color'         => Yii::$app->request->post('background_color'),
                'template_id'              => Yii::$app->request->post('template_id'),
                'background_image_file_id' => Yii::$app->request->post('background_image_file_id'),
            ]);

            if ($model->validate() && $scoreboard) {
                $scoreboard->setAttributes($model->getAttributes());
                $scoreboard->update();

                return [
                    'status' => 200,
                    'data'   => $scoreboard,
                    // 'errors' => $model->getFirstErrors(),
                ];
            } else {
                return [
                    'status' => 400,
                    'errors' => $model->getFirstErrors()
                ];
            }
        } else {
            $coreboardTemplates = ScoreboardTemplates::find()->orderBy(['name' => SORT_ASC])->all();

            return [
                'code'       => 200,
                'scoreboard' => $scoreboard,
                'templates'  => $coreboardTemplates,
                'event_id'   => $id,
            ];
        }
    }
}