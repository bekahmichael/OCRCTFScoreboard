<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/1/2019
 * Time: 15:38
 */

namespace app\modules\admin\controllers;


use yii\filters\AccessControl;

class QuestionsController extends Controller
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
                    'actions' => ['index', 'item', 'update-list', 'delete', 'answers', 'answer', 'delete-answer'],
                    'roles'   => ['admin'],
                ],
                [
                    'allow'   => true,
                    'actions' => ['image'],
                    'roles'   => ['?', 'admin'],
                ],
            ],
        ];

        return $behaviors;
    }
    public function actions()
    {
        return [
            'index'         => 'app\modules\admin\controllers\questions\IndexAction',
            'item'          => 'app\modules\admin\controllers\questions\ItemAction',
            'update-list'   => 'app\modules\admin\controllers\questions\UpdateListAction',
            'delete'        => 'app\modules\admin\controllers\questions\DeleteAction',
            'answers'       => 'app\modules\admin\controllers\questions\AnswersAction',
            'answer'        => 'app\modules\admin\controllers\questions\AnswerAction',
            'delete-answer' => 'app\modules\admin\controllers\questions\DeleteAnswerAction',
            'image'         => 'app\modules\admin\controllers\questions\ImageAction',
        ];
    }
}