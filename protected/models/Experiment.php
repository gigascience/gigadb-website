<?php

/**
 * This is the model class for table "experiment".
 *
 * The followings are the available columns in table 'experiment':
 * @property integer $id
 * @property string $experiment_type
 * @property string $experiment_name
 * @property string $exp_description
 * @property integer $dataset_id
 *
 * The followings are the available model relations:
 * @property SampleExperiment[] $sampleExperiments
 * @property Dataset $dataset
 * @property FileExperiment[] $fileExperiments
 * @property ExpAttributes[] $expAttributes
 */
class Experiment extends MyActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Experiment the static model class
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
        return 'experiment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dataset_id', 'numerical', 'integerOnly'=>true),
            array('experiment_type, experiment_name', 'length', 'max'=>100),
            array('exp_description', 'length', 'max'=>1000),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, experiment_type, experiment_name, exp_description, dataset_id', 'safe', 'on'=>'search'),
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
            'sampleExperiments' => array(self::HAS_MANY, 'SampleExperiment', 'experiment_id'),
            'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
            'fileExperiments' => array(self::HAS_MANY, 'FileExperiment', 'experiment_id'),
            'expAttributes' => array(self::HAS_MANY, 'ExpAttributes', 'exp_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'experiment_type' => 'Experiment Type',
            'experiment_name' => 'Experiment Name',
            'exp_description' => 'Exp Description',
            'dataset_id' => 'Dataset',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('experiment_type',$this->experiment_type,true);
        $criteria->compare('experiment_name',$this->experiment_name,true);
        $criteria->compare('exp_description',$this->exp_description,true);
        $criteria->compare('dataset_id',$this->dataset_id);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
}