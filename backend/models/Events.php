<?php

namespace app\models;

/**
 * This is the model class for table "events".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $event_date
 * @property string $event_time_start
 * @property string $event_time_end
 * @property int $status 0 - active, 1 - deleted
 * @property string $description
 * @property string $updated_at
 * @property int $user_id User which created event
 * @property int $updater_id
 *
 * @property EventQuizzes[] $event2quizzes
 * @property Quizzes[] $quizzes
 * @property EventTeams[] $eventTeams
 * @property Users $user
 */
class Events extends \yii\db\ActiveRecord
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
        return 'events';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'user_id'], 'required'],
            [['created_at', 'event_date', 'event_time_start', 'event_time_end', 'updated_at'], 'safe'],
            [['status', 'user_id', 'updater_id'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'created_at' => 'Created At',
            'event_date' => 'Event Date',
            'event_time_start' => 'Event Time Start',
            'event_time_end' => 'Event Time End',
            'status' => 'Status',
            'description' => 'Description',
            'updated_at' => 'Updated At',
            'user_id' => 'User ID',
            'updater_id' => 'Updater ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent2quizzes()
    {
        return $this->hasMany(EventQuizzes::class, ['event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuizzes()
    {
        return $this->hasMany(Quizzes::class, ['id' => 'quiz_id'])->viaTable('event2quizzes', ['event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventTeams()
    {
        return $this->hasMany(EventTeams::class, ['event_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
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

    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws \Throwable
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert === true) {
            $scoreboard = new Scoreboards([
                'template_id' => 1,
                'event_id'    => $this->id,
            ]);
            $scoreboard->insert();

//            create Quiz for Event by default at first
            $quiz = new Quizzes();
            $quiz->title = $this->name;
            $quiz->description = $this->description;
            $quiz->status = $this->status;

            $start_time = new \DateTime(date("Y-m-d") . ' ' . $this->event_time_start);
            $duration_time = $start_time->diff(new \DateTime(date("Y-m-d") . ' ' . $this->event_time_end));

            $quiz->time = $duration_time->i;
            if($quiz->save()) {
                $eventQuizzes = new EventQuizzes([
                    'event_id' => $this->id,
                    'quiz_id' => $quiz->id,
                ]);
                $eventQuizzes->save();
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }
}
