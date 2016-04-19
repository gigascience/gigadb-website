<?php
Yii::import('application.extensions.CAdvancedArBehavior');

class Dataset extends MyActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Dataset the static model class
     */
    
    const DATASET_PRIVATE = 'Private';
    
    const URL_RIS = 'http://data.datacite.org/application/x-research-info-systems/10.5524/';
    const URL_BIBTEXT = 'http://data.datacite.org/application/x-bibtex/10.5524/';
    const URL_TEXT = 'http://data.datacite.org/application/x-datacite+text/10.5524/';

    public $dTypes="";
    public $commonNames="";
    public $email;
    public $union;
    public $types;

    public static $statusList = array('Incomplete'=>'Incomplete',
                         'Request'=>'Request',
                         'Uploaded'=>'Uploaded',                                               
                         'Pending'=>'Pending',
                         'Private'=>'Private',
                         'Published'=>'Published'
                         );

    /*
     * List of Many To Many RelationShip
     */

    public $new_ext_acc_mirror;
    public $new_ext_acc_link;

#    public $projectIDs = array();
#    public $authorIDs = array();
#    public $sampleIDs = array();
#    public $datasetTypeIDs = array();

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

#    public function behaviors(){
#          return array( 'CAdvancedArBehavior' => array(
#                'class' => 'application.extensions.CAdvancedArBehavior'));
#    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'dataset';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('submitter_id, identifier, title, dataset_size, ftp_site', 'required'),
            array('submitter_id, image_id, publisher_id', 'numerical', 'integerOnly'=>true),
            array('dataset_size', 'numerical'),
            array('identifier, excelfile_md5', 'length', 'max'=>32),
            array('title', 'length', 'max'=>300),
            array('upload_status', 'length', 'max'=>45),
            array('ftp_site', 'length', 'max'=>100),
            array('excelfile', 'length', 'max'=>50),
            array('description, publication_date, modification_date, image_id, fairnuse, types', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, submitter_id, image_id, identifier, title, description, publisher, dataset_size, ftp_site, upload_status, excelfile, excelfile_md5, publication_date, modification_date', 'safe', 'on'=>'search'),
#            array('projectIDs , sampleIDs , authorIDs , datasetTypeIDs' , 'safe'),
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
            'authors' => array(self::MANY_MANY, 'Author', 'dataset_author(dataset_id,author_id)', 'order'=>'authors.first_name ASC', ),
            'projects' => array(self::MANY_MANY, 'Project', 'dataset_project(dataset_id,project_id)'),
            'submitter' => array(self::BELONGS_TO, 'User', 'submitter_id'),
            'image' => array(self::BELONGS_TO, 'Images', 'image_id'),
            'samples' => array(self::MANY_MANY, 'Sample', 'dataset_sample(dataset_id,sample_id)'),
            'externalLinks' => array(self::HAS_MANY, 'ExternalLink', 'dataset_id'),
            'datasetTypes' => array(self::MANY_MANY, 'Type', 'dataset_type(dataset_id,type_id)'),
            'files' => array(self::HAS_MANY, 'File', 'dataset_id'),
            'relations' => array(self::HAS_MANY, 'Relation', 'dataset_id'),
            'links' => array(self::HAS_MANY, 'Link', 'dataset_id'),
            'manuscripts' => array(self::HAS_MANY, 'Manuscript', 'dataset_id'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'datasetFunders'=>array(self::HAS_MANY, 'DatasetFunder', 'dataset_id'),
            'funders' =>array(self::HAS_MANY, 'Funder', 'dataset_funder(dataset_id, funder_id)'),
            'datasetLogs'=>array(self::HAS_MANY, 'DatasetLog', 'dataset_id'),
            'datasetAttributes' => array(self::HAS_MANY, 'DatasetAttributes', 'dataset_id'),
            'attributes' => array(self::MANY_MANY, 'Attribute', 'dataset_attributes(dataset_id, attribute_id)'), 
        );
    }

    public function getPolicy() {
        $att = Attribute::model()->findByAttributes(array('attribute_name'=>Attribute::FUP));
        if(!$att)
            return null;
        return DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$this->id, 'attribute_id'=>$att->id));
    }

    public function getSamplesInIds($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join dataset_sample ds on ds.sample_id = t.id";
        $crit->condition = "ds.dataset_id = :id";
        $crit->params = array(':id'=>$this->id); 
        $crit->addInCondition("t.id", $ids);             
        return Sample::model()->findAll($crit);
    }

    public function getFilesInIds($ids) {
        $crit = new CDbCriteria;
        $crit->condition = "dataset_id = :id";
        $crit->params = array(':id'=>$this->id); 
        $crit->addInCondition("id", $ids);               
        return File::model()->findAll($crit);
    }

    public function getPreviousDoi() {
        return Dataset::model()->find(array('condition' =>"identifier < :id and upload_status = 'Published'", 
                'params'=>array(':id'=>$this->identifier),
                'order'=>'identifier desc'
        ));
    }

    public function getNextDoi() {
        return Dataset::model()->find(array('condition' =>"identifier > :id and upload_status = 'Published'", 
                'params'=>array(':id'=>$this->identifier),
                'order'=>'identifier asc'
        ));
    }

    public function getCited() {
        $identifier = $this->identifier;
        $phrase = "10.5524/$identifier";
        $citeds = Utils::searchScholar($phrase);
        $sum = 0;
        if($citeds)
            $sum = count($citeds);       
        $url = Yii::app()->params['scholar_query'].$phrase;
        return array('total'=>$sum, 'url'=>$url);
    }

    public function getGoogleScholarLink() {
        return Yii::app()->params['scholar_query']."10.5524/".$this->identifier;
    }

    public function getEPMCLink() {
        return  Yii::app()->params['ePMC_query']."(REF:'10.5524/".$this->identifier."')";
    } 

    public static function clearDatasetSession() {
        $vars = array('dataset', 'images', 'authors', 'projects',
            'links', 'externalLinks', 'relations', 'samples', 'dataset_id', 'identifier', 'filecount',
            'link_database', 'isOld');
        foreach ($vars as $var) {
            unset($_SESSION[$var]);
            //    $_SESSION[$var] = CJSON::decode($dataset_session->$var);
        }

    }

    public function getAuthorNames() {
        $criteria = new CDbCriteria;
        $criteria->join = 'left join author a on a.id = t.author_id';
        $criteria->addCondition('t.dataset_id ='.$this->id);
        $criteria->order= 't.rank ASC, a.surname ASC, a.first_name ASC, a.middle_name ASC';
        $das = DatasetAuthor::model()->findAll($criteria);

        $l = array();
        foreach($das as $da) {
            $author = $da->author;
            $name = $author->displayName;
            $id = $author->id;
            $l[] = CHtml::link($name, "/search/new?keyword=$name&author_id=$id", array('class'=>'result-sub-links'));
        }
        return implode('; ', $l);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'submitter_id' => 'Submitter',
            'image_id' => 'Image',
            'identifier' => 'DOI',
            'title' => Yii::t('app' ,'Title'),
            'description' => 'Description',
            'publisher' => 'Publisher',
            'dataset_size' => 'Dataset Size',
            'ftp_site' => 'Ftp Site',
            'upload_status' => 'Upload Status',
            'excelfile' => 'Excelfile',
            'excelfile_md5' => 'Excelfile Md5',
            'publication_date' => Yii::t('app' ,'Publication Date'),
            'modification_date' => Yii::t('app' , 'Modification Date'),
            'new_image_url' => 'Image URL',
            'new_image_location' => 'Image Location',
            'fairnuse' => 'Fair Use Policy',
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
        $criteria->compare('submitter_id',$this->submitter_id);
        $criteria->compare('image_id',$this->image_id);
        $criteria->compare('LOWER(identifier)',strtolower($this->identifier),true);
        $criteria->compare('LOWER(title)',strtolower($this->title),true);
        $criteria->compare('LOWER(description)',strtolower($this->description),true);
        $criteria->compare('LOWER(publisher)',strtolower($this->publisher_id),true);
        $criteria->compare('LOWER(dataset_size)',strtolower($this->dataset_size),true);
        $criteria->compare('LOWER(ftp_site)',strtolower($this->ftp_site),true);
        $criteria->compare('LOWER(upload_status)', strtolower($this->upload_status),true);
        $criteria->compare('LOWER(excelfile)',strtolower($this->excelfile),true);
        $criteria->compare('LOWER(excelfile_md5)',strtolower($this->excelfile_md5),true);
        $criteria->compare('publication_date',$this->publication_date);
        $criteria->compare('modification_date',$this->modification_date);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function getTypeList($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join dataset_type dt on dt.type_id = t.id";
        $crit->addInCondition("dt.dataset_id", $ids);
        return Type::model()->findAll($crit);
    }

    public static function getProjectList($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join dataset_project dp on dp.project_id = t.id";
        $crit->addInCondition("dp.dataset_id", $ids);
        return Project::model()->findAll($crit);
    }

    public static function getExtLinkList($ids) {
        $crit = new CDbCriteria;
        $crit->join = "join external_link el on el.external_link_type_id = t.id";
        $crit->addInCondition("el.dataset_id", $ids);
        return ExternalLinkType::model()->findAll($crit);
    }

    public static function sphinxSearch($criteria, $extraDatasetIds = array()){
        $s = Utils::newSphinxClient();

        if (count($extraDatasetIds) > 0) {
            $keyword = '';
            $s->setSelect("id as myid");
            $s->SetFilter('myid', $extraDatasetIds);
        } else {
            $keyword=isset($criteria['keyword'])?$criteria['keyword']:"";
        }

        if(isset($criteria['exclude']) && !empty($criteria['exclude'])){
            $s->setSelect("id as myidex");
            $s->setFilter('myidex' , array_filter(explode(',' , $criteria['exclude'])) , true);
        }

        $dataset_type=isset($criteria['dataset_type'])?$criteria['dataset_type']:"";
        $common_name=isset($criteria['common_name'])?$criteria['common_name']:"";
        $project=isset($criteria['project'])?$criteria['project']:"";
        $pubdate_from=isset($criteria['pubdate_from'])?$criteria['pubdate_from']:"";
        $pubdate_to=isset($criteria['pubdate_to'])?$criteria['pubdate_to']:"";
        $moddate_from=isset($criteria['moddate_from'])?$criteria['moddate_from']:"";
        $moddate_to=isset($criteria['moddate_to'])?$criteria['moddate_to']:"";
        $external_link_type=isset($criteria['external_link_type'])?$criteria['external_link_type']:"";
        $pubdate_from_temp=Utils::convertDate($pubdate_from);
        $pubdate_to_temp=Utils::convertDate($pubdate_to);


        $moddate_from_temp=Utils::convertDate($moddate_from);
        $moddate_to_temp=Utils::convertDate($moddate_to);

        # KNN: -86400 to include the from day
        if($pubdate_from_temp && !$pubdate_to_temp){  # Set FromDate, Don't set To Date
            $pubdate_from=$pubdate_from_temp - 86400;
            $pubdate_to=floor(microtime(true));
        }else if(!$pubdate_from_temp && $pubdate_to_temp){ # Set To Date, Dont Set FromDate
            $pubdate_from = 1; # 1 is the very long time ago , near 1970
            $pubdate_to=$pubdate_to_temp;

        }else {
            $pubdate_from=$pubdate_from_temp - 86400;
            $pubdate_to=$pubdate_to_temp;
        }


        if($moddate_from_temp && !$moddate_to_temp){  # Set FromDate, Don't set To Date
            $moddate_from=$moddate_from_temp  - 86400;
            $moddate_to=floor(microtime(true));
        }else if(!$moddate_from_temp && $moddate_to_temp){ # Set To Date, Dont Set FromDate
            $moddate_from = 1;  # 1 is the very long time ago , near 1970
            $moddate_to=$moddate_to_temp;
        }else {
            $moddate_from=$moddate_from_temp - 86400;
            $moddate_to=$moddate_to_temp;
        }


        if(is_array($dataset_type)){
            $s->SetFilter( 'dataset_type_ids', $dataset_type );
        }


        if(is_array($common_name)){
            $s->SetFilter( 'species_ids', $common_name );
        }

        if(is_array($project)){
            $s->SetFilter( 'project_ids', $project );
        }
        if(is_array($external_link_type)){
            $s->SetFilter( 'external_type_ids', $external_link_type );
        }


        if($pubdate_from && $pubdate_to && $pubdate_to > $pubdate_from){
            $s->SetFilterRange('publication_date',$pubdate_from,$pubdate_to);
        }

        if($moddate_from && $moddate_to && $moddate_to > $moddate_from){
            $s->SetFilterRange('modification_date',$moddate_from,$moddate_to);
        }

        $result = $s->query($keyword, "dataset");

        $matches=array();
        if(isset($result['matches'])) {
            $matches=$result['matches'];
        }

        $result=array_keys($matches);
        return $result;
    }

    public function getListTitles(){
        $models=Dataset::model()->findAll(array(
                'select'=>'t.title',
                'distinct'=>true,
            ));
        $list=array();
        foreach ($models as $key=>$model){
            $list[] = $model->title;
        }
        return $list;
    }

    public function getDatasetTypes(){
        $list=array();

        foreach ($this->datasetTypes as $key => $type) {
            $list[]=$type->name;
        }
        return $list;
    }

    public function getImageUrl($default='') {
        if ($this->image) {
            $url = $this->image->url;
            if ($url) {
                if (!strstr($url , 'http://')) {
                    $url = 'http://' . $url;
                }
            } else {
                $url = $this->image->image('image_upload');
            }
            return $url;
        }
        return $default;
    }

    public static function getFileIdsByDatasetIds($datasetIds) {
        $datasetIds = implode(' , ' , $datasetIds);
        if(!$datasetIds) return array();
        $result = Yii::app()->db->createCommand()
            ->selectDistinct('id')
            ->from('file')
            ->where("dataset_id in ($datasetIds)")
            ->queryColumn();
        return $result;
    }

    /**
     * Get all authors in dataset
     * @return array
     */
    public function getAuthor()
    {
        $authors = Yii::app()->db->createCommand()
                            ->select('a.id, a.surname, a.first_name')
                            ->from('author a')
                            ->join('dataset_author da', 'a.id = da.author_id')
                            ->where('dataset_id = :id', array(':id' => $this->id))
                            ->queryAll();

        return $authors ? $authors : array();
    }

    /**
     * Get all samples in dataset
     * @return array
     */
    public function getSamples()
    {
        $samples = Yii::app()->db->createCommand()
                            ->select('s.name, sp.tax_id, sp.common_name, sp.genbank_name')
                            ->from('sample s')
                            ->join('dataset_sample ds', 's.id = ds.sample_id')
                            ->join('species sp', 's.species_id = sp.id')
                            ->where('ds.dataset_id = :id', array(':id' => $this->id))
                            ->queryAll();

        var_dump(count($samples)); die;

        return $samples ? $samples : array();
    }

    public function getIsProteomic() {
        $dt = DatasetType::model()->findByAttributes(array('dataset_id'=>$this->id,'type_id'=>10));
        if($dt) {
            return true;
        }
        return false;
    }

    public function getIsIncomplete() {
        return $this->upload_status == "Incomplete";
    }

    public function behaviors() {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetBehavior',
        );
    }

    public function getIsPublic() {
        return $this->upload_status == "Published";
    }

    public function getAllSamples() {
        $criteria = new CdbCriteria;
        $criteria->join = "join dataset_sample ds on ds.sample_id = t.id";
        $criteria->addCondition("ds.dataset_id = ".$this->id);
        return Sample::model()->findAll($criteria);
    }
    
    /**
     * Get the url with the title slugify
     * 
     * @return string
     */
    public function getDatasetUrl()
    {
        $url = 'dataset/' . $this->identifier . '/' . Convenients::slugify($this->title);
        return Yii::app()->createAbsoluteUrl($url);
    }

    public function getShortUrl() {
        $url = 'dataset/'.$this->identifier;
        return Yii::app()->createAbsoluteUrl($url);
    }

    public function getTypeIds() {
        $types = $this->datasetTypes;
        $ids = array();
        foreach($types as $type) {
            $ids[] = $type->id;
        }
        return $ids;
    }
   
}
