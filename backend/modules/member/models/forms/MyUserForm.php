<?php

namespace app\modules\member\models\forms;

use yii\base\Model;
use app\models\Users;
use app\components\password\StrengthValidator;

/**
 * LoginForm is the model behind the login form.
 */
class MyUserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name'], 'required'],
            ['password', 'required', 'when' => function ($model) {return $model->id == 0;}],
            // ['password', 'string', 'min' => 12],
            ['email', 'email'],
            ['password', 'safe'],
            ['middle_name', 'safe'],
            ['id', 'safe'],
            [['password'], StrengthValidator::class, 'preset'=>'normal'],
            [
                'email',
                'unique',
                'targetClass' => 'app\models\Users',
                'when' => function ($model) {
                    if ($model->id == 0) {
                        return true;
                    } else {
                        return $this->email != Users::findOne(['id' => $this->id])->email;
                    }
                }
            ],
            [
                'username',
                'unique',
                'targetClass' => 'app\models\Users',
                'when' => function ($model) {
                    if ($model->id == 0) {
                        return true;
                    } else {
                        return $this->username != Users::findOne(['id' => $this->id])->username;
                    }
                }
            ],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" ],
        ];
    }
}
