<?php

/**
 * DAO class to retrieve from cache the samples associated to a dataset
 *
 * @param ICache $cache Cache system to use
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetSamplesInterface $datasetSamples the adaptee class to fall back on if no cache variant
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetSamples extends DatasetComponents implements DatasetSamplesInterface
{
    private $_storedDatasetSamples;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetSamplesInterface $datasetSamples)
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
     * count number of samples associated to a dataset
     *
     * @return int how many samples are associated with the dataset
     */
    public function countDatasetSamples(): int
    {
        $countSamples =  $this->getCachedLocalData($this->getDatasetId());
        if (!$countSamples) {
            $countSamples = $this->_storedDatasetSamples->countDatasetSamples();
            $this->saveLocalDataInCache($this->getDatasetId(), $countSamples);
        }
        return $countSamples;
    }

    /**
     * retrieve from cache samples associated to a dataset
     *
     * @return array of samples array maps
     */
    public function getDatasetSamples(?string $limit = "ALL", ?int $offset = 0): array
    {
        $samples =  $this->getCachedLocalData($this->getDatasetId());
        if (!$samples) {
            $samples = $this->_storedDatasetSamples->getDatasetSamples($limit, $offset);
            $this->saveLocalDataInCache($this->getDatasetId(), $samples);
        }
        return $samples;
    }
}
