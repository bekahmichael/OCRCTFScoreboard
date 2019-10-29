<?php

namespace app\modules\general\controllers\auth;

use Yii;
use yii\base\Action;
use app\modules\general\models\forms\SignupForm;
use app\models\Users;

/**
 * {@inheritdoc}
 */
class SignupAction extends Action
{
    /**
     * {@inheritdoc}
     * @throws \Throwable
     */
    public function run()
    {
        $model = new SignupForm([
            'username'        => Yii::$app->request->post('username'),
            'email'           => Yii::$app->request->post('email'),
            'password'        => Yii::$app->request->post('password'),
            'password_repeat' => Yii::$app->request->post('password_repeat'),
        ]);

        if ($model->validate()) {
                $new = new Users([
                    'username'            => $model->username,
                    'email'               => $model->email,
                    'password'            => $model->password,
                    'status'              => 'pending',
                    'email_confirm_token' => Yii::$app->getSecurity()->generateRandomString(255),
                ]);

                $new->insert(false);
                $new->setRole('public');

                Users::sendConfirmRegistrationEmail($new->id);

                return [
                    'code' => 200,
                ];
        } else {
            return [
                'code'   => 400,
                'errors' => $model->getFirstErrors(),
            ];
        }
    }
}
