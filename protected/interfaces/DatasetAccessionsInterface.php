<?php
/**
 * Interface for retrieving from database, caching and presenting Links on a dataset view page
 *
 * will be implemented by:
 * - StoredDatasetAccessions( $doi, $db_connection) (retrieve links, and prefixes from storage)
 * - CachedDatasetAccessions( $cache, $storedDatasetAccessions) (retrieve links, and prefixes from cache or from StoredDatasetAccessions)
 * - AuthorisedDatasetAccessions( $current_user, $cachedDatasetAccessions) (retrieve links, prefixes
 * 	 and preferred link source (if user is logged in) from CachedDatasetAccessions
 * - FormattedDatasetAccessions( $authorisedDatasetAccessions, $html_attributes) (retrieve links, prefixes and preferred link source from
 * 	 AuthorisedDatasetAccessions then create HTML snippets)
 *
 *  Example of HTML snippets (where "name:code" is a link) :
 * 		$name :
 * 		``CHtml::link($code, $link->getFullUrl($link_type), array('target'=>'_blank'));``
 * 		or (if $name = http)
 * 		``CHtml::link($link->link , $link->link,array('target'=>'_blank'));``
 * @see DatasetController.php to see how these adapters are used
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetAccessionsInterface
{
	/**
	 * the database id of dataset is the internal input variable for retrieving and presenting dataset accessions
	 *
	 * @return int dataset id
	 */
	public function getDatasetId(): int;

	/**
	 * external facing identifier for dataset
	 *
	 * @return string
	 */
	public function getDatasetDOI(): string;

	/**
	 * manage retrievial, caching, auhorisation and presentation of links related to a dataset that are in GigaDB
	 *
	 * @return array of LinkInterface (implemented by Link, LinkWithPreference, LinkWithFormat)
	 */
	public function getPrimaryLinks(): array; //array of LinkInterface (pity PHP cannot enforce that detail)

	/**
	 * manage retrievial, caching, auhorisation and presentation of links related to a dataset that are from third parties
	 *
	 * @return array of LinkInterface (implemented by Link, LinkWithPreference, LinkWithFormat)
	 */
	public function getSecondaryLinks(): array; //array of LinkInterface (pity PHP cannot enforce that detail)

	/**
	 *  manage retrievial, caching, auhorisation and presentation of scientific url prefixes
	 *
	 */
	public function getPrefixes(): array;
}

?>