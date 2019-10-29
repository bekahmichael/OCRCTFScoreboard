<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;
use app\modules\admin\models\UserIdentity;

/**
 * LoginForm is the model behind the login form.
 *
 * @property UserIdentity|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            // username is validated by validateStatus()
            ['username', 'validateStatus']
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
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
    public function validateStatus($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
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
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return UserIdentity|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $user = UserIdentity::findByUsername($this->username);

            if ($user !== null) {
                if (Yii::$app->getAuthManager()->checkAccess($user->id, 'admin')) {
                    $this->_user = $user;
                }
            }
        }

        return $this->_user;
    }
}
