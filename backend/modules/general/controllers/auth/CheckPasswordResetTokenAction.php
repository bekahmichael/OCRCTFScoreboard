<?php

namespace app\modules\general\controllers\auth;

use yii\base\Action;
use app\models\Users;

/**
 * @inheritdoc
 */
class CheckPasswordResetTokenAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($token)
    {
        $user = Users::findOne(['reset_password_token' => $token, 'status' => 'active']);

        if ($user) {
            return [
                'code' => 200,
                'email'  => $user->email,
            ];
        } else {
            return [
                'code' => 400,
                'message' => 'Invalid Invite Links',
            ];
        }
    }
}