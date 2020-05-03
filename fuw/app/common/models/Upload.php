<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "upload".
 *
 * @property int $id
 * @property string $doi
 * @property string $name
 * @property int $size
 * @property string $status
 * @property string $location
 * @property string $description
 * @property string $initial_md5
 * @property string $datatype
 * @property string $extension
 * @property string $created_at
 * @property string $updated_at
 * @property string $sample_ids
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class Upload extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'upload';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['doi', 'name', 'size'], 'required'],
            [['size'], 'default', 'value' => null],
            [['size'], 'integer'],
            [['description', 'initial_md5','sample_ids'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['doi', 'status'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 128],
            [['location'], 'string', 'max' => 200],
            [['datatype'], 'string', 'max' => 32],
            [['sample_ids'], 'string', 'max' => 512],
            ['datatype', 'validateDataType'],
            ['extension', 'validateFileFormat'],
            [['extension'], 'string', 'max' => 32],
            [['name', 'description', 'datatype', 'initial_md5', 'extension','sample_ids'],'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'doi' => 'DOI',
            'name' => 'Name',
            'size' => 'Size',
            'status' => 'Status',
            'location' => 'Location',
            'description' => 'Description',
            'initial_md5' => 'Initial Md5',
            'datatype' => 'Data Type',
            'extension' => 'Extension',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'sample_ids' => 'Sample IDs',
        ];
    }

    /**
     * return related attribute objects
     */
    public function getUploadAttributes()
    {
        return $this->hasMany(Attribute::className(), ['upload_id' => 'id']);
    }

    /**
     * return the filedrop account this upload is associated to
     */
    public function getFiledropAccount()
    {
        return $this->hasOne(FiledropAccount::className(), ['doi' => 'doi']);
    }

    /**
     * Validate that the data type is in the GigaDB list of data types
     *
     * @param string $attribute the attribute currently being validated
     * @param mixed $params the value of the "params" given in the rule
     * @param \yii\validators\InlineValidator $validator related InlineValidator instance.
     * This parameter is available since version 2.0.11.
    */
    public function validateDataType($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, array_keys(json_decode(file_get_contents('/var/www/files/data/filetypes.json'),true)))) {
            $validator->addError($this, $attribute, 'Data type is not recognized: {value}');
        }
    }

    /**
     * Validate that the file format is in the GigaDB list of file formats
     *
     * @param string $attribute the attribute currently being validated
     * @param mixed $params the value of the "params" given in the rule
     * @param \yii\validators\InlineValidator $validator related InlineValidator instance.
     * This parameter is available since version 2.0.11.
    */
    public function validateFileFormat($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, array_keys(json_decode(file_get_contents('/var/www/files/data/fileformats.json'),true)))) {
            $validator->addError($this, $attribute, 'File format is not recognized: {value}');
        }
    }
}
