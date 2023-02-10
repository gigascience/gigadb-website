<?php

/**
 * DAO class to retrieve submitter email address from storage
 *
 * @param int dataset id for which to retrieve the submitter's email
 * @param CDbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetSubmitter extends DatasetComponents implements DatasetSubmitterInterface
{
    private $_id;
    private $_db;

    public function __construct(int $id, CDbConnection $db_connection)
    {
        parent::__construct();
        $this->_id =  $id;
        $this->_db = $db_connection;
    }

    public function getDatasetID(): int
    {
        return $this->_id;
    }
    public function getDatasetDOI(): string
    {
        return $this->getDOIfromId($this->_db, $this->_id);
    }

    public function getEmailAddress(): string
    {
        $sql = "select email from gigadb_user where id in (select submitter_id from dataset where id=:id)";
        $command = $this->_db->createCommand($sql);
        $command->bindParam(":id", $this->_id, PDO::PARAM_INT);
        $result_array = $command->queryRow();
        if (isset($result_array)) {
            return $result_array['email'];
        }
        return "";
    }
}
