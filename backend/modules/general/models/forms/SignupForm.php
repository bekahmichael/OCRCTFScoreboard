<?php

namespace app\modules\general\models\forms;

use yii\base\Model;
use app\components\password\StrengthValidator;

/**
 *  Signup is the model form.
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $status;
    public $role;
    public $password;
    public $password_repeat;
    public $sport;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'email','sport'], 'required'],
            ['email', 'email'],
            ['password', 'safe'],
            [['status', 'role'], 'safe'],
            [['password'], StrengthValidator::class, 'preset'=>'simple'],
            ['email', 'unique', 'targetClass' => 'app\models\Users'],
            ['username', 'unique', 'targetClass' => 'app\models\Users'],
            ['username', 'unique', 'targetClass' => 'app\models\Users', 'targetAttribute' => ['email']],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }
}
