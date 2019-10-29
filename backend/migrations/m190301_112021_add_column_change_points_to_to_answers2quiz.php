<?php

use yii\db\Migration;

/**
 * Handles adding column `change_points_to` to table `answers2quiz`.
 */
class m190301_112021_add_column_change_points_to_to_answers2quiz extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('answers2quiz', 'change_points_to', $this->double(10, 3)->defaultValue(null));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('answers2quiz', 'change_points_to');
    }
}
