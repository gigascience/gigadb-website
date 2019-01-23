<?php
/**
 * Retrieve Pieces of dataset related information related to connecting the dataset to other resources
 *
 * Supported connections are:
 * - links to related datasets (DONE)
 * - links to peer-review publications/manuscripts (DONE)
 * - links to projects (TODO)
 * - links to external links (TODO)
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
	 * @return array of string representing the list of related datasets
	*/
	public function getRelations(string $relationship_type = null): array;

	/**
	 * retrieval of publications
	 *
	 * @return array of string representing the list of peer-reviewed publications associated with the dataset
	*/
	public function getPublications(): array;

	/**
	 * retrieval of projects
	 *
	 * @return array of string representing the list of projects associated with the dataset
	*/
	public function getProjects(): array;

}
?>