<?php

namespace GigaScience;

use Aws\Sts\StsClient;
use Aws\Exception\AwsException;

/**
 * Class DeveloperSteps
 * steps specific to user story for curators
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 */
class DeveloperSteps extends \Codeception\Actor
{
    protected $I;
    protected $module;
    protected $targetDir;

    /** @const int represents the value of status code returned by successful CLI command */
    const EXIT_CODE_OK = 0 ;

    /** @const url of cnhk-infra variables  */
    const MISC_VARIABLES_URL = "https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables";

    /** @const url of Forks variables  */
    const FORKS_VARIABLES_URL = "https://gitlab.com/api/v4/groups/3501869/variables" ;

    public function __construct(\AcceptanceTester $I)
    {
        $this->I = $I;
        $this->targetDir = getenv("REPO_NAME") . "/" . (new \DateTimeImmutable())->format('Y-m-d.A');
    }

    /**
     * @Given I configure rclone with a :accountType account
     *
     *  - first retrieve the test access keys and secret keys from Gitlab variables
     *  - then, generate an Rclone configuration file from a Twig template, interpolating the variables from previous steps
     *  - finally, assert that the configuration has been generated correctly
     *
     * @param $accountType
     */
    public function iConfigureRcloneWithAAccount($accountType)
    {
        switch ($accountType) {
            case "Developer":
                $accessKeyToRetrieve = "CODECEPTDEV_WASABI_ACCESS_KEY_ID";
                $secretKeyToRetrieve = "CODECEPTDEV_WASABI_SECRET_ACCESS_KEY";
                break;
            case "Curator":
                $accessKeyToRetrieve = "CODECEPTCUR_WASABI_ACCESS_KEY_ID";
                $secretKeyToRetrieve = "CODECEPTCUR_WASABI_SECRET_ACCESS_KEY";
                break;
            case "Migration user":
                $accessKeyToRetrieve = "MIGRATION_ALT_WASABI_ACCESS_KEY_ID";
                $secretKeyToRetrieve = "MIGRATION_ALT_WASABI_SECRET_ACCESS_KEY";
                break;
        }

        list($accessKeyId, $secretKey) = $this->getWasabiCredentials(
            self::FORKS_VARIABLES_URL,
            $accessKeyToRetrieve,
            $secretKeyToRetrieve
        );

        $this->renderRcloneConfig($accessKeyId, $secretKey);

        $this->I->assertFileExists("/project/tests/_output/developer.conf");
    }

    /**
     * @When I run the command to list buckets
     */
    public function iRunTheCommandToListBuckets()
    {
        system("rclone --config=/project/tests/_output/developer.conf lsd wasabiTest:", $status);
        $this->I->assertEquals(self::EXIT_CODE_OK, $status);
    }

