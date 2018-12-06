<?php
/**
 * DAO class to retrieve dataset and associated information from cache
 *
 *
 * @param CCache $cache Cache system to use
 * @param StoreDatasetMainSection $storedDatasetMainSection the adaptee class to fall back on if no cache variant
 * @see DatasetMainSectionInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetMainSection extends yii\base\BaseObject implements DatasetMainSectionInterface
{
	private $_cache;
	private $_storedDatasetMainSection;

	public function __construct (CCache $cache, DatasetMainSectionInterface $datasetMainSection)
	{
		parent::__construct();
		$this->_cache = $cache;
		$this->_storedDatasetMainSection = $datasetMainSection;
	}

	/**
	 * return the dataset id
	 *
	 * @return int
	 */
	public function getDatasetId(): int
	{
		return $this->_storedDatasetMainSection->getDatasetId();
	}

	/**
	 * return the dataset identifier (DOI)
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string
	{
		return $this->_storedDatasetMainSection->getDatasetDOI();
	}

	/**
	 * for the header containing title, dataset types, release date
	 *
	 * @return array (of string)
	*/
	public function getHeadline(): array
	{
		$headline = $this->_cache->get("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__);
		if( false == $headline ) {
			$headline = $this->_storedDatasetMainSection->getHeadline() ;
			$this->_cache->set("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__, $headline, 60*60*24);
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
		$release_details = $this->_cache->get("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__);
		if( false == $release_details ) {
			$release_details = $this->_storedDatasetMainSection->getReleaseDetails();
			$this->_cache->set("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__, $release_details, 60*60*24);
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
		$description =  $this->_cache->get("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__);
		if ( false == $description ) {
			$description = $this->_storedDatasetMainSection->getDescription();
			$this->_cache->set("dataset_".$this->getDatasetId()."_".__CLASS__."_".__FUNCTION__, $description, 60*60*24);
		}
		return $description;
	}

	/**
	 * for the citation widgets containing links and icon to configurable scholarly search engines
	 *
	 * We delegate straight to $_storedDatasetMainSection as the data is read from config already loaded in memory,
	 * so no need to be cached.
	 *
	 * @param string $search_engine name of citations search engine, default to null
	 * @return array (of string)
	*/
	public function getCitationsLinks(string $search_engine = null): array
	{
		return $this->_storedDatasetMainSection->getCitationsLinks($search_engine);
	}
}
?>