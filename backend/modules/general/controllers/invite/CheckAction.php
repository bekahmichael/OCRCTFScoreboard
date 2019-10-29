<?php

namespace app\modules\general\controllers\invite;

use yii\base\Action;
use app\models\Users;

/**
 * @inheritdoc
 */
class CheckAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($key)
    {
        $user = Users::findOne(['invite_key' => $key, 'status' => 'pending']);

        if ($user) {
            return [
                'code'   => 200,
                'email'  => $user->email,
            ];
        } else {
            return [
                'code'    => 400,
                'message' => 'Invalid Invite Links',
            ];
        }
    }
}