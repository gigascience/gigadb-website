<?php
/**
 * Interface for classes managing dataset samples
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
interface DatasetSamplesInterface
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
     * count number of samples associated to a dataset
     *
     * @return int how many samples are associated with the dataset
     */
    public function countDatasetSamples(): int;

	/**
	 * retrieve, cache and format the samples associated to a dataset
     * @param string|null $limit how many rows need to be returned (Or ALL)
     * @param int|null $offset how many rows need to be skipped
     *
	 * @return array of files array maps
	 */
	public function getDatasetSamples(?string $limit, ?int $offset): array;

}