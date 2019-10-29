<?php

namespace app\modules\general\controllers\invite;

use Yii;
use yii\base\Action;
use app\models\Users;
use app\modules\general\models\forms\InviteForm;

/**
 * @inheritdoc
 */
class ConfirmAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($key)
    {
        $user = Users::findOne(['invite_key' => $key, 'status' => 'pending']);

        if ($user && Yii::$app->request->isPost) {
            $model = new InviteForm([
                'password' => Yii::$app->request->post('password'),
                'password_repeat' => Yii::$app->request->post('password_repeat'),
            ]);

            if ($model->validate()) {
                $user->password = $model->password;
                $user->status = 'active';
                $user->invite_key = '';
                $user->update(false);
                return [
                    'code' => 200,
                ];
            } else {
                return [
                    'code'   => 400,
                    'errors' => $model->getFirstErrors(),
                ];
            }
        } else {
            return [];
        }
    }
}