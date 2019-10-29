<?php

namespace app\modules\member\controllers;

use yii\filters\AccessControl;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['except'] = ['token'];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['token'],
                ],
                [
                    'allow' => true,
                    'roles' => ['public'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
       return [
            'token'   => 'app\modules\member\controllers\auth\TokenAction',
            'profile' => 'app\modules\member\controllers\auth\ProfileAction',
       ];
    }
}
