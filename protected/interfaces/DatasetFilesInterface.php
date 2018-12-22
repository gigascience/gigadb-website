<?php
/**
 * Interface for classes managing dataset files
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetFilesInterface
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
	 * retrieve, cache and format the files associated to a dataset
	 *
	 * @return array of files array maps
	 */
	public function getDatasetFiles(): array;

	/**
	 * retrieve, cache and format the sample attached to files associated to a dataset
	 *
	 * @return array of files array maps
	 */
	public function getDatasetFilesSamples(): array;

}