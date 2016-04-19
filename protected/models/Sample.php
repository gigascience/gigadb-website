<?php

/**
 * This is the model class for table "sample".
 *
 * The followings are the available columns in table 'sample':
 * @property integer $id
 * @property integer $species_id
 * @property string $name
 * @property string $consent_document
 * @property integer $submitted_id
 * @property string $submission_date
 * @property string $contact_author_name
 * @property string $contact_author_email
 * @property string $sampling_protocol
 *
 * The followings are the available model relations:
 * @property GigadbUser $submitted
 * @property SampleRel[] $sampleRels
 * @property SampleExperiment[] $sampleExperiments
 * @property FileSample[] $fileSamples
 * @property Species $species
 * @property DatasetSample[] $datasetSamples
 * @property Species $speciesSampleAttribute[] $sampleAttributes
 */
class Sample extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Sample the static model class
	 */
    public $species_search;
    public $dois_search;
    public $attr_search;
    public $attr_search_lowercase;
    public $attributesList;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sample';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('species_id', 'required'),
			array('species_id, submitted_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>50),
            		array('consent_document, contact_author_name', 'length', 'max'=>45),
            		array('contact_author_email, sampling_protocol', 'length', 'max'=>100),
			array('submission_date', 'safe'),
			//array('code', 'required'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('species_search, dois_search, name, attr_search', 'safe', 'on'=>'search'),
			//array('id, species_id, name, consent_document, submitted_id, submission_date, contact_author_name, contact_author_email, sampling_protocol, species_search, dois_search', 'safe', 'on'=>'search'),
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
			//'files' => array(self::HAS_MANY, 'File', 'sample_id'),
			'species' => array(self::BELONGS_TO, 'Species', 'species_id'),
			'submitted' => array(self::BELONGS_TO, 'GigadbUser', 'submitted_id'),
			'sampleRels' => array(self::HAS_MANY, 'SampleRel', 'sample_id'),
			'sampleExperiments' => array(self::HAS_MANY, 'SampleExperiment', 'sample_id'),
			'fileSamples' => array(self::HAS_MANY, 'FileSample', 'sample_id'),
			'datasetSamples' => array(self::HAS_MANY, 'DatasetSample', 'sample_id'),
			'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_sample(dataset_id,sample_id)'),
			'sampleAttributes' => array(self::HAS_MANY, 'SampleAttribute', 'sample_id', 'order'=>'id asc'),
			'attributes' => array(self::HAS_MANY, 'Attribute', array('id' => 'attribute_id'), 'through' => 'sampleAttributes'),
			'alternativeIdentifiers' => array(self::HAS_MANY, 'AlternativeIdentifiers', 'sample_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'species_id' => 'Species',
			//'code' => 'Sample ID',
			'name' => Yii::t('app', 'Sample ID'),
			'taxonomic_id' => Yii::t('app','Taxonomic ID'),
			'common_name' => Yii::t('app','Common Name'),
			'genbank_name' => Yii::t('app','Genbank Name'),
			'scientific_name' => Yii::t('app','Scientific Name'),
			'attribute' => Yii::t('app','Sample Attributes'),
			'attr_search' => Yii::t('app','Sample Attributes'),
            		'species_search' => Yii::t('app','Species Name'),
            		'dois_search' => Yii::t('app','DOIs'),
			'consent_document' => 'Consent Document',
            		'submitted_id' => 'Submitted',
            		'submission_date' => 'Submission Date',
            		'contact_author_name' => 'Contact Author Name',
            		'contact_author_email' => 'Contact Author Email',
            		'sampling_protocol' => 'Sampling Protocol',
		);
	}

	public function getDataset() {
		$crit = new CDbCriteria;
		$crit->join = "JOIN dataset_sample ds ON ds.dataset_id = t.id";
		$crit->condition = "ds.sample_id = :sid";
		$crit->params = array(':sid'=>$this->id);
		return Dataset::model()->find($crit);
	}

	public function getShortAttrDesc() {
		$desc = "";
		$attrs = $this->sampleAttributes;
		if(!$attrs)
			return $desc;
        foreach($attrs as $idx => $sa){        	
            $name = $sa->attribute->attribute_name;
            $attr = ucfirst($name). ":".$sa->value;
            $short = strlen($attr) > 50 ? substr($attr, 0, 50). "...<br/>":$attr ."<br/>";
            $desc .= $short;
            if($idx > 1)
            	break;             
        }                    
        return $desc."...";
	}

	public function getFullAttrDesc() {
		$desc = "";
        foreach($this->sampleAttributes as $sa){
            $name = $sa->attribute->attribute_name;
            $attr = ucfirst($name). ":".$sa->value."<br/>";       
            $desc .= $attr;             
        }                    
        return $desc;
	}

	public function getDisplayAttr() {
		$num = count($this->sampleAttributes);
		if($num > 3) {
		$display = <<<HTML
		<span class="js-short-$this->id">$this->shortAttrDesc</span>
        		<span class="js-long-$this->id" style="display: none;">$this->fullAttrDesc</span>
HTML;
		        if($this->shortAttrDesc) 
		            $display .= "<a href='#' class='js-desc' data='$this->id'>+</a>";
  		} else {
  		$display = <<<HTML
        		<span class="js-long-$this->id">$this->fullAttrDesc</span>
HTML;
  		}
        return $display;                        
	}

	public static function getCommonList($ids) {
		$crit = new CDbCriteria;
        $crit->join = "join sample on sample.species_id = t.id";
        $crit->addInCondition("sample.id", $ids);
        return Species::model()->findAll($crit);
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
		$criteria->select = 't.*, (SELECT min(d.identifier) from dataset d LEFT JOIN dataset_sample ds ON ds.dataset_id = d.id WHERE ds.sample_id = t.id) as minDoi, (SELECT a.attribute_name from sample_attribute sa LEFT JOIN attribute a ON sa.attribute_id = a.id WHERE sa.sample_id = t.id ORDER BY attribute_name limit 1) as aname';
        		$criteria->with = array('species','datasets');
		$criteria->compare('LOWER(t.name)',strtolower($this->name),true);
 		$criteria->compare('LOWER(species.common_name)', strtolower($this->species_search), true);
		if ($this->dois_search) {
            $sql = <<<EO_SQL
SELECT sample_id FROM dataset_sample
WHERE dataset_id in (
SELECT dataset.id FROM dataset WHERE identifier LIKE '%{$this->dois_search}%'
)
EO_SQL;
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $criteria->addInCondition('t.id' , $command->queryColumn());
		}
		if($this->attr_search) {
			$this->attr_search_lowercase = strtolower($this->attr_search);
            $sql = <<<EO_SQL
SELECT sa.sample_id FROM sample_attribute sa LEFT JOIN attribute a ON sa.attribute_id = a.id
WHERE LOWER(sa.value) LIKE '%{$this->attr_search_lowercase}%' or LOWER(a.attribute_name) LIKE '%{$this->attr_search_lowercase}%'
EO_SQL;
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $criteria->addInCondition('t.id' , $command->queryColumn());
		}

		$sort = new CSort();
		$sort->attributes = array(
			'name' => array(
				'asc' => 'name asc',
				'desc' => 'name desc',
			),
			'species_search' => array(
				'asc' => 'species.common_name asc',
				'desc' => 'species.common_name desc',
			),
			// sort by min(identifeir) order
			'dois_search' => array(
				'asc' => 'minDoi ASC',
				'desc' => 'minDoi DESC',
			),
			// sort by attribute name order
			'attr_search' => array(
				'asc' => 'aname ASC',
				'desc' => 'aname DESC',
			),
		);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort' => $sort,
		));
	}

	/*
	* Convert sample attributes to an array
	*/
	public function sampleAttributesToArray($sa) {
        $i = 0;
        $start_key = 0;
        $start_value = 0;
        $result = array();
        while($i < strlen($sa)){
            if($sa[$i] == '='){ // hitting =, start recording the key and value
                $key = trim(substr($sa , $start_key , $i - $start_key));
                while($sa[$i] != '"'){
                    ++$i;
                }
                ++$i; // get passed the first "
                $start_value = $i ;
                while($sa[$i] != '"'){ // get passed the second "
                    ++$i;
                }
                $result[$key] = substr($sa , $start_value , $i - $start_value);

                while($i < strlen($sa) && $sa[$i] != ','){
                    ++$i;
                }
                $start_key = $i+1;
            }
            ++$i;
        }
        return $result;
    }

	public function embedDiseaseLinkInAttributes($sampleAttributes){
		$attributesArray = $this->sampleAttributesToArray($sampleAttributes);
		if (isset($attributesArray['disease'])) {
			$value = $attributesArray['disease']; // value can be: X:Y:Z == "hepatitis B:DOID:2043"
			$firstColonIndex = strpos($value, ':',0); // first colon
			$secondColonIndex = strpos($value, ':',$firstColonIndex+1); // second colon

			$X = substr($value,0,$firstColonIndex);
			$Y = substr($value,$firstColonIndex+1,$secondColonIndex-$firstColonIndex-1);
			$Z = substr($value,$secondColonIndex+1,strlen($value)-$secondColonIndex-1);
			// generate a link like http://purl.obolibrary.org/obo_DOID_2043

			if ('DOID' == $Y)
				$websiteURL = 'http://purl.obolibrary.org/obo/';
			elseif ('MDR' == $Y)
				$websiteURL = 'http://purl.bioontology.org/ontology/';
            else
                return $sampleAttributes;

            if ($Z == '')
                return $sampleAttributes;

			$link = $websiteURL.$Y.'_'.$Z;

			return str_replace($value,"<a href='$link'>$X</a>",$sampleAttributes);

		} else {
			return $sampleAttributes;
		}
	}

	/**
	 * Get Samples Attributes from sample.id
	 * @return string
	 */
	public function getSampleAttribute()
	{
		$string = '';
		$sampleAttributes = Yii::app()->db->createCommand()
			->select('a.structured_comment_name, sa.value')
			->from('sample_attribute sa')
			->join('attribute a', 'a.id = sa.attribute_id')
			->where('sa.sample_id = :id', array(':id' => $this->id))
			->queryAll();

		// Concat all attribute=value
		foreach ($sampleAttributes as $sampleAttribute) {
			$string .= $sampleAttribute['structured_comment_name'] 
					. '="' . $sampleAttribute['value'] . '"<br/>';
		}

		return $string;
	}

	/**
	 * SphinxSearch search sample
	 * 
	 * @param  array $criteria
	 * @param  array $extraSampleIds
	 * @return array
	 */
	public function sphinxSearch($criteria, $extraSampleIds)
	{
		$s = Utils::newSphinxClient();
		$keyword = isset($criteria['keyword']) ? $criteria['keyword'] : "";
		$result = $s->query($keyword, "sample");

		$matches = array();
		if (isset($result['matches'])) {
			$matches = $result['matches'];
		}

		return array_keys($matches);
	}

	public function getLinkName() {
		$prefix = "SAMPLE:";
		$len = strlen($prefix);
		$name = $this->name;
		$prefer = 'EBI';
		if(!Yii::app()->user->isGuest) {
			$user = User::model()->findByPk(Yii::app()->user->_id);
			if($user)
				$prefer = $user->preferred_link;
		}
		if(substr($name,0,$len) == $prefix) {
			$linkName = substr($name,$len,strlen($name));
			$prefix_text = 'sample';
			$criteria = new CDbCriteria;
			$criteria->compare('lower(prefix)', $prefix_text);
			$criteria->compare('source', $prefer);
			$criteria->limit = 1;
			$prefix_models = Prefix::model()->findAll($criteria);

			if(!$prefix_models)
				$criteria = new CDbCriteria;
				$criteria->compare('lower(prefix)', $prefix_text);
				$criteria->limit = 1;
				$prefix_models = Prefix::model()->findAll($criteria);

			$link = '';
			if($prefix_models) {
				$link = $prefix_models[0]->url.$linkName;
			}

			return CHtml::link($linkName, $link, array('target'=>'_blank'));
		}

		return $name;
	}

	public function getDatasetsByOrder() {
		$criteria = new CDbCriteria;
		$criteria->join = 'LEFT JOIN dataset_sample ds on ds.dataset_id = t.id';
		$criteria->addCondition('ds.sample_id = '.$this->id);
		$criteria->order = 't.identifier asc';
		return Dataset::model()->findAll($criteria);
	}

	public function getListOfDataset() {
		return implode(', ', CHtml::listData($this->datasetsByOrder,'id','identifier'));
	}

	public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetRelatedTableBehavior',
        );
    }
    
    /**
     * Get list content attributes for admin
     * @param boolean $form
     * @return string
     */
    public function getAttributesList($form = false)
    {
        $content = '';
        
        foreach ($this->sampleAttributes as $key => $sampleAttribute) {
            $content .= $sampleAttribute->attribute->structured_comment_name. '="' . $sampleAttribute->value . '"';
            if ($key < count($this->sampleAttributes) - 1) {
                $content .= $form ? "," : "<br/>";
            }
        }
        
        return $content;
    }
}
