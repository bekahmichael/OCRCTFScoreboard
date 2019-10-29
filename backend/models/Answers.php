<?php

namespace app\models;

use app\models\query\AnswersQuery;
use yii\behaviors\AttributesBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "answers".
 *
 * @property int $id
 * @property int $question_id
 * @property string $description
 * @property int $is_correct
 * @property int $status
 * @property int $user_id User which created question
 * @property int $updater_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $file_id
 * @property int $sequence
 *
 * @property Files $file
 * @property Questions $question
 * @property Users $user
 */
class Answers extends ActiveRecord
{
    const ACTIVE = 0;
    const BLOCKED = 1;
    const DELETED = 2;
    const STATUSES = [0 => 'Active', 1 => 'Blocked', 2 => 'Deleted'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => Answers::ACTIVE],
            [['question_id', 'description', 'is_correct', 'status', 'user_id'], 'required'],
            [['question_id', 'is_correct', 'status', 'user_id', 'updater_id', 'file_id', 'sequence'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::class, 'targetAttribute' => ['file_id' => 'id']],
            [['question_id'], 'exist', 'skipOnError' => true, 'targetClass' => Questions::class, 'targetAttribute' => ['question_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question_id' => 'Question ID',
            'description' => 'Possible answer',
            'is_correct' => 'Is Correct',
            'status' => 'Status',
            'user_id' => 'User ID',
            'updater_id' => 'Updater ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'file_id' => 'File ID',
            'sequence' => 'Sequence',
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
    public function getFile()
    {
        return $this->hasOne(Files::class, ['id' => 'file_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Questions::class, ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return AnswersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AnswersQuery(get_called_class());
    }

    public function beforeSave($insert)
    {
        $sequence = $this::find()->where([
            'question_id' => $this->question_id,
        ])->max('sequence');
        if($sequence) {
            $this->sequence = ++$sequence;
        } else {
            $this->sequence = 1;
        }
        return parent::beforeSave($insert);
    }
}
