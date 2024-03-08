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
 * @property FileUploadService $_fileUploadService
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
    private $_skip_cache;


    public function __construct(Dataset $dataset, CApplication $app, FileUploadService $srv, $config = [])
    {
        $this->_dataset = $dataset;
        $this->_app = $app;
        $this->_fileUploadService = $srv;
        $this->_cacheDependency = new CDbCacheDependency();
        $this->_skip_cache = (bool)$config['skip_cache'];

    }

    /**
     * Factory method to create an instance of DatasetPageAssembly
     *
     * @param Dataset $d Dataset instance to pass to the instanciated Page assembly
     * @param CApplication $app Yii web application from which to access cache and database
     * @param FileUploadService $srv GigaDB client to File Upload Wizard API
     * @param ?array $config Additional options to configure the assembly (only 'skip_cache' for now)
     * @return DatasetPageAssembly  a new instance of DatasetAssembly
     */
    public static function assemble(Dataset $d, CApplication $app, FileUploadService $srv, array $config = null): DatasetPageAssembly
    {
        return new DatasetPageAssembly($d, $app, $srv, $config);
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetSubmitter(
                    $this->_dataset->id,
                    $this->_app->db
                );
                break;
            default:
                $dataSource = new CachedDatasetSubmitter(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    new StoredDatasetSubmitter(
                        $this->_dataset->id,
                        $this->_app->db
                    )
                );
        }
        $this->_submitter = $datasetSubmitter = new AuthorisedDatasetSubmitter(
            $this->_app->user,
            $dataSource
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetAccessions(
                    $this->_dataset->id,
                    $this->_app->db
                );
                break;
            default:
                $dataSource = new CachedDatasetAccessions(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    new StoredDatasetAccessions(
                        $this->_dataset->id,
                        $this->_app->db
                    )
                );
        }

        $this->_accessions = new FormattedDatasetAccessions(
            new AuthorisedDatasetAccessions(
                $this->_app->user,
                $dataSource
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetMainSection(
                    $this->_dataset->id,
                    $this->_app->db
                );
                break;
            default:
                $dataSource = new CachedDatasetMainSection(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    new StoredDatasetMainSection(
                        $this->_dataset->id,
                        $this->_app->db
                    )
                );
        }

        $this->_mainSection = new FormattedDatasetMainSection(
            $dataSource
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetConnections(
                    $this->_dataset->id,
                    $this->_app->getDb(),
                    new \GuzzleHttp\Client()
                );
                break;
            default:
                $dataSource = new CachedDatasetConnections(
                    $this->_app->getCache(),
                    $this->_cacheDependency,
                    new StoredDatasetConnections(
                        $this->_dataset->id,
                        $this->_app->getDb(),
                        new \GuzzleHttp\Client()
                    )
                );
        }

        $this->_connections = new FormattedDatasetConnections(
            $this->_app->getController(),
            $dataSource
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetExternalLinks(
                    $this->_dataset->id,
                    $this->_app->db
                );
                break;
            default:
                $dataSource = new CachedDatasetExternalLinks(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    new StoredDatasetExternalLinks(
                        $this->_dataset->id,
                        $this->_app->db
                    )
                );
        }

        $this->_externalLinks = new FormattedDatasetExternalLinks(
            $dataSource
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
        switch ($source) {
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

        switch($this->_skip_cache) {
            case true:
                $dataSource = $datasetFiles;
                break;
            default:
                $dataSource = new CachedDatasetFiles(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    $datasetFiles
                );
        }

        $pager = new FilesPagination();
        $pager->setPageSize($pageSize);
        $this->_files = new FormattedDatasetFiles(
            $pager,
            $dataSource
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
        switch($this->_skip_cache) {
            case true:
                $dataSource = new StoredDatasetSamples(
                    $this->_dataset->id,
                    $this->_app->db
                );
                break;
            default:
                $dataSource = new CachedDatasetSamples(
                    $this->_app->cache,
                    $this->_cacheDependency,
                    new StoredDatasetSamples(
                        $this->_dataset->id,
                        $this->_app->db
                    )
                );
        }

        $pager = new FilesPagination();
        $pager->setPageSize($pageSize);
        $this->_samples = new FormattedDatasetSamples(
            $pager,
            $dataSource
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
