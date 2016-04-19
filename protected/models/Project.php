<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $url
 * @property string $name
 * @property string $image_location
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class Project extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Project the static model class
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
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url', 'required'),
                        array('url', 'url','message'=>'Please check the URL format'),
			array('url', 'length', 'max'=>128),
			array('name', 'length', 'max'=>255),
			array('image_location', 'length', 'max'=>100),
                        array('url','check_duplicate'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, url, name, image_location', 'safe', 'on'=>'search'),
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
			'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_project(project_id , dataset_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'url' => 'Url',
			'name' => 'Name',
			'image_location' => 'Image Location',
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
		$criteria->compare('LOWER(url)',strtolower($this->url),true);
		$criteria->compare('LOWER(name)',strtolower($this->name),true);
		$criteria->compare('LOWER(image_location)',strtolower($this->image_location),true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function getListProjects(){
        $models=Species::model()->findAll();
        $list=array();
        foreach ($models as $key=>$model){
            $list[$model->id] = $model->common_name;
        }
        return $list;
    }
    
    function check_duplicate(){
        
        
        $db_url= Project::model()->findBySql("select name from project where url='$this->url'");
        
        if($db_url !=null){
        $this->addError('url','Duplicate URL');}
        
        $db_name= Project::model()->findBySql("select url from project where name='$this->name'");
       
        if($db_name !=null){
        $this->addError('name','Duplicate Project Name');}
        
             
    }

}
