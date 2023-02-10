<?php

/**
 * This is a Read-only adapter to the Link model class that add a  "preferred_source" ephemeral property to the model
 *
 * That property is not meant to be saved in database.
 * The other properties available Link are accessible from this class too through delegation
 *
 * @param Link $link the link instance to enrich with addtional property
 * @param string $preferred_source  hold the value of user->preferred_link for current user
 *
 * @property string $prefered_source
 * @property int $id
 * @property int $dataset_id
 * @property string $link
 * @property boolean $is_primary
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class LinkWithPreference extends yii\base\BaseObject implements LinkInterface
{
    /**
     * @var string $_preferred_source hold the currently logged in  user preferred source for dataset links
     */
    private $_preferred_source;

    /**
     * @var Link $_link hold the Link object for which this is an adapter
     */
    private $_link;


    public function __construct(LinkInterface $link, string $preferred_source)
    {
        $this->_preferred_source = $preferred_source;
        $this->_link = $link;
    }

    public function getPreferred_Source(): string
    {
        return $this->_preferred_source;
    }

    public function getId()
    {
        return $this->_link->id;
    }

    public function getDataset_Id()
    {
        return $this->_link->dataset_id;
    }

    public function getLink()
    {
        return $this->_link->link;
    }

    public function getIs_Primary()
    {
        return $this->_link->is_primary;
    }

    public function getFullUrl(string $link_type = ''): string
    {
        return $this->_link->getFullUrl($link_type);
    }
}
