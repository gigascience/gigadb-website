<?php

/**
 * This is the model class for table "author".
 *
 * The followings are the available columns in table 'author':
 * @property integer $id
 * @property string $surname
 * @property string $middle_name
 * @property string $first_name
 * @property string $orcid
 * @property integer $position
 * @property integer $gigadb_user_id
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
            array('first_name, surname', 'required'),
            array('gigadb_user_id', 'numerical', 'integerOnly' => true),
            array('gigadb_user_id', 'unique', 'className' => 'Author'),
            array('surname, middle_name, first_name, custom_name', 'length', 'max' => 255),
            array('orcid', 'length', 'max' => 128),
            array('orcid', 'match', 'pattern' => '/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}$/'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, surname, middle_name, first_name, custom_name,orcid, gigadb_user_id, dois_search', 'safe', 'on' => 'search'),
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
            'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_author(dataset_id,author_id)'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'surname' => 'Last Name',
            'middle_name' => 'Middle Name',
            'first_name' => 'First Name',
            'custom_name' => 'Display Name',
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

    public function getAuthorDetails() {
        return preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', "{$this->id}. " . $this->getFirstName() ." ". $this->getMiddleName() . " " . $this->getSurname() . " (Orcid: " . ($this->orcid ? $this->orcid: "n/a") .")") ;
    }

    public function getDisplayName() {

        if (null != $this->custom_name) {
            return $this->custom_name;
        }
        else {
            return self::generateDisplayName($this->getSurname(), $this->first_name, $this->middle_name);
        }

    }

    public function getSurname() {

        return self::generateDisplayName($this->surname, null, null);
    }

    public function getFirstName() {

        return rtrim($this->first_name,",;  ");
    }

    public function getMiddleName() {

        return rtrim($this->middle_name,",;  ");
    }

    public function getInitials() {

        return self::generateDisplayName(null, $this->first_name, $this->middle_name);
    }

    public static function generateDisplayName($surname, $first_name, $middle_name) {

        $to_initial_func = function($value) {
            if( mb_ereg_match("[A-Z]+$", $value) || mb_ereg_match("Jr$", $value) ) { //keep asis If it's all initials or is "Jr"
                return $value;
            }
            return mb_substr($value,0,1); //otherwise get the first letter. Use mb_* functions to preserve accentuated chars
        };

        $names_array = mb_split("[\s,.]+", $first_name ." ". $middle_name);
        $initials =  implode("", array_map($to_initial_func, $names_array));

        if( null === $surname ) {
            return $initials ;
        }
        else if ( null === $first_name && null === $middle_name) {
            return rtrim($surname,",;  ") ; //Watch out: after the ";", there is a space AND an invisible non breakable space
        }
        else {
            return $surname . " " . $initials ;
        }


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

    public static function findAttachedAuthorByUserId($user_id) {
        $criteria = new CDbCriteria;
        $criteria->addCondition('gigadb_user_id = '.$user_id) ;
        return Author::model()->find($criteria);
    }

    public function getIdenticalAuthors() {
        $identicalToObj = Relationship::model()->findByAttributes(array("name"=>"IsIdenticalTo"));
        if(null == $identicalToObj){
            Yii::log("Error retrieving the relationship of name 'IsIdenticalTo'",'error');
            // print_r("Error retrieving the relationship of name 'IsIdenticalTo'");
            return false;
        }
        $rel_id = $identicalToObj->id;
        $author = $this->id;
        $sql = "select related_author_id as identical from author_rel where author_id=:author_id and relationship_id=:rel_id
        UNION
        select author_id as identical from author_rel where related_author_id=:author_id and relationship_id=:rel_id
        ORDER BY identical";
        $query_result = Yii::app()->db->createCommand($sql)->bindParam(":author_id",$author,PDO::PARAM_STR)->bindParam(":rel_id",$rel_id,PDO::PARAM_STR)->queryAll(false);
        // var_dump($query_result);
        $get_row = function ($row) {
            return (int) $row[0];
        };
        return array_map($get_row,$query_result);
    }

    function mergeAsIdenticalWithAuthor($author) {
        $identicalToObj = Relationship::model()->findByAttributes(array("name"=>"IsIdenticalTo"));
        if(null == $identicalToObj){
            Yii::log("Error retrieving the relationship of name 'IsIdenticalTo'",'error');
            // print_r("Error retrieving the relationship of name 'IsIdenticalTo'");
            return false;
        }

        $authorObj = Author::model()->findByPk($author);
        if(null == $authorObj){
            Yii::log("Error retrieving Author({$author}) to merge with",'error');
            // print_r("Error retrieving Author({$author}) to merge with");
            return false;
        }
        else {
            $target_graph = $authorObj->getIdenticalAuthors();
            $target_graph[] = $author;
            $target_count = count($target_graph);
        }

        if( in_array($this->id, $target_graph) ) {
            return false;
        }

        $origin_graph = $this->getIdenticalAuthors();
        $origin_graph[] = $this->id;
        $success = true;

        //proc to construct a valid db record for author_rel to pass on to createMultipleInsertCommand
        $id_to_record = function ($origin_id, $target_id, $relationship_id) {
            return ["author_id"=> $origin_id, "related_author_id"=>$target_id, "relationship_id"=>$relationship_id];
        };

        $connection = Yii::app()->db->getSchema()->getCommandBuilder();

        foreach ($origin_graph as $origin_node) {

            $command = $connection->createMultipleInsertCommand('author_rel', array_map( $id_to_record,
                    array_fill(0, $target_count, $origin_node),
                    $target_graph,
                    array_fill(0, $target_count, $identicalToObj->id)
                )
            );
            $inserted_count = $command->execute();
            $success = $success && ( $target_count == $inserted_count ? true : false );

            // foreach ($target_graph as $target_node) {

            //     $author_rel = new AuthorRel();
            //     $author_rel->author_id = $origin_node;
            //     $author_rel->related_author_id = $target_node ;
            //     $author_rel->relationship_id = $identicalToObj->id ;

            //     if($author_rel->save()) {
            //         Yii::log("Success creating a new AuthorRel({$origin_node},{$target_node})",'info');
            //         // print_r("Success creating a new AuthorRel({$origin_node},{$target_node})");
            //         $success = $success && true;
            //     }
            //     else {
            //         Yii::log("Error creating a new AuthorRel({$origin_node},{$target_node})",'error');
            //         // print_r("Error creating a new AuthorRel({$origin_node},{$target_node})");
            //         $success = $success && false;
            //     }

            // }
        }

        return $success;


    }


    public function unMerge() {
        $outward_edges_from_this_author = new CDbCriteria;
        $outward_edges_from_this_author->addCondition("author_id={$this->id} or related_author_id={$this->id}");
        $outward_edges = AuthorRel::model()->findAll($outward_edges_from_this_author);
        $success = true ;
        foreach($outward_edges as $edge) {
            $edge_id = $edge->id;
            if( $edge->delete() ) {
                // var_dump(AuthorRel::model()->findByPk($edge_id));
                Yii::log("success deleting edge {$edge_id}",'info');
                // print_r("success deleting edge {$edge_id}\n");
                $success = $success && true ;
            }
            else {
                // var_dump($edge->getErrors());
                Yii::log("error deleting edge {$edge_id}",'error');
                // print_r("error deleting edge {$edge_id}\n");
                $success = $success && false ;
            }
        }

        return $success;

    }

    public function getIdenticalAuthorsDisplayName( ) {
        $get_display_name = function ($author_id) {
            $author = Author::model()->findByPk($author_id);
            return !empty($author)?$author->getDisplayName():null;
        };
        return array_map($get_display_name,$this->getIdenticalAuthors());
    }

    public function IsIdenticalTo($author) {
        return $this->id == $author || in_array($author,$this->getIdenticalAuthors());
    }

    public function loadByData($data)
    {
        if (isset($data['first_name'])) {
            $this->first_name = $data['first_name'];
        }
        if (isset($data['middle_name'])) {
            $this->middle_name = $data['middle_name'];
        }
        if (isset($data['last_name'])) {
            $this->surname = $data['last_name'];
        }
        if(isset($data['orcid'])) {
            $this->orcid = $data['orcid'];
        }
    }

    public function loadByCsvRow($row)
    {
        if (isset($row[0])) {
            $this->first_name = $row[0];
        }
        if (isset($row[1])) {
            $this->middle_name = $row[1];
        }
        if (isset($row[2])) {
            $this->surname = $row[2];
        }
        if(isset($row[3])) {
            $this->orcid = $row[3];
        }
    }
}
