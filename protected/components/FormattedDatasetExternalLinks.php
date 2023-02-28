<?php

/**
 * Adapter class to present external links for a dataset
 *
 * @param DatasetExternalLinksInterface $datasetExternalLinks the adaptee class to fall back to
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetExternalLinks extends DatasetComponents implements DatasetExternalLinksInterface
{
    private $_cachedDatasetExternalLinks;

    public function __construct(DatasetExternalLinksInterface $datasetExternalLinks)
    {
        parent::__construct();
        $this->_cachedDatasetExternalLinks = $datasetExternalLinks;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_cachedDatasetExternalLinks->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return  $this->_cachedDatasetExternalLinks->getDatasetDOI();
    }

    /**
     * retrieve external links associated to a dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of external link array map
     */
    public function getDatasetExternalLinks(array $types = null): array
    {
        return  $this->_cachedDatasetExternalLinks->getDatasetExternalLinks($types);
    }


    /**
     * retrieve and cache the types and count for the external links associated to a dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of (type => number of link of that type).
     */
    public function getDatasetExternalLinksTypesAndCount(array $types = null): array
    {
        return  $this->_cachedDatasetExternalLinks->getDatasetExternalLinksTypesAndCount($types);
    }

    /**
     * Return the name and machine name (to be used in HTML attributes) of the external link types for this dataset
     *
     * @param array $types A list of the types of links to return. If null, return all types.
     * @return array of (type => number of link of that type).
     */
    public function getDatasetExternalLinksTypesNames(array $types = null): array
    {
        $machinize = function ($word) {
            return strtolower(str_replace(array("."," "), array("",""), $word));
        };

        $dataset_types = $this->_cachedDatasetExternalLinks->getDatasetExternalLinksTypesAndCount($types) ;
        // var_dump($dataset_types);
        // Yii::app->end();
        return  array_combine(
            array_keys($dataset_types),
            array_map($machinize, array_keys($dataset_types))
        );
    }
}
