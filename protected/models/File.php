<?php

/**
 * This is the model class for table "file".
 *
 * The followings are the available columns in table 'file':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $name
 * @property string $location
 * @property string $extension
 * @property string $size
 * @property string $description
 * @property string $date_stamp
 * @property integer $format_id
 * @property integer $type_id
 * @property string $code
 * @property string $index4blast
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property FileFormat $format
 * @property FileType $type
 * @property FileSample[] $fileSamples
 * @property FileRelationship[] $fileRelationships
 * @property FileExperiment[] $fileExperiments
 * @property FileAttributes[] $fileAttributes
 */
class File extends CActiveRecord
{
    public $doi_search;
    public $format_search;
    public $type_search;
    public $sample_name;

    // for adding new attribute
    public $attr_id;
    public $value;
    public $unit_id;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return File the static model class
     */
    public static function model($className=__CLASS__)
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
            array('dataset_id, name, extension, size, description', 'required'),
            array('dataset_id, format_id, type_id', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>100),
            array('location, code', 'length', 'max'=>200),
            array('index4blast', 'length', 'max'=>45),
            array('extension', 'length', 'max'=>30),
            array('description, date_stamp, code, sample_name', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dataset_id, name, location, extension, size, description, date_stamp, format_id, type_id , doi_search,format_search, type_search, index4blast, download_count, sample_name', 'safe', 'on'=>'search'),
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
            'fileAttributes' => array(self::HAS_MANY, 'FileAttributes', 'file_id', 'order'=>'id ASC'),
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
            'name' => Yii::t('app' , 'File Name'),
            'location' => 'Location',
            'extension' => 'Extension',
            'size' => Yii::t('app' , 'Size'),
            'description' => 'Description',
            'date_stamp' => Yii::t('app' , 'Release Date'),
            'format_id' => Yii::t('app' , 'File Format'),
            'type_id' => Yii::t('app' , 'Data Type'),
            'code' => Yii::t('app' , 'Sample ID') ,
            'doi_search' => 'DOI',
            'format_search' => 'File Format',
            'type_search' => 'Data Type',
            'index4blast' => 'Index4blast',
            'sample_name' => Yii::t('app', 'Sample ID'),
            'download_count' => Yii::t('app','# of Downloads'),
            'attribute' => Yii::t('app','File Attributes'),
        );
    }

    public function afterSave() {
        $log = new DatasetLog;
        $log->dataset_id = $this->dataset_id;
        if($this->isNewRecord) {
            $log->message = 'Additional file '.$this->name. ' added';
        }
        else
            $log->message ='File '.$this->name. ' updated';
        $log->model_id = $this->id;
        $log->model = get_class($this);
        $log->url = Yii::app()->createUrl('/adminFile/update', array('id'=>$this->id));
        if($this->dataset->isPublic) {
            $log->save();
        }
        return true;
    }

    public function beforeDelete() {
        $log = new DatasetLog;
        $log->dataset_id = $this->dataset_id;
        $log->message = 'File '.$this->name.' removed';
        $log->model_id = $this->id;
        $log->model = get_class($this);
        $log->url = '';
        if($this->dataset->isPublic) {
            $log->save();
        }
        return true;
    }

    public static function getTypeList($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join file on file.type_id = t.id";
        $crit->addInCondition("file.id", $ids);
        return FileType::model()->findAll($crit);
    }

    public static function getFormatList($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join file on file.format_id = t.id";
        $crit->addInCondition("file.id", $ids);
        return FileFormat::model()->findAll($crit);
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->with = array( 'dataset' , 'format' , 'type' );
        $criteria->compare('t.id',$this->id);
        $criteria->compare('LOWER(t.name)',strtolower($this->name),true);
        $criteria->compare('location',$this->location,true);
        $criteria->compare('extension',$this->extension,true);
        $criteria->compare('size',$this->size,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('date_stamp',$this->date_stamp);
        $criteria->compare('LOWER(code)',strtolower($this->code),true);
        $criteria->compare('LOWER(index4blast)',$this->index4blast,true);
        $criteria->compare('LOWER(dataset.identifier)',strtolower($this->doi_search),true);
        $criteria->compare('LOWER(format.name)',strtolower($this->format_search),true);
        $criteria->compare('LOWER(type.name)',strtolower($this->type_search),true);
        $criteria->compare('download_count',$this->download_count);

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
            'criteria'=>$criteria,
            'sort'=>$sort,
        ));
    }

    /**
     * Return the human readable binary size of files with configurable formatting
     *
     * It's extracted out of getSizeWithFormat so the functionality can be used in other contexts as well.
     *
     * @param int $bytes size in bytes to format/convert
     * @param string $unit unit to convert to. KiB, MiB, GiB, TiB, B or null
     * @param int $precision number of decimals after the dot
     * @return string formatted size
     * @todo move this function in a Helper class as it's not specific ot the File model class
     */
    public static function specifySizeUnits(int $bytes, string $unit = null, int $precision = null): string
    {
        if ($bytes<0) {
            return (string) $bytes;
        }
        if ( null == $precision ) {
            $precision = 2;
        }
        $metric = new ByteUnits\Binary($bytes);
        $formatted_size = $metric->format("$unit/$precision"," ");
        return $formatted_size ;
    }

    /**
     * return the size of the file formatted for display using Binary notation
     *
     * @param string $unit KiB, MiB, GiB, TiB, B or null
     * @param int $precision number of decimals after the dot
     *
     * @return string formatted size
     *
     * @uses ByteUnits\Binary
     **/
    public function getSizeWithFormat($unit = null, $precision = 2)
    {
        return File::specifySizeUnits($this->size, $unit, $precision);
    }


    public static function getDatasetIdsByFileIds($fileIds) {
        $fileIds = implode(' , ' , $fileIds);
        if(!$fileIds) return array();
        $result = Yii::app()->db->createCommand()
            ->selectDistinct('dataset_id')
            ->from('file')
            ->where("id in ($fileIds)")
            ->queryColumn();
        return $result;
    }



    public function getSample() {
        $criteria = new CDbCriteria;
        $criteria->join = "LEFT JOIN file_sample fs ON fs.sample_id = t.id";
        $criteria->compare('fs.file_id', $this->id);
        return Sample::model()->find($criteria);
    }

    public function getSampleName() {
        $sample = $this->sample;
        return ($sample)? $sample->linkName : "";
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }

    /**
     * Before save file
     */
    public function setSizeValue()
    {
        // Save the size from file if not set by user
        if ($this->attributes['location'] && $this->attributes['name']) {
            $size = trim($this->attributes['size']);
            if (empty($size)) {
                if (!file_exists(ReadFile::TEMP_FOLDER . $this->attributes['name'])) {
                    ReadFile::downloadRemoteFile($this->attributes['location'], $this->attributes['name']);
                }
                if (empty($this->size)) {
                    $this->size = filesize(ReadFile::TEMP_FOLDER . $this->name);
                }
            }
        }
    }

    /**
     * @param $dataset
     * @return array
     * @throws Exception
     */
    public static function updateAllByData($data, $dataset)
    {
        $transaction = Yii::app()->db->beginTransaction();

        $errors = array();

        $needFiles = array();
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {
            $model = File::model()->findByPk($data[$i]['id']);
            if (!$model) {
                $model = new File();
                $model->dataset_id = $dataset->id;
            } else {
                $needFiles[] = $model->id;
            }

            $model->attributes = $data[$i];
            if ($model->date_stamp == "") {
                $model->date_stamp = NULL;
            }

            if (!$model->validate()) {
                $errors[$i] = $model->getErrors();
            } else {
                $isNewRecord = $model->getIsNewRecord();
                $model->save();

                if ($isNewRecord) {
                    $needFiles[] = $model->id;
                }
            }
        }

        if (!$errors) {
            $criteria=new CDbCriteria();
            $criteria->compare('dataset_id',$dataset->id);
            $criteria->addNotInCondition('id',$needFiles);

            $files = File::model()->findAll($criteria);
            /** @var File $file */
            foreach ($files as $file) {
                foreach ($file->fileSamples as $fileSample) {
                    $fileSample->delete();
                }

                foreach ($file->fileRelationships as $fileRelationship) {
                    $fileRelationship->delete();
                }

                foreach ($file->fileAttributes as $fileAttribute) {
                    $fileAttribute->delete();
                }

                $file->delete();
            }

            $transaction->commit();
        } else {
            $transaction->rollback();
        }

        return $errors;
    }

    public function prepareFormatId()
    {
        switch ($this->extension) {
            case 'doc':
                $name = 'TEXT';
                break;
            case 'readme':
                $name = 'TEXT';
                break;
            case 'text':
                $name = 'TEXT';
                break;
            case 'txt':
                $name = 'TEXT';
                break;
            case 'gff3':
                $name = 'GFF';
                break;
            case 'gff':
                $name = 'GFF';
                break;
            case 'tar':
                $name = 'TAR';
                break;
            case 'pdf':
                $name = 'PDF';
                break;
            default:
                $name = 'FASTA';
                break;
        }

        $format = FileFormat::model()->findByAttributes(array('name' => $name));
        if ($format) {
            $this->format_id = $format->id;
        }
    }
}
