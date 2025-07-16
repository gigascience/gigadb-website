<?php

declare(strict_types=1);

Yii::import('application.extensions.CAdvancedArBehavior');

use Ramsey\Uuid\Uuid;

/**
 * This is the model class for table "Dataset".
 *
 * The followings are the available columns in table 'Dataset':
 *
 * @property int         $id
 * @property int         $submitter_id
 * @property int|null    $image_id
 * @property string      $identifier
 * @property string      $title
 * @property string      $description
 * @property int         $dataset_size
 * @property string      $ftp_site
 * @property string      $upload_status
 * @property string|null $excelfile
 * @property string|null $excelfile_md5
 * @property string|null $publication_date
 * @property string|null $modification_date
 * @property int|null    $publisher_id
 * @property string|null $token
 * @property string|null $fairnuse
 * @property int|null    $curator_id
 * @property string|null $manuscript_id
 * @property string|null $handling_editor
 * @property bool        $is_publishable
 * The followings are the available model relations:
 * @property Image|null      $image
 * @property User            $submitter
 * @property Publisher|null  $publisher
 * @property Type[]          $datasetTypes
 * @property Manuscript[]    $manuscripts
 * @property Link[]          $links
 * @property Relation[]      $relations
 * @property DatasetFunder[] $datasetFunders
 */
