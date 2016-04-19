<?php

/**
 * This is the model class for table "Type".
 *
 * The followings are the available columns in table 'Type':
 * @property integer $id
 * @property string $name
 * @property string $description
 *
 * The followings are the available model relations:
 * @property DatasetType[] $datasetTypes
 */
class Type extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetTypes the static model class
	 */
    
        public $number;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'type';
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
			array('name', 'length', 'max'=>32),
			array('description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description', 'safe', 'on'=>'search'),
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
			'datasetTypes' => array(self::HAS_MANY, 'DatasetType', 'type_id'),
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
			'description' => 'Description',
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
                
                
                $criteria->alias='t';
                $criteria->select='t.id, t.name, t.description, count(dataset_type.dataset_id) as number';
                $criteria->join='LEFT JOIN dataset_type ON dataset_type.type_id=t.id';
                $criteria->group='t.id';
                $criteria->order='number DESC';

		$criteria->compare('id',$this->id);
		$criteria->compare('LOWER(name)',strtolower($this->name) , true);
		$criteria->compare('LOWER(description)',strtolower($this->description) , true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListTypes(){
        $models=Type::model()->findAll();
        $list=array();
        foreach ($models as $key=>$model){
            $list[$model->id] = $model->name;
        }
        return $list;
    }
}
