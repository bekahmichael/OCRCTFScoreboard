<?php

namespace app\modules\member\models;

use Yii;
use app\models\OAuthTokens;
use app\models\AuthAssignment;

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
     * Finds account by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByUsername($email)
    {
        return static::findOne([
            'email' => $email,
            'id' => AuthAssignment::find()->select('user_id')->where(['item_name' => 'public'])->asArray()->column(),
        ]);
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
     * @throws \yii\base\Exception
     */
    public function getAuthKey()
    {
        OAuthTokens::deleteAll(['user_id' => $this->id]);
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
     * @return bool if password provided is valid for current account
     */
    public function validatePassword($password)
    {
        if (empty($this->getAttribute('password'))) {
            return false;
        }
        return Yii::$app->getSecurity()->validatePassword($password, $this->getAttribute('password'));
    }
}
