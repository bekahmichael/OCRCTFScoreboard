<?php

namespace app\modules\general\controllers;

/**
 * @inheritdoc
 */
class InviteController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['check', 'confirm'];
        return $behaviors;
    }

    public function actions()
    {
       return [
            'check' => 'app\modules\general\controllers\invite\CheckAction',
            'confirm' => 'app\modules\general\controllers\invite\ConfirmAction',
       ];
    }
}