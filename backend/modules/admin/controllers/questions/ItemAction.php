<?php

namespace app\modules\admin\controllers\questions;

use app\models\Questions;
use app\models\Answers;
use yii\base\Action;

/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/1/2019
 * Time: 17:13
 */

class ItemAction extends Action
{
    public function run($id)
    {
        if (\Yii::$app->request->isGet) {
            $question = Questions::findOne($id);
            return [
                'status' => 200,
                'data' => $question,
                'answers' => $question->getAnswers()->all()
            ];
        } else {
            $data = \Yii::$app->request->post();
            if ($id == 0) {
                $model = new Questions();
            } else {
                $model = Questions::findOne($id);
            }

            $model->setAttributes($data);
            if ($model->validate()) {
                $model->save(false);
                if (is_array($data['answers'])){
                    foreach ($data['answers'] as $answer) {
                        if ($id == 0){
                            $answerModel              = new Answers();
                            $answerModel->question_id = $model->id;
                        } else {
                            $answerModel              = Answers::findOne($answer['id']);    
                        }
                        $answerModel->description = $answer['description'];
                        $answerModel->is_correct  = $answer['is_correct'];
                        $answerModel->file_id     = $answer['file_id'];
                        $answerModel->sequence    = $answer['sequence'];
                        $answerModel->save();
                    }        
                }                
                return [
                    'status' => 200,
                    'data' => $model,
                    'answers' => $model->getAnswers()->all()
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