<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

/**
 *  EventForm is the model form.
 */
class EventForm extends Model
{
    public $id;
    public $name;
    public $description;
    public $status;
    public $event_date;
    public $event_time_start;
    public $event_time_end;
    public $duration;
    public $group_by_level;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'trim'],
            [['name', 'description', 'event_date', 'event_time_start', 'event_time_end', 'duration'], 'required'],
            [['event_date', 'event_time_start', 'event_time_end', 'updated_at'], 'safe'],
            [['status', 'group_by_level'], 'integer'],
            [['duration'], 'integer', 'min' => 1],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            ['event_date', 'date', 'format'=>Yii::$app->formatter->dateFormat],
            ['event_time_start', 'date', 'format'=>'php:H:i'],
            ['event_time_end', 'date', 'format'=>'php:H:i'],
            ['event_time_end','compare','compareAttribute'=>'event_time_start','operator'=>'>'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'duration' => 'Duration Time',
        ];
    }
}
