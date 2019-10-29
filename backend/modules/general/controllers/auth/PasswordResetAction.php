<?php

namespace app\modules\general\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\Users;
use app\models\OAuthTokens;
use app\modules\general\models\UserIdentity;
use app\modules\general\models\forms\PasswordResetForm;

/**
 * {@inheritdoc}
 */
class PasswordResetAction extends Action
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $model = new PasswordResetForm([
                'token'           => Yii::$app->request->post('token'),
                'password'        => Yii::$app->request->post('password'),
                'password_repeat' => Yii::$app->request->post('password_repeat'),
            ]);
            if ($model->validate()) {
                $user = Users::findOne(['reset_password_token' => $model->token]);
                $user->password = $model->password;
                $user->reset_password_token = '';
                $user->update(false);

                Yii::$app->user->login(UserIdentity::findOne($user->id), 0);
                return [
                    'code'         => 200,
                    'access_token' => Yii::$app->user->identity->getAuthKey(),
                    'expires_in'   => OAuthTokens::EXPIRE_TIME,
                    'user' => [
                        'id'         => Yii::$app->user->id,
                        'username'   => Yii::$app->user->identity->username,
                        'first_name' => Yii::$app->user->identity->first_name,
                        'last_name'  => Yii::$app->user->identity->last_name,
                        'email'      => Yii::$app->user->identity->email,
                        'roles'      => Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
                    ],
                ];
            } else {
                return [
                    'code'   => 400,
                    'errors' => $model->getFirstErrors(),
                ];
            }
        } else {
            return [
                'code'   => 400,
            ];
        }
    }
}