    /**
     * @Then I should see the list of buckets
     */
    public function iShouldSeeBuckets()
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf lsd wasabiTest:");
        $this->I->assertTrue(str_contains($output, " -1 gigadb-datasets"));
        $this->I->assertTrue(str_contains($output, " -1 test-gigadb-datasets"));
    }

    /**
     * @When I run the command to download file :file from the :env environment
     */
    public function iRunTheCommandToDownloadFileFromTheEnvironment($file, $env)
    {
        $outputDir =  (new \DateTimeImmutable())->format('Y-m-d-H') . "-" . getmypid();
        system("rclone --config=/project/tests/_output/developer.conf copy wasabiTest:gigadb-datasets/$env/$file /project/tests/_output/$outputDir/$env/$file", $status);
        $this->I->assertEquals(self::EXIT_CODE_OK, $status);
    }

    /**
     * @Then I can see :file on my local filesystem
     */
    public function iCanSeeOnMyLocalFilesystem($file)
    {
        $outputDir =  (new \DateTimeImmutable())->format('Y-m-d-H') . "-" . getmypid();
        $this->I->assertFileExists("/project/tests/_output/$outputDir/$file");
    }

    /**
     * @When I run the command to upload file :file to the :env environment
     */
    public function iRunTheCommandToUploadFileToTheEnvironment($file, $env)
    {
        $output = system("rclone --config=/project/tests/_output/developer.conf copy --s3-no-check-bucket /project/tests/_data/$file wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir, $status);
        codecept_debug($output);
    }

    /**
     * @Then I can see the file :file on the :env environment
     */
    public function iCanSeeTheFileOnTheEnvironment($file, $env)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir);
        codecept_debug($output);
        $this->I->assertTrue(str_contains($output, $file));
    }

    /**
     * @Then I cannot see the file :file on the :env environment
     */
    public function iCannotSeeTheFileOnTheEnvironment($file, $env)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir);
        $this->I->assertFalse(str_contains($output, $file));
    }


    /**
     * @When I run the command to delete the file :file on the :env environment
     */
    public function iRunTheCommandToDeleteTheFileOnTheEnvironment($file, $env)
    {
        system("rclone --config=/project/tests/_output/developer.conf delete --s3-no-check-bucket wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir . "/$file", $status);
    }

    /**
     * @When I run the command to delete existing file
     */
    public function iRunTheCommandToDeleteExistingFile()
    {
        system("rclone --config=/project/tests/_output/developer.conf delete --s3-no-check-bucket wasabiTest:gigadb-datasets/live/DoNotDelete.txt", $status);
    }
    /**
     * @Then the file is not deleted
     */
    public function theFileIsNotDeleted()
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:gigadb-datasets/live/DoNotDelete.txt");
        $this->I->assertTrue(str_contains($output, "  35 DoNotDelete.txt"));
    }


    /**
     * @When I run the command to delete the file :file uploaded to the :env environment
     */
    public function iRunTheCommandToDeleteTheFileUploadedToTheEnvironment($file, $env)
    {
        system("rclone --config=/project/tests/_output/developer.conf delete --s3-no-check-bucket wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir . "/$file", $status);
    }

    /**
     * @Then the file :file is deleted from the :env environment
     */
    public function theFileIsDeletedFromTheEnvironment($file, $env)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:gigadb-datasets/$env/tests/" . $this->targetDir . "/$file");
        $this->I->assertNull($output);
    }

    /**
     * @param $accessKeyId
     * @param $secretKey
     * @return void
     */
    public function renderRcloneConfig($accessKeyId, $secretKey, $sessionToken = null): void
    {
        $loader = new \Twig\Loader\FilesystemLoader('/project/tests/_data/RcloneConfigs');
        $twig = new \Twig\Environment($loader);
        try {
            file_put_contents(
                "/project/tests/_output/developer.conf",
                $twig->render('developer.conf.twig', [
                    'wasabi_group_developer_test_access_key_id' => $accessKeyId,
                    'wasabi_group_developer_test_secret_access_key' => $secretKey,
                    'wasabi_group_developer_test_session_token' => $sessionToken,
                ]),
            );
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            codecept_debug($e->getMessage());
        }
    }

    /**
     * @Given I assume the Admin role
     */
    public function iAssumeTheAdminRole()
    {


        $roleToAssumeArn = 'arn:aws:iam::100000166496:role/Admin';

        list($accessKeyId, $secretKey) = $this->getWasabiCredentials();

        /**
         * Assume Role
         *
         * This code expects that you have AWS credentials set up per:
         * https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials.html
         */
        $client = new StsClient([
            'endpoint' => 'https://sts.wasabisys.com',
            'region' => 'us-east-1',
            'version' => '2011-06-15',
            'credentials' => [
                'key'    => $accessKeyId,
                'secret' => $secretKey,
            ],
        ]);


        try {
            $result = $client->assumeRole([
                'RoleArn' => $roleToAssumeArn,
                'RoleSessionName' => 'codeceptsession'
            ]);
            // output AssumedRole credentials, you can use these credentials
            // to initiate a new AWS Service client with the IAM Role's permissions
            $this->renderRcloneConfig($result['Credentials']['AccessKeyId'], $result['Credentials']['SecretAccessKey'], $result['Credentials']['SessionToken']);
        } catch (AwsException $e) {
            // output error message if fails
            codecept_debug($e->getMessage());
        }
    }

    /**
     * @param string $variablesEndpoint
     * @param string $accessKeyVariableName
     * @param string $secretKeyVariableName
     * @return array
     */
    public function getWasabiCredentials(string $variablesEndpoint = self::MISC_VARIABLES_URL, string $accessKeyVariableName = "wasabi_group_developer_test_access_key_id", string $secretKeyVariableName = "wasabi_group_developer_test_secret_access_key"): array
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get("$variablesEndpoint/$accessKeyVariableName", [
                'headers' => [
                    'PRIVATE-TOKEN' => getenv("GITLAB_PRIVATE_TOKEN")
                ],
            ]);
            $accessKeyId = json_decode($response->getBody(), true)["value"];
            $response = $client->get("$variablesEndpoint/$secretKeyVariableName", [
                'headers' => [
                    'PRIVATE-TOKEN' => getenv("GITLAB_PRIVATE_TOKEN")
                ],
            ]);
            $secretKey = json_decode($response->getBody(), true)["value"];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            codecept_debug($e->getMessage());
        }
        return array($accessKeyId, $secretKey);
    }
}
