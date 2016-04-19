<?php

/**
 * This is the model class for table "unit".
 *
 * The followings are the available columns in table 'unit':
 * @property string $id
 * @property string $name
 * @property string $definition
 *
 * The followings are the available model relations:
 * @property SampleAttribute[] $sampleAttributes
 * @property FileAttributes[] $fileAttributes
 * @property ExpAttributes[] $expAttributes
 * @property DatasetAttributes[] $datasetAttributes
 */
class Unit extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Unit the static model class
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
        return 'unit';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('id', 'length', 'max'=>30),
            array('name', 'length', 'max'=>200),
            array('definition', 'length', 'max'=>500),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, definition', 'safe', 'on'=>'search'),
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
            'sampleAttributes' => array(self::HAS_MANY, 'SampleAttribute', 'unit_id'),
            'fileAttributes' => array(self::HAS_MANY, 'FileAttributes', 'unit_id'),
            'expAttributes' => array(self::HAS_MANY, 'ExpAttributes', 'units_id'),
            'datasetAttributes' => array(self::HAS_MANY, 'DatasetAttributes', 'units_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'name' => 'Name',
            'definition' => 'Definition',
        );
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('definition',$this->definition,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
