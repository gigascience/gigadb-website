<?php

/**
 * This is the model class for table "Prefix".
 *
 * The followings are the available columns in table 'Prefix':
 * @property integer $id
 * @property string $prefix
 * @property string $url
 *
 * The followings are the available model relations:
 * @property DatasetType[] $datasetTypes
 */
class Prefix extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetTypes the static model class
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
		return 'prefix';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('prefix,url', 'required'),
			array('prefix', 'length', 'max'=>20),
			array('prefix, url, source','safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, prefix, url', 'safe', 'on'=>'search'),
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
//			'datasetTypes' => array(self::HAS_MANY, 'DatasetType', 'type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'prefix' => 'Prefix',
			'url' => 'Url',
		
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
		$criteria->compare('prefix',$this->prefix,true);
		$criteria->compare('url',$this->url,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListPrefixes(){
        $models=Type::model()->findAll();
        $list=array();
        foreach ($models as $key=>$model){
            $list[$model->id] = $model->prefix;
        }
        return $list;
    }
}

