<?php
/**
 * DAO class to retrieve related dataset and keywords for a given dataset
 *
 *
 * @param string $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @see DatasetMainSectionInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetConnections extends DatasetComponents implements DatasetConnectionsInterface
{
	private $_id;
	private $_db;

	public function __construct (int $dataset_id, CDbConnection $dbConnection)
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
	 * retrieval of related datasets
	 *
	 * @param string optionally pass the list of types of relations to retrieve, otherwise retrieve them all
	 * @return array of string representing the dataset headline attributes
	*/
	public function getRelations(string $relationship_type = null): array
	{
		$filter = "";
		if( !empty($relationship_type) ) {
			$filter = "and rs.name = :filter_by";
		}
		$sql="select r.dataset_id as dataset_id, dd.identifier as dataset_doi, d.id as related_id, related_doi, rs.name as relationship
    from relation r, dataset d, relationship rs, dataset as dd
    where
    r.related_doi=d.identifier
    and rs.id = r.relationship_id
    and r.dataset_id=:id
    and r.dataset_id = dd.id
    $filter
    order by dataset_id, related_id;";
		$command = $this->_db->createCommand($sql);
		$command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
		if( $filter ) {
			$command->bindParam( ":filter_by", $relationship_type, PDO::PARAM_STR );
		}
		$results = $command->queryAll();
		return $results;
	}

	/**
	 * retrieval of keywords
	 *
	 * @todo
	 * @return array of string representing the dataset headline attributes
	*/
	public function getKeywords(): array
	{
		return [];
	}
}
?>