class Dataset extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Dataset the static model class
     */

    const NAMESPACE = "http://gigadb.org/namespaces/dataset";

    const DATASET_PRIVATE = 'Private';

    const URL_RIS = 'http://data.datacite.org/application/x-research-info-systems/10.5524/';
    const URL_BIBTEXT = 'http://data.datacite.org/application/x-bibtex/10.5524/';
    const URL_TEXT = 'http://data.datacite.org/application/x-datacite+text/10.5524/';

    // Directory names representing ranges of dataset DOIs
    const RANGES = ['104001_105000', '103001_104000', '102001_103000', '101001_102000', '100001_101000'];

    public const ORIGINAL_UPLOAD_STATUS_LIST = [
        'ImportFromEM' => 'ImportFromEM',
        'UserStartedIncomplete' => 'UserStartedIncomplete',
        'Rejected' => 'Rejected',
        'Not required' => 'Not required',
        'Submitted' => 'Submitted',
        'Curation' => 'Curation',
        'AuthorReview' => 'AuthorReview',
        'Private' => 'Private',
        'Published' => 'Published',
    ];

    public const FUW_UPLOAD_STATUS_LIST = [
        'AssigningFTPbox' => 'AssigningFTPbox',
        'UserUploadingData' => 'UserUploadingData',
        'DataAvailableForReview' => 'DataAvailableForReview',
        'DataPending' => 'DataPending',
    ];

    /*
     * List of Many To Many RelationShip
     */

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

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
            array('submitter_id, image_id, publisher_id', 'numerical', 'integerOnly' => true),
            array('dataset_size', 'numerical'),
            array('identifier', 'unique', 'message' => 'The DOI already exists'),
            array('identifier, excelfile_md5', 'length', 'max' => 32),
            array('title', 'length', 'max' => 300),
            array('upload_status', 'length', 'max' => 45),
            array('upload_status', 'in', 'range' => array_merge(self::ORIGINAL_UPLOAD_STATUS_LIST, self::FUW_UPLOAD_STATUS_LIST), 'message' => 'The value is not valid'),
            array('ftp_site', 'length', 'max' => 100),
            array('excelfile', 'length', 'max' => 50),
            array('description, publication_date, modification_date, image_id, fairnuse, types', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, manuscript_id, submitter_id, image_id, identifier, title, description, publisher, dataset_size, ftp_site, upload_status, excelfile, excelfile_md5, publication_date, modification_date', 'safe', 'on' => 'search'),
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
            'authors' => array(self::MANY_MANY, 'Author', 'dataset_author(dataset_id,author_id)', 'order' => 'authors.first_name ASC', ),
            'projects' => array(self::MANY_MANY, 'Project', 'dataset_project(dataset_id,project_id)'),
            'submitter' => array(self::BELONGS_TO, 'User', 'submitter_id'),
            'image' => array(self::BELONGS_TO, 'Image', 'image_id'),
            'samples' => array(self::MANY_MANY, 'Sample', 'dataset_sample(dataset_id,sample_id)', 'order' => 'samples.id DESC'),
            'externalLinks' => array(self::HAS_MANY, 'ExternalLink', 'dataset_id'),
            'datasetTypes' => array(self::MANY_MANY, 'Type', 'dataset_type(dataset_id,type_id)'),
            'files' => array(self::HAS_MANY, 'File', 'dataset_id', 'order' => 'files.id DESC'),
            'relations' => array(self::HAS_MANY, 'Relation', 'dataset_id'),
            'links' => array(self::HAS_MANY, 'Link', 'dataset_id'),
            'manuscripts' => array(self::HAS_MANY, 'Manuscript', 'dataset_id'),
            'publisher' => array(self::BELONGS_TO, 'Publisher', 'publisher_id'),
            'datasetFunders' => array(self::HAS_MANY, 'DatasetFunder', 'dataset_id'),
            'funders' => array(self::HAS_MANY, 'Funder', 'dataset_funder(dataset_id, funder_id)'),
            'datasetLogs' => array(self::HAS_MANY, 'DatasetLog', 'dataset_id'),
            'datasetAttributes' => array(self::HAS_MANY, 'DatasetAttributes', 'dataset_id'),
            'attributes' => array(self::MANY_MANY, 'Attributes', 'dataset_attributes(dataset_id, attribute_id)'),
        );
    }

    public function getPolicy(): ?DatasetAttributes
    {
        $att = Attributes::model()->findByAttributes(array('attribute_name' => Attributes::FUP));
        if (!$att) {
            return null;
        }

        return DatasetAttributes::model()->findByAttributes(array('dataset_id' => $this->id, 'attribute_id' => $att->id));
    }

    /**
     * @return Sample[]
     */
    public function getSamplesInIds(array $ids): array
    {
        $crit = new CDbCriteria();
        $crit->join = "join dataset_sample ds on ds.sample_id = t.id";
        $crit->condition = "ds.dataset_id = :id";
        $crit->params = array(':id' => $this->id);
        $crit->addInCondition("t.id", $ids);

        return Sample::model()->findAll($crit);
    }

    /**
     * @return File[]
     */
    public function getFilesInIds(array $ids): array
    {
        $crit = new CDbCriteria();
        $crit->condition = "dataset_id = :id";
        $crit->params = array(':id' => $this->id);
        $crit->addInCondition("id", $ids);

        return File::model()->findAll($crit);
    }

    public function getPreviousDoi(): ?Dataset
    {
        return Dataset::model()->find(array('condition' => "identifier < :id and upload_status = 'Published'",
                'params' => array(':id' => $this->identifier),
                'order' => 'identifier desc'
        ));
    }

    public function getNextDoi(): ?Dataset
    {
        return Dataset::model()->find(array('condition' => "identifier > :id and upload_status = 'Published'",
                'params' => array(':id' => $this->identifier),
                'order' => 'identifier asc'
        ));
    }

    public static function clearDatasetSession(): void
    {
        $vars = array('dataset', 'images', 'authors', 'projects',
            'links', 'externalLinks', 'relations', 'samples', 'dataset_id', 'identifier', 'filecount',
            'link_database', 'isOld');

        foreach ($vars as $var) {
            unset($_SESSION[$var]);
        }
    }

    public function getAuthorNames(): string
    {
        $das = Yii::app()->db->createCommand()
            ->select('a.id')
            ->from('dataset_author')
            ->leftJoin('author a', 'a.id = author_id')
            ->where('dataset_id = :id', array(':id' => $this->id))
            ->order(array('rank ASC', 'a.surname ASC', 'a.first_name ASC', 'a.middle_name ASC'))
            ->queryAll();

        $l = array();
        foreach ($das as $da) {
            $author = Author::model()->findByPk($da['id']);
            $name = $author->getDisplayName();
            $l[] = CHtml::link($name, "/search/new?keyword=$name&author_id=" . $da['id'], array('class' => 'result-sub-links'));
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
            'title' => Yii::t('app', 'Title'),
            'description' => 'Description',
            'publisher' => 'Publisher',
            'dataset_size' => 'Dataset Size',
            'ftp_site' => 'Ftp Site',
            'upload_status' => 'Upload Status',
            'excelfile' => 'Excelfile',
            'excelfile_md5' => 'Excelfile Md5',
            'publication_date' => Yii::t('app', 'Publication Date'),
            'modification_date' => Yii::t('app', 'Modification Date'),
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

        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('submitter_id', $this->submitter_id);
        $criteria->compare('image_id', $this->image_id);
        $criteria->compare('LOWER(identifier)', strtolower($this->identifier ?: ''), true);
        $criteria->compare('LOWER(title)', strtolower($this->title ?: ''), true);
        $criteria->compare('LOWER(description)', strtolower($this->description ?: ''), true);
        $criteria->compare('LOWER(publisher)', strtolower($this->publisher_id ?: ''), true);
        $criteria->compare('LOWER(dataset_size)', strtolower($this->dataset_size ?: ''), true);
        $criteria->compare('LOWER(ftp_site)', strtolower($this->ftp_site ?: ''), true);
        $criteria->compare('LOWER(upload_status)', strtolower($this->upload_status ?: ''), true);
        $criteria->compare('LOWER(excelfile)', strtolower($this->excelfile ?: ''), true);
        $criteria->compare('LOWER(excelfile_md5)', strtolower($this->excelfile_md5 ?: ''), true);
        $criteria->compare('publication_date', $this->publication_date);
        $criteria->compare('modification_date', $this->modification_date);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
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
    public function getCuratorName()
    {
        $curator = User::model()->findByPk($this->curator_id);

        $curatorName = "";
        if ($curator) {
            $curatorName = $curator->getFullName();
        }

        return $curatorName;
    }

    /**
     * @param $ids
     *
     * @return Type[]
     */
    public static function getTypeList(array $ids): array
    {
        $crit = new CDbCriteria();
        $crit->join = "join dataset_type dt on dt.type_id = t.id";
        $crit->addInCondition("dt.dataset_id", $ids);

        return Type::model()->findAll($crit);
    }

    /**
     * @param array $ids
     *
     * @return Project[]
     */
    public static function getProjectList(array $ids): array
    {
        $crit = new CDbCriteria();
        $crit->join = "join dataset_project dp on dp.project_id = t.id";
        $crit->addInCondition("dp.dataset_id", $ids);
        return Project::model()->findAll($crit);
    }

    /**
     * @param array $ids
     *
     * @return ExternalLinkType[]
     */
    public static function getExtLinkList(array $ids): array
    {
        $crit = new CDbCriteria();
        $crit->join = "join external_link el on el.external_link_type_id = t.id";
        $crit->addInCondition("el.dataset_id", $ids);

        return ExternalLinkType::model()->findAll($crit);
    }

    /**
     * @return array
     */
    public function getListTitles(): array
    {
        $models = Dataset::model()->findAll(array(
                'select' => 't.title',
                'distinct' => true,
            ));

        return array_map(function ($el) {
            return $el->title;
        }, $models);
    }

    /**
     * @return array
     */
    public function getDatasetTypes(): array
    {
        $types = $this->datasetTypes;

        return array_map(function ($el) {
            return $el->name;
        }, $types);
    }

    public function getImageUrl(string $default = ''): string
    {
        if ($this->image) {
            return $this->image->url;
        }

        return $default;
    }

    public static function getFileIdsByDatasetIds(array $datasetIds): array
    {
        $datasetIds = implode(' , ', $datasetIds);
        if (!$datasetIds) {
            return array();
        }

        return  Yii::app()->db->createCommand()
            ->selectDistinct('id')
            ->from('file')
            ->where("dataset_id in ($datasetIds)")
            ->queryColumn();
    }

    /**
     * Get all authors in dataset by dataset id and ordered by rank
     *
     * @return array
     */
    public function getAuthors(): array
    {
        return Yii::app()->db->createCommand()
            ->select('a.id, a.surname, a.first_name, da.rank, a.orcid, a.middle_name, a.custom_name, a.gigadb_user_id')
            ->from('author a')
            ->join('dataset_author da', 'a.id = da.author_id')
            ->where('dataset_id = :id', array(':id' => $this->id))
            ->order('da.rank ASC, a.surname ASC, a.first_name ASC, a.middle_name')
            ->queryAll();
    }
    /**
     * Get all samples in dataset
     * @return array
     */
    public function getSamples(): array
    {
        return Yii::app()->db->createCommand()
                            ->select('s.name, sp.tax_id, sp.common_name, sp.genbank_name')
                            ->from('sample s')
                            ->join('dataset_sample ds', 's.id = ds.sample_id')
                            ->join('species sp', 's.species_id = sp.id')
                            ->where('ds.dataset_id = :id', array(':id' => $this->id))
                            ->queryAll();
    }

    public function getProjects(): array
    {
        return Yii::app()->db->createCommand()
            ->select('p.name, p.url, p.image_location')
            ->from('project p')
            ->join('dataset_project dp', 'p.id = dp.project_id')
            ->where('dp.dataset_id = :id', array(':id' => $this->id))
            ->queryAll();
    }

    public function getExternalLinks(): array
    {
        return Yii::app()->db->createCommand()
            ->select('el.url, elt.name')
            ->from('external_link el')
            ->join('external_link_type elt', 'el.external_link_type_id = elt.id')
            ->where('el.dataset_id = :id', array(':id' => $this->id))
            ->queryAll();
    }

    public function getIsProteomic(): bool
    {
        return (bool) DatasetType::model()->findByAttributes(array('dataset_id' => $this->id,'type_id' => 10));
    }

    public function getIsIncomplete(): bool
    {
        return $this->upload_status === "UserStartedIncomplete";
    }

    public function behaviors()
    {
        return array(
            'ActiveRecordLogableBehavior' => 'application.behaviors.DatasetBehavior',
        );
    }

    public function getIsPublic(): bool
    {
        return $this->upload_status === "Published";
    }

    /**
     * @return Sample[]
     */
    public function getAllSamples(): array
    {
        $criteria = new CDbCriteria();
        $criteria->join = "join dataset_sample ds on ds.sample_id = t.id";
        $criteria->addCondition("ds.dataset_id = " . $this->id);

        return Sample::model()->findAll($criteria);
    }

    public function getShortUrl(): string
    {
        $url = 'dataset/' . $this->identifier;
        return Yii::app()->createAbsoluteUrl($url);
    }

    public function getTypeIds(): array
    {
        $types = $this->datasetTypes;

        return array_map(function ($el) {
            return $el->id;
        }, $types);
    }

    public function getSemanticKeywords(): array
    {
        $sKeywordAttr = Attributes::model()->findByAttributes(array('attribute_name' => 'keyword'));

        $sk = DatasetAttributes::model()->findAllByAttributes(array('dataset_id' => $this->id,'attribute_id' => $sKeywordAttr->id));

        return array_map(function ($el) {
            return $el->value;
        }, $sk);
    }

    public function getUrlToRedirectAttribute(): string
    {
        $criteria = new CDbCriteria(array('order' => 'id ASC'));

        $urlToRedirectAttr = Attributes::model()->findByAttributes(array('attribute_name' => 'urltoredirect'));

        $urlToRedirectDatasetAttribute = DatasetAttributes::model()->findByAttributes(array('dataset_id' => $this->id,'attribute_id' => $urlToRedirectAttr->id), $criteria);

        return isset($urlToRedirectDatasetAttribute) ? $urlToRedirectDatasetAttribute->value : '';
    }

    /**
     * toXML(): function tha return Datacite XML for this dataset
     *
     * @return bool|string XML 4.0 for this dataset
     */
    public function toXML()
    {
        $xmlstr = "<?xml version='1.0' ?>\n" .
            '<resource xmlns="http://datacite.org/schema/kernel-4"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                        xsi:schemaLocation="http://datacite.org/schema/kernel-4 http://schema.datacite.org/meta/kernel-4/metadata.xsd"
                >
                </resource>';

        // create the SimpleXMLElement object with an empty <book> element
        $xml = new SimpleXMLElement($xmlstr);

        // <identifier identifierType="DOI">$mds_prefix/example-full</identifier>
        $identifier = $xml->addChild('identifier', Yii::app()->params['mds_prefix'] . '/' . $this->identifier);
        $identifier->addAttribute('identifierType', 'DOI');

        //<creators>
        $creators = $xml->addChild('creators');

        // <creator>
        $authors = $this->getAuthors();
        foreach ($authors as $author) {
            $creator = $creators->addChild('creator');
            $nameType = strpos($author['surname'], 'Consortium') ? 'Organizational' : 'Personal';
            $fullName = $author['middle_name'] ? $author['first_name'] . ', ' . $author['middle_name'] . ', ' . $author['surname'] : $author['first_name'] . ', ' . $author['surname'];
            $creatorName = $creator->addChild('creatorName', $fullName);
            $creatorName->addAttribute('nameType', $nameType);
            $givenName = $author['middle_name'] ? $author['first_name'] . ', ' . $author['middle_name'] : $author['first_name'];
            $creator->addChild('givenName', $givenName);
            $creator->addChild('familyName', $author['surname']);

            if ($author['orcid'] !== null) {
                $name_identifier = $creator->addChild('nameIdentifier', $author['orcid']);
                $name_identifier->addAttribute('schemeURI', 'http://orcid.org/');
                $name_identifier->addAttribute('nameIdentifierScheme', 'ORCID');
            }
            if ($author['gigadb_user_id'] != null) {
                /** @var User $userModel */
                $userModel = User::model();
                $user = $userModel->find('id=?', array($author['gigadb_user_id']));
                $creator->addChild('affiliation', $user->affiliation);
            }
        }

        //<titles>
        $titles = $xml->addChild('titles');

        //<title xml:lang="en-us">Full DataCite XML Example</title>
        $title = $titles->addChild('title', htmlspecialchars($this->title, ENT_QUOTES, 'UTF-8'));
        $title->addAttribute('xml:lang', 'en-US', 'http://www.w3.org/XML/1998/namespace');

        //<publisher>GigaScience Database</publisher>
        $publisher = $xml->addChild('publisher', $this->publisher->name);
        $publisher->addAttribute('xml:lang', 'en-US', 'http://www.w3.org/XML/1998/namespace');
        $publisher->addAttribute('publisherIdentifier', 'http://doi.org/10.17616/R3TG83');
        $publisher->addAttribute('publisherIdentifierScheme', 're3data');
        $publisher->addAttribute('schemeURI', 'https://www.re3data.org/');

        //<publicationYear>2014</publicationYear>
        $publication_date = null;
        if ($this->publication_date) {
            $publication_date = new DateTime($this->publication_date);
            $xml->addChild('publicationYear', $publication_date->format('Y'));
        }

        //<subjects>
        $subjects = $xml->addChild('subjects');

        //<subject xml:lang="en-US">dataset type</subject>
        foreach ($this->getDatasetTypes() as $dataset_type) {
            $subject = $subjects->addChild('subject', $dataset_type);
            $subject->addAttribute('xml:lang', 'en-US', 'http://www.w3.org/XML/1998/namespace');
        }

        //<subject xml:lang="en-US">keywords</subject>
        foreach ($this->getSemanticKeywords() as $keyword) {
            $subject = $subjects->addChild('subject', $keyword);
            $subject->addAttribute('xml:lang', 'en-US', 'http://www.w3.org/XML/1998/namespace');
        }

        //<dates>
        //  <date dateType="Available">2014-10-17</date>
        $dates = $xml->addChild('dates');
        if ($publication_date) {
            $date = $dates->addChild('date', $publication_date->format('Y-m-d'));
            $date->addAttribute('dateType', 'Available');
        }

        //<language>en-us</language>
        $xml->addChild('language', 'en-US');

        //<resourceType resourceTypeGeneral="Dataset">GigaDB Dataset</resourceType>
        $resource_type = $xml->addChild('resourceType', 'GigaDB Dataset');
        $resource_type->addAttribute('resourceTypeGeneral', 'Dataset');

        //<relatedIdentifiers>
        $manuscripts = $this->manuscripts;
        $links = $this->links;
        $projects = $this->getProjects();
        $externalLinks = $this->getExternalLinks();

        $internal_links = $this->relations;
        $fundings = $this->datasetFunders;

        $related_identifiers = $xml->addChild('relatedIdentifiers');

        foreach ($manuscripts as $manuscript) {
            $related_identifier = $related_identifiers->addchild('relatedIdentifier', $manuscript->identifier);
            $related_identifier->addAttribute('relatedIdentifierType', 'DOI');
            $related_identifier->addAttribute('relationType', 'IsCitedBy');
            $related_identifier->addAttribute('resourceTypeGeneral', 'JournalArticle');
        }

        foreach ($links as $link) {
            if (!$link->is_primary) {
                continue;
            }

            $linkname = explode(':', $link->link);
            $name = $linkname[0];
            /** @var Prefix $prefixModel */
            $prefixModel = Prefix::model();
            $modelurl = $prefixModel->find('lower(prefix) = :p', array(':p' => strtolower($name)));
            $relatedIdentifier = $modelurl ? sprintf('%s%s', $modelurl->url, $linkname[1]) : $linkname[1];
            $related_identifier = $related_identifiers->addchild('relatedIdentifier', htmlspecialchars($relatedIdentifier, ENT_QUOTES, 'UTF-8'));
            $related_identifier->addAttribute('resourceTypeGeneral', 'Dataset');
            $related_identifier->addAttribute('relatedIdentifierType', 'URL');
            $related_identifier->addAttribute('relationType', 'References');
        }

        foreach ($projects as $project) {
            $related_identifier = $related_identifiers->addchild('relatedIdentifier', $project['url']);
            $related_identifier->addAttribute('relatedIdentifierType', 'URL');
            $related_identifier->addAttribute('relationType', 'IsPartOf');
            $related_identifier->addAttribute('resourceTypeGeneral', 'Project');
        }

        foreach ($externalLinks as $externalLink) {
            switch ($externalLink['name']) {
                case 'Github links':
                    $relatedIdentifier = $externalLink['url'];
                    $resourceTypeGeneral = 'Software';
                    $relatedIdentifierType = 'URL';
                    $relationType = 'HasPart';

                    break;
                case 'Protocols.io':
                    $relatedIdentifier = $externalLink['url'];
                    $resourceTypeGeneral = 'Workflow';
                    $relatedIdentifierType = 'DOI';
                    $relationType = 'References';

                    break;

                case '3D Models':
                    $relatedIdentifier = $externalLink['url'];
                    $resourceTypeGeneral = 'Image';
                    $relatedIdentifierType = 'URL';
                    $relationType = 'References';

                    break;
                default:
                    $relatedIdentifier = $externalLink['url'];
                    $resourceTypeGeneral = 'Other';
                    $relatedIdentifierType = 'URL';
                    $relationType = 'References';
            }

            $related_identifier = $related_identifiers->addchild('relatedIdentifier', htmlspecialchars($relatedIdentifier, ENT_QUOTES, 'UTF-8'));
            $related_identifier->addAttribute('relatedIdentifierType', $relatedIdentifierType);
            $related_identifier->addAttribute('relationType', $relationType);
            $related_identifier->addAttribute('resourceTypeGeneral', $resourceTypeGeneral);
        }

        foreach ($internal_links as $relation) {
            $related_identifier = $related_identifiers->addchild('relatedIdentifier', $relation->related_doi);
            $related_identifier->addAttribute('relatedIdentifierType', 'DOI');
            $related_identifier->addAttribute('relationType', $relation->relationship->name);
            $related_identifier->addAttribute('resourceTypeGeneral', 'Other');
        }

        $funding_References = $xml->addChild('fundingReferences');

        foreach ($fundings as $funding) {
            $funder = Funder::model()->findByAttributes(array('id' => $funding->funder_id));
            $fundingReference = $funding_References->addChild('fundingReference');
            $fundingReference->addChild('funderName', str_replace(array('&', '>', '<', '"'), array('&amp;', '&gt;', '&lt;', '&quot;'), $funder->primary_name_display));
            $funderidentifier = $fundingReference->addChild('funderIdentifier', $funder->uri);
            $funderidentifier->addAttribute('funderIdentifierType', 'Crossref Funder ID');
            $fundingReference->addChild('awardNumber', htmlentities($funding->grant_award, ENT_QUOTES, 'UTF-8'));
        }

        //<sizes><size>
        // TODO: use the already installed Byte-Units library to do those size calculation
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($this->dataset_size, 0);
        $pow = floor(($this->dataset_size ? log($this->dataset_size) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $precision = 2;

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);

        $size = round($bytes, $precision) . ' ' . $units[$pow];

        $sizes = $xml->addChild('sizes');
        $sizes->addChild('size', $size);

        //<rightsList>
        $rights_list = $xml->addChild('rightsList');
        $rights = $rights_list->addChild('rights', 'CC0 1.0 Universal');
        $rights->addAttribute('xml:lang', 'en', 'http://www.w3.org/XML/1998/namespace');
        $rights->addAttribute('rightsURI', 'http://creativecommons.org/publicdomain/zero/1.0/');
        $rights->addAttribute('rightsIdentifier', 'CC0 1.0 Universal');

        //<descriptions><description xml:lang="en-US" descriptionType="Abstract">
        $descriptions = $xml->addChild('descriptions');
        $desc = str_replace('<br>', '<br />', $this->description);
        if (!mb_check_encoding($desc, 'UTF-8')) {
            $text = mb_convert_encoding($desc, 'UTF-8');
        }
        $desc = preg_replace('/[^\x09\x0A\x0D\x20-\xD7FF\xE000-\xFFFD]/u', '', $desc);
        $desc = htmlspecialchars($desc, ENT_XML1, 'UTF-8');
        $description = $descriptions->addChild('description', $desc);
        $description->addAttribute('xml:lang', 'en-US', 'http://www.w3.org/XML/1998/namespace');
        $description->addAttribute('descriptionType', 'Abstract');

        return $xml->asXML();
    }

    /**
     * Return a UUID based on the dataset id
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function getUuid(): \Ramsey\Uuid\UuidInterface
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, self::NAMESPACE . "/id/" . $this->id);
    }

    public function getAvailableStatusList(): array
    {
        if (Yii::app()->featureFlag->isEnabled("fuw")) {
            return CMap::mergeArray(self::ORIGINAL_UPLOAD_STATUS_LIST, self::FUW_UPLOAD_STATUS_LIST);
        }
        return self::ORIGINAL_UPLOAD_STATUS_LIST;
    }

    public function nullifyDateValueIfEmpty(): void
    {
        $this->publication_date = $this->publication_date ?: null;
        $this->modification_date = $this->modification_date ?: null;
        $this->fairnuse = $this->fairnuse ?: null;
    }

    public function updateDatasetTypes(array $postDatasetTypes): void
    {
        $actualTypeIdsByDataset = [];
        //fetch types
        $datasetTypeMaps = $this->datasetTypes;
        $command = Yii::app()->db->createCommand();

        foreach ($datasetTypeMaps as $datasetTypeMap) {
            $actualTypeIdsByDataset[] = $typeId = $datasetTypeMap->id;
            if (!in_array($typeId, $postDatasetTypes, true)) {
                $command->delete('dataset_type', 'dataset_id=:dataset_id AND type_id=:type_id ', array(':dataset_id' => $this->id, ':type_id' => $typeId));
            }
        }

        $diffIds = array_diff($postDatasetTypes, $actualTypeIdsByDataset);

        foreach ($diffIds as $typeId) {
            $newDatasetTypeRelationship = new DatasetType();
            $newDatasetTypeRelationship->dataset_id = $this->id;
            $newDatasetTypeRelationship->type_id = $typeId;
            $newDatasetTypeRelationship->save();
        }
    }

    /**
     * replace with the url of the generic image if one removes a custom image but saves the form
     * without replacing it
     *
     * @param CUploadedFile|null $datasetImage
     *
     * @return bool
     */
    public function updateImageAndMetafields(CUploadedFile $datasetImage = null): bool
    {
        /** @var CWebApplication $app */
        $app = Yii::app();
        if ($datasetImage) {
            $this->image = new Image();
            $this->image->attributes = Yii::app()->request->getPost('Image');
            if (!$this->image->write(Yii::$app->cloudStore, $this->getUuid(), $datasetImage)) {
                Yii::log('Error writing file to storage for dataset ' . $this->identifier, 'error');
                $app->user->setFlash('updateError', 'Fail to update your image');

                return false;
            }
        } else {
            if (!$this->image->url && $this->image->id !== Image::GENERIC_IMAGE_ID) {
                $this->image->url = Image::GENERIC_IMAGE_URL;
            }

            if ($this->image->url) {
                $this->image->attributes = Yii::app()->request->getPost('Image');
            }

            $this->image->scenario = 'update';
        }

        if ($this->image->id !== Image::GENERIC_IMAGE_ID && !$this->image->save()) {
            $app->user->setFlash('updateError', 'Fail to update image!');
            Yii::log(print_r($this->getErrors(), true), 'error');

            return false;
        }

        $this->image_id = !$this->image->url ? Image::GENERIC_IMAGE_ID : $this->image->id;

        return true;
    }

    /**
     * @param string      $status
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return Dataset[]
     */
    public function findByStatusAndDate(string $status, string $startDate = null, string $endDate = null): array
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'upload_status = :upload_status';
        $criteria->params = array(':upload_status' => $status);

        if ($startDate) {
            $criteria->condition .= ' AND publication_date >= :start_date';
            $criteria->params[':start_date'] = $startDate;
        }
        if ($endDate) {
            $criteria->condition .= ' AND publication_date <= :end_date';
            $criteria->params[':end_date'] = $endDate;
        }
        $criteria->order = 'publication_date DESC';

        return Dataset::model()->findAll($criteria);
    }
}
