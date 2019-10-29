<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use app\models\Teams;
use app\modules\admin\models\forms\TeamForm;

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
            return [
                'status' => 200,
                'data' => Teams::findOne($id),
            ];
        } else {
            $model = new TeamForm([
                'id' => $id,
                'name'   => Yii::$app->request->post('name'),
                'status' => Yii::$app->request->post('status'),
                'avatar_file_id' => Yii::$app->request->post('avatar_file_id'),
            ]);
            if ($model->validate()) {
                if ($id == 0) {
                    $new = new Teams([
                        'name'           => $model->name,
                        'avatar_file_id' => $model->avatar_file_id,
                        'status'         => $model->status,
                    ]);
                    $new->insert(false);
                    return [
                        'status' => 200,
                        'data' => $new
                    ];
                } else {
                    $team = Teams::findOne($id);
                    $team->name = $model->name;
                    $team->avatar_file_id = $model->avatar_file_id;
                    $team->status = $model->status;
                    $team->save(false);

                    return [
                        'status' => 200,
                        'team'   => $team,
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
