<?php

/**
 * This is the model class for table "dataset_sample".
 *
 * The followings are the available columns in table 'dataset_sample':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $sample_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property Sample $sample
 */
class DatasetSample extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetSample the static model class
	 */

	public $doi_search;
	public $sample_name;
	public $species; //common_name in species
	public $attribute;
	public $code;
	public $tax_id;
	public $dataset_title;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dataset_sample';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, sample_id', 'required'),
			array('dataset_id, sample_id', 'numerical', 'integerOnly'=>true),
                         	array('species,code,attribute','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, sample_id, doi_search, sample_name, attribute', 'safe', 'on'=>'search'),
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
			'sample' => array(self::BELONGS_TO, 'Sample', 'sample_id'),
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
			'sample_id' => 'Sample',
			'doi_search' => 'DOI',
			'species' =>'Species',
			'attribute' =>'Sample Attributes',
			'code' =>'Sample ID',
			'dataset_title'=>'Dataset Title',
			'sample_name'=>'Sample Name',
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
		$criteria->with = array(
			'sample',
			'dataset',
		);
		$criteria->compare('t.id',$this->id);
		$criteria->compare('t.dataset_id',$this->dataset_id);
		$criteria->compare('t.sample_id',$this->sample_id);
		$criteria->compare('dataset.identifier',$this->doi_search,true);
		$criteria->compare('sample.name', $this->sample_name,true);

		$sort = new CSort();
		$sort->attributes = array(
			'doi_search' => array(
				'asc' => 'identifier ASC',
				'desc' => 'identifier DESC',
			),
		);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
		));
	}

	public function behaviors() {
		return array(
			'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
		);
	}
}
