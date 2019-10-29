<?php

namespace app\modules\admin;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';

    public function init()
    {
        \Yii::$app->set('user', [
            'class'           => 'yii\web\User',
            'identityClass'   => 'app\modules\admin\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession'   => false,
        ]);
    }
}