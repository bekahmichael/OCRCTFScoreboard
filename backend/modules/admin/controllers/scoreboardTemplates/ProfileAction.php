<?php

namespace app\modules\admin\controllers\scoreboardTemplates;

use Yii;
use yii\base\Action;
use app\models\ScoreboardTemplates;
use app\modules\admin\models\forms\ScoreboardTemplateForm;
/**
 * @inheritdoc
 */
class ProfileAction extends Action
{
    /**
     * @inheritdoc
     */
    public function run($id)
    {
        if (Yii::$app->request->isGet) {
            return [
                'status' => 200,
                'data' => ScoreboardTemplates::findOne($id)
            ];
        } else {
            $model = new ScoreboardTemplateForm([
                'id'                       => $id,
                'name'                     => Yii::$app->request->post('name'),
                'background_color'         => Yii::$app->request->post('background_color'),
                'foreground_color'         => Yii::$app->request->post('foreground_color'),
                'background_image_file_id' => Yii::$app->request->post('background_image_file_id'),
                'text_color'               => Yii::$app->request->post('text_color'),
                'title_color'              => Yii::$app->request->post('title_color'),
                'column_color'             => Yii::$app->request->post('column_color'),
            ]);

            if ($model->validate()) {
                if ($id == 0) {
                    $new = new ScoreboardTemplates([
                        'name'                      => $model->name,
                        'background_color'          => $model->background_color,
                        'foreground_color'          => $model->foreground_color,
                        'background_image_file_id'  => $model->background_image_file_id,
                        'text_color'                => $model->text_color,
                        'title_color'               => $model->title_color,
                        'column_color'              => $model->column_color,
                    ]);
                    $new->insert(false);
                    return [
                        'status' => 200,
                        'data'   => $new
                    ];
                } else {
                    $company = ScoreboardTemplates::findOne($id);
                    $company->name                      = $model->name;
                    $company->background_color          = $model->background_color;
                    $company->foreground_color          = $model->foreground_color;
                    $company->background_image_file_id  = $model->background_image_file_id;
                    $company->text_color                = $model->text_color;
                    $company->title_color               = $model->title_color;
                    $company->column_color              = $model->column_color;
                    $company->update(false);
                    return [
                        'status' => 200
                    ];
                }
            } else {
                return [
                    'status' => 400,
                    'errors' => $model->getFirstErrors()
                ];
            }
        }
    }
}
