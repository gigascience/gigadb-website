<?php

/**
 * This is the model class for table "dataset_type".
 *
 * The followings are the available columns in table 'dataset_type':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $type_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property Type $type
 */
class DatasetType extends MyActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetType the static model class
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
		return 'dataset_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, type_id', 'required'),
			array('dataset_id, type_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, type_id', 'safe', 'on'=>'search'),
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
			'type' => array(self::BELONGS_TO, 'Type', 'type_id'),
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
			'type_id' => 'Type',
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
		$criteria->compare('type_id',$this->type_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function createDatasetType($dataset_id, $type_id) {
		$newDatasetTypeRelationship = new DatasetType;
		$newDatasetTypeRelationship->dataset_id = $dataset_id;
		$newDatasetTypeRelationship->type_id = $type_id;
		if(!$newDatasetTypeRelationship->save(false)) {
			Yii::log(__FUNCTION__."> Errors: ". print_r($newDatasetTypeRelationship->getErrors(), true), 'error');
			return false;
		}
		return true;
	}

	public static function storeDatasetTypes($dataset_id, $types) {
            $currentTypes = DatasetType::model()->findAllByAttributes(array('dataset_id'=>$dataset_id));
            $currentIds = array();
            foreach($currentTypes as $currentType) {
                $currentIds[] = $currentType->type_id;
            }

            foreach($currentTypes as $currentType) {
                if(!in_array($currentType->type_id, $types)) {
                    if(!$currentType->delete(false)) {
                        return false;
                    }
                }
            }

            foreach($types as $id) {
                    if(!in_array($id, $currentIds)) {
                                       if (!self::createDatasetType($dataset_id, $id)) {
                                    return false;
                                }
                            }
            }

            return true;
	}

	public function behaviors() {
	        return array(
	            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
	        );
	    }

}
