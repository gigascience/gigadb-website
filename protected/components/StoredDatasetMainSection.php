<?php

/**
 * DAO class to retrieve dataset and associated information from storage
 *
 *
 * @param int $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetMainSection extends DatasetComponents implements DatasetMainSectionInterface
{
    private $_id;
    private $_db;

    public function __construct(int $id, CDbConnection $dbConnection)
    {
        parent::__construct();
        $this->_id = $id;
        $this->_db = $dbConnection;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_id;
    }
    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->getDOIfromId($this->_db, $this->_id);
    }

    /**
     * for the header containing title, dataset types, release date
     *
     * @return array (of string)
    */
    public function getHeadline(): array
    {
        $sql = "select title, publication_date as release_date, t.name as types from dataset d, dataset_type dt, type t where d.id = :id and d.id = dt.dataset_id and dt.type_id = t.id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result_array = $command->queryAll();
        $headline = [];
        if (!empty($result_array)) {
            $headline['title'] = $result_array[0]['title'];
            $headline['types'] = [];
            foreach ($result_array as $row) {
                array_push($headline['types'], $row['types']);
            }
            $headline['types'] = array_unique($headline['types']);
            $headline['release_date'] = $result_array[0]['release_date'];
        }
        return $headline;
    }
    /**
     * for the release panel containing author names, title, publisher name and release year and DOI badge
     *
     * @return array (of string)
    */
    public function getReleaseDetails(): array
    {
        $release_details = [];
        $doi_prefix = Yii::app()->params['mds_prefix'];

        $author_sql = "select a.id, a.surname, a.first_name, a.middle_name, a.custom_name from author a, dataset_author da, dataset d where a.id=da.author_id and d.id = da.dataset_id and d.id=:id order by rank ASC, a.surname ASC, a.first_name ASC, a.middle_name ASC";
        $command = $this->_db->createCommand($author_sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $authors_result = $command->queryAll();
        if (!empty($authors_result)) {
            $release_details['authors'] = [];
            foreach ($authors_result as $author) {
                array_push($release_details['authors'], $author);
            }
        }

        $publishing_sql = "select identifier, to_char(publication_date,'YYYY') as release_year, title, publisher.name as publisher_name from dataset, publisher where dataset.id = :id and publisher_id = publisher.id";
        $command = $this->_db->createCommand($publishing_sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $publishing_result = $command->queryRow();
        if (!empty($publishing_result)) {
            $release_details['release_year'] =  $publishing_result['release_year'];
            $release_details['dataset_title'] =  $publishing_result['title'];
            $release_details['publisher'] =  $publishing_result['publisher_name'];
            $release_details['full_doi'] =  $doi_prefix . "/" . $publishing_result['identifier'];
        }

        return $release_details;
    }
    /**
     * for the article body containing the description
     *
     * @return array (of string)
    */
    public function getDescription(): array
    {
        $sql = "select description from dataset where id = :id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result = $command->queryRow();
        if (!empty($result)) {
            return $result;
        }
        return [];
    }

    /**
     * for the citation widgets containing links and icon to configurable scholarly search engines
     *
     * @return array (of string)
    */
    public function getCitationsLinks(): array
    {
        $doi_prefix = Yii::app()->params['mds_prefix'];
        $citations = Yii::app()->params['citations'];

        $citationLinks = [] ;

        $citationLinks['services'] = $citations['services'];
        $citationLinks['urls'] = preg_replace("/@id/", $doi_prefix . "/" . $this->getDatasetDOI(), $citations['urls']);
        $citationLinks['images'] = $citations['images'];
        return $citationLinks;
    }

    /**
     * Fetch keywords associated with a dataset
     *
     */
    public function getKeywords(): array
    {
        $sql = "select value from dataset_attributes da, attribute a where da.attribute_id = a.id and a.attribute_name='keyword' and da.dataset_id=:id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result = $command->queryAll();
        $flatten = function ($row) {
            return $row['value'];
        };
        return array_map($flatten, $result);
    }

    /**
     * Fetch the history of changes made to the dataset
     *
     */
    public function getHistory(): array
    {

        $sql = "select id, dataset_id, message, created_at, model, model_id, url from dataset_log where dataset_id=:id ";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result = $command->queryAll();
        return $result;
    }

    /**
     * Fetch the Funding data for to the dataset
     *
     */
    public function getFunding(): array
    {
        $sql = "select df.id, dataset_id, primary_name_display as funder_name,grant_award, comments, awardee
		from dataset_funder df, funder_name f where df.funder_id = f.id and df.dataset_id=:id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result = $command->queryAll();
        return $result;
    }
}
