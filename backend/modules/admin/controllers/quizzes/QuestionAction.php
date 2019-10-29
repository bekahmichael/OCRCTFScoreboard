<?php
/**
 * Created by PhpStorm.
 * User: blr_tromax
 * Date: 2/5/2019
 * Time: 16:15
 */

namespace app\modules\admin\controllers\quizzes;

use app\models\Questions;
use app\models\Answers;
use app\models\QuizQuestions;
use app\models\Files;
use yii\base\Action;

class QuestionAction extends Action
{

    public function run($id)
    {
        if (\Yii::$app->request->isGet) {
            $question = Questions::findOne($id);
            return [
                'status' => 200,
                'data'   => $question,
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
                if ($model->file_id > 0 && $model->library_question_id > 0 && $model->id == 0){
                    $file = Files::find()->where(['id' => $model->file_id])->one();
                    if ($file){
                        $model->file_id = $file->duplicate()->id;
                    }
                }
                $model->save(false);
                if (isset($data['add_to_library']) && $data['add_to_library'] == 1 && $id == 0){
                    $libModel = new Questions();
                    $libModel->attributes = $model->attributes;
                    $libModel->id = null;
                    $libModel->is_library_question = 1;
                    $libModel->library_created_from_id = $model->id;
                    if ($libModel->file_id > 0){
                        $file = Files::find()->where(['id' => $libModel->file_id])->one();
                        if ($file){
                            $libModel->file_id = $file->duplicate()->id;
                        }
                    }
                    $libModel->save();
                }
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
                        if ($model->file_id > 0 && $model->library_question_id > 0 && $id == 0){
                            // clone from library
                            $file = Files::find()->where(['id' => $answer['file_id']])->one();
                            if ($file){
                                $answerModel->file_id = $file->duplicate()->id;
                            }                        
                        }
                        $answerModel->sequence    = $answer['sequence'];
                        $answerModel->save();
                        if ($libModel){
                        }
                    }        
                }
                (new QuizQuestions(['quiz_id' => \Yii::$app->request->post('quiz_id'), 'question_id' => $model->id]))->save();
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