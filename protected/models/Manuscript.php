<?php

/**
 * This is the model class for table "manuscript".
 *
 * The followings are the available columns in table 'manuscript':
 * @property integer $id
 * @property string $identifier
 * @property integer $pmid
 * @property integer $dataset_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class Manuscript extends MyActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Manuscript the static model class
	 */
    public $doi_search;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'manuscript';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('identifier, dataset_id', 'required'),
			array('pmid, dataset_id', 'numerical', 'integerOnly'=>true),
			array('identifier', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, identifier, pmid, dataset_id , doi_search', 'safe', 'on'=>'search'),
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
			'identifier' => 'Identifier',
			'pmid' => 'Pmid',
			'dataset_id' => 'Dataset',
            'doi_search' => 'DOI',
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

        $criteria->with = array( 'dataset' );
		$criteria->compare('t.id',$this->id);
		$criteria->compare('LOWER(t.identifier)',strtolower($this->identifier),true);
		$criteria->compare('pmid',$this->pmid);
		$criteria->compare('dataset_id',$this->dataset_id);

		$criteria->compare('dataset.identifier',$this->doi_search,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getDOILink(){
    	return "http://dx.doi.org/".$this->identifier;
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
}
