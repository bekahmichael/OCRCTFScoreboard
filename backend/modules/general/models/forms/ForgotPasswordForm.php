<?php

namespace app\modules\general\models\forms;

use yii\base\Model;
use app\models\Users;

/**
 *  ForgotPasswordForm is the model form.
 */
class ForgotPasswordForm extends Model
{
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['email'], 'trim'],
           [['email'], 'required'],
           ['email', 'validateExist'],
           ['email', 'validateStatus'],
        ];
    }

    /**
     * Validates the status.
     * This method serves as the inline validation for status.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateStatus($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = Users::findOne(['email' => $this->email]);
            if (!$user || $user->status !== 'active') {
                if ($user->status === 'deleted') {
                    $this->addError($attribute, 'Your account was deleted.');
                } else {
                    $this->addError($attribute, 'Your account was blocked.');
                }
            }
        }
    }

    /**
     * Validates the status.
     * This method serves as the inline validation for status.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateExist($attribute, $params)
    {
      if (!$this->hasErrors()) {
          $exists = Users::find()->where(['email' => $this->email])->exists();
          if (!$exists) {
              $this->addError($attribute, 'The email not found.');
          }
      }
    }
}
