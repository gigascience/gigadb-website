<?php
/**
 * DAO class to retrieve dataset links, and prefixes from storage
 *
 * Because at the layer closer to the view we want to be able to call $link->getFullUrl()
 * It's better to use the CActiveRecord finder methods rather than DAO functions from CDbCommand
 * that use SQL and returns arrays for retrieving the links
 *
 * @param string $doi of the dataset for which to retrieve the information
 * @param CDbConnection The database connection object to interact with the database storage
 * @see DatasetAccessionsInterface.php
 * @uses Link.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetAccessions extends yii\base\BaseObject implements DatasetAccessionsInterface
{
	private $_doi;
	private $_db;

	public function __construct (string $doi, CDbConnection $db_connection)
	{
		parent::__construct();
		$this->_doi = $doi;
		$this->_db = $db_connection;
	}

	public function getDatasetDOI(): string
	{
		return $this->_doi;
	}

	public function getPrimaryLinks(): array
	{
		return $this->getLinks(true);
	}

	public function getSecondaryLinks(): array
	{
		return $this->getLinks(false);
	}

	/**
	 * Prefix are static reference data. We can use DAO methods instead of ActiveRecord for performance
	 * @return array
	 */
	public function getPrefixes(): array
	{
		$sql="select id, prefix, url, source from prefix";
		$command = $this->_db->createCommand($sql);
		$prefix_array = $command->queryAll();
		return $prefix_array;
	}

	/**
	 * Fetch the primary or secondary links from the database
	 *
	 * @param bool $isPrimary
	 * @return array
	 * @uses Link.php
	 */
	private function getLinks(bool $isPrimary): array
	{
		$sql="select * from link where is_primary=:is_primary and dataset_id in (select id from dataset where identifier=:doi)";
		$link_array=Link::model()->findAllBySql($sql, array(":is_primary" => $isPrimary, ":doi" => $this->_doi) );
		return $link_array;
	}
}
?>