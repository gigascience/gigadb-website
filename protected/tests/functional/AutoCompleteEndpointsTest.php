<?php

use aik099\PHPUnit\BrowserTestCase;

class AutoCompleteEndpointsTest extends BrowserTestCase
{

	public static $browsers = array(
        array(
            'driver' => 'goutte',
            'browserName' => 'goutte',
            'baseUrl' => 'http://gigadb.dev',
        ),
    );

    public function setUp()
    {
        // This is Mink's Session.
        $session = $this->getSession();

        //First login as a registered user
        $session->visit("http://gigadb.dev/site/login");
        $session->getPage()->fillField("LoginForm_username", "user@gigadb.org");
        $session->getPage()->fillField("LoginForm_password", "gigadb");
        $session->getPage()->pressButton("Login");

        $this->assertTrue($session->getPage()->hasContent("John's GigaDB Page"));
    }

    /**
     * The autocomplete action for AdminDatasetController returns like terms
     *
     * @dataProvider termsProvider
     */
    public function testItShouldDisplayArrayOfTermsForDatasetSample($term, $expectation)
    {
    	// This is Mink's Session. We assumed we are already logged in.
        $session = $this->getSession();

        // Make a call to the autoComplete endpoint in the web site
        $url = "http://gigadb.dev/adminDatasetSample/autocomplete?term=$term" ;
        $session->visit($url);

        //Check that the content of the page match our $expectation given $term
        $this->assertTrue(
            $session->getPage()->hasContent($expectation)
        );

    }


    /**
     * The autocomplete action for AdminExternalLinkController returns like terms
     *
     * @dataProvider termsProvider
     */
    public function testItShouldDisplayArrayOfTermsForExternalLink($term, $expectation)
    {
        // This is Mink's Session. We assumed we are already logged in.
        $session = $this->getSession();

        // Make a call to the autoComplete endpoint in the web site
        $url = "http://gigadb.dev/adminExternalLink/autocomplete?term=$term" ;
        $session->visit($url);

        //Check that the content of the page match our $expectation given $term
        $this->assertTrue(
            $session->getPage()->hasContent($expectation)
        );

    }


    /**
     * provide terms and expected results
     *
     * @return array[][]
     */
    public function termsProvider()
    {
        return [
            "partial" => ["guin", "[\"9238:Adelie penguin,Pygoscelis adeliae\"]" ],
            "not found" => ["clown", "[]" ],
        ];
    }
}

?>