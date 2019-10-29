<?php

namespace app\modules\quiz\models\forms;

use Yii;
use yii\base\Model;
use app\models\EventTeams;

/**
 *  QuizAccessForm is the model form.
 */
class QuizAccessForm extends Model
{
    public $pin;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['pin'], 'trim'],
            [['pin'], 'required'],
            [['pin'], 'validatePin'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pin' => 'PIN',
        ];
    }


    /**
     * Validates the PIN.
     * This method serves as the inline validation for PIN.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePin($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $event_team = EventTeams::findOne(['pin' => $this->pin]);

            if (!$event_team) {
                $this->addError($attribute, 'Incorrect PIN code.');
            }
        }
    }
}
