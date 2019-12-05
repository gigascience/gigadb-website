<?php

/**
 * This is the model class for table "dataset_attributes".
 *
 * The followings are the available columns in table 'dataset_attributes':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $attribute_id
 * @property string $value
 * @property string $units_id
 *
 * The followings are the available model relations:
 * @property Attribute $attribute
 * @property Dataset $dataset
 * @property Unit $units
 * @property integer $image_id
 * @property string $until_date
 */
class DatasetAttributes extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return DatasetAttributes the static model class
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
        return 'dataset_attributes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dataset_id, attribute_id, image_id', 'numerical', 'integerOnly'=>true),
            array('value', 'required'),
            array('value', 'length', 'max'=>200),
            array('value', 'rejectCode'),
            array('units_id', 'length', 'max'=>30),
            array('until_date', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, dataset_id, attribute_id, value, units_id, image_id, until_date', 'safe', 'on'=>'search'),
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
            'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
            'units' => array(self::BELONGS_TO, 'Unit', 'units_id'),
            'image' => array(self::BELONGS_TO, 'Images', 'image_id'),
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
            'attribute_id' => 'Attribute',
            'value' => 'Value',
            'units_id' => 'Units',
            'image_id' => 'Image',
            'until_date' => 'Until Date',
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

        $criteria->compare('dataset_id',$this->dataset_id);

        $criteria->compare('attribute_id',$this->attribute_id);

        $criteria->compare('value',$this->value,true);

        $criteria->compare('units_id',$this->units_id,true);

        $criteria->compare('image_id',$this->image_id);

        $criteria->compare('until_date',$this->until_date,true);

        return new CActiveDataProvider('DatasetAttributes', array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Reject values that have HTML/PHP/javascript tags in them
     *
     * @param string $attr the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function rejectCode($attr,$params) {
        $rawValue = CHtml::decode($this->value);
        $strippedValue = strip_tags($rawValue);
        if ($rawValue !== $strippedValue) {
            $this->addError($attr,'Rejected value because of illegal characters detected');
        }
    }
}