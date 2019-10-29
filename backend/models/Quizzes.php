<?php

namespace app\models;

use yii\behaviors\AttributesBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "quizzes".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $time Time in minutes
 * @property int $user_id User which created quiz
 * @property int $updater_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property EventQuizzes[] $event2quizzes
 * @property Events[] $events
 * @property QuizQuestions[] $quiz2questions
 * @property Questions[] $questions
 * @property Users $user
 */
class Quizzes extends ActiveRecord
{
    /**
     * List of statuses.
     */
    const STATUS_ACTIVE   = 0;
    const STATUS_DELETED  = 1;
    const STATUS_STARTED  = 2;
    const STATUS_PAUSED   = 3;
    const STATUS_FINISHED = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quizzes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'status', 'time', 'user_id'], 'required'],
            [['description'], 'string'],
            [['status', 'time', 'user_id', 'updater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'time' => 'Time',
            'user_id' => 'User ID',
            'updater_id' => 'Updater ID',
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
            [
                'class'              => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'updater_id',
            ],
            [
                'class'      => AttributesBehavior::class,
                'attributes' => [
                    'user_id' => [
                        ActiveRecord::EVENT_BEFORE_VALIDATE => \Yii::$app->user->id,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent2quizzes()
    {
        return $this->hasMany(EventQuizzes::class, ['quiz_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Events::class, ['id' => 'event_id'])->viaTable('event2quizzes', ['quiz_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz2questions()
    {
        return $this->hasMany(QuizQuestions::class, ['quiz_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Questions::class, ['id' => 'question_id'])->viaTable('quiz2questions', ['quiz_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }
}
