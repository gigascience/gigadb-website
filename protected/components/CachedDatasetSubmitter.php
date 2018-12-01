<?php
/**
 * DAO class to retrieve submitter email address from cache
 *
 * @param CCache cache object
 * @param StoredDatasetSubmitter the DAO for which this is a cache adapter
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetSubmitter extends yii\base\BaseObject implements DatasetSubmitterInterface
{
	private $_storedDatasetSubmitter;
	private $_cache;

	public function __construct (CCache $cache, StoredDatasetSubmitter $storedDatasetSubmitter)
	{
		parent::__construct();
		$this->_cache = $cache;
		$this->_storedDatasetSubmitter = $storedDatasetSubmitter;
	}

	public function getDatasetDOI(): string
	{
		return $this->_storedDatasetSubmitter->getDatasetDOI();
	}

	/**
	 * Retrieve from the cache the email address for dataset_id passed through $_storedDatasetSubmitter
	 *
	 * If cache return false (because of expiry or invalidation), we retrieve the email address from the
	 * storage using $_storedDatasetSubmitter and then we set the email address into the cache for 24 hours
	 *
	 * @uses StoredDatasetSubmitter.php
	 * @return string the email address of the submitter of the dataset
	 */
	public function getEmailAddress(): string
	{
		$cachedEmailAddress = $this->_cache->get("dataset_".$this->getDatasetDOI()."_submitterEmailAddress");
		if (null == $cachedEmailAddress) {
			$cachedEmailAddress = $this->_storedDatasetSubmitter->getEmailAddress();
			$this->_cache->set("dataset_".$this->getDatasetDOI()."_submitterEmailAddress", $cachedEmailAddress, 60*60*24);
		}
		return $cachedEmailAddress;
	}
}
?>