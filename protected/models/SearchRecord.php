<?php

/**
 * This is the model class for table "search".
 *
 * The followings are the available columns in table 'search':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property string $query
 *
 * The followings are the available model relations:
 * @property GigadbUser $user
 */
class SearchRecord extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Search the static model class
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
		return 'search';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, name, query', 'required'),
			array('user_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, name, query', 'safe', 'on'=>'search'),
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
			'user' => array(self::BELONGS_TO, 'User', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'name' => 'Name',
			'query' => 'Query',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('query',$this->query,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function getResultDetail() {
		if(!$this->result)
			return '';
		$r = CJSON::decode($this->result);
		$dataset_count = (isset($r['datasets']))? count($r['datasets']) : 0 ;
		$sample_count = (isset($r['samples']))? count($r['samples']) : 0 ;
		$file_count = (isset($r['files']))? count($r['files']) : 0;

		return $dataset_count . " datasets, " . $sample_count . " samples, " . $file_count . " files";
	}

	public function getSearchPage() {
		if(!$this->name)
			return '#';
		$q = CJSON::decode($this->query);
		return Yii::app()->createUrl('/search/new', $q);
	}


	public function convertCriteria($getkeyword=false){
		$result="";

		$json=json_decode($this->query,true);
		foreach ($json as $key => $query) {
			if(!empty($query) && ($key!="keyword" || ($key=="keyword" && $getkeyword))){
				if(is_array($query)){
					$result.=$this->convertKey($key)." : ".$this->convertUnit($key,$query,$json)."<br>";
				}else {
					if($key!="size_from_unit" && $key!="size_to_unit"){
						$result.=$this->convertKey($key)." : ".$this->convertUnit($key,$query,$json)."<br>";
					}
				}
				
			}
		}
		return $result;
	}

	private function convertKey($key){
		$array=array(
			'pubdate_from' => 'Publication Date From',
			'pubdate_to' => 'Publication Date To',
			'moddate_from' => 'Modification Date From',
			'moddate_to' => 'Modification Date To',
			'reldate_from' => 'Release Date From',
			'reldate_to' => 'Release Date To',
			'size_from' => 'Size From',
			'size_to' => 'Size To',
		);

		if(isset($array[$key])){
			return $array[$key];
		}else if(stristr($key, "_")){
			$temp = explode("_", $key);
			$result="";
			foreach ($temp as $key => $value) {
				$result.=ucfirst($value)." ";
			}
			return $result;
		}else {
			return ucfirst($key);
		}
	}

	private function convertUnit($key,$query,$array){
		if($key=="dataset_type"){
			$types=Type::getListTypes();
			return $this->convertResult($query,$types);
		}

		if($key=="publisher"){
			$publishers = Publisher::getListPublishers();
			return $this->convertResult($query,$publishers);
		}

		if($key=="common_name"){
			$common_names = Species::getListCommonNames();
			return $this->convertResult($query,$common_names);
		}

		if($key=="project"){
			$projects= Project::getListProjects();
			return $this->convertResult($query,$projects);
		}

		if($key=="external_link_type"){
			$external_link_types= ExternalLinkType::getListTypes();
			return $this->convertResult($query,$external_link_types);

		}

		if($key=="size_from"){
			return $query." ".$this->convertSizeUnit($array['size_from_unit']);

		}

		if($key=="size_to"){
			return $query." ".$this->convertSizeUnit($array['size_to_unit']);
		}

		if(is_array($query)){
			return implode(",", $query);
		}else {
			return $query;
		}

	}

	private function convertSizeUnit($id){
		$array=array("1"=>"KB","2"=>"MB","3"=>"GB","4"=>"TB");
		if(isset($array[$id])){
			return $array[$id];
		}else {
			return "Unknown";
		}

	}

	private function convertResult($listId,$listValues){
			$result="";
			foreach ($listId as $key => $value) {
				if(empty($result)){
					$result.=$listValues[$value];
				}else {
					$result.=", ".$listValues[$value];
				}
			}
			return $result;

	}
}