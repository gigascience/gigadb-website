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
Use Yii;

class ReviewerSteps #extends \common\tests\AcceptanceTester
{
    const MOCKUP_LIFETIME = 1 ;
	protected $I;
    public $mockupUrl;
    public $dt;


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
     * @When I browse to the mockup url
     */
    public function iBrowseToTheMockupUrl()
    {
        $this->I->amOnPage($this->mockupUrl);
    }

}