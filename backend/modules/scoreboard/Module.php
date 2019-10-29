<?php

namespace app\modules\scoreboard;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\scoreboard\controllers';

    public function init()
    {
        \Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\scoreboard\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ]);
    }
}