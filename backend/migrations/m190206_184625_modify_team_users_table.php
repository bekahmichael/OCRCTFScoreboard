<?php

use yii\db\Migration;

/**
 * Handles adding column `access_key`, `status`, `created_at` to table `event_teams`.
 */
class m190206_184625_modify_team_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_teams', 'access_key', $this->char(80)->unique());
        $this->addColumn('event_teams', 'status', $this->smallInteger()->notNull());
        $this->addColumn('event_teams', 'created_at', $this->dateTime()->notNull());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('event_teams', 'created_at');
        $this->dropColumn('event_teams', 'status');
        $this->dropColumn('event_teams', 'access_key');
    }
}
