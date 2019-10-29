<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "oauth_refresh_tokens".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $created_at
 * @property string $refresh_token
 */
class OAuthRefreshTokens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'oauth_refresh_tokens';
    }

    /**
     * Returns new refresh token for the user.
     * @param  integer $user_id
     * @return string the token.
     * @throws \yii\base\Exception
     */
    public static function getNewRefreshToken($user_id)
    {
        static::deleteAll(['user_id' => $user_id]);
        $refresh_token = Yii::$app->getSecurity()->generateRandomString(128);

        $OAuthRefreshToken = new static([
            'user_id'       => $user_id,
            'created_at'    => date('Y-m-d H:i:s'),
            'refresh_token' => $refresh_token,
        ]);
        $OAuthRefreshToken->save(false);

        return $refresh_token;
    }
}
