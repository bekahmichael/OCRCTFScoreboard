<?php

namespace app\modules\admin\controllers\files;

use Yii;
use yii\base\Action;
use yii\web\UploadedFile;
use app\models\Files;
use app\modules\admin\models\forms\FilesImageForm;
use app\components\phpthumb\GdThumb;
use \yii\helpers\FileHelper;

/**
 * @inheritdoc
 */
class ImageDeleteAction extends Action
{
    /**
     * @inheritdoc
     * @throws \Exception
     * @throws \Throwable
     */
    public function run($id = null)
    {
        if ($id) {
            /** @var Files $file */
            $file = Files::find()->where(['id' => $id])->one();
            $filePath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $file->id . '.' .$file->extension);
            if(file_exists($filePath) && unlink($filePath)) {
                $file->delete();
                return [
                    'status' => 200,
                ];
            }
        }
        return [
            'status' => 400,
        ];
    }
}