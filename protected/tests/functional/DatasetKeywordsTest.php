<?php

 /**
 * Functional test for semantic keywords management on Dataset forms
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
*/
class DatasetKeywordsTest extends FunctionalTesting
{
    use BrowserSignInSteps;
    use BrowserPageSteps;
    use BrowserFormSteps;
    use BrowserFindSteps;

    /**
     *
     * @uses \BrowserSignInSteps::loginToWebSiteWithSessionAndCredentialsThenAssert()
     */
    public function setUp()
    {
        parent::setUp();
        $this->loginToWebSiteWithSessionAndCredentialsThenAssert("admin@gigadb.org","gigadb","Admin");
    }

    /**
     * DatasetController::actionUpdate update keywords in the database
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \BrowserFormSteps::fillDatasetUpdateFormJustKeywords()
     * @uses \BrowserFindSteps::findLink()
     *
     * @dataProvider keywordsProvider
     */
    public function testItShouldUpdateKeywordsOnUpdate($input, $expectation)
    {

        // Make a call to the dataset update form
        $url = "http://gigadb.dev/adminDataset/update/id/8" ;
        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull( $url, null);

        $this->fillDatasetUpdateFormJustKeywords($input);

        // Check that after submission we land on the dataset view
        $this->assertEquals( "http://gigadb.dev/dataset/100006", $this->getCurrentUrl() );

        // Check that the content of the page match our $expectation given $input
        foreach($expectation as $keyword) {
	        $this->assertNotNull($this->findLink($keyword), "$keyword" );
        }

    }


    /**
     * test that DatasetController::actionCreate1 add keywords in the database
     * and that DatasetController::actionDatasetManagement update them.
     *
     * @uses \BrowserPageSteps::visitPageWithSessionAndUrlThenAssertContentHasOrNull()
     * @uses \BrowserPageSteps::assertPageHasContent()
     * @uses \BrowserPageSteps::getCurrentUrl()
     * @uses \BrowserFormSteps::fillDatasetCreate1FormDummyFieldsJustKeywords()
     *
     * @dataProvider keywordsProvider
     */
//    public function testItShouldUpdateKeywordsOnCreate1OnManagement($input, $expectation)
//    {
//
//        // Make a call to the dataset update form
//        $url = "http://gigadb.dev/datasetSubmission/create1" ;
//        $this->visitPageWithSessionAndUrlThenAssertContentHasOrNull( $url, null);
//
//        $this->fillDatasetCreate1FormDummyFieldsJustKeywords($input);
//
//
//        // Check that after submission we land on the author tab
//        $this->assertStringStartsWith('http://gigadb.dev/datasetSubmission/authorManagement/id/', $this->getCurrentUrl());
//
//        // Lets navigate back to the Dataset Form
//        $this->session->getPage()->clickLink("Study");
//        $this->assertStringStartsWith('http://gigadb.dev/datasetSubmission/datasetManagement/id/', $this->getCurrentUrl());
//
//        // Check that the content of the page match our $expectation given $input
//        foreach($expectation as $keyword) {
//	        $this->assertPageHasContent($keyword);
//        }
//
//        // Check that we can still update from the datasetManagement screen
//        $suffix = function ($item) {
//        	return trim($item)."2";
//        };
//        $new_input = implode(",", array_map($suffix, explode(",",$input)));
//
//      //   $this->session->getPage()->fillField("keywords", $new_input);
//      //   $this->session->getPage()->checkField("Images[is_no_image]");
//     	// $this->session->getPage()->pressButton("Next");
//        $this->fillDatasetManagementFormJustKeywords($new_input);
//
//     	// Check that after submission we land on the author tab
//        $this->assertStringStartsWith('http://gigadb.dev/datasetSubmission/authorManagement/id/', $this->getCurrentUrl());
//
//        // Lets navigate back to the Dataset Form
//        $this->session->getPage()->clickLink("Study");
//        $this->assertStringStartsWith('http://gigadb.dev/datasetSubmission/datasetManagement/id/', $this->getCurrentUrl());
//
//        // Check that the content of the page match our $expectation given $input
//        foreach($expectation as $keyword) {
//	        $this->assertPageHasContent($keyword."2");
//        }
//    }

    public function keywordsProvider()
    {
    	return [
    		"no keyword" => ["", []],
    		"two clean keywords" => ["bam, dam", ["bam", "dam"]],
    		"two keywords and one dodgy" => [" am",
    										 ["am"]],
    	];
    }

}
?>