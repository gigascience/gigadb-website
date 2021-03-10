<?php

/**
 * Store dataset links information into cache for preview
 *
 * Class CachedDatasetLinksPreview
 *
 * @param CCache $cache object
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @param DatasetLinksPreviewInterface $datasetLinksPreview the adaptee class to fall back on if no cache variant
 */
class CachedDatasetLinksPreview extends DatasetComponents implements DatasetLinksPreviewInterface
{
    private $_storeDatasetLinksPreview;

    public function __construct(CCache $cache, CCacheDependency $cacheDependency, DatasetLinksPreviewInterface $datasetLinksPreview)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_storeDatasetLinksPreview = $datasetLinksPreview;
    }

    /**
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_storeDatasetLinksPreview->getDatasetId();
    }

    public function getDatasetDOI(): string
    {
        return $this->_storeDatasetLinksPreview->getDatasetDOI();
    }


    public function getPreviewDataForLinks(): array
    {
        $previewData = $this->getCachedLocalData($this->getDatasetId());
        if (false == $previewData) {
            $previewData = $this->_storeDatasetLinksPreview->getPreviewDataForLinks();
            $this->saveLocaldataInCache($this->getDatasetId(), $previewData);
        }

        return $previewData;
    }
}