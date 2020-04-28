<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \backend\models\FiledropAccount;
use \Facebook\WebDriver\WebDriverElement;
use \Behat\Gherkin\Node\TableNode;
use \Codeception\Util\ActionSequence;

use Yii;
use common\models\Upload;
use backend\models\DockerManager;

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
            $nbColumns = count($row);
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            foreach(range(0,$nbColumns-1) as $column) {
                $this->I->see($row[$column]);
            }
        }
     }

    /**
     * @Then I should see metadata
     */
     public function iShouldSeeMetadata(TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->I->see($row[0]);
            $this->I->seeElement([ 'css' => 'form input' ], [ 'value' => $row[1] ]);
            $this->I->seeElement([ 'css' => 'form select' ], [ 'value' => $row[2] ]);
        }
     }


    /**
     * @Given file uploads with attributes for DOI :arg1 exist
     */
     public function fileUploadsWithAttributesForDOIExist($doi)
     {
        $rand1 = Yii::$app->security->generateRandomString(6);
        $rand2 = Yii::$app->security->generateRandomString(6);
        // Database record
        $this->I->amConnectedToDatabase('fuwdb');
        $uploadId1 = $this->I->haveInDatabase('upload', [
                'doi' => $doi,
                'name' => $rand1.".csv",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://".$rand1,
                'extension' => 'CSV',
                'datatype' => 'Text'
          ]);
        $this->I->haveInDatabase('attribute', [
                'name' => "Attribute A",
                'value' => "42",
                'unit' => "Metre",
                'upload_id' => $uploadId1,
          ]);
        $uploadId2 = $this->I->haveInDatabase('upload', [
                'doi' => $doi,
                'name' => $rand2.".jpg",
                'size' => 34564343334,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://".$rand2,
                'extension' => 'JPG',
                'datatype' => 'Image'
          ]);   

        $this->I->seeInDatabase('upload',[ "name" => $rand1.".csv" ]);
        $this->I->seeInDatabase('attribute',[ "name" => "Attribute A" ]);
        $this->I->seeInDatabase('upload',[ "name" => $rand2.".jpg" ]);
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
        
     }

    /**
     * @Given I press the first delete button
     */
     public function iPressTheFirstDeleteButton()
     {
        $this->I->click('.delete-button-0');
     }

    /**
     * @When I attach the file :arg1
     */
     public function iAttachTheFile($arg1)
     {
         $this->I->attachFile('#bulkmetadata',$arg1);
     }

    /**
     * @When I press Attributes button for :arg1
     */
     public function iPressAttributesButtonFor($arg1)
     {
        $this->I->click(".$arg1");
     }

}