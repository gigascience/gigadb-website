<?php

/**
 * This is the model class for table "external_link_type".
 *
 * The followings are the available columns in table 'external_link_type':
 * @property integer $id
 * @property string $name
 *
 * The followings are the available model relations:
 * @property ExternalLink[] $externalLinks
 */
class ExternalLinkType extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExternalLinkType the static model class
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
		return 'external_link_type';
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
			array('name', 'length', 'max'=>45),
            array('name', 'length', 'max'=>250),
            array('name', 'unique', 'message'=> 'Duplicate entry'),
            array('description', 'length', 'max'=>250),
            array('multiple', 'boolean'),
            array('can_self_referred', 'boolean'),
            array('displayed_as', 'in', 'range' => array('link', 'tab')),
            array('relationship_id', 'required'),
            // The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, description, multiple, displayed_as, can_self_referred', 'safe', 'on'=>'search'),
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
			'externalLinks' => array(self::HAS_MANY, 'ExternalLink', 'external_link_type_id'),
            'relationship' => [self::BELONGS_TO, 'Relationship', 'relationship_id'],
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
            'multiple' => 'Can be multiple instances of that external link type per dataset ',
            'displayed_as' => 'Displayed As',
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
        $criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListTypes(){
        $models=ExternalLinkType::model()->findAll();
        $list=array();
        foreach (array_values($models) as $model){
            $list[$model->id] = $model->name;
        }
        return $list;
    }
}
