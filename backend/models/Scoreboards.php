<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "scoreboards".
 *
 * @property int $id
 * @property int $template_id
 * @property int $event_id
 * @property string $background_color
 * @property int $background_image_file_id
 *
 * @property Events $event
 * @property ScoreboardTemplates $template
 */
class Scoreboards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'scoreboards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['template_id', 'event_id'], 'required'],
            [['template_id', 'event_id', 'show_countdown_clock', 'show_quiz_title', 'show_teams_avatars', 'background_image_file_id'], 'integer'],
            [['background_color'], 'string', 'max' => 100],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Events::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ScoreboardTemplates::className(), 'targetAttribute' => ['template_id' => 'id']],
            [['background_color'], 'trim'],
            [['background_image_file_id'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
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
    public function getTemplate()
    {
        return $this->hasOne(ScoreboardTemplates::className(), ['id' => 'template_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->access_key = sha1(uniqid());
            $this->show_countdown_clock = 1;
            $this->show_quiz_title = 1;
            $this->show_teams_avatars = 1;
            return parent::beforeSave($insert);
        }

        return true;
    }
}
