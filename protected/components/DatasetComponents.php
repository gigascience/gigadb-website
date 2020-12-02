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
		$parent_function = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['function'];
		$parent_class = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2]['class'];

		return "dataset_${dataset_id}_${parent_class}_${parent_function}";
	}


	/**
	 * retrieve local cached data transparently.
	 *
	 * @uses Cacheable::getCacheKeyForLocalData()
	 * @return mixed the content retrieved from cache, or false if content is expired
	 */
	public function getCachedLocalData(string $dataset_id)
	{
		 $result = $this->_cache->get( $this->getCacheKeyForLocalData( $dataset_id ) );
		 // if (false == $result) {
		 // 	Yii::log("cache MISS for ". $this->getCacheKeyForLocalData( $dataset_id ),'info');
		 // }
		 // else {
		 // 	Yii::log("cache HIT for ". $this->getCacheKeyForLocalData( $dataset_id ),'info');
		 // }
		 return $result;
	}

	/**
	 * Store content from local context (collection, class, method) into the cache
	 *
	 * The content is cached until TTL read from config, or if there's a new entry in dataset_log table (invalidation query)
	 * @param mixed $content content to cache
	 * @return boolean true if operation successful, false otherwise
	 */
	public function saveLocaldataInCache(string $dataset_id, $content): bool
	{
		// Yii::log("Saving to cache the data from:". $this->getCacheKeyForLocalData( $dataset_id ),'info');
		$invalidationQuery = preg_replace("/@id/", $dataset_id,Yii::app()->params['cacheConfig']['DatasetComponents']['invalidationQuery']);
		$ttl = preg_replace("/@id/", $dataset_id,Yii::app()->params['cacheConfig']['DatasetComponents']['timeToLive']);
		$this->_cacheDependency->sql = defined('YII_DEBUG') ? "select current_time;" : $invalidationQuery;
		return $this->_cache->set( $this->getCacheKeyForLocalData( $dataset_id ),
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
		$sql="select identifier from dataset where id=:id";
		$command = $db->createCommand($sql);
		$command->bindParam(":id", $id, PDO::PARAM_INT);
		$row = $command->queryRow();
		return $row['identifier'];
	}
}
?>