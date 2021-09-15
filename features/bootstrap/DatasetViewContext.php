<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;

/**
 * Contains the steps definitions used in author-names.feature and dataset-view.feature
 *
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 * @see http://docs.behat.org/en/latest/quick_start.html#defining-steps
 *
 * @uses GigadbWebsiteContext For loading production like data
 */
class DatasetViewContext implements Context
{
    private $surname = null;
    private $first_name = null;
    private $middle_name =  null;


    /**
     * @var GigadbWebsiteContext
     */
    private $gigadbWebsiteContext;
    private $minkContext;

    /**
     * The method to retrieve needed contexts from the Behat environment
     *
     * @param BeforeScenarioScope $scope parameter needed to retrieve contexts from the environment
     *
     * @BeforeScenario
     *
    */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->gigadbWebsiteContext = $environment->getContext('GigadbWebsiteContext');
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * @Given /^Gigadb web site is loaded with production-like data$/
     */
    public function gigadbWebSiteIsLoadedWithProductionLikeData()
    {
        $sqlfile = "production_like.pgdmp";
        // return array(
        //     new Step\Given("Gigadb web site is loaded with \"${sqlfile}\" data"),
        // );
        $this->gigadbWebsiteContext->gigadbWebSiteIsLoadedWithData($sqlfile);
    }

    /**
     * @Given /^author has surname "([^"]*)"$/
     */
    public function authorHasSurname($arg1)
    {
        $this->surname = $arg1 ;
    }

    /**
     * @Given /^author has first name "([^"]*)"$/
     */
    public function authorHasFirstName($arg1)
    {
        $this->first_name = $arg1 ;
    }

    /**
     * @Given /^author has middle name "([^"]*)"$/
     */
    public function authorHasMiddleName($arg1)
    {
        $this->middle_name = $arg1 ;
    }



    /**
     * Ensure name fields are reset before scenario run for author-names.feature and name-preview.feature
     *
     * @BeforeScenario @author-names-display&&@edit-display-name
     */
    public function reset()
    {
        $this->surname = null;
        $this->first_name = null;
        $this->middle_name = null;
    }


