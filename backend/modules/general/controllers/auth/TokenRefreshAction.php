<?php

namespace app\modules\general\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\OAuthTokens;
use app\models\OAuthRefreshTokens;
use app\modules\general\models\forms\RefreshTokenForm;

/**
 * @inheritdoc
 */
class TokenRefreshAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $model = new RefreshTokenForm(['refresh_token' => Yii::$app->request->post('refresh_token')]);

        if ($model->validate()) {
            $user = OAuthRefreshTokens::findOne(['refresh_token' => $model->refresh_token]);

            return [
                'code'          => 200,
                'expires_in'    => OAuthTokens::EXPIRE_TIME,
                'access_token'  => OAuthTokens::getNewToken($user->user_id),
                'refresh_token' => OAuthRefreshTokens::getNewRefreshToken($user->user_id),
            ];
        } else {
            $errors = $model->getFirstErrors();
            return [
                'code'    => 422,
                'message' => current($errors),
                'errors'  => $errors,
            ];
        }
    }
}