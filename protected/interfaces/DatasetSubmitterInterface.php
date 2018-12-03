<?php
/**
 * Interface for retrieving from database, caching and presenting submitter's email on a dataset view page
 *
 * Using this interface allow for interchanbility if the database server, the cache mechanism or authorisation mechanism
 * need to change in the future, as the client code that use the code that implements this interface won't need to change
 * The interface enables elegant implementation of Adapter pattern that allow any give code to depend only on more stable code
 *
 * This interface was created to be implemented by StoredDatasetSubmitter.php and its adapters:
 * - CachedDatasetSubmitter.php
 * - AuthorisedDatasetSubmitter.php
 *
 * @see DatasetController.php to see how these adapters are used
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetSubmitterInterface
{
	public function getEmailAddress(): string;
	public function getDatasetDOI(): string;
}
?>