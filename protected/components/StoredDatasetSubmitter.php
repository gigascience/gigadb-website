<?php
/**
 * DAO class to retrieve submitter email address from storage
 *
 * @param int dataset id for which to retrieve the submitter's email
 * @param CDbConnection The database connection object to interact with the database storage
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetSubmitter extends yii\base\BaseObject implements DatasetSubmitterInterface
{

	private $_doi;
	private $_db;

	public function __construct (string $doi, CDbConnection $db_connection)
	{
		parent::__construct();
		$this->_doi =  $doi;
		$this->_db = $db_connection;
	}

	public function getDatasetDOI(): string
	{
		return $this->_doi;
	}

	public function getEmailAddress(): string
	{
		$sql="select email from gigadb_user where id in (select submitter_id from dataset where identifier=:doi)";
		$command = $this->_db->createCommand($sql);
		$command->bindParam(":doi", $this->_doi, PDO::PARAM_INT);
		$result_array = $command->queryRow();
		if( isset($result_array) ) {
			return $result_array['email'];
		}
		return "";
	}
}