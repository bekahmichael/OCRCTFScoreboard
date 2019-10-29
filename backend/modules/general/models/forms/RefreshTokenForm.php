<?php

namespace app\modules\general\models\forms;

use yii\base\Model;
use app\models\OAuthRefreshTokens;

/**
 *  RefreshTokenForm is the model form.
 */
class RefreshTokenForm extends Model
{
    public $refresh_token;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['refresh_token'], 'required'],
           [
               'refresh_token',
               'exist',
               'skipOnError' => true,
               'targetClass' => OAuthRefreshTokens::class,
               'targetAttribute' => ['refresh_token' => 'refresh_token']
            ],
        ];
    }
}
