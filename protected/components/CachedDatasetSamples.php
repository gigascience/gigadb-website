<?php
/**
 * DAO class to retrieve from cache the samples associated to a dataset
 *
 * @param CCache $cache Cache system to use
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetSamplesInterface $datasetSamples the adaptee class to fall back on if no cache variant
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetSamples extends DatasetComponents implements DatasetSamplesInterface
{
	private $_storedDatasetSamples;

	public function __construct (CCache $cache, CCacheDependency $cacheDependency, DatasetSamplesInterface $datasetSamples)
	{
		parent::__construct();
		$this->_cache = $cache;
		$this->_cacheDependency = $cacheDependency;
		$this->_storedDatasetSamples = $datasetSamples;
	}

	/**
	 * return the dataset id
	 *
	 * @return int
	 */
	public function getDatasetId(): int
	{
		return $this->_storedDatasetSamples->getDatasetId();
	}

	/**
	 * return the dataset identifier (DOI)
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string
	{
		return $this->_storedDatasetSamples->getDatasetDOI();
	}


	/**
	 * retrieve from cache samples associated to a dataset
	 *
	 * @return array of samples array maps
	 */
	public function getDatasetSamples(): array
	{
		$samples =  $this->getCachedLocalData( $this->getDatasetId() );
		if (false == $samples) {
			$samples = $this->_storedDatasetSamples->getDatasetSamples();
			$this->saveLocaldataInCache( $this->getDatasetId(), $samples );
		}
		return $samples;
	}

}

?>