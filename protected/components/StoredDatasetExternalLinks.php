<?php

/**
 * DAO class to retrieve the external links associated to a dataset
 *
 *
 * @param int $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetExternalLinks extends DatasetComponents implements DatasetExternalLinksInterface
{
    private $_id;
    private $_db;

    public function __construct(int $dataset_id, CDbConnection $dbConnection)
    {
        parent::__construct();
        $this->_id = $dataset_id;
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
     * retrieve, cache and format external links associated to a dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of external link array map
     */
    public function getDatasetExternalLinks(array $types = null): array
    {
        $results = $this->_db->createCommand()
                                ->select('l.id, dataset_id, url, external_link_type_id, t.name as external_link_type_name')
                                ->from('external_link l')
                                ->join('external_link_type t', 'l.external_link_type_id = t.id')
                                ->where('dataset_id = :id', array(':id' => $this->_id))
                                ->andWhere(array('in','t.name', $types))
                                ->order('l.id')
                                ->queryAll();
        return $results;
    }


    /**
     * Retrieve and cache the types and count for the external links associated to a dataset
     *
     * we need to use a data reader here for binding columns
     * so we can easily have associative arrays (type => count) as a result
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of (type => number of link of that type).
     */
    public function getDatasetExternalLinksTypesAndCount(array $types = null): array
    {
        $results = [];
        $reader = $this->_db->createCommand()
                                ->select('t.name, count(*) as number')
                                ->from('external_link l')
                                ->join('external_link_type t', 'l.external_link_type_id = t.id')
                                ->where('dataset_id = :id', array(':id' => $this->_id))
                                ->andWhere(array('in','t.name', $types))
                                ->group('t.name')
                                ->query();

        $reader->bindColumn(1, $type);
        $reader->bindColumn(2, $count);
        while ($reader->read() !== false) {
            $results[$type] = $count;
        }
        return $results;
    }
}
