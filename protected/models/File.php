<?php

declare(strict_types=1);

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 *
 * @property int         $id
 * @property int         $dataset_id
 * @property string      $name
 * @property string      $location
 * @property string      $extension
 * @property int         $size
 * @property string      $description
 * @property string|null $date_stamp
 * @property int|null    $format_id
 * @property int|null    $type_id
 * @property string      $code
 * @property string|null $index4blast
 * @property int         $download_count
 * @property string|null $alternative_location
 *
 * The followings are the available model relations:
 * @property Dataset            $dataset
 * @property FileFormat|null    $format
 * @property FileType|null      $type
 * @property FileSample[]       $fileSamples
 * @property FileRelationship[] $fileRelationships
 * @property FileExperiment[]   $fileExperiments
 * @property FileAttributes[]   $fileAttributes
 */
class File extends CActiveRecord
{
    public ?string $doi_search = null;
    public ?string $format_search = null;
    public ?string $type_search = null;
    public ?string $sample_name = null;

    /** @const string  DATABASE_ATTRIBUTE_ID_FOR_MD5_CHECKSUM the attribute id for MD5 checksum in attribute database table */
    const DATABASE_ATTRIBUTE_ID_FOR_MD5_CHECKSUM = "605";

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return File the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dataset_id, name, location, extension, size', 'required'),
            array('dataset_id, format_id, type_id, size', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 100),
            array('location, code', 'length', 'max' => 200),
            array('index4blast', 'length', 'max' => 45),
            array('extension', 'length', 'max' => 30),
            array('description, date_stamp, code, sample_name', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dataset_id, name, location, extension, size, description, date_stamp, format_id, type_id , doi_search,format_search, type_search, index4blast, download_count, sample_name', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
            'format' => array(self::BELONGS_TO, 'FileFormat', 'format_id'),
            'type' => array(self::BELONGS_TO, 'FileType', 'type_id'),
            'fileSamples' => array(self::HAS_MANY, 'FileSample', 'file_id'),
            'fileRelationships' => array(self::HAS_MANY, 'FileRelationship', 'file_id'),
            'fileExperiments' => array(self::HAS_MANY, 'FileExperiment', 'file_id'),
            'fileAttributes' => array(self::HAS_MANY, 'FileAttributes', 'file_id', 'order' => 'id DESC'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'dataset_id' => 'Dataset',
            'name' => Yii::t('app', 'File Name'),
            'location' => 'Location',
            'extension' => 'Extension',
            'size' => Yii::t('app', 'Size'),
            'description' => 'Description',
            'date_stamp' => Yii::t('app', 'Release Date'),
            'format_id' => Yii::t('app', 'File Format'),
            'type_id' => Yii::t('app', 'Data Type'),
            'code' => Yii::t('app', 'Sample ID') ,
            'doi_search' => 'DOI',
            'format_search' => 'File Format',
            'type_search' => 'Data Type',
            'index4blast' => 'Index4blast',
            'sample_name' => Yii::t('app', 'Sample ID'),
            'download_count' => Yii::t('app', '# of Downloads'),
            'attribute' => Yii::t('app', 'File Attributes'),
        );
    }

    public function afterSave(): bool
    {
        if (!$this->dataset->getIsPublic()) {
            return true;
        }

        $log = new DatasetLog();
        $log->dataset_id = $this->dataset_id;
        if ($this->isNewRecord) {
            $log->message = 'Additional file ' . $this->name . ' added';
        } else {
            $log->message = 'File ' . $this->name . ' updated';
        }
        $log->model_id = $this->id;
        $log->model = get_class($this);
        $log->url = Yii::app()->createUrl('/adminFile/update', array('id' => $this->id));

        return $log->save();
    }

    public function beforeDelete(): bool
    {
        if (!$this->dataset->getIsPublic()) {
            return true;
        }
        $log = new DatasetLog();
        $log->dataset_id = $this->dataset_id;
        $log->message = 'File ' . $this->name . ' removed';
        $log->model_id = $this->id;
        $log->model = get_class($this);
        $log->url = '';

        return $log->save();
    }

    /**
     * @param array $ids
     *
     * @return FileType[]
     */
    public static function getTypeList(array $ids): array
    {
        /** @var FileType $fileTypeModel */
        $fileTypeModel = FileType::model();
        $crit = new CDbCriteria();
        $crit->join = "join file on file.type_id = t.id";
        $crit->addInCondition("file.id", $ids);

        return $fileTypeModel->findAll($crit);
    }

