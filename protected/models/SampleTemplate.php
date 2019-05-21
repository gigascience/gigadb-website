<?php

/**
 * This is the model class for table "sample_template".
 *
 * The followings are the available columns in table 'sample_template':
 * @property integer $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property Attribute[] $attributes
 */
class SampleTemplate extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sample_template';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
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
            'attributes' => array(self::MANY_MANY, 'Attribute', 'sample_template_attribute(sample_template_id,attribute_id)'),
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
		);
	}
}
