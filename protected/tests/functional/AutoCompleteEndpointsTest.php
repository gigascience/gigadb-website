<?php

 /**
 * Functional test for autoComplete service
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class AutoCompleteEndpointsTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("user@gigadb.org","gigadb","John's GigaDB Page");
    }

    /**
     * The autocomplete action for AdminDatasetController returns like terms
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     * @dataProvider termsProvider
     */
    public function testItShouldDisplayArrayOfTermsForDatasetSample($term, $expectation)
    {

        // Make a call to the autoComplete endpoint in the web site
        $url = "http://gigadb.dev/adminDatasetSample/autocomplete?term=$term" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, $expectation);

    }


    /**
     * The autocomplete action for AdminExternalLinkController returns like terms
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     *
     * @dataProvider termsProvider
     */
    public function testItShouldDisplayArrayOfTermsForExternalLink($term, $expectation)
    {

        // Make a call to the autoComplete endpoint in the web site
        $url = "http://gigadb.dev/adminExternalLink/autocomplete?term=$term" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull($url, $expectation);

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