<?php

namespace app\modules\quiz\controllers;

/**
 * @inheritdoc
 */
class QuizController extends Controller
{
    public function actions()
    {
        return [
            'index' => 'app\modules\quiz\controllers\quiz\IndexAction',
            'access' => 'app\modules\quiz\controllers\quiz\AccessAction',
        ];
    }
}