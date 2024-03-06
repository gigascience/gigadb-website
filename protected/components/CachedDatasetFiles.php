<?php

/**
 * DAO class to retrieve from cache the files associated to a dataset
 *
 * @param ICache $cache Cache system to use
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param DatasetFilesInterface $datasetFiles the adaptee class to fall back on if no cache variant
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetFiles extends DatasetComponents implements DatasetFilesInterface
{
    private $_storedDatasetFiles;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetFilesInterface $datasetFiles)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_storedDatasetFiles = $datasetFiles;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_storedDatasetFiles->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->_storedDatasetFiles->getDatasetDOI();
    }


    /**
     * retrieve from cache files associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetFiles(?string $limit = "ALL", ?int $offset = 0): array
    {
        $files =  $this->getCachedLocalData($this->getDatasetId()."_".$limit."_".$offset);
        if (!$files) {
            $files = $this->_storedDatasetFiles->getDatasetFiles($limit, $offset);
            $this->saveLocaldataInCache($this->getDatasetId()."_".$limit."_".$offset, $files);
        }
        return $files;
    }

     /**
     * count number of files associated to a dataset
     *
     * @return int how many files are associated with the dataset
     */
    public function countDatasetFiles(): int
    {
        $countFiles =  $this->getCachedLocalData($this->getDatasetId());
        if (!$countFiles) {
            $countFiles = $this->_storedDatasetFiles->countDatasetFiles();
            $this->saveLocaldataInCache($this->getDatasetId(), $countFiles);
        }
        return $countFiles;
    }

    /**
     * retrieve, cache and format the sample attached to files associated to a dataset
     *
     * @return array of files array maps
     */
    public function getDatasetFilesSamples(): array
    {
        $samples =  $this->getCachedLocalData($this->getDatasetId());
        if (false == $samples) {
            $samples = $this->_storedDatasetFiles->getDatasetFilesSamples();
            $this->saveLocaldataInCache($this->getDatasetId(), $samples);
        }
        return $samples;
    }
}
