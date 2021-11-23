<?php
Yii::import('application.extensions.CAdvancedArBehavior');

class Dataset extends CActiveRecord
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

    public static $statusList = array('ImportFromEM'=>'ImportFromEM',
                         'UserStartedIncomplete'=>'UserStartedIncomplete',
                         'Rejected'=>'Rejected',
                         'Not required'=>'Not required',
                         'AssigningFTPbox'=>'AssigningFTPbox',
                         'UserUploadingData'=>'UserUploadingData',
                         'DataAvailableForReview'=>'DataAvailableForReview',
                         'Submitted'=>'Submitted',
                         'DataPending'=>'DataPending',
                         'Curation'=>'Curation',
                         'AuthorReview'=>'AuthorReview',
                         'Private'=>'Private',
                         'Published' =>'Published',
                         );

    public static $availableStatusList = array('ImportFromEM'=>'ImportFromEM',
        'UserStartedIncomplete'=>'UserStartedIncomplete',
        'Rejected'=>'Rejected',
        'Not required'=>'Not required',
//        'AssigningFTPbox'=>'AssigningFTPbox',
//        'UserUploadingData'=>'UserUploadingData',
//        'DataAvailableForReview'=>'DataAvailableForReview',
        'Submitted'=>'Submitted',
//        'DataPending'=>'DataPending',
        'Curation'=>'Curation',
        'AuthorReview'=>'AuthorReview',
        'Private'=>'Private',
        'Published' =>'Published',
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

        $das = Yii::app()->db->createCommand()
            ->select('a.id')
            ->from('dataset_author')
            ->leftJoin('author a','a.id = author_id')
            ->where('dataset_id = :id', array(':id'=>$this->id))
            ->order(array('rank ASC', 'a.surname ASC', 'a.first_name ASC', 'a.middle_name ASC'))
            ->queryAll();

        $l = array();
        foreach($das as $da) {
            $author = Author::model()->findByPk($da['id']);
            $name = $author->displayName;
            $l[] = CHtml::link($name, "/search/new?keyword=$name&author_id=".$da['id'], array('class'=>'result-sub-links'));
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

    /**
     * Return the name of the curator associated to this dataset
     *
     * If no curator is associated, an empty string is returned.
     * Otherwise, the full name is returned as a string.
     *
     * @return string
     */
    public function getCuratorName(){

      $curator = User::model()->findByPk($this->curator_id);

      if( isset($curator) ) {
        $curator_name = $curator->getFullName();
      }
      else {
        $curator_name = "";
      }

      return $curator_name;

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

    public function getListTitles(){
        $models=Dataset::model()->findAll(array(
                'select'=>'t.title',
                'distinct'=>true,
            ));
        $list=array();
        foreach (array_values($models) as $model){
            $list[] = $model->title;
        }
        return $list;
    }

    public function getDatasetTypes(){
        $list=array();

        foreach (array_values($this->datasetTypes) as $type) {
            $list[]=$type->name;
        }
        return $list;
    }

    public function getImageUrl($default='') {
        if ($this->image) {
            $url = $this->image->url;
            if ($url) {
                if (!strstr($url , 'http://')) {
                    $url = '//' . $url;
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
        return $this->upload_status == "UserStartedIncomplete";
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

    public function getSemanticKeywords() {
        $sKeywordAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'keyword'));

        $sk = DatasetAttributes::model()->findAllByAttributes(array('dataset_id'=>$this->id,'attribute_id'=>$sKeywordAttr->id));

        $list=array();

        foreach (array_values($sk) as $keyword) {
            $list[]=$keyword->value;
        }
        return $list;
    }

    public function getUrlToRedirectAttribute() {

        $criteria = new CDbCriteria(array('order'=>'id ASC'));

        $urlToRedirectAttr = Attribute::model()->findByAttributes(array('attribute_name'=>'urltoredirect'));

        $urlToRedirectDatasetAttribute = DatasetAttributes::model()->findByAttributes(array('dataset_id'=>$this->id,'attribute_id'=>$urlToRedirectAttr->id), $criteria);

        return isset($urlToRedirectDatasetAttribute) ? $urlToRedirectDatasetAttribute->value : '';
    }

    /**
     * toXML(): fucntion tha treturn Datacite XML for this dataset
     * @return Datacite XML 4.0 for this dataset
     */
    public function toXML() {
        $xmlstr = "<?xml version='1.0' ?>\n".
              '<resource xmlns="http://datacite.org/schema/kernel-4"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://datacite.org/schema/kernel-4 http://schema.datacite.org/meta/kernel-4/metadata.xsd"
                >
                </resource>';

        // create the SimpleXMLElement object with an empty <book> element
        $xml = new SimpleXMLElement($xmlstr);

        // <identifier identifierType="DOI">10.5072/example-full</identifier>
        $identifier = $xml->addChild("identifier", Yii::app()->params['mds_prefix']."/".$this->identifier);
        $identifier->addAttribute("identifierType", "DOI");

        //<creators>
        $creators = $xml->addChild("creators");

        // <creator>
        $authors=$this->authors;
        foreach($authors as $author)
        {
            $creator = $creators->addChild('creator');
            $creator->addChild('creatorName',$author->surname." ".$author->middle_name." ". $author->first_name);

            if ( $author->orcid != null ) {
                $name_identifier = $creator->addChild('nameIdentifier',$author->orcid);
                $name_identifier->addAttribute('schemeURI','http://orcid.org/');
                    $name_identifier->addAttribute('nameIdentifierScheme','ORCID');
            }
            if( $author->gigadb_user_id != null ) {
                $user = User::model()->find("id=?", array($author->gigadb_user_id));
                $creator->addChild('affiliation',$user->affiliation);
            }
        }

        //<titles>
        $titles = $xml->addChild("titles");

        //<title xml:lang="en-us">Full DataCite XML Example</title>
        $title = $titles->addChild('title',$this->title);
        $title->addAttribute('xml:lang','en-US','http://www.w3.org/XML/1998/namespace');

        //<publisher>GigaScience Database</publisher>
        $xml->addChild('publisher',$this->publisher->name);

        //<publicationYear>2014</publicationYear>
        $publication_date = new DateTime($this->publication_date);
        $xml->addChild('publicationYear',$publication_date->format('Y'));


        //<subjects>
        $subjects = $xml->addChild("subjects");

        //<subject xml:lang="en-US">dataset type</subject>
        foreach ($this->getDatasetTypes() as $dataset_type) {
            $subject = $subjects->addChild('subject',$dataset_type);
            $subject->addAttribute('xml:lang','en-US','http://www.w3.org/XML/1998/namespace');
        }

        //<subject xml:lang="en-US">keywords</subject>
        foreach ($this->getSemanticKeywords() as $keyword) {
            $subject = $subjects->addChild('subject',$keyword);
            $subject->addAttribute('xml:lang','en-US','http://www.w3.org/XML/1998/namespace');
        }

        //<dates>
    	//	<date dateType="Available">2014-10-17</date>
        $dates = $xml->addChild("dates");
        $date = $dates->addChild('date',$publication_date->format('Y-m-d'));
        $date->addAttribute('dateType','Available');

        //<language>en-us</language>
        $xml->addChild('language','en-US');

        //<resourceType resourceTypeGeneral="Dataset">GigaDB Dataset</resourceType>
        $resource_type = $xml->addChild('resourceType','GigaDB Dataset');
        $resource_type->addAttribute('resourceTypeGeneral','Dataset');

        //<relatedIdentifiers>
        $manuscripts=$this->manuscripts;
        $internal_links=$this->relations;
        
        $fundings=$this->datasetFunders;

        $related_identifiers = $xml->addChild("relatedIdentifiers");

        if ( isset($manuscripts) ){
            foreach($manuscripts as $manuscript){
                $related_identifier = $related_identifiers->addchild("relatedIdentifier", $manuscript->identifier);
                $related_identifier->addAttribute('relatedIdentifierType','DOI');
                $related_identifier->addAttribute('relationType','IsReferencedBy');
            }

        }
        if ( isset($internal_links) ){
            foreach($internal_links as $relation){
                $related_identifier = $related_identifiers->addchild("relatedIdentifier",$relation->related_doi);
                $related_identifier->addAttribute('relatedIdentifierType','DOI');
                $related_identifier->addAttribute('relationType',$relation->relationship->name);
            }

        }

        $funding_References = $xml->addChild("fundingReferences");

        if (isset($fundings)){
            foreach($fundings as $funding){

                $funder =  Funder::model()-> findByAttributes(array('id'=>$funding->funder_id));
                $fundingReference = $funding_References->addChild("fundingReference");
                $fundingReference->addChild('funderName',str_replace(array('&','>','<','"'), array('&amp;','&gt;','&lt;','&quot;'), $funder->primary_name_display));
                $funderidentifier= $fundingReference->addChild('funderIdentifier',$funder->uri);
                $funderidentifier->addAttribute('funderIdentifierType','Crossref Funder ID');
                $fundingReference->addChild('awardNumber',$funding->grant_award);

            }

        }

        //<sizes><size>
        // TODO: use the already installed Byte-Units library to do those size calculation
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($this->dataset_size, 0);
        $pow = floor(($this->dataset_size ? log($this->dataset_size) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $precision=2;

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        $size = round($bytes, $precision) . ' ' . $units[$pow];

        $sizes = $xml->addChild("sizes");
        $sizes->addChild('size',$size);

        //<rightsList>
        $rights_list = $xml->addChild("rightsList");
        $rights = $rights_list->addChild('rights','CC0 1.0 Universal');
        $rights->addAttribute('rightsURI','http://creativecommons.org/publicdomain/zero/1.0/');

        //<descriptions><description xml:lang="en-US" descriptionType="Abstract">
        $descriptions = $xml->addChild("descriptions");
        $description = $descriptions->addChild('description',str_replace(array('&','>','<','"'), array('&amp;','&gt;','&lt;','&quot;'), $this->description));
        $description->addAttribute('xml:lang','en-US','http://www.w3.org/XML/1998/namespace');
        $description->addAttribute('descriptionType','Abstract');


        return $xml->asXML();
    }
}
