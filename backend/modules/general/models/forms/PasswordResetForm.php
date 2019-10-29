<?php

namespace app\modules\general\models\forms;

use yii\base\Model;
use app\components\password\StrengthValidator;
use app\models\Users;

/**
 *  PasswordResetForm is the model form.
 */
class PasswordResetForm extends Model
{
    public $token;
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['token', 'password', 'password_repeat'], 'trim'],
            [['token', 'password', 'password_repeat'], 'required'],
            [['password'], StrengthValidator::class, 'preset' => 'simple'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Passwords don't match" ],
            ['token', 'validateExist'],
        ];
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
            $exists = Users::find()->where(['reset_password_token' => $this->token])->exists();
            if (!$exists) {
                $this->addError($attribute, 'The token not found.');
            }
        }
    }
}
