<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "team_users".
 *
 * @property int $id
 * @property int $team_id
 * @property int $user_id
 * @property string $created_at
 * @property int $status
 *
 * @property Teams $team
 * @property Users $user
 */
class TeamUsers extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_DELETED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'team_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['team_id', 'user_id', 'status'], 'required'],
            [['team_id', 'user_id', 'status'], 'integer'],
            [['created_at'], 'safe'],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teams::className(), 'targetAttribute' => ['team_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Teams::className(), ['id' => 'team_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = gmdate('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }
}
