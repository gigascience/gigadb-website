<?php
/**
 * DAO class to retrieve link and metadata of resources connected to a dataset
 *
 *
 * @param int $id of the dataset for which to retrieve the information
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @param GuzzleHttp\Client $webClient web client to fetch citations
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class StoredDatasetConnections extends DatasetComponents implements DatasetConnectionsInterface
{
	private $_id;
	private $_db;
	private $_web;

	public function __construct (int $dataset_id, CDbConnection $dbConnection, GuzzleHttp\Client $webClient)
	{
		parent::__construct();
		$this->_id = $dataset_id;
		$this->_db = $dbConnection;
		$this->_web = $webClient;
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
	 * Retrieval of publications from storage
	 *
	 * Althougth the information is from the manuscript table in the database, the full citation is retrieved over HTTP
	 * So we also need to retrieve the citation at this stage from remote "storage",
	 * so it can be treated like other attributes (cached and formatted).
	 * We also construct the pubmed url using url template from config so it can be cached and formated in other adapters
	 *
	 * @uses \GuzzleHttp\Client
	 * @see http://docs.guzzlephp.org/en/stable/psr7.html?highlight=getbody#body
	 * @return array of string representing the list of peer-reviewed publications associated with the dataset
	*/
	public function getPublications(): array
	{

		$sql = "select id, identifier, pmid, dataset_id from manuscript where dataset_id = :id order by id";
		$command = $this->_db->createCommand($sql);
		$command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
		$rows = $command->queryAll();
		$results = [];
		foreach ($rows as $result) {
			$response = null;
			try {
				$response = $this->_web->request('GET', 'https://doi.org/'. $result['identifier'], [
									    'headers' => [
									        'Accept' => 'text/x-bibliography',
									    ],
									    'connect_timeout' => 30
									]);
			}
			catch(GuzzleException $e) {
				Yii::log( Psr7\str($e->getRequest()) , "error");
			    if ($e->hasResponse()) {
			        Yii::log( Psr7\str($e->getResponse()), "error");
			    }
			}
			catch(GuzzleHttp\Exception\ServerException $se) {
				Yii::log( "{$se->getResponse()->getStatusCode()} {$se->getResponse()->getReasonPhrase()} with https://doi.org/". $result['identifier'], "error");
				Yii::log($se->getTrace(), "debug");
			}
			$result['citation'] = $response !== null ? (string) $response->getBody() : null;
			$result['pmurl'] = null;
			if (null !== $result['pmid']) {
				$result['pmurl'] = preg_replace("/@id/", $result['pmid'], Yii::app()->params['publications']['pubmed']);
			}
			$results[]= $result;
		}
		return $results;
	}

	/**
	 * retrieval of projects
	 *
	 * @return array of string representing the list of projects associated with the dataset
	*/
	public function getProjects(): array
	{
		$sql = "select p.id, p.url, p.name, p.image_location from project p, dataset_project dp where dp.project_id = p.id  and dp.dataset_id=:id order by p.id";
		$command = $this->_db->createCommand($sql);
		$command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
		$results = $command->queryAll();
		return $results;
	}
}
?>
