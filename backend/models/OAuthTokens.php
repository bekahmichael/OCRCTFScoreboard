<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oauth_tokens".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property string $last_active_time
 *
 * @property Users $user
 */
class OAuthTokens extends \yii\db\ActiveRecord
{
    const EXPIRE_TIME = 86400; // 60*60*24

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oauth_tokens';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * Returns new token for the user.
     * @param  integer $user_id
     * @return string the token.
     * @throws \yii\base\Exception
     */
    public static function getNewToken($user_id)
    {
        self::removeExpiredTokens();
        $token_key = Yii::$app->getSecurity()->generateRandomString(128);

        $authToken = new self();
        $authToken->user_id = $user_id;
        $authToken->last_active_time = date('Y-m-d H:i:s');
        $authToken->token = $token_key;

        $authToken->save();

        return $token_key;
    }

    /**
     * Removes expired tokens.
     */
    public static function removeExpiredTokens()
    {
        return OAuthTokens::deleteAll(['<', 'last_active_time', date('Y-m-d H:i:s', time() - self::EXPIRE_TIME)]);
    }

    /**
     * Check token access.
     */
    public function checkAccess()
    {
        return strtotime($this->last_active_time) > time() - static::EXPIRE_TIME;
    }
}
