<?php

namespace app\modules\quiz\controllers\files;

use Yii;
use yii\base\Action;
use app\models\Files;
use app\components\phpthumb\GdThumb;

/**
 * @inheritdoc
 */
class ImageAction extends Action
{
    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function run($id = null)
    {
        /** @var Files $file */
        $file     = Files::find()->where(['id' => $id])->one();
        $filePath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $file->id . '.' . $file->extension);

        $r = Yii::$app->request->get('r', null);

        if ($r !== null) {
            $type = Yii::$app->request->get('type', 'normal');
            list($resize_width, $resize_height) = explode('x', $r);
            if ($resize_width * $resize_height > 0) {
                $pathinfo = pathinfo($filePath);

                $resizedImageFile = strtr('{dirname}/{filename}.{size}.{type}.{extension}', [
                    '{dirname}'   => $pathinfo['dirname'],
                    '{filename}'  => $pathinfo['filename'],
                    '{size}'      => $resize_width . 'x' . $resize_height,
                    '{type}'      => $type,
                    '{extension}' => $pathinfo['extension'],
                ]);

                if (!file_exists($resizedImageFile)) {
                    $gd = new GdThumb($filePath, ['resizeUp' => true]);
                    if ($type === 'adaptive') {
                        $gd->adaptiveResize($resize_width, $resize_height);
                    } else {
                        $gd->resize($resize_width, $resize_height);
                    }

                    $gd->save($resizedImageFile);
                }

                $filePath = $resizedImageFile;
            }
        }

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