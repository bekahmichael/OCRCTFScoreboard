<?php

namespace app\modules\admin\controllers\teams;

use Yii;
use yii\base\Action;
use yii\web\UploadedFile;
use app\models\Files;
use app\modules\admin\models\forms\TeamAvatarForm;
use app\components\phpthumb\GdThumb;

/**
 * @inheritdoc
 */
class AvatarAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id = null)
    {
        // if (Yii::$app->request->isPost && !Yii::$app->user->can('admin')) {
        //     Yii::$app->response->statusCode = 401;
        //     return [
        //         'name'   => 'Unauthorized',
        //         'status' => 401,
        //     ];
        // }

        if (Yii::$app->request->isPost) {
            $crop = Yii::$app->request->post('crop');
            $files = UploadedFile::getInstancesByName('files');

            $errors = [];
            foreach ($files as $i => $file) {
                $model = new TeamAvatarForm(['file' => $file]);
                if (!$model->validate()) {
                    $errors[$i] = $model->getFirstErrors();
                }
                break;
            }

            if (count($errors) > 0) {
                return [
                    'status' => 400,
                    'errors' => $errors,
                ];
            } else {
                // Save
                $outFiles = [];
                foreach ($files as $i => $file) {
                    $dbFile = new Files([
                        'name'      => $file->name,
                        'size'      => $file->size,
                        'extension' => $file->getExtension(),
                    ]);
                    $dbFile->insert();

                    $filePath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $dbFile->id . '.' . $file->getExtension());

                    $file->saveAs($filePath);

                    $gd = new GdThumb($filePath, ['resizeUp' => true]);
                    $gd->crop($crop['left'], $crop['top'], $crop['width'], $crop['height']);
                    $gd->adaptiveResize(150, 150);
                    $gd->save($filePath);

                    $outFiles[] = [
                        'id'   => $dbFile->id,
                        'name' => $file->name,
                        'ext'  => $file->getExtension(),
                    ];
                }

                return [
                    'status' => 200,
                    'file'   => $outFiles[0],
                ];
            }
        } else {
            /** @var Files $file */
            $file = Files::find()->where(['id' => $id])->one();
            $filePath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $file->id . '.' .$file->extension);

            if ($file->extension === 'png') {
                header("Content-Type: image/png");
            } else if ($file->extension === 'jpg' || $file->extension === 'jpeg') {
                header("Content-Type: image/jpeg");
            } else {
                return [
                    'status' => 400,
                ];
            }

            header('Content-disposition:  inline; filename="' . $file->name . '"');
            header("Content-length: " . filesize($filePath));
            readfile($filePath);
            exit;
        }
    }
}