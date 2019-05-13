<?php

/**
 * This is the model class for table "file_format".
 *
 * The followings are the available columns in table 'file_format':
 * @property integer $id
 * @property string $name
 * @property string $description
 *
 * The followings are the available model relations:
 * @property File[] $files
 */
class FileFormat extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FileFormat the static model class
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
		return 'file_format';
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
			array('name', 'length', 'max'=>10),
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
			'files' => array(self::HAS_MANY, 'File', 'format_id'),
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

		$criteria->compare('id',$this->id);
		$criteria->compare('LOWER(name)',strtolower($this->name),true);
		$criteria->compare('LOWER(description)',strtolower($this->description),true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'pagination' => False
		));
	}

	public static function getListFormats(){
        $models=FileFormat::model()->findAll();
        $list=array();
        foreach (array_values($models) as $model){
            $list[$model->id] = $model->name;
            $list[$model->id.'_description'] = $model->description;
        }
        return $list;
    }
}
