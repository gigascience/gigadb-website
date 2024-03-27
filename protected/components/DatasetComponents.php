<?php

/**
 * Parent class for the components implementing one of the Dataset*Interface object interface
 *
 * Its main purpose is to abstract common code in those components (like interacting with the cache system)
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class DatasetComponents extends yii\base\BaseObject implements Cacheable
{
    protected $_cache;
    protected $_cacheDependency;

    /**
     * compose the key to access (read/write) the appropriate cache entry for local context's data
     *
     * @param string $dataset_id the identifier (dataset id) of the dataset associated with the local context's data
     * @return string a key to access objects in cache
     */
    public function getCacheKeyForLocalData(string $dataset_id): string
    {
        $parent_function = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
        $parent_class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'];

        return "dataset_${dataset_id}_${parent_class}_${parent_function}";
    }


    /**
     * retrieve local cached data transparently.
     *
     * @params string $dataset_id ID of the cached dataset
     * @params string $range range (limit, offset) being cached
     *
     * @uses Cacheable::getCacheKeyForLocalData()
     * @return mixed the content retrieved from cache, or false if content is expired
     */
    public function getCachedLocalData(string $dataset_identifier, string $range = "ALL_0")
    {
        $result = null;
        if (!defined('DISABLE_CACHE') || false === DISABLE_CACHE) {
            $result = $this->_cache->get($this->getCacheKeyForLocalData($dataset_identifier."_".$range));
        }
        if (defined('YII_DEBUG') && true === YII_DEBUG) {
            Yii::log("cache for " . $this->getCacheKeyForLocalData($dataset_identifier."_".$range) . ": " . (false === $result ? "MISS" : "HIT"), 'info');
        }

         return $result;
    }

    /**
     * Store content from local context (collection, class, method) into the cache
     *
     * The content is cached until TTL read from config, or if there's a new entry in dataset_log table (invalidation query)
     *
     * @param string identifier for the collection of objects
     * @param mixed $content content to cache
     * @param string range of query defined by its limit and offset
     * @return boolean true if operation successful, false otherwise
     */
    public function saveLocalDataInCache(string $dataset_identifier,  $content, string $range = "ALL_0"): bool
    {
        $invalidationQuery = preg_replace("/@id/", $dataset_identifier, Yii::app()->params['cacheConfig']['DatasetComponents']['invalidationQuery']);
        $ttl = preg_replace("/@id/", $dataset_identifier, Yii::app()->params['cacheConfig']['DatasetComponents']['timeToLive']);
        $this->_cacheDependency->sql = $this->isCacheDisabled() ? "select current_time;" : $invalidationQuery;
        return $this->_cache->set(
            $this->getCacheKeyForLocalData($dataset_identifier."_".$range),
            $content,
            $ttl,
            $this->_cacheDependency
        );
    }

    /**
     * return the dataset identifier (DOI) given a dataset ID
     *
     * this is aimed to abstract the commoon part of the implementation of getDatasetDOI() method from the Dataset interfaces
     * @param CDbConnection $db
     * @param int $id
     * @return string
     */
    public function getDOIfromId(CDbConnection $db, int $id): string
    {
        $sql = "select identifier from dataset where id=:id";
        $command = $db->createCommand($sql);
        $command->bindParam(":id", $id, PDO::PARAM_INT);
        $row = $command->queryRow();
        return $row['identifier'];
    }

    /**
     * Check whether the cache is to be disabled
     *
     * @use DISABLE_CACHE
     * @return bool
     */
    public function isCacheDisabled(): bool
    {
        return defined('DISABLE_CACHE') && DISABLE_CACHE === true;
    }
}
