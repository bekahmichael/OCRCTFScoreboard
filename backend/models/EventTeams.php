<?php

namespace app\models;

use app\models\query\EventTeamsQuery;
use Yii;

/**
 * This is the model class for table "event_teams".
 *
 * @property int $id
 * @property int $team_id
 * @property int $event_id
 * @property string $access_key
 * @property int $status
 * @property string $created_at
 *
 * @property Events $event
 * @property Teams $team
 */
class EventTeams extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_DELETED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_teams';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'event_id'], 'required'],
            [['team_id', 'event_id'], 'integer'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teams::className(), 'targetAttribute' => ['team_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'team_id' => 'Team ID',
            'event_id' => 'Event ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Events::className(), ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = gmdate('Y-m-d H:i:s');
            $this->access_key = static::getUniqueAccessKey();
            $this->pin = static::generatePin();
        }

        return parent::beforeSave($insert);
    }

    /**
     * Generate a security acess key.
     *
     * @return string the random string
     * @throws \yii\base\Exception
     */
    public static function getUniqueAccessKey()
    {
        return Yii::$app->getSecurity()->generateRandomString(80);
    }

    /**
     * @inheritdoc
     * @return EventTeamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EventTeamsQuery(get_called_class());
    }

    /**
     * Generate random uniq pin number for a team access.
     *
     * @return string
     */
    public static function generatePin()
    {
        $pin = '';
        for ($i=0; $i < 100000; $i++) {
            $tryPin = $tryPin = substr(md5(uniqid()), 0, 10);;
            if (static::findOne(['pin' => $tryPin]) === null) {
                $pin = $tryPin;
                break;
            }
        }

        return $pin;
    }
}