    /**
     * @Then I should see all the authors with links
     */
    public function iShouldSeeAllTheAuthorsWithLinks(TableNode $table)
    {
        foreach($table as $row) {
            PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->findLink($row['Author']) );
        }
    }

    /**
     * @Then I should see links to all associated peer-reviewed publications
     */
    public function iShouldSeeLinksToAllAssociatedPeerReviewedPublications(TableNode $table)
    {
        foreach($table as $row) {
            PHPUnit_Framework_Assert::assertTrue(
                $this->minkContext->getSession()->getPage()->hasLink($row['Publications'])
            );
        }
    }

    /**
     * @Then I should see links to :arg1
     */
    public function iShouldSeeLinksTo($arg1, TableNode $table)
    {
        foreach($table as $row) {
            PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasLink($row[$arg1]) );
        }
    }

    /**
     * @Given I have added the following keywords to dataset :arg1
     */
    public function iHaveAddedTheFollowingKeywordsToDataset($arg1, TableNode $table)
    {
        $this->gigadbWebsiteContext->loadUserData("Keywords_${arg1}_test_data");
        // $keywords_arr = [];
        // foreach($table as $row) {
        //     $keywords_arr[] =  $row['Keywords'];
        // }
        // $this->gigadbWebsiteContext->iSignInAsAnAdmin();
        // $this->minkContext->visit("/adminDataset/update/id/80");
        // $this->minkContext->fillField("keywords", implode(",", $keywords_arr) );
        // $this->minkContext->pressButton("Save");
        // $this->minkContext->assertResponseContains("Genome sequence of the duck");
    }

    /**
     * @Then I should see image :arg1 with title :arg2
     */
    public function iShouldSeeImageWithTitle($arg1, $arg2)
    {
        $imageNode = $this->minkContext->getSession()->getPage()->find('css',"img.media-object[src='$arg1']");
        if( $imageNode->hasAttribute('title') ) {
            PHPUnit_Framework_Assert::assertEquals($arg2, $imageNode->getAttribute('title'));
        }
    }

    /**
     * @Then I should see a :arg1 related links to :arg2
     */
    public function iShouldSeeARelatedLinksTo($arg1, $arg2)
    {
        PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasContent($arg1) );
        PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasLink($arg2) );
    }

    /**
     * @Then I should see image :arg1 linking to :arg2
     */
    public function iShouldSeeImageLinkingTo($arg1, $arg2)
    {
        $imageNode = $this->minkContext->getSession()->getPage()->find('css',"a img.dataset-des-images[src='$arg1']");
        PHPUnit_Framework_Assert::assertEquals($arg2, $imageNode->getParent()->getAttribute('href') );
    }

    /**
     * @Then I should see a link :arg1 to :arg2 with title :arg3
     */
    public function iShouldSeeALinkToWithTitle($arg1, $arg2, $arg3)
    {
        $linkNode = $this->minkContext->getSession()->getPage()->find('css',"a[title='$arg3']");
        PHPUnit_Framework_Assert::assertEquals($arg2, $linkNode->getAttribute('href'), "link target matches" );
        PHPUnit_Framework_Assert::assertEquals($arg1, $linkNode->getText(), "link text matches");
    }

    /**
     * @Given :arg1 external link is attached to dataset :arg2
     */
    public function externalLinkIsAttachedToDataset($arg1, $arg2)
    {

        $this->gigadbWebsiteContext->loadUserData("${arg1}_${arg2}_test_data");
    }

    /**
     * @Then I should see :arg1 tab with text :arg2
     */
    public function iShouldSeeTabWithText($arg1, $arg2)
    {
        $this->minkContext->getSession()->getPage()->clickLink($arg1);
        PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasContent($arg2) );
    }

    /**
     * @Then I should see a button :arg1 linking to submitter's email
     */
    public function iShouldSeeAButtonLinkingToSubmittersEmail($arg1)
    {
        $contactButton = $this->minkContext->getSession()->getPage()->findLink($arg1);
        PHPUnit_Framework_Assert::assertEquals("mailto:test+gigadb112@gigasciencejournal.com", $contactButton->getAttribute('href') );
    }

    /**
     * @Then I should see a button :arg1
     */
    public function iShouldSeeAButton($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasLink($arg1) );
    }

    /**
     * A function to simulate a key press
     * @param $key
     * @param $field_name
     */
    public function sendKeyPressFromField($key, $field_name)
    {
        $xpath = '//*[@id="'.$field_name.'"]';
        $this->minkContext->getSession()->getDriver()->getWebDriverSession()->element('xpath', $xpath)->postValue(['value' =>[$key]]);
    }

    /**
     * @When I hit return
     */
    public function iHitReturn()
    {
        $this->sendKeyPressFromField("\r\n","pageNumber");
    }

    /**
     * @Then I should see a button :arg1 with no link
     */
    public function iShouldSeeAButtonWithNoLink($arg1)
    {
         $contactButton = $this->minkContext->getSession()->getPage()->findLink($arg1);
         PHPUnit_Framework_Assert::assertEquals("#", $contactButton->getAttribute('href') );
    }

    /**
     * @Then I should see a button :arg1 with link :arg2
     */
    public function iShouldSeeAButtonWithLink($arg1, $arg2)
    {
         $button = $this->minkContext->getSession()->getPage()->findLink($arg1);
         PHPUnit_Framework_Assert::assertEquals($arg2, $button->getAttribute('href') );
    }

    /**
     * @Then I should not see a button :arg1
     */
    public function iShouldNotSeeAButton($arg1)
    {
        PHPUnit_Framework_Assert::assertFalse( $this->minkContext->getSession()->getPage()->hasLink($arg1) );
    }

    /**
     * @Given I have added awardee :arg1 to dataset :arg2
     */
    public function iHaveAddedAwardeeToDataset($arg1, $arg2)
    {
        $this->gigadbWebsiteContext->loadUserData("funding_for_${arg2}");
    }

    /**
     * @Then I should see a file attribute table
     */
    public function iShouldSeeAFileAttributeTable(TableNode $table)
    {
        foreach ($table as $row) {
            PHPUnit_Framework_Assert::assertTrue(
                $this->minkContext->getSession()->getPage()->hasContent($row['Attribute Name'])
            );
            PHPUnit_Framework_Assert::assertTrue(
                $this->minkContext->getSession()->getPage()->hasContent($row['Value'])
            );
            PHPUnit_Framework_Assert::assertTrue(
                $this->minkContext->getSession()->getPage()->hasContent($row['Unit'])
            );
        }
    }

    /**
     * @Then I should see a view file table with row name :arg1
     */
    public function iShouldSeeAViewFileTableWithRowName($arg1, TableNode $table)
    {
        foreach ($table as $row) {
            PHPUnit_Framework_Assert::assertTrue(
                $this->minkContext->getSession()->getPage()->hasContent($row[$arg1])
            );
        }
    }

    /**
     * @Then I should see :arg1 tab with table
     */
    public function iShouldSeeTabWithTable($arg1, TableNode $table)
    {
        if ("Funding" == $arg1) {
            $this->minkContext->getSession()->getPage()->clickLink($arg1);
            //| Funding body                    | Awardee           | Award ID      | Comments |
            foreach($table as $row) {
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Funding body'])
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Awardee'])
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Award ID'])
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Comments'])
                );
            }
        }
        elseif("Files" == $arg1) {
            //| File Name | Description | Data Type | Size | File Attributes | link |
            foreach($table as $row) {
                $link = $row['link'];
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['File Name']), "File Name match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Description']), "Description match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Data Type']), "Data Type match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Size']), "Size match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['File Attributes']), "File Attributes match"
                );
                if ($link) {
                    $this->minkContext->assertSession()->elementExists('css',"a.download-btn[href='$link']");
                }
            }
        }
        elseif("Sample" == $arg1) {
            //| Sample ID  | Common Name  | Scientific Name         | Sample Attributes                                                                                                                   | Taxonomic ID | Genbank Name |
            foreach($table as $row) {
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Sample ID']), "Sample ID match"
                );
                if($row['Common Name']) {
                    PHPUnit_Framework_Assert::assertTrue(
                        $this->minkContext->getSession()->getPage()->hasContent($row['Common Name']), "Common Name match"
                    );
                }
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Scientific Name']), "Scientific Name match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Sample Attributes']), "Sample Attributes match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Taxonomic ID']), "Taxonomic ID match"
                );
                PHPUnit_Framework_Assert::assertTrue(
                    $this->minkContext->getSession()->getPage()->hasContent($row['Genbank Name']), "Genbank Name match"
                );
            }
        }
        else {
            PHPUnit_Framework_Assert::fail("Unknown type of tab");
        }
    }

    /**
     * @Then I should not see :arg1 tab with table
     */
    public function iShouldNotSeeTabWithTable($arg1, TableNode $table)
    {
        //| File Name                                        | Sample ID  | Data Type         | File Format | Size      | Release date | link |
        foreach($table as $row) {
            if ("Files" == $arg1) {
                PHPUnit_Framework_Assert::assertFalse(
                    $this->minkContext->getSession()->getPage()->hasContent($row['File Name']), "File Name match"
                );
            }
            elseif("Sample" == $arg1) {
                if ($row['Sample ID']) {
                    PHPUnit_Framework_Assert::assertFalse(
                        $this->minkContext->getSession()->getPage()->hasContent($row['Sample ID']), "Sample ID match"
                    );
                }
                if ($row['Common Name']) {
                    PHPUnit_Framework_Assert::assertFalse(
                        $this->minkContext->getSession()->getPage()->hasContent($row['Common Name']), "Common Name match"
                    );
                }
            }
            else {
                PHPUnit_Framework_Assert::fail("Unknown type of tab");
            }
        }
    }


    /**
     * @Given I have added :arg1 link :arg2 to dataset :arg3
     */
    public function iHaveAddedLinkToDataset($arg1, $arg2, $arg3)
    {
        if ("3D Viewer" == $arg1 ) {
            $this->gigadbWebsiteContext->loadUserData("3D_Viewer_${arg3}_test_data");
        }
        elseif ("Code Ocean" == $arg1 ) {
            $this->gigadbWebsiteContext->loadUserData("Code_Ocean_${arg3}_test_data");
        }
        else {
            PHPUnit_Framework_Assert::fail("Unknown type of external link");
        }
    }


    /**
     * @Given I have set pageSize to :arg1 on :arg2
     */
    public function iHaveSetPagesizeToOn($arg1, $arg2)
    {
        sleep(1);
        $this->minkContext->clickLink("$arg2");
        sleep(1);
        if("samples_table_settings"  == $arg2) {
            $this->minkContext->selectOption("samplePageSize","$arg1");
            $this->minkContext->clickLink("save-samples-settings");
        }
        elseif("files_table_settings"  == $arg2) {
            $this->minkContext->selectOption("pageSize","$arg1");
            $this->minkContext->clickLink("save-files-settings");
        }
        else {
            PHPUnit_Framework_Assert::fail("Unknown type of option");
        }
    }

    /**
     * @Then I go to new tab and should see :arg1
     */
    public function iGoToNewTabAndShouldSee($arg1)
    {
        $session = $this->minkContext->getSession();
        $numberOfTab = $session->getWindowNames();
        if (sizeof($numberOfTab) < 2) {
            print ("Expected to see at least 2 windows opened.");
            exit;
        }
        $session->switchToWindow($numberOfTab[1]);

        PHPUnit_Framework_Assert::assertTrue($session->getPage()->hasContent($arg1));
    }

    /**
     * @When I click :arg1
     * To trigger onclick event
     */
    public function iClick($arg1)
    {
        $element = $this->minkContext->getSession()->getPage()->find('css', "a[id='$arg1']" );
        $element->click();
    }

    /**
     * @Then There is a meta tag :arg1 with value :arg2
     */
    public function thereIsAMetaTagWithValue($arg1, $arg2)
    {
        $metaNode = $this->minkContext->getSession()->getPage()->find('xpath', "//meta[@name='$arg1' and @content='$arg2']");
        PHPUnit_Framework_Assert::assertNotNull($metaNode);
    }
}
