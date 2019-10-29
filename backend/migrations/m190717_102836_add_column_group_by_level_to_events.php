<?php

use yii\db\Migration;

/**
 * Handles adding column `group_by_level` to table `events`.
 */
class m190717_102836_add_column_group_by_level_to_events extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('events', 'group_by_level', $this->boolean()->notNull()->defaultValue('0'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('events', 'group_by_level');
    }
}
