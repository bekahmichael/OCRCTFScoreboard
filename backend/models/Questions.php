<?php

namespace app\models;

use app\models\query\QuestionsQuery;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "questions".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $status
 * @property int $user_id User which created question
 * @property int $updater_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $points
 * @property int $type multiple, radio, opened
 * @property int $file_id
 * @property int $level
 * @property int $show_description
 *
 * @property Answers[] $answers
 * @property Files $file
 * @property Users $user
 * @property QuizQuestions[] $quiz2questions
 * @property Quizzes[] $quizzes
 */
class Questions extends ActiveRecord
{
    const ACTIVE = 0;
    const BLOCKED = 1;
    const DELETED = 2;
    const STATUSES = [0 => 'Active', 1 => 'Blocked', 2 => 'Deleted'];

    const TYPES = [0 => 'Multiple choice', 1 => 'Checkboxes', 2 => 'Dropdown', 3 => 'Open answer'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'questions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => Questions::ACTIVE],
            [['title', 'status', 'user_id', 'level'], 'required'],
            [['title', 'description'], 'string'],
            [['status', 'user_id', 'updater_id', 'type', 'file_id', 'show_description', 'is_library_question'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['points'], 'number'],
            [['level'], 'number', 'min' => 1, 'integerOnly'=>true],
            [['points'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
            [['library_question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['library_question_id' => 'id']],
            [['library_created_from_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['library_created_from_id' => 'id']],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), ['sequence']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'user_id' => 'User ID',
            'updater_id' => 'Updater ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'points' => 'Points',
            'level' => 'Level',
            'type' => 'Type',
            'file_id' => 'File ID',
            'show_description' => 'Show Description',
            'library_question_id' => 'Library ID',
            'is_library_question' => 'Is Library Question',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
            [
                'class'              => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'updater_id',
            ],
            [
                'class'      => AttributesBehavior::class,
                'attributes' => [
                    'user_id' => [
                        ActiveRecord::EVENT_BEFORE_VALIDATE => \Yii::$app->user->id,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answers::class, ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActiveAnswers()
    {
        return $this->hasMany(Answers::class, ['question_id' => 'id'])->where(['status' => Answers::ACTIVE])->select([
            'id',
            'question_id',
            'description',
            'file_id'
        ])->addOrderBy('answers.sequence');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuiz2questions()
    {
        return $this->hasMany(QuizQuestions::class, ['question_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuizzes()
    {
        return $this->hasMany(Quizzes::class, ['id' => 'quiz_id'])->viaTable('quiz2questions', ['question_id' => 'id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert) {
            $this->refresh();
        }
    }

    /**
     * @inheritdoc
     * @return QuestionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new QuestionsQuery(get_called_class());
    }
}
