<?php

namespace app\modules\admin\controllers\questions;

use app\models\Answers;
use app\models\Questions;
use app\models\Files;
use app\models\QuizQuestions;
use yii\base\Action;
use yii\base\DynamicModel;

/**
 * Created by PhpStorm.
 * User: blr_tromax
 */
class UpdateListAction extends Action
{
    public function run($quiz_id)
    {
        $model          = $this->getModel();
        $model->quiz_id = $quiz_id;

        if (!$model->validate()) {
            return [
                'errors' => $model->getErrors()
            ];
        }

        if (\Yii::$app->request->isPost) {
            $questions = \Yii::$app->request->post();

            $errors = [];
            foreach ($questions as $key => $question) {
                $q2q = QuizQuestions::findOne(['quiz_id' => $quiz_id, 'question_id' => $question['id']]);
                if($q2q) {
                    $q2q->sequence = $question['sequence'];
                    $q2q->save();
                }
                $model                   = Questions::findOne($question['id']);
                $isFileChanged = $model->file_id != $question['file_id'];
                $model->title            = $question['title'];
                $model->description      = $question['description'];
                $model->show_description = $question['show_description'];
                $model->points           = $question['points'];
                $model->level            = $question['level'];
                $model->type             = $question['type'];
                $model->file_id          = $question['file_id'];
                if (!$model->save()) {
                    $errors[] = [
                        'question_key' => $key,
                        'question_errors' => $model->getErrors(),
                    ];
                }
                $libModel = Questions::findOne([ "library_created_from_id" => $question['id']]);
                if ($libModel){
                    $libModel->title = $model->title;
                    $libModel->description = $model->description;
                    $libModel->show_description = $model->show_description;
                    $libModel->points = $model->points;
                    $libModel->level = $model->level;
                    $libModel->type = $model->type;
                    $libModel->file_id = $model->file_id;
                    if ($model->file_id > 0 && $isFileChanged){
                        $file = Files::find()->where(['id' => $model->file_id])->one();
                        if ($file){
                            $libModel->file_id = $file->duplicate()->id;
                        }                        
                    }
                    $libModel->save();
                }
                foreach ($question['answers'] as $aKey => $answer) {
                    $answerModel              = Answers::findOne($answer['id']);
                    $isFileChanged = $answerModel->file_id != $answer['file_id'];
                    $answerModel->description = $answer['description'];
                    $answerModel->is_correct  = $answer['is_correct'];
                    $answerModel->file_id     = $answer['file_id'];
                    $answerModel->sequence    = $answer['sequence'];
                    if (!$answerModel->save()) {
                        $errors[] = [
                            'question_key'  => $key,
                            'answer_key'    => $aKey,
                            'answer_errors' => $answerModel->getErrors(),
                        ];
                    } elseif($libModel) {
                        $libAnswerModel = Answers::findOne(["library_created_from_id" => $answerModel->id]);
                        if (!$libAnswerModel){
                            $libAnswerModel = new Answers();
                            $libAnswerModel->library_created_from_id = $answerModel->id;
                            $libAnswerModel->question_id = $libModel->id;
                        }
                        $libAnswerModel->file_id = $answerModel->file_id;
                        if ($answerModel->file_id > 0 && $isFileChanged){
                            $file = Files::find()->where(['id' => $answerModel->file_id])->one();
                            if ($file){
                                $libAnswerModel->file_id = $file->duplicate()->id;
                            }                        
                        }
                        $libAnswerModel->description = $answer['description'];
                        $libAnswerModel->is_correct  = $answer['is_correct'];
                        $libAnswerModel->sequence    = $answer['sequence'];
                        $libAnswerModel->save();
                    }
                }
            }
            if (count($errors) === 0) {
                return [
                    'status' => 200,
                ];
            } else {
                return [
                    'status' => 400,
                    'errors' => $errors,
                ];
            }
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getModel()
    {
        $model = new DynamicModel([
            'quiz_id' => null,
        ]);
        $model->addRule(['quiz_id'], 'required');
        $model->addRule(['quiz_id'], 'integer');
        return $model;
    }
}