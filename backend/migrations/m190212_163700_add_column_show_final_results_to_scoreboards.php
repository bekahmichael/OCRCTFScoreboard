<?php

use yii\db\Migration;

/**
 * Handles adding column `show_final_results` to table `scoreboards`.
 */
class m190212_163700_add_column_show_final_results_to_scoreboards extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('scoreboards', 'show_final_results', $this->smallInteger()->notNull()->defaultValue('0'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('scoreboards', 'show_final_results');
    }
}
