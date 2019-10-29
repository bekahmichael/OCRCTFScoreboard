<?php

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;
use app\models\Teams;

/**
 *  TeamForm is the model form.
 */
class TeamForm extends Model
{
    public $name;
    public $id;
    public $avatar_file_id;
    public $status;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
           [['name', 'avatar_file_id'], 'trim'],
           [['name'], 'required'],
           [['id'], 'safe'],
           [['avatar_file_id', 'status'], 'number'],
           ['name', 'string', 'max' => 255],
           [
                'name',
                'unique',
                'targetClass' => Teams::class,
                'when' => function ($model) {
                    if ($model->id == 0) {
                        return true;
                    } else {
                        return mb_strtolower($this->name) != mb_strtolower(Teams::findOne(['id' => $this->id])->name);
                    }
                }
            ],
        ];
    }
}
