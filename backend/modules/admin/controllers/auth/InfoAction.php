<?php

namespace app\modules\admin\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\Users;

/**
 * @inheritdoc
 */
class InfoAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $user = Users::findOne(Yii::$app->user->id)->toArray();
        unset($user['password']);
        return $user;
    }
}
