<?php

use yii\db\Migration;
use yii\helpers\Console;
use app\models\Users;

/**
 * Handles the adding changes to RBAC.
 */
class m000000_000002_add_rbac_changes extends Migration
{
    /**
     * @inheritdoc
     * @throws Exception
     */
    public function safeUp()
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAllRoles();

        // Add admin role
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // Add public role
        $public = $auth->createRole('public');
        $public->description = 'Public';
        $auth->add($public);

        $user = Users::findOne(['id' => 1]);
        $user->setRole('admin');
        echo $this->ansiFormat('Assign Role: Admin -> admin' . "\n", [Console::FG_GREEN]);

        $user = Users::findOne(['id' => 2]);
        $user->setRole('public');
        echo $this->ansiFormat('Assign Role: Public -> public' . "\n", [Console::FG_GREEN]);

        echo $this->ansiFormat('The operation was successfully done!' . "\n", [Console::FG_GREEN]);
    }

    /**
     * Helper for output.
     * @param $string
     * @param array $format
     * @return string
     */
    static function ansiFormat($string, $format = [])
    {
        $code = implode(';', $format);
        return "\033[0m" . ($code !== '' ? "\033[" . $code . 'm' : '') . $string . "\033[0m";
    }
}
