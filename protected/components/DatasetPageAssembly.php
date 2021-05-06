<?php

/**
 * Component for assembling parts of dataset page information architecture for use in controllers
 * 
 * we use a fluent interface to improve readability in the way it is used
 *
 * @property DatasetSubmitterInterface $_submitter
 * @property DatasetAccessionsInterface $_accessions
 * @property DatasetMainSectionInterface $_mainSection
 * @property DatasetConnectionsInterface $_connections
 * @property DatasetExternalLinksInterface $_externalLinks
 * @property DatasetFilesInterface $_files
 * @property DatasetSamplesInterface $_samplesProvider
 * @property Dataset $_dataset
 * @property SearchForm $_searchForm
 * @property FileUplooadService $_fileUploadService
 *
 * @uses yii::app()
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */

class DatasetPageAssembly extends yii\base\Component
{
	private $_app;
	private $_cacheDependency;
	private $_submitter;
	private $_accessions;
	private $_mainSection;
	private $_connections;
	private $_externalLinks;
	private $_files;
	private $_samples;
	private $_dataset;
	private $_searchForm;
	private $_fileUploadService;


	public function __construct(Dataset $dataset, CApplication $app, FileUploadService $srv, $config = []) {
		$this->_dataset = $dataset;
		$this->_app = $app;
		$this->_fileUploadService = $srv;
		$this->_cacheDependency = new CDbCacheDependency();
	}

	/**
	 * Factory method to create an instance of DatasetPageAssembly
	 *
	 * @param Dataset $d Dataset instance to pass to the instanciated Page assembly
	 * @param CApplication $app Yii web application from which to access cache and database
	 * @param FileUploadService $srv GigaDB client to File Upload Wizard API
	 * @return DatasetPageAssembly  a new instance of DatasetAssembly
	 */
	public static function assemble(Dataset $d, CApplication $app, FileUploadService $srv): DatasetPageAssembly
	{
		return new DatasetPageAssembly($d, $app, $srv);
	}

	/**
	 * Getter
	 * @return Dataset
	 */
	public function getDataset(): Dataset
	{
		return $this->_dataset;
	}

