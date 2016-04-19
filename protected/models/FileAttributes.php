<?php

/**
 * This is the model class for table "file_attributes".
 *
 * The followings are the available columns in table 'file_attributes':
 * @property integer $id
 * @property integer $file_id
 * @property integer $attribute_id
 * @property string $value
 * @property string $unit_id
 *
 * The followings are the available model relations:
 * @property Attribute $attribute
 * @property File $file
 * @property Unit $unit
 */
class FileAttributes extends CActiveRecord
{

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FileAttributes the static model class
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
        return 'file_attributes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('file_id, attribute_id', 'required'),
            array('file_id, attribute_id', 'numerical', 'integerOnly' => true),
            array('value', 'length', 'max' => 50),
            array('unit_id', 'length', 'max' => 30),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, file_id, attribute_id, value, unit_id', 'safe', 'on' => 'search'),
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
            'attribute' => array(self::BELONGS_TO, 'Attribute', 'attribute_id'),
            'file' => array(self::BELONGS_TO, 'File', 'file_id'),
            'unit' => array(self::BELONGS_TO, 'Unit', 'unit_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'file_id' => 'File',
            'attribute_id' => 'Attribute',
            'value' => 'Value',
            'unit_id' => 'Unit',
        );
    }

    public function afterSave() {
        $log = new DatasetLog;
        $log->dataset_id = $this->file->dataset_id;
        if($this->isNewRecord) {
            $log->message = $this->file->name. ': additional file attribute added';
        }
        else
            $log->message = $this->file->name. ': file attribute updated';
        $log->model_id = $this->id;
        $log->model = get_class($this);
        $log->url = Yii::app()->createUrl('/adminFile/update', array('id'=>$this->file->id));
        if($this->file->dataset->isPublic) {
            $log->save();
        }
        return true;
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('file_id', $this->file_id);
        $criteria->compare('attribute_id', $this->attribute_id);
        $criteria->compare('value', $this->value, true);
        $criteria->compare('unit_id', $this->unit_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
