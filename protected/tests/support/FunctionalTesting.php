<?php

use aik099\PHPUnit\BrowserTestCase;

 /**
 * Initialisation and common setup for functional tests
 *
 * Integration with Browser automation is done here too
 * All funtional test should inherit from this class
 * If they define their own setUp(), they should still call parent::setUp()
 * If they define their own tearDown(), they should still call parent::tearDown()
 *
 * @uses \BrowserTestCase::getSession()
 * @property mixed $session browser automation session
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class FunctionalTesting extends BrowserTestCase
{
	/** @var mixed browser session to be used by test cases */
	protected $session;

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

	/**
	 * create a browser session and assign it to the $session property
	 *
	 */
    public function setBrowserSession()
    {
    	$this->session = $this->getSession();
    }

	/**
	 * Destroy the browser session and unassign the $session property
	 *
	 */
    public function unsetBrowserSession()
    {
    	$this->session = null;
    }

    /**
	 * Common PHPUnit setUp function for all functional test cases to create browser session
	 *
	 */
    public function setUp()
    {
        $this->setBrowserSession();
    }

    /**
	 * Common PHPUnit tearDown function for all functional test cases to destroy browser session
	*/
    public function tearDown()
    {
        $this->unsetBrowserSession();
    }
}
?>