<?php

/**
 * This is the model class for table "attribute".
 *
 * The followings are the available columns in table 'attribute':
 * @property integer $id
 * @property string $attribute_name
 * @property string $definition
 * @property string $model
 * @property string $structured_comment_name
 * @property string $value_syntax
 * @property string $allowed_units
 * @property string $occurance
 * @property string $ontology_link
 * @property string $note
 */
class Attribute extends CActiveRecord
{
	
	const FUP = 'Fairly Use Policy';
        const AUTO_ATTRIBUTE = "'file_size', 'num_amino_acids', 'num_nucleotides', 'num_words', 'num_lines', 'num_rows', 'num_columns'";
	/**
	 * Returns the static model of the specified AR class.
	 * @return Attribute the static model class
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
		return 'attribute';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('attribute_name, allowed_units', 'length', 'max'=>100),
			array('definition, ontology_link', 'length', 'max'=>1000),
			array('model', 'length', 'max'=>30),
			array('structured_comment_name, note', 'length', 'max'=>50),
			array('value_syntax', 'length', 'max'=>500),
			array('occurance', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, attribute_name, definition, model, structured_comment_name, value_syntax, allowed_units, occurance, ontology_link, note', 'safe', 'on'=>'search'),
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
			'exp_attributes' => array(self::HAS_MANY, 'ExpAttributes', 'attribute_id'),
			'sample_attributes' => array(self::HAS_MANY, 'SampleAttribute', 'attribute_id'),
			'dataset_attributes' => array(self::HAS_MANY, 'DatasetAttributes', 'attribute_id'),
			'file_attributes' => array(self::HAS_MANY, 'FileAttributes', 'attribute_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'attribute_name' => 'Attribute Name',
			'definition' => 'Definition',
			'model' => 'Model',
			'structured_comment_name' => 'Structured Comment Name',
			'value_syntax' => 'Value Syntax',
			'allowed_units' => 'Allowed Units',
			'occurance' => 'Occurance',
			'ontology_link' => 'Ontology Link',
			'note' => 'Note',
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

		$criteria->compare('attribute_name',$this->attribute_name,true);

		$criteria->compare('definition',$this->definition,true);

		$criteria->compare('model',$this->model,true);

		$criteria->compare('structured_comment_name',$this->structured_comment_name,true);

		$criteria->compare('value_syntax',$this->value_syntax,true);

		$criteria->compare('allowed_units',$this->allowed_units,true);

		$criteria->compare('occurance',$this->occurance,true);

		$criteria->compare('ontology_link',$this->ontology_link,true);

		$criteria->compare('note',$this->note,true);

		return new CActiveDataProvider('Attribute', array(
			'criteria'=>$criteria,
		));
	}

	static function findByAttrName($attributeName)
    {
        $criteria = new CDbCriteria( array(
            'condition' => "LOWER(attribute_name) = LOWER(:match)",
            'params'    => array(':match' => $attributeName)
        ) );

        $attribute = Attribute::model()->find($criteria);

        return $attribute;
    }

    static function findSimilarByAttrName($attributeName)
    {
        $criteria = new CDbCriteria( array(
            'condition' => "LOWER(:match) LIKE CONCAT('%', LOWER(attribute_name), '%') OR LOWER(attribute_name) LIKE CONCAT('%', LOWER(:match), '%')",
            'params'    => array(':match' => $attributeName)
        ) );

        return Attribute::model()->find($criteria);
    }

    static function findAllSimilarByAttrName($attributeName)
    {
        $criteria = new CDbCriteria( array(
            'condition' => "LOWER(:match) LIKE CONCAT('%', LOWER(attribute_name), '%') OR LOWER(attribute_name) LIKE CONCAT('%', LOWER(:match), '%')",
            'params'    => array(':match' => $attributeName)
        ) );

        return Attribute::model()->findAll($criteria);
    }
}

