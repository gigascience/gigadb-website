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
            $this->minkContext->getSession()->getPage()->findLink($row['Author']);
        }
    }

    /**
     * @Then I should see links to all associated peer-reviewed publications
     */
    public function iShouldSeeLinksToAllAssociatedPeerReviewedPublications(TableNode $table)
    {
        foreach($table as $row) {
            $this->minkContext->getSession()->getPage()->findLink($row['Publications']);
        }
    }

    /**
     * @Then I should see links to :arg1
     */
    public function iShouldSeeLinksTo($arg1, TableNode $table)
    {
        foreach($table as $row) {
            $this->minkContext->getSession()->getPage()->findLink($row[$arg1]);
        }
    }

    /**
     * @Given I have added the following keywords to dataset :arg1
     */
    public function iHaveAddedTheFollowingKeywordsToDataset($arg1, TableNode $table)
    {
        $this->gigadbWebsiteContext->iSignInAsAnAdmin();
        $this->minkContext->visit("/dataset/update/id/211");
        $this->minkContext->fillField("keywords", implode( ", ", $table->getRows() ) );
        $this->minkContext->pressButton("Save");
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
        $this->minkContext->getSession()->getPage()->hasContent($arg1);
        $this->minkContext->getSession()->getPage()->hasLink($arg2);
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

}
