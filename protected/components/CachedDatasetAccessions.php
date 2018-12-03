<?php
/**
 * DAO class to retrieve dataset links, and prefixes from cache
 *
 * @param CCache $cache object
 * @param DatasetAccessionsInterface $datasetAccessions DAO for which this is a cache adapter.
 * 		  We use PHP object interface for future flexibility (The O and L in SOLID principles)
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetAccessions extends yii\base\BaseObject implements DatasetAccessionsInterface
{
	private $_datasetAccessions;
	private $_cache;

	public function __construct (CCache $cache, DatasetAccessionsInterface $datasetAccessions)
	{
		parent::__construct();
		$this->_cache = $cache;
		$this->_datasetAccessions = $datasetAccessions;
	}

	public function getDatasetDOI(): string
	{
		return $this->_datasetAccessions->getDatasetDOI();
	}

	public function getPrimaryLinks(): array
	{
		$primaryLinks = $this->_cache->get("dataset_".$this->getDatasetDOI()."_accessionsPrimaryLinks");
		if (null == $primaryLinks) {
			$primaryLinks = $this->_datasetAccessions->getPrimaryLinks();
			$this->_cache->set("dataset_".$this->getDatasetDOI()."_accessionsPrimaryLinks", $primaryLinks, 60*60*24 );
		}
		return $primaryLinks;
	}

	public function getSecondaryLinks(): array
	{
		$secondaryLinks = $this->_cache->get("dataset_".$this->getDatasetDOI()."_accessionsSecondaryLinks");
		if (null == $secondaryLinks) {
			$secondaryLinks = $this->_datasetAccessions->getSecondaryLinks();
			$this->_cache->set("dataset_".$this->getDatasetDOI()."_accessionsSecondaryLinks", $secondaryLinks, 60*60*24 );
		}
		return $secondaryLinks;
	}

	public function getPrefixes(): array
	{
		$prefixes = $this->_cache->get("dataset_".$this->getDatasetDOI()."_prefixes");
		if (null == $prefixes) {
			$prefixes = $this->_datasetAccessions->getPrefixes();
			$this->_cache->set("dataset_".$this->getDatasetDOI()."_prefixes", $prefixes, 60*60*24);
		}
		return $prefixes;
	}


}

?>