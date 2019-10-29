<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "scoreboard_templates".
 *
 * @property int $id
 * @property string $name
 *
 * @property Scoreboards[] $scoreboards
 */
class ScoreboardTemplates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scoreboard_templates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScoreboards()
    {
        return $this->hasMany(Scoreboards::className(), ['template_id' => 'id']);
    }
}
