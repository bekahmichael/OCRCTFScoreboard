<?php

namespace app\modules\general\controllers\auth;

use Yii;
use yii\base\Action;
use app\modules\general\models\forms\ForgotPasswordForm;
use app\models\Users;

/**
 * {@inheritdoc}
 */
class ForgotPasswordAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function run($email)
    {
        if (Yii::$app->request->isPost) {
            $model = new ForgotPasswordForm([
                'email' => $email,
            ]);

            if ($model->validate()) {
                    $user = Users::findOne(['email' => $email]);
                    Users::sendForgotPasswordEmail($user->id);
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
            return [
                'code' => 400,
            ];
        }
    }
}
