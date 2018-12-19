<?php
/**
 * Retrieve dataset, type, publisher and author info from DB, caching it and formatting it to be shown on dataset view page
 *
 * To be implemented by:
 * - StoredDatasetMainSection
 * - CachedDatasetMainSection
 * - FormattedDatasetMainSection
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetMainSectionInterface
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
	 * for the header containing title, dataset types, release date
	 *
	 * @return array of string representing the dataset headline attributes
	*/
	public function getHeadline(): array;

	/**
	 * for the release panel containing author names, title, publisher name and release year and DOI badge
	 *
	 * @return array of string representing the dataset release details attributes
	*/
	public function getReleaseDetails(): array;

	/**
	 * for the article body containing the description
	 *
	 * @return array of string representing the dataset description attributes
	*/
	public function getDescription(): array;

	/**
	 * for the citation widgets containing links and icon to configurable scholarly search engines
	 * @param string|null $search_engine if present select the link to that citation search engine's results,
	 *        otherwise, select all configured citation search engines
	 * @return array of string with the links to citations seach engines with the DOI of the dataset
	*/
	public function getCitationsLinks(string $search_engine = null): array;

	/**
	 * Fetch keywords associated with a dataset
	 *
	 */
	public function getKeywords(): array;

	/**
	 * Fetch the history of changes made to the dataset
	 *
	 */
	public function getHistory(): array;
}
?>