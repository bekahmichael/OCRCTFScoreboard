<?php

use yii\db\Migration;

/**
 * Handles adding column `access_key` to table `scoreboards`.
 */
class m190204_060056_add_column_access_key_to_scoreboards extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('scoreboards', 'access_key', $this->string(40)->notNull()->defaultValue(""));
        $this->createIndex('idx_scoreboards_access_key', 'scoreboards', ['access_key']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropIndex('idx_scoreboards_access_key', 'scoreboards');
        $this->dropColumn('scoreboards', 'access_key');
    }
}
