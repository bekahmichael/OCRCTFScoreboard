<?php

namespace app\modules\quiz;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\quiz\controllers';

    public function init()
    {
        \Yii::$app->set('user', [
            'class'           => 'yii\web\User',
            'identityClass'   => 'app\modules\quiz\models\UserIdentity',
            'enableAutoLogin' => false,
            'enableSession'   => false,
        ]);
    }
}