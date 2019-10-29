<?php

namespace app\modules\member\controllers\auth;

use Yii;
use yii\base\Action;
use app\models\Users;
use app\modules\member\models\forms\MyUserForm;

/**
 * @inheritdoc
 */
class ProfileAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $model = new MyUserForm([
                'id'              => Yii::$app->user->id,
                'first_name'      => $data['first_name'],
                'last_name'       => $data['last_name'],
                'username'        => $data['username'],
                'email'           => $data['email'],
                'password'        => isset($data['password']) ? $data['password'] : '',
                'password_repeat' => isset($data['password_repeat']) ? $data['password_repeat'] : '',
            ]);

            if ($model->validate()) {
                $user = Users::findOne(['id' => Yii::$app->user->id]);
                $user->setAttributes($model->getAttributes(), false);
                $user->update();
                return [
                    'code' => 200,
                    'data' => $user->toArray(),
                ];
            } else {
                return [
                    'code'   => 400,
                    'errors' => $model->getFirstErrors(),
                ];
            }
        } else {
            $user = Users::find()->where(['id' => Yii::$app->user->id])->with('roles')->asArray()->one();
            unset($user['password']);
            return [
                'code' => 200,
                'data' => $user,
            ];
        }
    }
}