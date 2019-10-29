<?php

namespace app\models;

/**
 * This is the model class for table "quiz2questions".
 *
 * @property int $quiz_id
 * @property int $question_id
 * @property int $sequence
 *
 * @property Questions $question
 * @property Quizzes $quiz
 */
class QuizQuestions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quiz2questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quiz_id', 'question_id'], 'required'],
            [['quiz_id', 'question_id', 'sequence'], 'integer'],
            [['quiz_id', 'question_id'], 'unique', 'targetAttribute' => ['quiz_id', 'question_id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['question_id' => 'id']],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quizzes::class, 'targetAttribute' => ['quiz_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'quiz_id' => 'Quiz ID',
            'question_id' => 'Question ID',
            'sequence' => 'Sequence',
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
    public function getQuiz()
    {
        return $this->hasOne(Quizzes::class, ['id' => 'quiz_id']);
    }

    public function beforeSave($insert)
    {
        $sequence = $this::find()->where([
            'quiz_id' => $this->quiz_id,
        ])->max('sequence');
        if($sequence) {
            $this->sequence = ++$sequence;
        } else {
            $this->sequence = 1;
        }

        return parent::beforeSave($insert);
    }
}
