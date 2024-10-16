<?php

/**
 * This is the model class for table "relationship".
 *
 * The followings are the available columns in table 'relationship':
 * @property integer $id
 * @property string $name
 */
class Relationship extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Relationship the static model class
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
		return 'relationship';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name', 'safe', 'on'=>'search'),
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
			'file_relationships' => array(self::HAS_MANY, 'FileRelationship', 'relationship_id'),
			'relations' => array(self::HAS_MANY, 'Relation', 'relationship_id'),
			'sample_rels' => array(self::HAS_MANY, 'SampleRel', 'relationship_id'),
            'fileRelationships' => array(self::HAS_MANY, 'FileRelationship', 'relationship_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'name' => 'Name',
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

		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider('Relationship', array(
			'criteria'=>$criteria,
		));
	}

    /**
     * @param $name
     *
     * @return void
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

}
