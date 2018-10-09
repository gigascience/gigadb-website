<?php

/**
 * Browser automation steps for finding stuff on the page
 *
 * This trait is to be used in functional tests
 * This trait's function is to ensure browser mediation is performed only in one place as much as possible.
 * Making it easier to change browser mediation framework as needed
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait BrowserFindSteps
{

    /**
     * Find a link on the page
     *
     * @param string $keyword the text|id|alt content to find
     * @return mixed DOM element (NodeElement if Mink is used) or null if not found
     */
	public function findLink($keyword)
	{
        return $this->session->getPage()->findLink($keyword);
	}

    /**
     * Find all node that matches a CSS path
     *
     * @param string $css_path css path (can be obtained using a browser dev tools)
     * @return mixed DOM element (NodeElement[] if Mink is used)
     */
    public function findAllByCSS($css_path)
    {
        return $this->session->getPage()->findAll('css',$css_path);
    }

    /**
     * Run a regex on a DOM node for a pattern
     *
     * @param NodeElement $node
     * @param string $pattern
     * @return array matches
     */
    public function nodeMatch($node, $pattern)
    {
        preg_match($pattern, $node->getHtml(), $matches);
        return $matches;
    }

}
?>