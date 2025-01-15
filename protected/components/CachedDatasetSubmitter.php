<?php

/**
 * DAO class to retrieve submitter email address from cache
 *
 * @param ICache cache object
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetSubmitterInterface $datasetSubmitter the DAO for which this is a cache adapter
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetSubmitter extends DatasetComponents implements DatasetSubmitterInterface
{
    private $_storedDatasetSubmitter;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetSubmitterInterface $datasetSubmitter)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_storedDatasetSubmitter = $datasetSubmitter;
    }

    public function getDatasetID(): int
    {
        return $this->_storedDatasetSubmitter->getDatasetID();
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
        $cachedEmailAddress = $this->getCachedLocalData($this->getDatasetId());
        if (null == $cachedEmailAddress) {
            $cachedEmailAddress = $this->_storedDatasetSubmitter->getEmailAddress();
            $this->saveLocalDataInCache($this->getDatasetId(), $cachedEmailAddress);
        }
        return $cachedEmailAddress;
    }
}
