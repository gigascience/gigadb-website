<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use \backend\models\FiledropAccount;
use \Facebook\WebDriver\WebDriverElement;
use \Behat\Gherkin\Node\TableNode;
use \Codeception\Util\ActionSequence;
use Ramsey\Uuid\Uuid;
use Lcobucci\JWT\Builder;
use common\models\Upload;
Use Yii;

class ReviewerSteps #extends \common\tests\AcceptanceTester
{
    const MOCKUP_LIFETIME = 1 ;
	protected $I;
    public $mockupUrl;
    public $dt;
    public $uploadIds;


	public function __construct(\common\tests\AcceptanceTester $I)
	{
	    $this->I = $I;
	}

    /**
     * @Given a mockup url has been created for reviewer :arg1 and dataset with DOI :arg2
     */
     public function aMockupUrlHasBeenCreatedForReviewerAndDatasetWithDOI($reviewerEmail, $doi)
    {
        $this->dt = new \DateTime();
        $validity = 1;
        $signer = new \Lcobucci\JWT\Signer\Hmac\Sha256();
        $issuedTime = $this->dt->format('U');
        $notBeforeTime = $issuedTime ;
        $expirationTime = $this->dt->modify("+$validity months")->format('U');

        // Generate a valid JWT token
        
        $client_token = (new Builder())
            ->setIssuer('www.gigadb.org') // Configures the issuer (iss claim)
            ->setAudience('fuw.gigadb.org') // Configures the audience (aud claim)
            ->setSubject('JWT token for a unique and time-limited mockup url') // Configures the subject
            ->set('reviewerEmail', $reviewerEmail)
            ->set('monthsOfValidity', $validity)
            ->set('DOI', $doi)
            ->setIssuedAt($issuedTime) // Configures the time that the token was issue (iat claim)
            ->setNotBefore($notBeforeTime) // Configures the time before which the token cannot be accepted (nbf claim)
            ->setExpiration($expirationTime) // Configures the expiration time of the token (exp claim) 1 year
            ->sign($signer, Yii::$app->jwt->key)// creates a signature using [[Jwt::$key]]
            ->getToken(); // Retrieves the generated token

        // Generate a UUID
        $uuid = Uuid::uuid4();

        // Create a mockup_url record
        $this->I->amConnectedToDatabase('fuwdb');
        $mockupUrl = $this->I->haveInDatabase('mockup_url', [
                'url_fragment' => $uuid->toString(),
                'jwt_token' => (string) $client_token,
          ]);

        // create a user record
        $this->I->amConnectedToDatabase('fuwdb');
        $this->I->haveInDatabase('public.user', [
              'username' => "${reviewerEmail}_$doi",
              'auth_key' => FiledropAccount::generateRandomString(6),
              'password_hash' => FiledropAccount::generateRandomString(6),
              'email' => $reviewerEmail,
              'created_at' => date("U"),
              'updated_at' => date("U"),
            ]);
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);

        $this->mockupUrl = "/dataset/mockup/uuid/".$uuid->toString();
    }

    /**
     * @Given file uploads have been uploaded for DOI :arg1
     */
     public function fileUploadsHaveBeenUploadedForDOI($doi)
     {
        $this->I->amConnectedToDatabase('fuwdb');
        $this->uploadIds[] = $this->I->haveInDatabase('public.upload', [
                'doi' => $doi,
                'name' => "seq1.fa",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://seq1.fa",
                'extension' => 'FASTA',
                'datatype' => 'Sequence assembly'
          ]);
        $this->uploadIds[] = $this->I->haveInDatabase('public.upload', [
                'doi' => $doi,
                'name' => "Specimen.pdf",
                'size' => 19564,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://Specimen.pdf",
                'extension' => 'PDF',
                'datatype' => 'Annotation'
          ]);
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
     }

    /**
     * @Given there are file attributes associated with those files
     */
     public function thereAreFileAttributesAssociatedWithThoseFiles()
     {
        $temps = [ 45, 51];
        $humidities = [ 75, 90];
        $this->I->amConnectedToDatabase('fuwdb');
        foreach($this->uploadIds as $uploadId) {

            $this->I->haveInDatabase('public.attribute', [
                'name' => "Temperature",
                'value' => array_pop($temps),
                'unit' => "Celsius",
                'upload_id' => $uploadId,
            ]);

            $this->I->haveInDatabase('public.attribute', [
                'name' => "Humidity",
                'value' => array_pop($humidities),
                'unit' => "Celsius",
                'upload_id' => $uploadId,
            ]);
        }
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
     }

     /**
     * @When I browse to the mockup url
     */
    public function iBrowseToTheMockupUrl()
    {
        $this->I->amOnPage($this->mockupUrl);
    }

    /**
     * @Then I should see the files
     */
    public function iShouldSeeTheFiles(TableNode $files)
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
     * @Then there is a download link for each file associated with DOI :arg1
     */
     public function thereIsADownloadLinkForEachFileAssociatedWithDOI($doi,TableNode $files)
     {
        foreach ($files->getRows() as $index => $row) {
            $nbColumns = count($row);
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            
            $this->I->amConnectedToDatabase('fuwdb');
            $location = $this->I->grabFromDatabase('public.upload', 'location', ['name' => $row[0] ]);
            $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);
            $this->I->canSeeElement("a[href='$location']");
        }
     }


}