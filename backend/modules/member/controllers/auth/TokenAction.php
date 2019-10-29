<?php

namespace app\modules\member\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\OAuthTokens;
use app\models\OAuthRefreshTokens;
use app\modules\member\models\forms\LoginForm;

/**
 * Authentication action.
 *
 * @return array
 */
class TokenAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = new LoginForm();
        $model->setAttributes(Yii::$app->request->post());

        if ($model->validate() && $model->login()) {
            return [
                'code'          => 200,
                'access_token'  => Yii::$app->user->identity->getAuthKey(),
                'refresh_token' => OAuthRefreshTokens::getNewRefreshToken(Yii::$app->user->id),
                'expires_in'    => OAuthTokens::EXPIRE_TIME,
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
    }
}
