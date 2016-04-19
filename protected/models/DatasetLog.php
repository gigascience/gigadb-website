<?php

/**
 * This is the model class for table "dataset_log".
 *
 * The followings are the available columns in table 'dataset_log':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $message
 * @property string $created_at
 * @property string $model
 * @property string $model_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class DatasetLog extends CActiveRecord
{
    public $doi;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DatasetLog the static model class
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
        return 'dataset_log';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dataset_id', 'required'),
            array('dataset_id', 'numerical', 'integerOnly'=>true),
            array('message, created_at, model, model_id', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dataset_id, message, created_at, model, model_id, doi', 'safe', 'on'=>'search'),
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
            'message' => 'Message',
            'created_at' => 'Created At',
            'model' => 'Table Changed',
            'model_id' => 'Table Row',
            'doi' => 'DOI',
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
        $criteria->with = array('dataset');

        $criteria->compare('id',$this->id);
        $criteria->compare('dataset_id',$this->dataset_id);
        $criteria->compare('message',$this->message,true);
        $criteria->compare('created_at',$this->created_at,true);
        $criteria->compare('model',$this->model,true);
        $criteria->compare('model_id',$this->model_id,true);
        $criteria->compare('dataset.identifier', $this->doi, true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
} 
