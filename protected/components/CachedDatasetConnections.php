<?php

/**
 * DAO class to retrieve from cache related dataset and keywords for a given dataset
 *
 *
 * @param ICache $cache object
 * @param CCacheDependency $cacheDependency Cache dependency for invalidating the cache
 * @param CDbConnection $dbConnection The database connection object to interact with the database storage
 * @param DatasetConnectionsInterface $datasetConnections the adaptee class to fall back on if no cache variant
 * @see DatasetMainSectionInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class CachedDatasetConnections extends DatasetComponents implements DatasetConnectionsInterface
{
    private $_storedDatasetConnections;

    public function __construct(ICache $cache, CCacheDependency $cacheDependency, DatasetConnectionsInterface $datasetConnections)
    {
        parent::__construct();
        $this->_cache = $cache;
        $this->_cacheDependency = $cacheDependency;
        $this->_storedDatasetConnections = $datasetConnections;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_storedDatasetConnections->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return  $this->_storedDatasetConnections->getDatasetDOI();
    }

    /**
     * retrieval from cache of links and metadata of resources connected to a dataset
     *
     * Note: you need to wrap array_filter with array_values because the former preserve keys,
     * thus returning an associative array instead of the classic array list
     * @param string optionally pass the list of types of relations to retrieve, otherwise retrieve them all
     * @return array of relations arrays
    */
    public function getRelations(string $relationship_type = null): array
    {
        $filterByRelationshipType = function ($relation) use ($relationship_type) {
            if (null == $relationship_type) {
                return true;
            }
            if ($relation["relationship"] == $relationship_type) {
                return true;
            }
            return false;
        };

        $relations =  $this->getCachedLocalData($this->getDatasetId());
        if (false == $relations) {
            $relations = $this->_storedDatasetConnections->getRelations();
            $this->saveLocalDataInCache($this->getDatasetId(), $relations);
        }

        return  array_values(array_filter($relations, $filterByRelationshipType)) ;
    }

    /**
     * retrieval of publications
     *
     * @return array of string representing the list of peer-reviewed publications associated with the dataset
    */
    public function getPublications(): array
    {
        $results = $this->getCachedLocalData($this->getDatasetId());
        if (false == $results) {
            $results = $this->_storedDatasetConnections->getPublications();
            $this->saveLocalDataInCache($this->getDatasetId(), $results);
        }
        return $results;
    }

    /**
     * retrieval of projects
     *
     * @return array of string representing the list of projects associated with the dataset
    */
    public function getProjects(): array
    {
        $results =  $this->getCachedLocalData($this->getDatasetId());
        if (false == $results) {
            $results = $this->_storedDatasetConnections->getProjects();
            $this->saveLocalDataInCache($this->getDatasetId(), $results);
        }
        return $results;
    }
}
