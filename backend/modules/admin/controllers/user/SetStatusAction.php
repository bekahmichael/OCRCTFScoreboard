<?php

namespace app\modules\admin\controllers\user;

use Yii;
use yii\base\Action;
use app\models\Users;

/**
 * @inheritdoc
 */
class SetStatusAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $users = Yii::$app->request->post('users');
        $status = Yii::$app->request->post('status');

        foreach ($users as $user_id) {
            $user = Users::findOne(['id' => $user_id]);
            $user->status = $status;
            $user->password = null;
            $user->save();
        }

        return [
            'code' => 200,
        ];
    }
}
