<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

/**
 *  ProjectAvatarForm is the model form.
 */
class TeamAvatarForm extends Model
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
                'checkExtensionByMimeType' => false,
            ],
        ];
    }
}
