<?php

namespace app\modules\admin\controllers;

/**
 * @inheritdoc
 */
class ScoreboardTemplatesController extends Controller
{
    public function actions()
    {
       return [
            'index' => 'app\modules\admin\controllers\scoreboardTemplates\IndexAction',
            'profile' => 'app\modules\admin\controllers\scoreboardTemplates\ProfileAction',
            'delete' => 'app\modules\admin\controllers\scoreboardTemplates\DeleteAction',
       ];
    }
}