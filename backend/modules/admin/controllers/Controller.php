<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

/**
 * Base controller.
 */
class Controller extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                QueryParamAuth::class,
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

        if (Yii::$app->request->isOptions) {
            exit;
        }

        //now call parent implementation
        parent::beforeAction($action);
        return true;
    }
}