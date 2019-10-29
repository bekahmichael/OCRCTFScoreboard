<?php

namespace app\models;

/**
 * This is the model class for table "event2quizzes".
 *
 * @property int $event_id
 * @property int $quiz_id
 * @property int $status
 * @property string $time_start
 * @property string $time_finish
 * @property int $time
 * @property int $passed_time
 *
 * @property Quizzes $quiz
 * @property Events $event
 */
class EventQuizzes extends \yii\db\ActiveRecord
{
    /**
     * List of statuses.
     */
    const STATUS_NOT_STARTED = 0;
    const STATUS_ACTIVE      = 1;
    const STATUS_PAUSED      = 2;
    const STATUS_FINISHED    = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event2quizzes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_id', 'quiz_id'], 'required'],
            [['event_id', 'quiz_id'], 'integer'],
            [['event_id', 'quiz_id'], 'unique', 'targetAttribute' => ['event_id', 'quiz_id']],
            [['quiz_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quizzes::class, 'targetAttribute' => ['quiz_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Events::class, 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'event_id' => 'Event ID',
            'quiz_id' => 'Quiz ID',
        ];
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
    public function getEvent()
    {
        return $this->hasOne(Events::class, ['id' => 'event_id']);
    }

    /**
     * @param $newStatus
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function changeStatus($newStatus)
    {
        $this->status = $newStatus;
        if ($this->update(false)) {
            if ($this->status === self::STATUS_NOT_STARTED) {
                AnswersQuiz::deleteAll(['event_id' => $this->quiz_id, 'quiz_id' => $this->quiz_id]);
            }

            $data = [
                'quiz_id' => $this->quiz_id,
                'event_id' => $this->event_id,
                'status' => $this->status,
            ];
            pushDataToWebsocketWorker($data);
        }
    }
}
