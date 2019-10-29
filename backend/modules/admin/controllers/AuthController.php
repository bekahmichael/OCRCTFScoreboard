<?php

namespace app\modules\admin\controllers;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = ['token'];
        return $behaviors;
    }

    public function actions()
    {
       return [
            'token' => 'app\modules\admin\controllers\auth\TokenAction',
       ];
    }
}
