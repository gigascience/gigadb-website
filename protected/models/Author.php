<?php

declare(strict_types=1);

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
 * @property string|null $custom_name
 * @property integer|null $gigadb_user_id
 * @property string $surname
 *
 * The followings are the available model relations:
 * @property DatasetAuthor[] $datasetAuthors
 */
class Author extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Author the static model class
     */
    public ?string $dois_search = null;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'author';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('surname', 'required'),
            array('gigadb_user_id', 'numerical', 'integerOnly' => true),
            array('gigadb_user_id', 'unique', 'className' => 'Author'),
            array('surname, middle_name, first_name, custom_name', 'length', 'max' => 255),
            array('orcid', 'length', 'max' => 128),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, surname, middle_name, first_name, custom_name,orcid, gigadb_user_id, dois_search', 'safe', 'on' => 'search'),
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
            'datasetAuthors' => array(self::HAS_MANY, 'DatasetAuthor', 'author_id'),
            'datasets' => array(self::MANY_MANY, 'Dataset', 'dataset_author(dataset_id,author_id)')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'surname' => 'Surname',
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
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria();
        $criteria->select = 't.*, (SELECT min(d.identifier) from dataset d LEFT JOIN dataset_author da ON da.dataset_id = d.id WHERE da.author_id = t.id) as minDoi';
        $criteria->compare('id', $this->id);
        $criteria->compare('LOWER(surname)', strtolower($this->surname ?: ''), true);
        $criteria->compare('LOWER(middle_name)', strtolower($this->middle_name ?: ''), true);
        $criteria->compare('LOWER(first_name)', strtolower($this->first_name ?: ''), true);
        $criteria->compare('LOWER(orcid)', strtolower($this->orcid ?: ''), true);
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
            $criteria->addInCondition('t.id', $command->queryColumn());
        }

        $sort = new CSort();
        $sort->attributes = array(
            'surname' => array(
                'asc' => 'surname ASC',
                'desc' => 'surname DESC',
            ),
            'middle_name' => array(
                'asc' => 'middle_name ASC',
                'desc' => 'middle_name DESC',
            ),
            'first_name' => array(
                'asc' => 'first_name ASC',
                'desc' => 'first_name DESC',
            ),
            'orcid' => array(
                'asc' => 'orcid ASC',
                'desc' => 'orcid DESC',
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

    public function getFullAuthor(): string
    {
        return $this->first_name . ' ' . $this->surname . ' - ORCID:' . $this->orcid;
    }

    /**
     * Return first name and surname
     * @return string
     */
    public function getName()
    {
        return $this->surname . ', ' . $this->first_name;
    }

    /**
     * Find an author by > surname . ' ' . first_name
     */
    public function findByCompleteName(string $name): ?Author
    {
        $criteria = new CDbCriteria();
        $criteria->limit = 1;
        $criteria->addSearchCondition("LOWER(surname) || ' ' || LOWER(first_name)", '%' . strtolower($name) . '%', false);
        $result = $this->findAll($criteria);

        return $result ? $result[0] : null;
    }

    public function getAuthorDetails(): ?string
    {
        return preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', "{$this->id}. " . $this->getFirstName() . " " . $this->getMiddleName() . " " . $this->getSurname() . " (Orcid: " . ($this->orcid ? $this->orcid : "n/a") . ")") ;
    }

    public function getDisplayName(): string
    {
        if ($this->custom_name) {
            return $this->custom_name;
        }

        return self::generateDisplayName($this->getSurname(), $this->first_name, $this->middle_name);
    }

    public function getSurname(): string
    {
        return self::generateDisplayName($this->surname, null, null);
    }

    public function getFirstName(): string
    {
        return $this->first_name ? rtrim($this->first_name, ",;  ") : '';
    }

    public function getMiddleName(): string
    {
        return $this->middle_name ? rtrim($this->middle_name, ",;  ") : '';
    }

    public function getInitials(): string
    {
        return self::generateDisplayName(null, $this->first_name, $this->middle_name);
    }

    public static function generateDisplayName(
        ?string $surname = null,
        ?string $first_name = null,
        ?string $middle_name = null
    ): string
    {
        $to_initial_func = function ($value) {
            if (mb_ereg_match("[A-Z]+$", $value) || mb_ereg_match("Jr$", $value)) { //keep asis If it's all initials or is "Jr"
                return $value;
            }
            return mb_substr($value, 0, 1); //otherwise get the first letter. Use mb_* functions to preserve accentuated chars
        };

        $names_array = mb_split("[\s,.]+", $first_name . " " . $middle_name);
        $initials =  implode("", array_map($to_initial_func, $names_array));

        //TODO can surname be null ? and also if first_name and middle_name are null?
        if (!$surname) {
            return $initials ;
        }

        if (!$first_name && !$middle_name) {
            return rtrim($surname, ",;  ") ; //Watch out: after the ";", there is a space AND an invisible non breakable space
        }

        return $surname . " " . $initials ;
    }

    /**
     * @return Dataset[]
     */
    public function getDatasetsByOrder(): array
    {
        $criteria = new CDbCriteria();
        $criteria->join = 'LEFT JOIN dataset_author da on da.dataset_id = t.id';
        $criteria->addCondition('da.author_id = ' . $this->id);
        $criteria->order = 't.identifier asc';

        return Dataset::model()->findAll($criteria);
    }

    public function getListOfDataset(): string
    {
        return implode(', ', CHtml::listData($this->getDatasetsByOrder(), 'id', 'identifier'));
    }

    public static function findAttachedAuthorByUserId(int $user_id): ?Author
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('gigadb_user_id = ' . $user_id) ;

        return Author::model()->find($criteria);
    }

    /**
     * @return array|bool
     * @throws CException
     * @throws CHttpException
     */
    public function getIdenticalAuthors()
    {
        $identicalToObj = Relationship::model()->findByAttributes(array("name" => "IsIdenticalTo"));
        if (!$identicalToObj) {
            Yii::log("Error retrieving the relationship of name 'IsIdenticalTo'", 'error');
            throw new CHttpException(404, Yii::t("Author", "The requested relationship does not exist."));
        }

        $rel_id = $identicalToObj->id;
        $author = $this->id;
        $sql = "select related_author_id as identical from author_rel where author_id=:author_id and relationship_id=:rel_id
        UNION
        select author_id as identical from author_rel where related_author_id=:author_id and relationship_id=:rel_id
        ORDER BY identical";
        $query_result = Yii::app()->db->createCommand($sql)->bindParam(":author_id", $author, PDO::PARAM_STR)->bindParam(":rel_id", $rel_id, PDO::PARAM_STR)->queryAll(false);

        $get_row = function ($row) {
            return (int) $row[0];
        };

        return array_map($get_row, $query_result);
    }

    function mergeAsIdenticalWithAuthor(int $author): bool
    {
        $identicalToObj = Relationship::model()->findByAttributes(array("name" => "IsIdenticalTo"));
        if (!$identicalToObj) {
            Yii::log("Error retrieving the relationship of name 'IsIdenticalTo'", 'error');
            throw new CHttpException(404, Yii::t('Author', 'The requested relationship does not exist.'));
        }

        $authorObj = Author::model()->findByPk($author);
        if (!$authorObj) {
            Yii::log("Error retrieving Author({$author}) to merge with", 'error');
            return false;
        }

        $target_graph = $authorObj->getIdenticalAuthors();
        $target_graph[] = $author;
        $target_count = count($target_graph);

        if (in_array($this->id, $target_graph)) {
            return false;
        }

        $origin_graph = $this->getIdenticalAuthors();
        $origin_graph[] = $this->id;
        $success = true;

        //proc to construct a valid db record for author_rel to pass on to createMultipleInsertCommand
        $id_to_record = function ($origin_id, $target_id, $relationship_id) {
            return ["author_id" => $origin_id, "related_author_id" => $target_id, "relationship_id" => $relationship_id];
        };

        $connection = Yii::app()->db->getSchema()->getCommandBuilder();

        foreach ($origin_graph as $origin_node) {
            $command = $connection->createMultipleInsertCommand('author_rel', array_map(
                $id_to_record,
                array_fill(0, $target_count, $origin_node),
                $target_graph,
                array_fill(0, $target_count, $identicalToObj->id)
            ));
            $inserted_count = $command->execute();
            $success = $success && ((int) $target_count === (int) $inserted_count);
        }

        return $success;
    }


    public function unMerge(): bool
    {
        $outward_edges_from_this_author = new CDbCriteria();
        $outward_edges_from_this_author->addCondition("author_id={$this->id} or related_author_id={$this->id}");
        $outward_edges = AuthorRel::model()->findAll($outward_edges_from_this_author);
        $success = true ;
        foreach ($outward_edges as $edge) {
            $edge_id = $edge->id;
            if ($edge->delete()) {
                Yii::log("success deleting edge {$edge_id}", 'info');
            } else {
                Yii::log("error deleting edge {$edge_id}", 'error');
                $success = false ;
            }
        }

        return $success;
    }

    public function getIdenticalAuthorsDisplayName(): array
    {
        $get_display_name = function ($author_id) {
            $author = Author::model()->findByPk($author_id);
            return !empty($author) ? $author->getDisplayName() : null;
        };
        return array_map($get_display_name, $this->getIdenticalAuthors());
    }

    public function IsIdenticalTo(int $author): bool
    {
        return (int) $this->id === (int) $author || in_array($author, $this->getIdenticalAuthors());
    }
}
