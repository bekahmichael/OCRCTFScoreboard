<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m000000_000001_create_users_table extends Migration
{
    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function up()
    {
        $this->createTable('users', [
            'id'         => $this->primaryKey(),
            'username'   => $this->string(32),
            'first_name' => $this->string(32),
            'last_name'  => $this->string(32),
            'email'      => $this->string(255),
            'password'   => $this->string(72)->append('CHARACTER SET utf8 COLLATE utf8_bin'),
            'status'     => "enum('active', 'blocked', 'deleted') NOT NULL",
            'created_at' => $this->timestamp(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createIndex('u_email', 'users', 'email', true);
        $this->createIndex('u_username', 'users', 'username', true);

        $this->insert('users', [
            'username'   => 'admin',
            'first_name' => 'Admin',
            'last_name'  => 'Admin',
            'email'      => 'admin@example.com',
            'status'     => 'active',
            'password'   => \Yii::$app->getSecurity()->generatePasswordHash('@!pa$$pa$$'),
        ]);

        $this->insert('users', [
            'username'   => 'member',
            'first_name' => 'Member',
            'last_name'  => 'Member',
            'email'      => 'member@example.com',
            'status'     => 'active',
            'password'   => \Yii::$app->getSecurity()->generatePasswordHash('@!pa$$word!'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex('u_email', 'users');
        $this->dropIndex('u_username', 'users');
        $this->dropTable('users');
    }
}
