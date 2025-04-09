<?php

/**
 * This is the model class for table "link".
 *
 * The followings are the available columns in table 'link':
 * @property integer $id
 * @property integer $dataset_id
 * @property boolean $is_primary
 * @property string $link
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 */
class Link extends CActiveRecord implements LinkInterface
{

    public $doi_search;
    public $acc_num;
    public $database;

	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Link the static model class
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
		return 'link';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, link', 'required'),
			array('dataset_id', 'numerical', 'integerOnly'=>true),
			array('link', 'length', 'max'=>100),
			array('is_primary', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, is_primary, link, doi_search', 'safe', 'on'=>'search'),
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
            'acc_num' => 'Accession number',
            'database' => 'Database',
            'is_primary' => 'Is Primary',
            'link' => 'Link',
            'doi_search' => 'DOI',
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
        $criteria->with = array( 'dataset' );
		$criteria->compare('t.id',$this->id);
		$criteria->compare('dataset_id',$this->dataset_id);
		$criteria->compare('is_primary',$this->is_primary);
		$criteria->compare('LOWER(link)',strtolower($this->link),true);
		$criteria->compare('dataset.identifier',$this->doi_search,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    // See protected/scripts/bioregistry/README.md for more info on this function
    public function getFullUrl(string $source = ''): string {
        $trimmedLink = trim($this->link);

        // if link does not contain a (:) we cannot build it
        if (empty($trimmedLink) || !str_contains($trimmedLink, ':')) {
            return "#";
        }

        // Option A
        return "https://bioregistry.io/$trimmedLink";
        // Option B: everything below is ignored unless previous line is commented out.

        $linkParts = explode(":", $trimmedLink);
        $prefix = strtolower($linkParts[0]);
        $value = $linkParts[1];

        $prefixModel = null;

        if (!empty($source)) {
            $prefixModel = Prefix::model()->find(
                "lower(prefix) = :p and source = :s",
                array(':p' => $prefix, ':s' => $source)
            );
        }

        if (!$prefixModel) {
            $prefixModel = Prefix::model()->find(
                'lower(prefix) = :p',
                array(':p' => $prefix)
            );
        }

        if (!$prefixModel || !isset($prefixModel->url)) {
            return "#";
        }

        if (str_contains($prefixModel->url, '$1')) {
            return str_replace('$1', $value, $prefixModel->url);
        }

        return $prefixModel->url . $value;
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }

}
