<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "teams".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property int $status
 * @property int $avatar_file_id
 *
 * @property Files $avatarFile
 */
class Teams extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 0;
    const STATUS_DELETED = 1;
    const STATUS_BLOCKED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'teams';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['status', 'avatar_file_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['avatar_file_id'], 'exist', 'skipOnError' => true, 'targetClass' => Files::className(), 'targetAttribute' => ['avatar_file_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'status' => 'Status',
            'avatar_file_id' => 'Avatar File ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAvatarFile()
    {
        return $this->hasOne(Files::className(), ['id' => 'avatar_file_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->created_at = gmdate('Y-m-d H:i:s');
            return parent::beforeSave($insert);
        }

        return true;
    }
}