	/**
	 * Create a dataset submitter dataset component to be use in a dataset page
	 *
	 * @return DatasetPageAssembly
	 */
	public function setDatasetSubmitter(): DatasetPageAssembly
	{
		$this->_submitter = $datasetSubmitter = new AuthorisedDatasetSubmitter(
                                $this->_app->user,
                                new CachedDatasetSubmitter(
                                    $this->_app->cache,
                                    $this->_cacheDependency,
                                    new StoredDatasetSubmitter(
                                        $this->_dataset->id,
                                        $this->_app->db
                                    )
                                )
                      	);
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetSubmitterInterface
	 */
	public function getDatasetSubmitter(): DatasetSubmitterInterface
	{
		return $this->_submitter;
	}

	/**
	 * Create an accessions dataset component to be use in a dataset page
	 *
	 * @return DatasetPageAssembly
	 */
	public function setDatasetAccessions(): DatasetPageAssembly
	{
		$this->_accessions = new FormattedDatasetAccessions(
                                new AuthorisedDatasetAccessions(
                                    $this->_app->user,
                                    new CachedDatasetAccessions(
                                        $this->_app->cache,
                                        $this->_cacheDependency,
                                        new StoredDatasetAccessions(
                                            $this->_dataset->id,
                                            $this->_app->db
                                        )
                                    )
                                ),
                                'target="_blank"'
        );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetAccessionsInterface
	 */
	public function getDatasetAccessions(): DatasetAccessionsInterface
	{
		return $this->_accessions;
	}

	/**
	 * Create an accessions dataset component to be use in a dataset page
	 *
	 * TODO: we may need a AuthorisedDatasetMainSection.php later maybe?
	 *
	 * @return DatasetPageAssembly
	 */
	public function setDatasetMainSection(): DatasetPageAssembly
	{
		$this->_mainSection = new FormattedDatasetMainSection(
                        new CachedDatasetMainSection (
                            $this->_app->cache,
                            $this->_cacheDependency,
                            new StoredDatasetMainSection(
                                $this->_dataset->id,
                                $this->_app->db
                            )
                    )
                );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetMainSectionInterface
	 */
	public function getDatasetMainSection(): DatasetMainSectionInterface
	{
		return $this->_mainSection;
	}

	/**
	 * Create a connections dataset component to be use in a dataset page
	 *
	 * @return DatasetPageAssembly
	 */
	public function setDatasetConnections(): DatasetPageAssembly
	{
		$this->_connections = new FormattedDatasetConnections(
                            $this->_app->getController(),
                        new CachedDatasetConnections (
                            $this->_app->getCache(),
                            $this->_cacheDependency,
                            new StoredDatasetConnections(
                                $this->_dataset->id,
                                $this->_app->getDb(),
                                new \GuzzleHttp\Client()
                            )
                    )
                );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetConnectionsInterface
	 */
	public function getDatasetConnections(): DatasetConnectionsInterface
	{
		return $this->_connections;
	}

	/**
	 * Create an ExternalLinks dataset component to be use in a dataset page
	 *
	 * @return DatasetPageAssembly
	 */
	public function setDatasetExternalLinks(): DatasetPageAssembly
	{
		$this->_externalLinks = new FormattedDatasetExternalLinks(
                            new CachedDatasetExternalLinks(
                                $this->_app->cache,
                                 $this->_cacheDependency,
                                new StoredDatasetExternalLinks(
                                    $this->_dataset->id,
                                    $this->_app->db
                                )
                            )
                        );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetExternalLinksInterface
	 */
	public function getDatasetExternalLinks(): DatasetExternalLinksInterface
	{
		return $this->_externalLinks;
	}

	/**
	 * Create a dataset files component to be use in a dataset page
	 *
	 * @param int $pageSize item per page settings
	 * @param string "stored"|"resourced" determines wetheer to pull files from DB or API 
	 * @return DatasetPageAssembly
	 */
	public function setDatasetFiles(int $pageSize, string $source): DatasetPageAssembly
	{
		switch($source) {
			case "resourced":
				$datasetFiles = new ResourcedDatasetFiles(
                                    $this->_dataset->id,
                                    $this->_app->db,
                                    $this->_fileUploadService
                                );
				break;
			case "stored":
				$datasetFiles = new StoredDatasetFiles(
                                    $this->_dataset->id,
                                    $this->_app->db
                                );
				break;
			default:
				$datasetFiles = null;
				Yii::log("setDatasetFiles second parameter is incorrect. Expects 'stored' or 'resourced', got '$source'", "error");		

		}

		$this->_files = new FormattedDatasetFiles(
							$pageSize,
                            new CachedDatasetFiles(
                                $this->_app->cache,
                                 $this->_cacheDependency,
                                $datasetFiles
                            )
                        );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetFilesInterface
	 */
	public function getDatasetFiles(): DatasetFilesInterface
	{
		return $this->_files;
	}

	/**
	 * Create a Samples dataset component to be use in a dataset page
	 *
	 * @param int $pageSize item per page settings
	 * @return DatasetPageAssembly
	 */
	public function setDatasetSamples(int $pageSize): DatasetPageAssembly
	{
		$this->_samples = new FormattedDatasetSamples(
							$pageSize,
                            new CachedDatasetSamples(
                                $this->_app->cache,
                                 $this->_cacheDependency,
                                new StoredDatasetSamples(
                                    $this->_dataset->id,
                                    $this->_app->db
                                )
                            )
                        );
		return $this;
	}

	/**
	 * Getter
	 * @return DatasetSamplesInterface
	 */
	public function getDatasetSamples(): DatasetSamplesInterface
	{
		return $this->_samples;
	}

	/**
	 * Create a Search Form component to be use in a dataset page
	 *
	 * @return DatasetPageAssembly
	 */
	public function setSearchForm(): DatasetPageAssembly
	{
		$this->_searchForm = new SearchForm();
		return $this;
	}

	/**
	 * Getter
	 * @return SearchForm
	 */
	public function getSearchForm(): SearchForm
	{
		return $this->_searchForm;
	}

}