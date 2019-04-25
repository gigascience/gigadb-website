<?php

/**
 * This is the model class for table "external_link".
 *
 * The followings are the available columns in table 'external_link':
 * @property integer $id
 * @property integer $dataset_id
 * @property string $url
 * @property integer $external_link_type_id
 * @property integer $type
 * @property integer $description
 *
 * The followings are the available model relations:
 * @property Dataset $dataset
 * @property ExternalLinkType $externalLinkType
 */
class ExternalLink extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExternalLink the static model class
	 */
    public $doi_search;
    public $external_link_type_search;
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'external_link';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dataset_id, url', 'required'),
			array('dataset_id, external_link_type_id, type', 'numerical', 'integerOnly'=>true),
			array('url', 'length', 'max'=>128),
			//array('url', 'validateUrlUnique'),
			array('url', 'validateUrlByPattern'),
			array('description', 'length', 'max'=>200),
			array('type', 'in', 'range' => array(AIHelper::MANUSCRIPTS, AIHelper::PROTOCOLS, AIHelper::_3D_IMAGES, AIHelper::CODES, AIHelper::SOURCES)),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dataset_id, url, external_link_type_id, doi_search, external_link_type_search', 'safe', 'on'=>'search'),
		);
	}

    public function validateUrlUnique($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $exLink = ExternalLink::model()->findByAttributes(array(
                'dataset_id'=> $this->dataset_id,
                'url' => $this->$attribute
            ));

            if ($exLink) {
                $labels = $this->attributeLabels();
                $this->addError($attribute, $labels[$attribute] . ' already exist.');
            }
        }
    }

    public function validateUrlByPattern($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $pattern = AIHelper::getRegExp($this->type);
            if (!preg_match($pattern, $this->$attribute)) {
                $labels = $this->attributeLabels();
                $this->addError($attribute, $labels[$attribute] . ' is invalid.');
            }
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
			'dataset' => array(self::BELONGS_TO, 'Dataset', 'dataset_id'),
			'external_link_type' => array(self::BELONGS_TO, 'ExternalLinkType', 'external_link_type_id'),
			'externalLinkType' => array(self::BELONGS_TO, 'ExternalLinkType', 'external_link_type_id'),
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
			'url' => 'Url',
			'external_link_type_id' => 'External Link Type',
            'doi_search' => 'DOI',
            'external_link_type_search' => 'Type',
            'type' => 'Type',
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

        $criteria->with = array('dataset','external_link_type');
		$criteria->compare('t.id',$this->id);
		$criteria->compare('dataset_id',$this->dataset_id);
		$criteria->compare('LOWER(url)',strtolower($this->url),true);
		$criteria->compare('external_link_type_id',$this->external_link_type_id);

		$criteria->compare('dataset.identifier',$this->doi_search,true);
		$criteria->compare('LOWER(external_link_type.name)',strtolower($this->external_link_type_search),true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }

    public function getTypeName()
    {
        return AIHelper::getTypeName($this->type);
    }

    public function loadByData($data)
    {
        $this->dataset_id = $data['dataset_id'];
        $this->url = $data['url'];
        $this->type = $data['externalLinkType'];
        $this->description = $data['externalLinkDescription'];
    }
}
