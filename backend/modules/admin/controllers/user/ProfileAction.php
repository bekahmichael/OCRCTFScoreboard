<?php

namespace app\modules\admin\controllers\user;

use Yii;
use yii\base\Action;
use app\models\Users;
use app\modules\admin\models\forms\UserForm;

/**
 * @inheritdoc
 */
class ProfileAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isGet) {
            $user = Users::find()->where(['id' => $id])->with('roles')->asArray()->one();
            $user['role'] = isset($user['roles'][0]['item_name']) ? $user['roles'][0]['item_name']: '';

            unset($user['password']);
            return [
                'code' => 200,
                'data' => $user,
            ];
        } else if (Yii::$app->request->isPost) {
            $model = new UserForm();

            $model->setAttributes(Yii::$app->request->post('data'));
            $model->setAttributes(['id' => $id]);

            if ($model->validate()) {
                // update
                if ($id > 0) {
                    $user = Users::findOne(['id' => $id]);
                    $user->setAttributes($model->getAttributes(), false);
                    $user->update();
                    $user->setRole($model->role);
                    return [
                        'code' => 200,
                        'data' => $user->toArray(),
                    ];
                } else {
                    // new
                    $user = new Users($model->getAttributes());
                    $user->insert();
                    $user->setRole($model->role);
                    return [
                        'code' => 200,
                        'data' => $user->toArray(),
                    ];
                }
            } else {
                return [
                    'code'   => 400,
                    'errors' => $model->getFirstErrors(),
                ];
            }
        }

        return [
            'code' => 400,
        ];
    }
}