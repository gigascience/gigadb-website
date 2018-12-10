<?php
/**
 * Retrieve related datasets and keywords associated to a dataset from db, then cache them and format them for presentation
 *
 * To be implemented by:
 *
 * @see StoredDatasetConnections.php
 * @see CachedDatasetConnections.php
 * @see FormattedDatasetConnections.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetConnectionsInterface
{
	/**
	 * return the dataset id
	 *
	 * @return int
	 */
	public function getDatasetId(): int;

	/**
	 * return the dataset identifier (DOI)
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string;

	/**
	 * retrieval of related datasets
	 *
	 * @param string optionally pass the list of types of relations to retrieve, otherwise retrieve them all
	 * @return array of string representing the dataset headline attributes
	*/
	public function getRelations(string $relationship_type = null): array;

	/**
	 * retrieval of keywords
	 *
	 * @return array of string representing the dataset headline attributes
	*/
	public function getKeywords(): array;
}
?>