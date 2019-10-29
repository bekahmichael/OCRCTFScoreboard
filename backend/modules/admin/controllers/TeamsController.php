<?php

namespace app\modules\admin\controllers;

use yii\filters\AccessControl;

/**
 * @inheritdoc
 */
class TeamsController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['optional'] = ['avatar'];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow'   => true,
                    'actions' => ['index', 'profile', 'delete', 'participants', 'assign-participant', 'block', 'activate'],
                    'roles'   => ['admin'],
                ],
                [
                    'allow'   => true,
                    'actions' => ['avatar'],
                    'roles'   => ['?', 'admin'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
       return [
            'index'              => 'app\modules\admin\controllers\teams\IndexAction',
            'profile'            => 'app\modules\admin\controllers\teams\ProfileAction',
            'avatar'             => 'app\modules\admin\controllers\teams\AvatarAction',
            'delete'             => 'app\modules\admin\controllers\teams\DeleteAction',
            'participants'       => 'app\modules\admin\controllers\teams\ParticipantsAction',
            'assign-participant' => 'app\modules\admin\controllers\teams\AssignParticipantAction',
            'block'              => 'app\modules\admin\controllers\teams\BlockAction',
            'activate'           => 'app\modules\admin\controllers\teams\ActivateAction',
       ];
    }
}