<?php

namespace app\modules\admin\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 *  ProjectAvatarForm is the model form.
 */
class FilesImageForm extends Model
{
    /**
     * @var UploadedFile file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                ['file'],
                'file',
                'mimeTypes' => 'image/jpeg, image/png',
                'extensions' => 'jpg, jpeg, png',
                'maxSize' => 10 * 1024  * 1024,
            ],
        ];
    }
}
