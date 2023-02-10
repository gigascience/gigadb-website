<?php

/**
 * DAO class to retrieve dataset links, and prefixes from cache,as well as current user's preferred link source
 *
 * @uses LinkWithPreference.php for the non-enforced rule that the DatasetAccessionsInterface returns array of LinkInterface
 * @param CWebUser $current_user  current user to extract preferred source of link from
 * @param DatasetAccessionsInterface $datasetAccessions DAO for which this is a cache adapter.
 *        We use PHP object interface for future flexibility (The O and L in SOLID principles)
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class AuthorisedDatasetAccessions extends yii\base\BaseObject implements DatasetAccessionsInterface
{
    private $_datasetAccessions;
    private $_currentUser;

    public function __construct(CWebUser $current_user, DatasetAccessionsInterface $datasetAccessions)
    {
        parent::__construct();
        $this->_currentUser = $current_user;
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
        $links_with_preferred_source = $this->identifyPreferredSourceForLinks($primaryLinks);

        return $links_with_preferred_source;
    }
    public function getSecondaryLinks(): array
    {
        // retrieving the links from cache
        $secondaryLinks = $this->_datasetAccessions->getSecondaryLinks();
        $links_with_preferred_source = $this->identifyPreferredSourceForLinks($secondaryLinks);

        return $links_with_preferred_source;
    }

    /**
     * no need to format that one, so delegate straight to the adaptee class
     */
    public function getPrefixes(): array
    {
        return $this->_datasetAccessions->getPrefixes();
    }

    /**
     * Identify the preferred source of current logged user and assign it to each link
     *
     * @uses LinkWithPreference.php
     * @return array
     */
    private function identifyPreferredSourceForLinks(array $links): array
    {
        $links_with_preferred_source = [];

        // retrieving the preferred link source if the current user is logged in
        if ($this->_currentUser->getIsGuest()) {
            $link_type = '';
        } else {
            $link_type = $this->_currentUser->getState("preferred_link") ?? '';
        }

        foreach ($links as $link) {
            array_push($links_with_preferred_source, new LinkWithPreference($link, $link_type));
        }

        return $links_with_preferred_source;
    }
}
