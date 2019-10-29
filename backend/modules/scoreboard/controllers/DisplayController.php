<?php

namespace app\modules\scoreboard\controllers;

class DisplayController extends Controller
{
    public function actions()
    {
       return [
            'index' => 'app\modules\scoreboard\controllers\display\IndexAction',
       ];
    }
}