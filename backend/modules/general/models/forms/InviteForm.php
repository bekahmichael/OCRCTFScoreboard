<?php

namespace app\modules\general\models\forms;

use yii\base\Model;
use app\components\password\StrengthValidator;

/**
 * LoginForm is the model behind the login form.
 */
class InviteForm extends Model
{
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            [['password'], StrengthValidator::class, 'preset'=>'normal'],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'password_repeat' => 'Confirm Password',
        ];
    }
}
