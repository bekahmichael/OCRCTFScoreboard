<?php

namespace app\models\query;
use app\models\EventTeams;

/**
 * This is the ActiveQuery class for [[\app\models\EventTeams]].
 *
 * @see \app\models\EventTeams
 */
class EventTeamsQuery extends \yii\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\EventTeams[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\EventTeams|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActive()
    {
        return $this->andWhere(['status' => EventTeams::STATUS_ACTIVE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotDeleted()
    {
        return $this->andWhere(['not', ['status' => EventTeams::STATUS_DELETED]]);
    }
}
