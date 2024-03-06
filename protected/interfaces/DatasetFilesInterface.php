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
     * count number of files associated to a dataset
     *
     * @return int how many files are associated with the dataset
     */
    public function countDatasetFiles(): int;

	/**
	 * retrieve, cache and format the files associated to a dataset
	 *
	 * @param string|null $limit how many rows need to be returned (Or ALL)
	 * @param int|null $offset how many rows need to be skipped
	 * 
	 * @return array of files array maps
	 */
	public function getDatasetFiles(?string $limit, ?int $offset): array;

	/**
	 * retrieve, cache and format the sample attached to files associated to a dataset
	 *
	 * @return array of files array maps
	 */
	public function getDatasetFilesSamples(): array;

}