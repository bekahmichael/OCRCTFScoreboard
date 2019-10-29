<?php

use yii\db\Migration;

/**
 * Handles the modification of table ``.
 */
class m190228_125840_modify_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('users', 'created_at', $this->timestamp()->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
    }
}
