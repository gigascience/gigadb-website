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
	public function getDatasetDOI(): string;
	public function getPrimaryLinks(): array; //array of LinkInterface (pity PHP cannot enforce that detail)
	public function getSecondaryLinks(): array; //array of LinkInterface (pity PHP cannot enforce that detail)
	public function getPrefixes(): array;
}

?>