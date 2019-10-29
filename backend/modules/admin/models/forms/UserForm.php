<?php

namespace app\modules\admin\models\forms;

use yii\base\Model;
use app\models\Users;
use app\components\password\StrengthValidator;

/**
 * LoginForm is the model behind the login form.
 */
class UserForm extends Model
{
    public $id;
    public $username;
    public $email;
    public $first_name;
    public $last_name;
    public $status;
    public $role;
    public $password;
    public $password_repeat;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'email', 'first_name', 'last_name'], 'required'],
            ['password', 'required', 'when' => function ($model) {
                if ($model->role === 'public') {
                    return false;
                } else {
                    return $model->id == 0;
                }
            }],
            ['email', 'email'],
            ['password', 'safe'],
            ['id', 'safe'],
            [['status', 'role'], 'safe'],
            [['password'], StrengthValidator::class, 'preset'=>'fair', 'when' => function ($model) {
                if ($model->role === 'public') {
                    return false;
                } else {
                    return true;
                }
            }],
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
                        return mb_strtolower($this->username) != mb_strtolower(Users::findOne(['id' => $this->id])->username);
                    }
                }
            ],
            [
                'username',
                'unique',
                'targetClass' => 'app\models\Users',
                'targetAttribute' => ['email'],
                'when' => function ($model) {
                    if ($model->id == 0) {
                        return true;
                    } else {
                        return $this->id != $model->id;
                    }
                }
            ],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match", 'when' => function ($model) {
                if ($model->role === 'public') {
                    return false;
                } else {
                    return true;
                }
            }],
        ];
    }
}
