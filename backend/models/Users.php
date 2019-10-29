<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property integer $created_at
 * @property string $invite_key
 * @property string $email_confirm_token
 * @property string $reset_password_token
 *
 */
class Users extends \yii\db\ActiveRecord
{
    public $password_repeat;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 254],
            [['password'], 'string', 'max' => 72],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'first_name' => 'First Name',
            'last_name'  => 'Last Name',
            'email'      => 'Email',
            'password'   => 'Password',
        ];
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $oldAttributes = $this->getOldAttributes();
            if (!$this->isNewRecord && !empty($this->password) && $this->password !== $oldAttributes['password']) {
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            } elseif($this->isNewRecord) {
                $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            } else {
                unset($this->password);
            }

            if ($this->isNewRecord) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return roles.
     */
    public function getRoles()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    /**
     * Assign role to user.
     *
     * @param string $role the name of role.
     * @throws \Exception
     */
    public function setRole($role)
    {
        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole($role);
        $auth->revokeAll($this->id);
        $auth->assign($authorRole, $this->id);
    }

    /**
     * @param $id
     * @return void
     */
    public static function sendForgotPasswordEmail($id)
    {
        $user = Users::findOne($user_id);
        $user->email_confirm_token = Yii::$app->getSecurity()->generateRandomString(255);
        $user->update(false);

        $host_info = parse_url(Yii::$app->getUrlManager()->getHostInfo());

        Yii::$app->mailer->compose('general/signup-email-confirmation', [
            'link'     => Yii::$app->getUrlManager()->getHostInfo() . '/confirm-registration/' . $user->email_confirm_token,
            'username' => $user->username,
        ])
        ->setFrom('no-reply@' . $host_info['host'])
        ->setTo($user->email)
        ->setSubject('Signup mail confirmation')
        ->send();
    }

    /**
     * @param $id
     * @return void
     */
    public static function sendConfirmRegistrationEmail($id)
    {
        $user = Users::findOne($user_id);
        $user->reset_password_token = Yii::$app->getSecurity()->generateRandomString(255);
        $user->update(false);

        $host_info = parse_url(Yii::$app->getUrlManager()->getHostInfo());

        Yii::$app->mailer->compose('general/forgot-password', [
            'link'     => Yii::$app->getUrlManager()->getHostInfo() . '/password-reset/' . $user->reset_password_token,
            'username' => $user->username,
        ])
        ->setFrom('no-reply@' . $host_info['host'])
        ->setTo($user->email)
        ->setSubject('Forgot password')
        ->send();
    }
}
