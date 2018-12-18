<?php
/**
 * Interface for classes managing external links by types
 *
 * Types: the values in the external_link_type table
 * Placement: main (in additional information) or aside (in tabs)
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetExternalLinksInterface
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
	 * retrieve, cache and format external links associated to a dataset
	 *
	 * @param array $types A list of the types of links to return. If null, return all types.
	 * @return array of external link array map
	 */
	public function getDatasetExternalLinks(array $types = null): array;


	/**
	 * retrieve and cache the types and count for the external links associated to a dataset
	 *
	 * @param array $types A list of the types of links to return. If null, return all types.
	 * @return array of (type => number of link of that type).
	 */
	public function getDatasetExternalLinksTypesAndCount(array $types = null): array;
}