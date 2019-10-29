<?php

namespace app\modules\general\controllers;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['except'] = [
            'signup',
            'confirm-registration',
            'forgot-password',
            'check-password-reset-token',
            'password-reset',
            'token-refresh',
        ];

        return $behaviors;
    }

    public function actions()
    {
       return [
            'signup'                     => 'app\modules\general\controllers\auth\SignupAction',
            'confirm-registration'       => 'app\modules\general\controllers\auth\ConfirmRegistrationAction',
            'forgot-password'            => 'app\modules\general\controllers\auth\ForgotPasswordAction',
            'check-password-reset-token' => 'app\modules\general\controllers\auth\CheckPasswordResetTokenAction',
            'password-reset'             => 'app\modules\general\controllers\auth\PasswordResetAction',
            'token-refresh'              => 'app\modules\general\controllers\auth\TokenRefreshAction',
       ];
    }
}