<?php

/**
 * DAO class to retrieve dataset links, and prefixes from cache
 *
 * @param ICache $cache object
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetAccessionsInterface $datasetAccessions DAO for which this is a cache adapter.
 *        We use PHP object interface for future flexibility (The O and L in SOLID principles)
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetAccessions extends DatasetComponents implements DatasetAccessionsInterface
{
    private $_datasetAccessions;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetAccessionsInterface $datasetAccessions)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_datasetAccessions = $datasetAccessions;
    }

    /**
     * the database id of dataset is the internal input variable for retrieving and presenting dataset accessions
     *
     * @return int dataset id
     */
    public function getDatasetId(): int
    {
        return $this->_datasetAccessions->getDatasetId();
    }

    /**
     * external facing identifier for dataset
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->_datasetAccessions->getDatasetDOI();
    }

    public function getPrimaryLinks(): array
    {
        $primaryLinks = $this->getCachedLocalData($this->getDatasetId());
        if (null == $primaryLinks) {
            $primaryLinks = $this->_datasetAccessions->getPrimaryLinks();
            $this->saveLocalDataInCache($this->getDatasetId(), $primaryLinks);
        }
        return $primaryLinks;
    }

    public function getSecondaryLinks(): array
    {
        $secondaryLinks = $this->getCachedLocalData($this->getDatasetId());
        if (null == $secondaryLinks) {
            $secondaryLinks = $this->_datasetAccessions->getSecondaryLinks();
            $this->saveLocalDataInCache($this->getDatasetId(), $secondaryLinks);
        }
        return $secondaryLinks;
    }

    public function getPrefixes(): array
    {
        $prefixes = $this->getCachedLocalData($this->getDatasetId());
        if (null == $prefixes) {
            $prefixes = $this->_datasetAccessions->getPrefixes();
            $this->saveLocalDataInCache($this->getDatasetId(), $prefixes);
        }
        return $prefixes;
    }
}
