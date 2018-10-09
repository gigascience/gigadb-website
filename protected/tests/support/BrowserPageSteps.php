<?php

/**
 * browser automation steps to visit the web site's HTML and XML endpoint.
 * It optionally assert for content to be present on the page.
 *
 * This trait is to be used in functional tests
 * This trait's function is to ensure browser mediation is performed only in one place as much as possible.
 * Making it easier to change browser mediation framework as needed
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
trait BrowserPageSteps
{

	/**
	 * Visit a page at $url and optionally assert for presence of $content in the page
	 *
	 * @param string $url
	 * @param string $content
	 */
	public function visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, $content)
	{
        $this->session->visit($url);

        // Validate text presence on a page.
        if (null !== $content) {
	        $this->assertTrue($this->session->getPage()->hasContent($content));
        }

	}

	/**
	 * Visit a page at $url, fetch the content and create an SimpleXMLelement object with the content
	 *
	 * @uses \SimpleXMLElement::new()
	 * @param string $url
	 * @return SimpleXMLElement
	 */
	public function getXMLWithSessionAndUrl($url)
	{
		$this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, null);
		$xml = $this->session->getPage()->getContent();
		 // Loading content into an XML structure
        $feed = new SimpleXMLElement($xml);
        return $feed;
	}

	/**
	 * Assert for presence of $content in the current page
	 *
	 * @param string $content
	 */
	public function assertPageHasContent($content)
	{
		$this->assertTrue( $this->session->getPage()->hasContent($content) );
	}

	/**
	 * Return url for the current page
	 *
	 * @return string
	 */
	public function getCurrentUrl()
	{
		return $this->session->getCurrentUrl();
	}

	/**
	 * Return status code of the last request
	 *
	 * @return int
	 */
	public function getStatusCode()
	{
		return $this->session->getStatusCode();
	}

}
?>