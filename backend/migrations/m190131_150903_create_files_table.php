<?php

use yii\db\Migration;

/**
 * Handles the creation of table `files`.
 */
class m190131_150903_create_files_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('files', [
            'id'        => $this->primaryKey(),
            'name'      => $this->string()->notNull(),
            'extension' => $this->string()->notNull(),
            'size'      => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('files');
    }
}
