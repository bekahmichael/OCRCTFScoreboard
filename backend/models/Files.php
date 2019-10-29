<?php

namespace app\models;
use Yii;
/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property string $name
 * @property string $extension
 * @property int $size
 */
class Files extends \yii\db\ActiveRecord
{
    /**
     * Directory where files are uploaded to
     */
    const UPLOAD_DIR = '@app/storage/files';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'extension', 'size'], 'required'],
            [['size'], 'integer'],
            [['name', 'extension'], 'string', 'max' => 255],
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
            'extension' => 'Extension',
            'size' => 'Size',
        ];
    }


    public function duplicate()
    {
        $clone = new self();
        $clone->attributes = $this->attributes;
        $clone->id = null;
        if ($clone->save()){
            $inPath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $this->id . '.' .$this->extension);
            $outPath = Yii::getAlias(Files::UPLOAD_DIR . '/' . $clone->id . '.' .$clone->extension);
            copy($inPath, $outPath);
            return $clone;
        }
        return null;
    }
}
