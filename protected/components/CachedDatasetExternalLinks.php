<?php

/**
 * DAO class to retrieve from cache external links associated with a given dataset
 *
 *
 * @param ICache $cache object
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @param DatasetExternalLinksInterface $datasetExternalLinks the adaptee class to fall back on if no cache variant
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetExternalLinks extends DatasetComponents implements DatasetExternalLinksInterface
{
    private $_storedDatasetExternalLinks;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetExternalLinksInterface $datasetExternalLinks)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_storedDatasetExternalLinks = $datasetExternalLinks;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_storedDatasetExternalLinks->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return  $this->_storedDatasetExternalLinks->getDatasetDOI();
    }

    /**
     * retrieve external links associated to a dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of external link array map
     */
    public function getDatasetExternalLinks(array $types = null): array
    {
        $filter_by = "external_link_type_name" ;
        $filterByType = function ($item) use ($filter_by, $types) {
            if (empty($types)) {
                return true;
            }
            if (in_array($item[$filter_by], $types)) {
                return true;
            }
            return false;
        };
        $externalLinks = $this->getCachedLocalData($this->getDatasetId());
        if (false == $externalLinks) {
            $externalLinks = $this->_storedDatasetExternalLinks->getDatasetExternalLinks();
            $this->saveLocalDataInCache($this->getDatasetId(), $externalLinks);
        }
        return  array_values(array_filter($externalLinks, $filterByType)) ;
    }


    /**
     * retrieve and cache the types and count for the external links associated to a dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of (type => number of link of that type).
     */
    public function getDatasetExternalLinksTypesAndCount(array $types = null): array
    {
        $typesAndCount =  $this->getCachedLocalData($this->getDatasetId());
        if (false == $typesAndCount) {
            $typesAndCount = $this->_storedDatasetExternalLinks->getDatasetExternalLinksTypesAndCount();
            $this->saveLocalDataInCache($this->getDatasetId(), $typesAndCount);
        }

        if (!empty($types)) {
            $typesKeyValues = array_combine($types, array_fill(0, count($types), 0)); // transform types filter into array ("type" => 0) so that,
            return array_intersect_key($typesAndCount, $typesKeyValues) ; // we can calculate intersection with the array ("type" => count)
        }
        return $typesAndCount;
    }
}
