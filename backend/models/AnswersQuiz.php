<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "answers2quiz".
 *
 * @property int $id
 * @property int $event_id
 * @property int $quiz_id
 * @property int $team_id
 * @property int $question_id
 * @property string $answer_ids
 * @property string $answer_text
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Questions $question
 * @property Events $event
 * @property Quizzes $quiz
 * @property Teams $team
 */
class AnswersQuiz extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answers2quiz';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_id', 'quiz_id', 'team_id', 'question_id'], 'required'],
            [['event_id', 'quiz_id', 'team_id', 'question_id'], 'integer'],
            [['answer_text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['answer_ids'], 'string', 'max' => 255],
            [['event_id', 'quiz_id', 'team_id', 'question_id'], 'unique', 'targetAttribute' => ['event_id', 'quiz_id', 'team_id', 'question_id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['question_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Events::class, 'targetAttribute' => ['event_id' => 'id']],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quizzes::class, 'targetAttribute' => ['quiz_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => Teams::class, 'targetAttribute' => ['team_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'quiz_id' => 'Quiz ID',
            'team_id' => 'Team ID',
            'question_id' => 'Question ID',
            'answer_ids' => 'Answer Ids',
            'answer_text' => 'Answer Text',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Questions::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Events::class, ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz()
    {
        return $this->hasOne(Quizzes::class, ['id' => 'quiz_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(Teams::class, ['id' => 'team_id']);
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->answer_ids = explode(',', $this->answer_ids);
    }
}
