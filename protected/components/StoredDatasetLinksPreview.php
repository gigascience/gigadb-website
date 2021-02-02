<?php


class StoredDatasetLinksPreview extends DatasetComponents
{
    private $_id;
    private $_db;

    public function __construct (int $id, CDbConnection $db_connection)
    {
        parent::__construct();
        $this->_id = $id;
        $this->_db = $db_connection;
    }

    /**
     * return the dataset ID
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


}