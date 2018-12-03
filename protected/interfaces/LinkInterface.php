<?php
/**
 * Interface to help with making adapters for the Link model class
 * So that Link and its adapters can be used interchangeably. This is needed in the DatasetAccessions workflow
 * Implemented by
 * - LinkWithPreference
 * - LinkWithFormat
 * - Link
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface LinkInterface
{
	public function getFullUrl(string $source = ''): string;
}
?>