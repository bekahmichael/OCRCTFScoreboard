<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

/**
 *  EventTeamForm is the model form.
 */
class EventTeamForm extends Model
{
    public $pin;
    public $status;
    public $title;
    public $user_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['title', 'pin'], 'trim'],
           [['title', 'pin', 'user_id', 'status'], 'required'],
           ['title', 'string', 'max' => 255],
           ['pin', 'string', 'max' => 4, 'min' => 4],
           ['pin', 'number', 'max' => 9999, 'min' => 0],
           ['pin', 'unique', 'targetClass' => 'app\\models\\Conferences'],
           ['user_id', 'number'],
           ['status', 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Assign To',
        ];
    }
}
