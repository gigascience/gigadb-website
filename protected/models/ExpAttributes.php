<?php

/**
 * This is the model class for table "exp_attributes".
 *
 * The followings are the available columns in table 'exp_attributes':
 * @property integer $id
 * @property integer $exp_id
 * @property integer $attribute_id
 * @property string $value
 * @property string $units_id
 *
 * The followings are the available model relations:
 * @property Attribute $attribute
 * @property Experiment $exp
 * @property Unit $units
 */
class ExpAttributes extends MyActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return ExpAttributes the static model class
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
        return 'exp_attributes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('exp_id, attribute_id', 'numerical', 'integerOnly'=>true),
            array('value', 'length', 'max'=>1000),
            array('units_id', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, exp_id, attribute_id, value, units_id', 'safe', 'on'=>'search'),
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
            'exp' => array(self::BELONGS_TO, 'Experiment', 'exp_id'),
            'units' => array(self::BELONGS_TO, 'Unit', 'units_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'exp_id' => 'Exp',
            'attribute_id' => 'Attribute',
            'value' => 'Value',
            'units_id' => 'Units',
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
        $criteria->compare('exp_id',$this->exp_id);
        $criteria->compare('attribute_id',$this->attribute_id);
        $criteria->compare('value',$this->value,true);
        $criteria->compare('units_id',$this->units_id,true);

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