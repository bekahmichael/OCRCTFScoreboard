<?php

namespace app\modules\general\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\Users;
use app\models\OAuthTokens;
use app\modules\general\models\UserIdentity;

/**
 * @inheritdoc
 */
class ConfirmRegistrationAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($token)
    {
        if (Yii::$app->request->isPost && !empty($token)) {
            $user = Users::findOne(['email_confirm_token' => $token, 'status' => 'pending']);

            if ($user) {
                $user->status = 'active';
                $user->email_confirm_token = 'null';
                unset($user->password);
                $user->save(false);

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
            }
        }

        return [
            'code' => 400,
        ];
    }
}