<?php

use yii\db\Migration;
use app\models\EventTeams;

/**
 * Handles adding column `pin` to table `event_teams`.
 */
class m190307_083357_add_column_pin_to_event_teams extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('event_teams', 'pin', $this->char(10)->unique());

        $event_teams = EventTeams::find()->all();
        foreach ($event_teams as $row) {
            $row->pin =  EventTeams::generatePin();
            $row->update(false);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('event_teams', 'pin');
    }
}
