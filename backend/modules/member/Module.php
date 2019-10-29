<?php

namespace app\modules\member;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\member\controllers';

    public function init()
    {
        \Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\member\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ]);
    }
}