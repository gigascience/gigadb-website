<?php
namespace common\tests\Step\Acceptance;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
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
              'auth_key' => Yii::$app->security->generateRandomString(6),
              'password_hash' => Yii::$app->security->generateRandomString(6),
              'email' => $reviewerEmail,
              'created_at' => date("U"),
              'updated_at' => date("U"),
            ]);
        $this->I->amConnectedToDatabase(\Codeception\Module\Db::DEFAULT_DATABASE);

        $this->mockupUrl = "/dataset/mockup/uuid/".$uuid->toString();
    }

    /**
     * @Given file uploads with samples and attributes have been uploaded for DOI :arg1
     */
     public function fileUploadsWithSamplesAndAttributesHaveBeenUploadedForDOI($doi)
     {

        file_put_contents("/var/tmp/processing_flag/$doi", "flag");
        //Retrieve the filedrop_account to attach the uploads
        $this->I->amConnectedToDatabase('fuwdb');
        $filedropId = $this->I->grabFromDatabase('filedrop_account','id', array('doi' => $doi));
        if(!$filedropId) {
            Yii::error("FiledropAccount ID could not be retrieved");
        }

        $config = require "/app/common/config/main-local.php";
        $dbh_fuw = new \PDO(
                            $config["components"]["db"]["dsn"], 
                            $config["components"]["db"]["username"], 
                            $config["components"]["db"]["password"]
                        );

        $files = [
            [
                'doi' => $doi,
                'name' => "seq1.fa",
                'size' => 24564343,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://climb.genomics.cn/pub/10.5524/000007/seq1.fa",
                'extension' => 'FASTA',
                'datatype' => 'Sequence assembly',
                'sample_ids' => 'Sample A, Sample Z'
                ],
          [
                'doi' => $doi,
                'name' => "Specimen.pdf",
                'size' => 19564,
                'status' => Upload::STATUS_UPLOADING,
                'location' => "ftp://climb.genomics.cn/pub/10.5524/000007/Specimen.pdf",
                'extension' => 'PDF',
                'datatype' => 'Annotation',
                'sample_ids' => 'Sample E'
                ]
        ];

        $temps = [ 45, 51];
        $brightness = [ 75, 90];

        $insertFilesQuery = "insert into upload(doi, name, size, status, location, extension, datatype, sample_ids,filedrop_account_id) values(:doi, :name, :size, :status, :location, :extension, :datatype, :sample_ids, :account) returning id";
        $insertFilesStatement = $dbh_fuw->prepare($insertFilesQuery);
        foreach ($files as $file) {
            $insertFilesStatement->bindValue(':doi',$file['doi']);
            $insertFilesStatement->bindValue(':name',$file['name']);
            $insertFilesStatement->bindValue(':size',$file['size']);
            $insertFilesStatement->bindValue(':status',$file['status']);
            $insertFilesStatement->bindValue(':location',$file['location']);
            $insertFilesStatement->bindValue(':extension',$file['extension']);
            $insertFilesStatement->bindValue(':datatype',$file['datatype']);
            $insertFilesStatement->bindValue(':sample_ids',$file['sample_ids']);
            $insertFilesStatement->bindValue(':account',$filedropId);
            $isSuccess = $insertFilesStatement->execute();
            if(!$isSuccess) {
                Yii::error("Failed to write DB record for {$file['name']}");
            }
            $returnId = $insertFilesStatement->fetch(\PDO::FETCH_OBJ);
            $this->uploadIds[] = $returnId->id;

        }

        foreach($this->uploadIds as $uploadId) {
            $attributes = [
                [
                    'name' => "Temperature",
                    'value' => array_pop($temps),
                    'unit' => "degree celsius",
                    'upload_id' => $uploadId,
                ],
                [
                    'name' => "Brightness",
                    'value' => array_pop($brightness),
                    'unit' => "lumen",
                    'upload_id' => $uploadId,
                ]
            ];

            $insertAttributesQuery = "insert into public.attribute(name, value, unit, upload_id) values(:name, :value, :unit, :upload_id) returning id";
            $insertAttributesStatement = $dbh_fuw->prepare($insertAttributesQuery);
            foreach ($attributes as $attribute) {
                $insertAttributesStatement->bindValue(':name',$attribute['name']);
                $insertAttributesStatement->bindValue(':value',$attribute['value']);
                $insertAttributesStatement->bindValue(':unit',$attribute['unit']);
                $insertAttributesStatement->bindValue(':upload_id',$attribute['upload_id']);
                $isSuccess = $insertAttributesStatement->execute();
            }
        }

        file_put_contents("/var/repo/$doi/".$files[0]["name"], Yii::$app->security->generateRandomString(32));
        file_put_contents("/var/repo/$doi/".$files[1]["name"], Yii::$app->security->generateRandomString(32));
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
            $this->I->canSeeElement("a[href='ftp://climb.genomics.cn/pub/10.5524/000007/$row[0]']");
        }
     }


}