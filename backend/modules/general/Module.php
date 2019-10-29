<?php

namespace app\modules\general;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\general\controllers';

    public function init()
    {
        \Yii::$app->set('user', [
            'class' => 'yii\web\User',
            'identityClass' => 'app\modules\general\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ]);
    }
}