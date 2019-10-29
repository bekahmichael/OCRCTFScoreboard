<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/8/2019
 * Time: 20:02
 */

namespace app\modules\quiz\controllers;


use yii\filters\AccessControl;

class FilesController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['optional'] = ['image'];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['image', 'image-delete'],
                    'roles'   => ['?', 'admin'],
                ],
            ],
        ];

        return $behaviors;
    }
    public function actions()
    {
        return [
            'image' => 'app\modules\admin\controllers\files\ImageAction',
        ];
    }
}