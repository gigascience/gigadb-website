<?php

/**
 * This is the model class for table "funder_name".
 *
 * The followings are the available columns in table 'funder_name':
 * @property integer $id
 * @property string $uri
 * @property string $primary_name_display
 * @property string $country
 */
class Funder extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Funder the static model class
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
		return 'funder_name';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uri', 'required'),
			array('uri', 'length', 'max'=>100),
			array('primary_name_display', 'length', 'max'=>1000),
			array('country', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uri, primary_name_display, country', 'safe', 'on'=>'search'),
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
			'dataset_funders' => array(self::HAS_MANY, 'DatasetFunder', 'funder_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'uri' => 'Uri',
			'primary_name_display' => 'Primary Name Display',
			'country' => 'Country',
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

		$criteria->compare('uri',$this->uri,true);

		$criteria->compare('primary_name_display',$this->primary_name_display,true);

		$criteria->compare('country',$this->country,true);

		return new CActiveDataProvider('Funder', array(
			'criteria'=>$criteria,
		));
	}
}