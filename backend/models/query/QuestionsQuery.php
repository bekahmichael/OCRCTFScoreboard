<?php

namespace app\models\query;
use app\models\Questions;

/**
 * This is the ActiveQuery class for [[\app\models\Questions]].
 *
 * @see \app\models\Questions
 */
class QuestionsQuery extends \yii\db\ActiveQuery
{

    /**
     * @inheritdoc
     * @return \app\models\Questions[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Questions|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActive()
    {
        return $this->andWhere(['status' => Questions::ACTIVE]);
    }

    public function getWithSequence()
    {
        return $this->select(['questions.*', 'quiz2questions.sequence'])->addOrderBy('quiz2questions.sequence');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotDeleted()
    {
        return $this->andWhere(['not', ['status' => Questions::DELETED]]);
    }
}
