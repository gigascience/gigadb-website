<?php

/**
 * This is the model class for table "dataset_author".
 *
 * The followings are the available columns in table 'dataset_author':
 * @property integer $id
 * @property integer $dataset_id
 * @property integer $author_id
 * @property integer $contribution_id
 * @property integer $rank
 *
 * The followings are the available model relations:
 * @property Author $author
 * @property Dataset $dataset
 */
class DatasetAuthor extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DatasetAuthor the static model class
	 */
	public $doi_search;
	public $author_name_search;

    public $orcid_search;
    public $rank_search;
    public $author_name;
    public $rank;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dataset_author';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id ,rank, contribution_id', 'required'),
            array('contribution_id', 'validateContributionId'),
			array('dataset_id, author_id, rank', 'numerical', 'integerOnly'=>true),
                       	// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, author_id, doi_search, author_name_search , orcid_search , rank_search', 'safe', 'on'=>'search'),
		);
	}

    public function validateContributionId($attribute, $params)
    {
        $model = Contribution::model()->findByPk($this->$attribute);
        if (!$model) {
            $labels = $this->attributeLabels();
            $this->addError($attribute, $labels[$attribute] . ' is invalid.');
        }
    }

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'author' => array(self::BELONGS_TO, 'Author', 'author_id'),
			'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
            'contribution' => array(self::BELONGS_TO, 'Contribution', 'contribution_id'),
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
			'author_id' => 'Author ID',
			'doi_search' => 'DOI',
			'author_name_search' => 'Author Name',
			'rank'=>'Order',
            'orcid_search' => 'ORCID' ,
            'rank_search' => 'Rank',
            'aurhor_name' =>'Name',
            'contribution_id' => 'Credit',
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

        $criteria->with = array('dataset', 'author');

		$criteria->compare('id',$this->id);
		$criteria->compare('dataset_id',$this->dataset_id);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('dataset.identifier',$this->doi_search,true);
		//$criteria->compare('LOWER(author.name)',strtolower($this->author_name_search),true);
		$criteria->compare("LOWER(author.surname) || ' ' || LOWER(author.first_name)",strtolower($this->author_name_search),true);

		$criteria->compare('LOWER(author.orcid)',strtolower($this->orcid_search),true);
		$criteria->compare('rank',$this->rank_search);

        $sort = new CSort();
        $sort->attributes = array(
            'doi_search' => array(
                'asc' => '(SELECT identifier from dataset WHERE dataset.id = t.dataset_id) ASC',
                'desc' => '(SELECT identifier from dataset WHERE dataset.id = t.dataset_id) DESC',
            ),
            'author_name_search' => array(
                'asc' => '(SELECT surname from author WHERE author.id = t.author_id) ASC',
                'desc' => '(SELECT surname from author WHERE author.id = t.author_id) DESC',
            ),
            'orcid_search' => array(
                'asc' => '(SELECT orcid from author WHERE author.id = t.author_id) ASC',
                'desc' => '(SELECT orcid from author WHERE author.id = t.author_id) DESC',
            ),
            'rank_search' => array(
                'asc' => '(SELECT rank from author WHERE author.id = t.author_id) ASC',
                'desc' => '(SELECT rank from author WHERE author.id = t.author_id) DESC',
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
