<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/5/2019
 * Time: 16:15
 */

namespace app\modules\admin\controllers\questions;

use app\models\Answers;
use app\models\Files;
use app\models\Questions;
use yii\base\Action;

class AnswerAction extends Action
{
    public function run($id)
    {
        if (\Yii::$app->request->isGet) {
            $answer = Answers::findOne($id);
            return [
                'status' => 200,
                'data'   => $answer,
            ];
        } else {
            if ($id == 0) {
                $model = new Answers();
            } else {
                $model = Answers::findOne($id);
            }

            $data = \Yii::$app->request->post();
            $isFileChanged = $model->file_id != $data['file_id'];
            $model->setAttributes($data);
            if ($model->validate()) {
                $model->save(false);
                $libQuestion = Questions::findOne(["library_created_from_id" => $model->question_id]);
                if ($id == 0 && $libQuestion){
                    $libModel = new Answers();
                    $libModel->attributes = $model->attributes;
                    $libModel->id = null;
                    $libModel->question_id = $libQuestion->id;
                    $libModel->library_created_from_id = $model->id;
                    if ($model->file_id > 0 && $isFileChanged){
                        $file = Files::find()->where(['id' => $model->file_id])->one();
                        if ($file){
                            $libModel->file_id = $file->duplicate()->id;
                        }                        
                    }
                    $libModel->save();
                }
                // _d($question,1);
                return [
                    'status' => 200,
                    'data'   => $model,
                ];
            } else {
                return [
                    'status' => 400,
                    'errors' => $model->getErrors()
                ];
            }
        }
    }
}