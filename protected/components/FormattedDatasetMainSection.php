<?php

/**
 * Format a dataset's information for main section of the dataset view
 *
 *
 * @param CachedDatasetMainSection $datasetMainSection the adaptee class that return the cached information
 * @see DatasetMainSectionInterface.php
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetMainSection extends yii\base\BaseObject implements DatasetMainSectionInterface
{
    private $_cachedDatasetMainSection;

    public function __construct(DatasetMainSectionInterface $datasetMainSection)
    {
        parent::__construct();
        $this->_cachedDatasetMainSection = $datasetMainSection;
    }

    /**
     * return the dataset id
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_cachedDatasetMainSection->getDatasetId();
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->_cachedDatasetMainSection->getDatasetDOI();
    }

    /**
     * for the header containing title, dataset types, release date
     *
     * @return array (of string)
    */
    public function getHeadline(): array
    {
        $headline = $this->_cachedDatasetMainSection->getHeadline();
        $headline['title'] = $headline['title'] ?? "" ;
        $headline['types'] = isset($headline['types']) ? implode(", ", $headline['types']) : "";
        $headline['release_date'] = isset($headline['release_date']) ? strftime("%B %d, %Y", strtotime($headline['release_date'])) : "";

        return $headline;
    }
    /**
     * for the release panel containing author names, title, publisher name and release year and DOI badge
     *
     *         $expected = array(
     *                   "authors" => "",
     *                   "release_year" => "",
     *                   "dataset_title"=> "",
     *                   "publisher"=> "",
     *                   "full_doi"=> "",
     *               );
     * @return array (of string)
    */
    public function getReleaseDetails(): array
    {
        $release_details = $this->_cachedDatasetMainSection->getReleaseDetails();
        $release_details['authors'] = isset($release_details['authors']) ? $this->getAuthorsNameAndLink($release_details['authors']) : "";
        $release_details['release_year'] = $release_details['release_year'] ?? "" ;
        $release_details['dataset_title'] = $release_details['dataset_title'] ?? "" ;
        $release_details['publisher'] = $release_details['publisher'] ?? "" ;
        $release_details['full_doi'] = $release_details['full_doi'] ?? "" ;
        return $release_details;
    }
    /**
     * for the article body containing the description. It is just a pass-through to the cache layer as no need of formatting
     *
     * @return array (of string)
    */
    public function getDescription(): array
    {
        return $this->_cachedDatasetMainSection->getDescription();
    }

    /**
     * for the citation widgets containing links and icon to configurable scholarly search engines
     *
     * We delegate straight to $_storedDatasetMainSection as the data is read from config already loaded in memory,
     * so no need to be cached.
     *
     * @param string $search_engine name of citations search engine, default to null
     * @return array (of string)
    */
    public function getCitationsLinks(string $search_engine = null): array
    {
        $formattedCitations = [];
        $citationLinks =  $this->_cachedDatasetMainSection->getCitationsLinks($search_engine);
        foreach ($citationLinks['services'] as $service => $description) {
            $formattedCitations[$service] = "<span class=\"citation-popup\" data-content=\"${description}\"><a href=\"" . $citationLinks['urls'][$service] . "\" target=\"_blank\"><img class=\"dataset-des-images\" src=\"" . $citationLinks['images'][$service] . "\" alt=\"${description}\"/></a></span>";
        }

        return $formattedCitations;
    }

    /**
     * Take a list of authors and their properties and generate a list of HTML link as HTML snippet
     *
     * @param array @authors list of authors
     * @return string HTML snippet with author names linking to their authored datasets
     */
    private function getAuthorsNameAndLink(array $authors): string
    {
        $links = [];
        foreach ($authors as $author) {
            $formattedName = $author['custom_name'] ?? Author::generateDisplayName(
                Author::generateDisplayName($author['surname'], null, null),
                $author['first_name'],
                $author['middle_name']
            );
            array_push(
                $links,
                CHtml::link($formattedName, "/search/new?keyword=$formattedName&author_id=" . $author['id'], array('class' => 'result-sub-links'))
            );
        }
        return implode('; ', $links);
    }

    /**
     * Fetch keywords associated with a dataset
     *
     */
    public function getKeywords(): array
    {
        $keywords =  $this->_cachedDatasetMainSection->getKeywords();
        $linkify = function ($keyword) {
            return "<a href='/search/new?keyword=$keyword'>$keyword</a>";
        };
        return array_map($linkify, $keywords);
    }

    /**
     * Fetch the history of changes made to the dataset
     *
     */
    public function getHistory(): array
    {
        return $this->_cachedDatasetMainSection->getHistory();
    }

    /**
     * Fetch the funding data associated with the dataset
     *
     */
    public function getFunding(): array
    {
        return $this->_cachedDatasetMainSection->getFunding();
    }
}
