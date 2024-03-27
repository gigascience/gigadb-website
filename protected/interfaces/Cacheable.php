<?php
/**
 * This is interface is to be implemented by classes that read/write from/to a cache.
 *
 * It provide a constants for TTL and consistent method name for interacting with a cache key
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface Cacheable
{
	/** @var int defaultTTL constant for the default Time-to-live (TTL) for cached objects */
    /**
     * Store content from local context (collection, class, method) into the cache
     *
     * cacheDependency to invalidate out-of-date cache entry must be applied in the implementation
     *
     * @param string identifier for the collection of objects
     * @param mixed $content content to cache
     * @param string range of query defined by its limit and offset
     * @return boolean true if operation successful, false otherwise
     */
    public function saveLocalDataInCache(string $dataset_identifier,  $content, string $range): bool;

    const defaultTTL = 60*60*24; //1day

    /**
	 * For generating a caching key based on local context (collection type, calling class and method)
	 *
	 * @param string $collection_id the id of the colleciton for which we are keeping data cached
	 * @return string  the key name used to index the data from local context in the cache
	 */
	public function getCacheKeyForLocalData(string $collection_id): string;

    /**
	 * retrieve local cached data transparently.
	 *
	 * @param string identifier for the collection of objects
	 * @param string range in case data can be paginated, range of data slice as defined by its limit and offset
	 * @uses Cacheable::getCacheKeyForLocalData()
	 * @return mixed the content retrieved from cache
	 */
	public function getCachedLocalData(string $dataset_identifier, string $range);
}
?>