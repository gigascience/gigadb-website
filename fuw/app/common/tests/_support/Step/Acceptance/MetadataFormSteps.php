<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \backend\models\FiledropAccount;
use \Facebook\WebDriver\WebDriverElement;
use \Behat\Gherkin\Node\TableNode;
use \Codeception\Util\ActionSequence;

class MetadataFormSteps
{
	protected $I;


	public function __construct(\common\tests\AcceptanceTester $I)
	{
	    $this->I = $I;
	}

	/**
     * @Then I should see form elements:
     */
    public function iShouldSeeFormElements(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->seeElement([ 'css' => $row[1] ], [ 'id' => "upload-$index-datatype" ]);
            $this->I->seeElement([ 'css' => $row[3] ], [ 'id' => "upload-$index-description" ]);
            $this->I->seeElement([ 'css' => $row[4] ], [ 'id' => "upload-$index-tag" ]);
            $this->I->seeElement([ 'css' => $row[5] ], [ 'id' => "upload-$index-delete" ]);
        }
     }

     /**
     * @When I fill in the form with
     */
     public function iFillInTheFormWith(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->selectOption("form select[id=upload-$index-datatype]", $row[1]);
            $this->I->fillField("form input[id=upload-$index-description]", $row[2]);
        }
     }

    /**
     * @Then I should see a text input field :arg1
     */
     public function iShouldSeeATextInputField($arg1)
     {
         $this->I->seeElement('input', ['name' => mb_strtolower($arg1)]);     }

    /**
     * @When I fill in :arg1 with :arg2
     */
     public function iFillInWith($arg1, $arg2)
     {
        $this->I->fillField("form input[name=".mb_strtolower($arg1)."]", $arg2);
     }

    /**
     * @Then I should see
     */
     public function iShouldSee(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->see($row[0]);
            $this->I->see($row[1]);
            $this->I->see($row[2]);

        }
     }



}