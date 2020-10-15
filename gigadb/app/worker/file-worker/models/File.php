<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property int $dataset_id
 * @property string $name
 * @property string $location
 * @property string $extension
 * @property int $size
 * @property string $description
 * @property string|null $date_stamp
 * @property int|null $format_id
 * @property int|null $type_id
 * @property string|null $code
 * @property string|null $index4blast
 * @property int $download_count
 * @property string|null $alternative_location
 *
 * @property Dataset $dataset
 * @property FileFormat $format
 * @property FileAttributes[] $fileAttributes
 * @property FileExperiment[] $fileExperiments
 * @property FileRelationship[] $fileRelationships
 * @property FileSample[] $fileSamples
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dataset_id', 'name', 'location', 'extension', 'size'], 'required'],
            [['dataset_id', 'size', 'format_id', 'type_id', 'download_count'], 'default', 'value' => null],
            [['dataset_id', 'size', 'format_id', 'type_id', 'download_count'], 'integer'],
            [['description'], 'string'],
            [['date_stamp'], 'safe'],
            [['name', 'extension'], 'string', 'max' => 100],
            [['location', 'code', 'alternative_location'], 'string', 'max' => 200],
            [['index4blast'], 'string', 'max' => 50],
            [['dataset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dataset::className(), 'targetAttribute' => ['dataset_id' => 'id']],
            [['format_id'], 'exist', 'skipOnError' => true, 'targetClass' => FileFormat::className(), 'targetAttribute' => ['format_id' => 'id']],
            ['download_count', 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dataset_id' => 'Dataset ID',
            'name' => 'Name',
            'location' => 'Location',
            'extension' => 'Extension',
            'size' => 'Size',
            'description' => 'Description',
            'date_stamp' => 'Date Stamp',
            'format_id' => 'Format ID',
            'type_id' => 'Type ID',
            'code' => 'Code',
            'index4blast' => 'Index4blast',
            'download_count' => 'Download Count',
            'alternative_location' => 'Alternative Location',
        ];
    }

    /**
     * Gets query for [[Dataset]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDataset()
    {
        return $this->hasOne(Dataset::className(), ['id' => 'dataset_id']);
    }

    /**
     * Gets query for [[Format]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormat()
    {
        return $this->hasOne(FileFormat::className(), ['id' => 'format_id']);
    }

    /**
     * Gets query for [[FileAttributes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileAttributes()
    {
        return $this->hasMany(FileAttributes::className(), ['file_id' => 'id']);
    }

    /**
     * Gets query for [[FileExperiments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileExperiments()
    {
        return $this->hasMany(FileExperiment::className(), ['file_id' => 'id']);
    }

    /**
     * Gets query for [[FileRelationships]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileRelationships()
    {
        return $this->hasMany(FileRelationship::className(), ['file_id' => 'id']);
    }

    /**
     * Gets query for [[FileSamples]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileSamples()
    {
        return $this->hasMany(FileSample::className(), ['file_id' => 'id']);
    }
}
