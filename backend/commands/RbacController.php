<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\models\Users;

/**
 * RBAC Controller.
 *
 * @since 2.0
 */
class RbacController extends Controller
{
    /**
     * Init RBAC.
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAllRoles();

        // Add admin role
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // Add public role
        $public = $auth->createRole('public');
        $public->description = 'Public';
        $auth->add($public);
        $this->actionAssignRoles();
        echo $this->ansiFormat('The operation was successfully done!' . "\n", Console::FG_GREEN);
    }

    public function actionAssignRoles()
    {
        $user = Users::findOne(['id' => 1]);
        $user->setRole('admin');
        echo $this->ansiFormat('Assign Role: Admin -> admin' . "\n", Console::FG_GREEN);
    }
}
