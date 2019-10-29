<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

/**
 *  ScoreboardForm is the model form.
 */
class ScoreboardForm extends Model
{
    public $show_countdown_clock;
    public $show_quiz_title;
    public $show_teams_avatars;
    public $background_color;
    public $background_image_file_id;
    public $template_id;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['background_color'], 'trim'],
           [['show_countdown_clock', 'show_quiz_title', 'show_teams_avatars', 'template_id'], 'required'],
           [['show_countdown_clock', 'show_quiz_title', 'show_teams_avatars', 'background_image_file_id', 'template_id'], 'number'],
        ];
    }
}
