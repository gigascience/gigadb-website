<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;

/**
 * Contains the steps definitions used in author-names.feature and name-preview.feature
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
        $keywords_arr = [];
        foreach($table as $row) {
            $keywords_arr[] =  $row['Keywords'];
        }
        $this->gigadbWebsiteContext->iSignInAsAnAdmin();
        $this->minkContext->visit("/dataset/update/id/80");
        $this->minkContext->fillField("keywords", implode(",", $keywords_arr) );
        $this->minkContext->pressButton("Save");
        $this->minkContext->assertResponseContains("Genome sequence of the duck");
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
        PHPUnit_Framework_Assert::assertEquals($arg2, $linkNode->getAttribute('href') );
        PHPUnit_Framework_Assert::assertEquals($arg1, $linkNode->getText() );
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
        PHPUnit_Framework_Assert::assertEquals("mailto:liuxin@genomics.org.cn", $contactButton->getAttribute('href') );
    }

    /**
     * @Then I should see a button :arg1
     */
    public function iShouldSeeAButton($arg1)
    {
        PHPUnit_Framework_Assert::assertTrue( $this->minkContext->getSession()->getPage()->hasLink($arg1) );
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
     * @Then I should see :arg1 tab with table
     */
    public function iShouldSeeTabWithTable($arg1, TableNode $table)
    {
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


    /**
     * @Given I have added :arg1 link :arg2 to dataset :arg3
     */
    public function iHaveAddedLinkToDataset($arg1, $arg2, $arg3)
    {
        if ("3D Viewer" == $arg1 ) {
            $this->gigadbWebsiteContext->loadUserData("3D_Viewer_${arg3}_test_data");
        }
    }



}
