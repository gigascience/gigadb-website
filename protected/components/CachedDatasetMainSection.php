<?php
/**
 * DAO class to retrieve dataset and associated information from cache
 *
 *
 * @param CCache $cache Cache system to use
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetMainSectionInterface $datasetMainSection the adaptee class to fall back on if no cache variant
 * @see DatasetMainSectionInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetMainSection extends DatasetComponents implements DatasetMainSectionInterface
{
	private $_storedDatasetMainSection;

	public function __construct (CCache $cache, CCacheDependency $cacheDependency, DatasetMainSectionInterface $datasetMainSection)
	{
		parent::__construct();
		$this->_cache = $cache;
		$this->_cacheDependency = $cacheDependency;
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
		$headline = $this->getCachedLocalData( $this->getDatasetId() );
		if( false == $headline ) {
			$headline = $this->_storedDatasetMainSection->getHeadline() ;
			$this->saveLocaldataInCache( $this->getDatasetId(), $headline );
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
		$release_details = $this->getCachedLocalData( $this->getDatasetId() );
		if( false == $release_details ) {
			$release_details = $this->_storedDatasetMainSection->getReleaseDetails();
			$this->saveLocaldataInCache( $this->getDatasetId(), $release_details );
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
		$description =  $this->getCachedLocalData( $this->getDatasetId() );
		if ( false == $description ) {
			$description = $this->_storedDatasetMainSection->getDescription();
			$this->saveLocaldataInCache( $this->getDatasetId(), $description );
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