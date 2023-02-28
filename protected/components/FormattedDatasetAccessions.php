<?php

/**
 * Presentation class to retrieve dataset links, and prefixes from cache and wrap them in HTML
 *
 * @uses LinkWithFormat.php for the non-enforced rule that the DatasetAccessionsInterface returns array of LinkInterface
 * @param DatasetAccessionsInterface $datasetAccessions DAO for which this is a cache adapter.
 *        We use PHP object interface for future flexibility (The O and L in SOLID principles)
 * @param string $htmlAttributes HMTL attributes to add to the <a> element of the HMTL snippet
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FormattedDatasetAccessions extends yii\base\BaseObject implements DatasetAccessionsInterface
{
    private $_datasetAccessions;
    private $_htmlAttributes;

    public function __construct(DatasetAccessionsInterface $datasetAccessions, string $htmlAttributes = '')
    {
        parent::__construct();
        $this->_htmlAttributes = $htmlAttributes;
        $this->_datasetAccessions = $datasetAccessions;
    }

    /**
     * the database id of dataset is the internal input variable for retrieving and presenting dataset accessions
     *
     * @return int dataset id
     */
    public function getDatasetId(): int
    {
        return $this->_datasetAccessions->getDatasetId();
    }

    public function getDatasetDOI(): string
    {
        return $this->_datasetAccessions->getDatasetDOI();
    }

    public function getPrimaryLinks(): array
    {
        // retrieving the links from cache
        $primaryLinks = $this->_datasetAccessions->getPrimaryLinks();
        $formattedLinks = $this->formatLinks($primaryLinks);

        return $formattedLinks;
    }
    public function getSecondaryLinks(): array
    {
        // retrieving the links from AuthorisedDatasetAccessions
        $secondaryLinks = $this->_datasetAccessions->getSecondaryLinks();
        $formattedLinks = $this->formatLinks($secondaryLinks);

        return $formattedLinks;
    }

    /**
     * no need to format that one, so delegate straight to the adaptee class
     */
    public function getPrefixes(): array
    {
        return $this->_datasetAccessions->getPrefixes();
    }

    /**
     * Format the links into the HTML snippet suitable to be displayed on dataset view page
     * @uses LinkWithFormat.php
     * @return array
     */
    private function formatLinks(array $links): array
    {
        // retrieving the links from cache
        $formattedLinks = [];

        // creating the html snippets
        foreach ($links as $link) {
            $tokens = explode(':', $link->link);
            $name = $tokens[0];
            $code = $tokens[1];
            if ('http' == $name) {
                array_push($formattedLinks, new LinkWithFormat($link, 'Link: <a ' . $this->_htmlAttributes . ' href="' . $link->link . '">' . $link->link . '</a><br>'));
            } else {
                array_push($formattedLinks, new LinkWithFormat($link, $name . ': <a ' . $this->_htmlAttributes . ' href="' . $link->getFullUrl($link->preferred_source) . '">' . $code . '</a><br>'));
            }
        }
        return $formattedLinks;
    }
}
