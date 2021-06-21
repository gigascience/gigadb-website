<?php

use \Behat\Gherkin\Node\TableNode;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */



    /**
     * @Given the tool is configured
     */
    public function theToolIsConfigured()
    {
        //TODO check config are valid
        true;
    }

    /**
     * @When I run the command :command with options :options
     */
    public function iRunTheCommandWithOptions($command, $options)
    {
        $this->runShellCommand("$command $options");

    }

    /**
     * @Then I should see :output
     */
    public function iShouldSee($output)
    {
        $this->seeInShellOutput($output);
    }

    /**
     * @Given there are files attached to datasets:
     *
     * | dataset.identifier | file.location | dataset.ftp_site |
     */
    public function thereAreFilesAttachedToDatasets(TableNode $files)
    {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $doi = $row[0];
            $location = $row[1];
            $ftp_site = $row[2];


            $this->assertNotNull(
                $this->grabFromDatabase("dataset",'id',['identifier' => $row[0], 'ftp_site' =>$row[2] ]),
                "Dataset with identifier {$row[0]} and ftp_site {$row[2]} exist"
            );

        }
    }

    /**
     * @When I run the update script on datasets:
     */
    public function iRunTheUpdateScriptOnDatasets(TableNode $datasets)
    {
        foreach($datasets->getRows() as $index => $row) {
            if ($index === 0)
                continue;
            $id = $this->grabFromDatabase('dataset','id',['identifier' => $row[0]]);
            $after = $id - 1;
            $this->runShellCommand("./yii dataset-files/update-ftp-urls --next 1 --after {$after} --verbose");
        }
    }

    /**
     * @When I run the update script on datasets in dry run mode:
     */
    public function iRunTheUpdateScriptOnDatasetsInDryRunMode(TableNode $datasets)
    {
        foreach($datasets->getRows() as $index => $row) {
            if ($index === 0)
                continue;
            $id = $this->grabFromDatabase('dataset','id',['identifier' => $row[0]]);
            $after = $id - 1;
            $this->runShellCommand("./yii dataset-files/update-ftp-urls --next 1 --after {$after} --verbose --dryrun");
        }
    }


    /**
     * @When I navigate to the dataset pages:
     */
    public function iNavigateToTheDatasetPages(TableNode $datasets)
    {
        $this->amOnUrl(Helper\DatasetFilesGrabber::TARGET_URL);
        foreach($datasets->getRows() as $index => $row) {
            if ($index === 0)
                continue;
            $this->amOnPage("/dataset/{$row[0]}");
            Helper\DatasetFilesGrabber::$datasetUrls[$row[0]] = Helper\DatasetFilesGrabber::TARGET_URL."dataset/{$row[0]}";
        }
    }

    /**
     * @Then I see in the respective files tab:
     */
    public function iSeeInTheRespectiveFilesTab(TableNode $files)
    {

        $this->amOnUrl(Helper\DatasetFilesGrabber::TARGET_URL);
        foreach($files->getRows() as $index => $row) {
            if ($index === 0)
                continue;
            $this->amOnPage("/dataset/{$row[0]}");
            $this->assertContains($row[1],$this->grabPageSource());
            $ftpSite = "<a target=\"_blank\" class=\"button\" title=\"FTP site\" href=\"{$row[2]}\">(FTP site)</a>";
            $this->assertContains($ftpSite, $this->grabPageSource());
        }
    }


}
