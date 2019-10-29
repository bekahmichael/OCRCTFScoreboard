<?php

namespace app\modules\admin\controllers;

use yii\filters\AccessControl;

class UserController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['admin'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
       return [
            'find'       => 'app\modules\admin\controllers\user\FindAction',
            'set-status' => 'app\modules\admin\controllers\user\SetStatusAction',
            'profile'    => 'app\modules\admin\controllers\user\ProfileAction',
       ];
    }
}