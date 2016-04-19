<?php

/**
 * This is the model class for table "author".
 *
 * The followings are the available columns in table 'author':
 * @property integer $id
 * @property string $name$surname
 * @property string $middle_name
 * @property string $first_name
 * @property string $orcid
 * @property integer $position$gigadb_user_id
 *
 * The followings are the available model relations:
 * @property DatasetAuthor[] $datasetAuthors
 */
class Author extends CActiveRecord {
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Author the static model class
     */
    public $dois_search;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'author';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('surname', 'required'),
            array('gigadb_user_id', 'numerical', 'integerOnly' => true),
            array('surname, middle_name, first_name', 'length', 'max' => 255),
            array('orcid', 'length', 'max' => 128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, surname, middle_name, first_name, orcid, gigadb_user_id, dois_search', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'datasetAuthors' => array(self::HAS_MANY, 'DatasetAuthor', 'author_id'),
            'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_author(dataset_id,author_id)')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'surname' => 'Surname',
            'middle_name' => 'Middle Name',
            'first_name' => 'First Name',
            'orcid' => 'Orcid',
            'gigadb_user_id' => 'Gigadb User',
            'dois_search' => 'DOI(s)',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;
        $criteria->select = 't.*, (SELECT min(d.identifier) from dataset d LEFT JOIN dataset_author da ON da.dataset_id = d.id WHERE da.author_id = t.id) as minDoi';
        $criteria->compare('id', $this->id);
        $criteria->compare('LOWER(surname)', strtolower($this->surname), true);
        $criteria->compare('LOWER(middle_name)', strtolower($this->middle_name), true);
        $criteria->compare('LOWER(first_name)', strtolower($this->first_name), true);
        $criteria->compare('LOWER(orcid)', strtolower($this->orcid), true);
        $criteria->compare('gigadb_user_id', $this->gigadb_user_id);

        if ($this->dois_search) {
            $sql = <<<EO_SQL
SELECT author_id FROM dataset_author
WHERE dataset_id in (
SELECT dataset.id FROM dataset WHERE identifier LIKE '%{$this->dois_search}%'
)
EO_SQL;
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $criteria->addInCondition('t.id' , $command->queryColumn());
        }

        $sort = new CSort();
        $sort->attributes = array(
            'surname' => array(
                'asc'=>'surname ASC',
                'desc'=>'surname DESC',
            ),
            'middle_name' => array(
                'asc'=>'middle_name ASC',
                'desc'=>'middle_name DESC',
            ),
            'first_name' => array(
                'asc'=>'first_name ASC',
                'desc'=>'first_name DESC',
            ),
            'orcid' => array(
                'asc'=>'orcid ASC',
                'desc'=>'orcid DESC',
            ),
            'dois_search' => array(
                'asc' => 'minDoi ASC',
                'desc' => 'minDoi DESC',
            ),
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
        ));
    }

    public function getFullAuthor() {
        //return $this->name . ' - ORCID:' . $this->orcid . ' - RANK:' . $this->rank;
        return $this->first_name . ' ' . $this->surname . ' - ORCID:' . $this->orcid;
    }

    /**
     * Return first name and surname
     * @return string
     */
    public function getName() {
        return $this->surname . ', ' . $this->first_name;
    }

    /**
     * Find an author by > surname . ' ' . first_name
     * @return string
     */
    public function findByCompleteName($name) {

        $criteria = new CDbCriteria;
        $criteria->limit = 1;
        $criteria->addSearchCondition("LOWER(surname) || ' ' || LOWER(first_name)", '%' . strtolower($name) . '%', false);
        $result = $this->findAll($criteria);

        return $result ? $result[0] : false;
    }

    public static function searchAuthor($criteria)
    {
        $keyword = $criteria['keyword'] ? $criteria['keyword'] : '';
        $criteria = new CDbCriteria;
        $criteria->select = 'id';
        $criteria->limit = 1;
        $criteria->addSearchCondition("LOWER(surname) || ' ' || LOWER(first_name)", '%' . strtolower($keyword) . '%', false);
        $result = new CActiveDataProvider('Author', array('criteria' => $criteria));
        
        $data = array();
        foreach ($result->getData() as $author) {
            $data[] = $author->id;
        }

        return $data;
    }

    public function getDisplayName() {
        $first_initial = ($this->first_name)? strtoupper(substr($this->first_name, 0,  1)) : "";
        $name =  $this->surname . ', ' . $first_initial;
        if($this->middle_name) {
            $name .= ', ' . strtoupper(substr($this->middle_name, 0, 1));
        }

        return $name; 
    }

    public function getDatasetsByOrder() {
        $criteria = new CDbCriteria;
        $criteria->join = 'LEFT JOIN dataset_author da on da.dataset_id = t.id';
        $criteria->addCondition('da.author_id = '.$this->id);
        $criteria->order = 't.identifier asc';
        return Dataset::model()->findAll($criteria);
    }

    public function getListOfDataset() {
    return implode(', ', CHtml::listData($this->datasetsByOrder,'id','identifier'));
    }
}
