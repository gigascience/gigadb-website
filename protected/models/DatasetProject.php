<?php

/**
 * This is the model class for table "dataset_project".
 *
 * The followings are the available columns in table 'dataset_project':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $project_id
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property Project $project
 */
class DatasetProject extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetProject the static model class
	 */

	public $doi_search;
	public $project_name_search;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dataset_project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, project_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, project_id, doi_search, project_name_search', 'safe', 'on'=>'search'),
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
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
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
			'project_id' => 'Project',
			'doi_search' => 'DOI',
			'project_name_search' => 'Project Name',
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

        $criteria->with = array('dataset', 'project');

		$criteria->compare('id',$this->id);
		$criteria->compare('dataset_id',$this->dataset_id);
		$criteria->compare('project_id',$this->project_id);
		$criteria->compare('LOWER(dataset.identifier)',strtolower($this->doi_search),true);
		$criteria->compare('LOWER(project.name)',strtolower($this->project_name_search),true);

        $sort = new CSort();
        $sort->attributes = array(
            'doi_search' => array(
                'asc' => '(SELECT identifier from dataset WHERE dataset.id = t.dataset_id) ASC',
                'desc' => '(SELECT identifier from dataset WHERE dataset.id = t.dataset_id) DESC',
            ),
            'project_name_search' => array(
                'asc' => '(SELECT name from project WHERE project.id = t.project_id) ASC',
                'desc' => '(SELECT name from project WHERE project.id = t.project_id) DESC',
            ),
        );

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
            'sort' => $sort ,
		));
	}

	public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
}
