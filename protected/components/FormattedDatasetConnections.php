<?php

/**
 * Format and return a dataset's connections
 *
 *
 * @param CController $controller current controller so we can call render_file for special-case HTML snippet
 * @param DatasetConnectionsInterface $datasetConnections the adaptee class that return cached connections
 * @see DatasetConnectionsInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetConnections extends DatasetComponents implements DatasetConnectionsInterface
{
    private $_cachedDatasetConnections;
    private $_controller;

    public function __construct(CController $controller, DatasetConnectionsInterface $datasetConnections)
    {
        parent::__construct();
        $this->_cachedDatasetConnections = $datasetConnections;
        $this->_controller = $controller;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_cachedDatasetConnections->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->_cachedDatasetConnections->getDatasetDOI();
    }

    /**
     * retrieval of related datasets in order to show them on th view layer.
     *
     * Some relationship types need special treatment on the view so, we need an "extra_html" field
     * In this case, for the "IsPreviousVersionOf" relationship we ask Yii to rendre a partial view to generate
     * the special  HTML that will go into the "extra_html" field
     * Also we need to remove internal ids, as we want to use the public DOI and full DOI only on the view
     *
     * @param string optionally pass the list of types of relations to retrieve, otherwise retrieve them all
     * @return array of string representing the  list of relations ready to be displayed
    */
    public function getRelations(string $relationship_type = null): array
    {
        $formattedRelations = [];
        $relations = $this->_cachedDatasetConnections->getRelations();
        foreach ($relations as $relation) {
            $formattedRelation = $relation;
            //those two are internal ids, no need to be exposd in view
            unset($formattedRelation['dataset_id']);
            unset($formattedRelation['related_id']);
            //we need full DOI that include prefix too
            $formattedRelation['full_dataset_doi'] = Yii::app()->params['mds_prefix'] . "/" . $relation['dataset_doi'];
            $formattedRelation['full_related_doi'] = Yii::app()->params['mds_prefix'] . "/" . $relation['related_doi'];

            if ("IsPreviousVersionOf" == $formattedRelation['relationship']) {
                $formattedRelation['extra_html'] = $this->_controller->renderFile(
                    Yii::getPathOfAlias("application") . "/views/dataset/_connection_IsPreviousVersionOf.php",
                    array("relation" => $formattedRelation),
                    true // this tell Yii to return the rendered view instead of displaying it
                );
            } else {
                $formattedRelation['extra_html'] = "";
            }
            array_push($formattedRelations, $formattedRelation);
        }
        return $formattedRelations;
    }

    /**
     * retrieval of list of publications and formatting them to be presented on dataset view page
     *
     * @return array of string representing the list of peer-reviewed publications associated with the dataset
    */
    public function getPublications(): array
    {
        $formattedPublications = [];
        $publications = $this->_cachedDatasetConnections->getPublications();
        foreach ($publications as $publication) {
            $publication['citation'] = preg_replace("/(doi:)([0-9.]+\/.*)/", '<a href="https://doi.org/$2">$1$2</a>', $publication['citation']);
            $publication['pmurl'] = preg_replace("/^(http.*)$/", "(PubMed:<a href=\"$1\">" . $publication['pmid'] . "</a>)", $publication['pmurl']);
            $formattedPublications[] = $publication;
        }
        return $formattedPublications;
    }

    /**
     * retrieval of projects from cache and formatting the link depending on whether there is an image
     *
     * @return array of string representing the list of projects associated with the dataset
    */
    public function getProjects(): array
    {
        $formattedProjects = [] ;
        $projects = $this->_cachedDatasetConnections->getProjects();
        foreach ($projects as $project) {
            if (null !== $project['image_location']) {
                $project['format'] = '<a href="' . $project['url'] . '"><img src="' . $project['image_location'] . '" alt="Go to ' . $project['name'] . ' website"/></a>';
            } else {
                $project['format'] = '<a href="' . $project['url'] . '">' . $project['name'] . '</a>';
            }
            unset($project['id']);
            $formattedProjects[] = $project;
        }
        return $formattedProjects;
    }
}
