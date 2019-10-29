<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;
use app\models\ScoreboardTemplates;

/**
 *  ScoreboardTemplateForm is the model form.
 */
class ScoreboardTemplateForm extends Model
{
    public $id;
    public $name;
    public $background_color;
    public $foreground_color;
    public $background_image_file_id;
    public $text_color;
    public $title_color;
    public $column_color;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['background_color', 'foreground_color', 'text_color', 'title_color', 'column_color'], 'trim'],
           [['name'], 'required'],
           [
            'name',
            'unique',
            'targetClass' => 'app\models\ScoreboardTemplates',
            'when' => function ($model) {
                if ($model->id == 0) {
                    return true;
                } else {
                    return mb_strtolower($this->name) != mb_strtolower(ScoreboardTemplates::findOne(['id' => $this->id])->name);
                }
            }
            ],
        ];
    }
}
