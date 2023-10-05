<?php

/**
 * This is the model class for table "sample_attribute".
 *
 * The followings are the available columns in table 'sample_attribute':
 * @property integer $id
 * @property integer $sample_id
 * @property integer $attribute_id
 * @property string $value
 * @property string $unit_id
 */
class SampleAttribute extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return SampleAttribute the static model class
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
		return 'sample_attribute';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sample_id, attribute_id', 'required'),
			array('sample_id, attribute_id', 'numerical', 'integerOnly'=>true),
			array('value', 'length', 'max'=>200),
			array('unit_id', 'length', 'max'=>30),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, sample_id, attribute_id, value, unit_id', 'safe', 'on'=>'search'),
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
			'sample' => array(self::BELONGS_TO, 'Sample', 'sample_id'),
			'unit' => array(self::BELONGS_TO, 'Unit', 'unit_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'sample_id' => 'Sample',
			'attribute_id' => 'Attribute',
			'value' => 'Value',
			'unit_id' => 'Unit',
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

		$criteria->compare('sample_id',$this->sample_id);

		$criteria->compare('attribute_id',$this->attribute_id);

		$criteria->compare('value',$this->value,true);

		$criteria->compare('unit_id',$this->unit_id,true);

		return new CActiveDataProvider('SampleAttribute', array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
}

