<?php

namespace app\modules\admin\models;

use Yii;
use app\models\OAuthTokens;

/**
 * @property integer $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $status
 * @property integer $created_at
 * @property string $authKey
 *
 */
class UserIdentity extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
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
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $authToken = OAuthTokens::findOne(['token' => $token]);

        if ($authToken && $authToken->checkAccess()) {
            $authToken->last_active_time = date('Y-m-d H:i:s');
            $authToken->update();
            return static::findOne(['id' => $authToken->getAttribute('user_id')]);
        }

        return null;
    }

    /**
     * Finds user by email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $user = static::findOne(['email' => $username]);
        if (!$user) {
            $user = static::findOne(['username' => $username]);
        }
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return OAuthTokens::getNewToken($this->id);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (empty($this->getAttribute('password'))) {
            return false;
        }

        return Yii::$app->getSecurity()->validatePassword($password, $this->getAttribute('password'));
    }
}
