<?php

namespace app\modules\quiz\controllers;

use Yii;

/**
 * Base controller.
 */
class Controller extends \yii\rest\Controller
{

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