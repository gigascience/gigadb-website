<?php

/**
 * This is the model class for table "species".
 *
 * The followings are the available columns in table 'species':
 * @property integer $id
 * @property integer $tax_id
 * @property string $common_name
 * @property string $genbank_name
 * @property string $scientific_name
 *
 * The followings are the available model relations:
 * @property Sample[] $samples
 */
class Species extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Species the static model class
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
		return 'species';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tax_id, common_name, scientific_name', 'required'),
			array('tax_id', 'numerical', 'integerOnly'=>true),
			array('common_name', 'length', 'max'=>64),
			array('genbank_name, scientific_name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, tax_id, common_name, genbank_name, scientific_name', 'safe', 'on'=>'search'),
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
			'samples' => array(self::HAS_MANY, 'Sample', 'species_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tax_id' => 'Tax',
			'common_name' => 'Common Name',
			'genbank_name' => 'Genbank Name',
			'scientific_name' => 'Scientific Name',
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
		$criteria->compare('tax_id',$this->tax_id);
		$criteria->compare('LOWER(common_name)',strtolower($this->common_name),true);
		$criteria->compare('LOWER(genbank_name)',strtolower($this->genbank_name),true);
		$criteria->compare('LOWER(scientific_name)',strtolower($this->scientific_name),true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListCommonNames(){
        $models=Species::model()->findAll(array('limit'=>10));
        $list=array();
        foreach ($models as $key=>$model){
            $list[$model->id] = $model->common_name;
        }
        return $list;
    }

    public static function getTaxLink($tax_id){
    	return "http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?mode=Info&id=".$tax_id;
    }



}
