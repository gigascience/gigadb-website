<?php

/**
 * This is the model class for table "dataset_funder".
 *
 * The followings are the available columns in table 'dataset_funder':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $funder_id
 * @property string $grant_award
 * @property string $comments
 */
class DatasetFunder extends CActiveRecord
{
	public $doi_search;
	public $funder_search;
	/**
	 * Returns the static model of the specified AR class.
	 * @return DatasetFunder the static model class
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
		return 'dataset_funder';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, funder_id', 'required'),
			array('dataset_id, funder_id', 'numerical', 'integerOnly'=>true),
			array('grant_award, comments', 'safe'),
			array('funder_id', 'checkIsFunderDuplicate', 'on'=>'insert'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, funder_id, grant_award, comments, doi_search, funder_search', 'safe', 'on'=>'search'),
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
			'funder' => array(self::BELONGS_TO, 'Funder', 'funder_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'dataset_id' => 'Dataset',
			'funder_id' => 'Funder',
			'grant_award' => 'Grant Award',
			'comments' => 'Comments',
			'doi_search' => 'Dataset',
			'funder_search' => 'Funder',
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
		$criteria->with = array('dataset','funder');
		$criteria->compare('id',$this->id);
		$criteria->compare('dataset_id',$this->dataset_id);
		$criteria->compare('funder_id',$this->funder_id);
		$criteria->compare('grant_award',$this->grant_award,true);
		$criteria->compare('comments',$this->comments,true);
		$criteria->compare('dataset.identifier', $this->doi_search, true);
		$criteria->compare('LOWER(funder.primary_name_display)', strtolower($this->funder_search), true);

		$sort = new CSort();
		$sort->attributes = array(
			'doi_search' => array(
				'asc' => 'dataset.identifier asc',
				'desc' => 'dataset.identifier desc',
			),
			'funder_search' => array(
				'asc' => 'funder.primary_name_display asc',
				'desc' => 'funder.primary_name_display desc',
			),
			'grant_award' => array(
				'asc' => 'grant_award ASC',
				'desc' => 'grant_award DESC',
			),
			'comments' => array(
				'asc' => 'comments ASC',
				'desc' => 'comments DESC',
			),
		);

		return new CActiveDataProvider('DatasetFunder', array(
			'criteria'=>$criteria,
			'sort' => $sort,
		));
	}

	public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }

    public function checkIsFunderDuplicate() {
    	$model = self::model()->findByAttributes(array('dataset_id'=>$this->dataset_id,'funder_id'=>$this->funder_id));
    	if($model) {
    		$this->addError('funder_id', Yii::t("app", "This fundation has been added."));
		return false; 
    	}
    	return true;
    }
}