    /**
     * @return FileFormat[]
     */
    public static function getFormatList(array $ids): array
    {
        /** @var FileFormat $fileFormatModel */
        $fileFormatModel = FileFormat::model();
        $crit = new CDbCriteria();
        $crit->join = "join file on file.format_id = t.id";
        $crit->addInCondition("file.id", $ids);

        return $fileFormatModel->findAll($crit);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();

            $criteria->with = array( 'dataset' , 'format' , 'type' );
        $criteria->compare('t.id', $this->id);
        $criteria->compare('LOWER(t.name)', strtolower($this->name ?: ''), true);
        $criteria->compare('location', $this->location, true);
        $criteria->compare('extension', $this->extension, true);
        $criteria->compare('size', $this->size, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('date_stamp', $this->date_stamp);
        $criteria->compare('LOWER(code)', strtolower($this->code ?: ''), true);
        $criteria->compare('LOWER(index4blast)', $this->index4blast ?: '', true);
        $criteria->compare('LOWER(dataset.identifier)', strtolower($this->doi_search ?: ''), true);
        $criteria->compare('LOWER(format.name)', strtolower($this->format_search ?: ''), true);
        $criteria->compare('LOWER(type.name)', strtolower($this->type_search ?: ''), true);
        $criteria->compare('download_count', $this->download_count);

        $sort = new CSort();
        $sort->attributes = array(
            'doi_search' => array(
                'asc' => 'dataset.identifier ASC',
                'desc' => 'dataset.identifier DESC',
            ),
            'name' => array(
                'asc' => 't.name asc',
                'desc' => 't.name desc',
            ),
            'code' => array(
                'asc' => 'code asc',
                'desc' => 'code desc',
            ),
            'date_stamp' => array(
                'asc' => 'date_stamp asc',
                'desc' => 'date_stamp desc',
            ),
            'format_search' => array(
                'asc' => 'format.name asc',
                'desc' => 'format.name desc',
            ),
            'type_search' => array(
                'asc' => 'type.name asc',
                'desc' => 'type.name desc',
            ),
            'download_count' => array(
                'asc' => 'download_count asc',
                'desc' => 'download_count desc',
            ),
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
        ));
    }

    /**
     * return the size of the file formatted for display using Binary notation
     *
     * @param string $unit kB, MB, GB, TB, B or null
     * @param int $precision number of decimals after the dot
     *
     * @return string formatted size
     *
     **/
    public function getSizeWithFormat(?string $unit = null, int $precision = 2): string
    {
        return \UnitHelper::specifySizeUnits((int)$this->size, $unit, $precision);
    }


    public static function getDatasetIdsByFileIds(array $fileIds): array
    {
        $fileIds = implode(' , ', $fileIds);
        if (!$fileIds) {
            return array();
        }

        return Yii::app()->db->createCommand()
                             ->selectDistinct('dataset_id')
                             ->from('file')
                             ->where("id in ($fileIds)")
                             ->queryColumn();
    }



    public function getSample(): ?Sample
    {
        /** @var Sample $sampleModel */
        $sampleModel = Sample::model();
        $criteria = new CDbCriteria();
        $criteria->join = "LEFT JOIN file_sample fs ON fs.sample_id = t.id";
        $criteria->compare('fs.file_id', $this->id);

        return $sampleModel->find($criteria);
    }

    //TODO: not used atm?
    /*public function getSampleName(): string
    {
        $sample = $this->sample;
        return ($sample) ? $sample->linkName : "";
    }*/

    public function behaviors()
    {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }

    /**
     * Before save file
     *
     * ensure the release date can be unset
     *
     * @see https://www.yiiframework.com/doc/api/1.1/CActiveRecord#beforeSave-detail
     * @return boolean
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->date_stamp = ('' === $this->attributes['date_stamp']) ? null :  $this->attributes['date_stamp'] ;
            return true;
        }
        return false;
    }

    public function setSizeValue(): bool
    {
        // Save the size from file if not set by user
        if ($this->attributes['location'] && $this->attributes['name']) {
            $size = trim($this->attributes['size']);

            if (!preg_match('/^[1-9][0-9]*$/', $size)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Updates the MD5 checksum file attribute for a given file
     *
     * @param $md5_value
     * @return void
     */
    public function updateMd5Checksum(?string $md5_value = null)
    {
        $fa = FileAttributes::model()->findByAttributes(array(
            'file_id' => $this->id,
            'attribute_id' => (int) self::DATABASE_ATTRIBUTE_ID_FOR_MD5_CHECKSUM,
        ));
        // In case no MD5 FileAttribute can be found for $file_id
        if (!$fa) {
            $fa = new FileAttributes();
            $fa->file_id = $this->id;
            $fa->attribute_id = (int) self::DATABASE_ATTRIBUTE_ID_FOR_MD5_CHECKSUM;
        }
        $fa->value = $md5_value;
        if (!$fa->save()) {
            throw new \yii\base\Exception('Failed to save model: ' . json_encode($fa->getErrors()));
        }
        echo "Saved md5 file attribute with id: " . $fa->id . PHP_EOL;
    }
}
