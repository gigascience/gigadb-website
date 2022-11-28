<?php

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

    /** @const int represents the value of status code returned by successful CLI command */
    const EXIT_CODE_OK = 0 ;

    /** @const url of cnhk-infra variables  */
    const MISC_VARIABLES_URL = "https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables";

    public function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @Given I configure rclone with a Developer account
     *
     *  - first retrieve the test access keys and secret keys from Gitlab variables
     *  - then, generate an Rclone configuration file from a Twig template, interpolating the variables from previous steps
     *  - finally, assert that the configuration has been generated correctly
     *
     */
    public function iConfigureRcloneWithADeveloperAccount()
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->get(self::MISC_VARIABLES_URL . "/wasabi_group_developer_test_access_key_id", [
                'headers' => [
                    'PRIVATE-TOKEN' => getenv("GITLAB_PRIVATE_TOKEN")
                ],
            ]);
            $accessKeyId = json_decode($response->getBody(), true)["value"];
            $response = $client->get(self::MISC_VARIABLES_URL . "/wasabi_group_developer_test_secret_access_key", [
                'headers' => [
                    'PRIVATE-TOKEN' => getenv("GITLAB_PRIVATE_TOKEN")
                ],
            ]);
            $secretKey = json_decode($response->getBody(), true)["value"];
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            codecept_debug($e->getMessage());
        }

        $loader = new \Twig\Loader\FilesystemLoader('/project/tests/_data/RcloneConfigs');
        $twig = new \Twig\Environment($loader, [
            'cache' => '/project/tests/_output',
        ]);
        try {
            file_put_contents(
                "/project/tests/_output/developer.conf",
                $twig->render('developer.conf.twig', [
                    'wasabi_group_developer_test_access_key_id' => $accessKeyId,
                    'wasabi_group_developer_test_secret_access_key' => $secretKey
                ]),
            );
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            codecept_debug($e->getMessage());
        }

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
        $this->I->assertTrue(str_contains($output," -1 gigadb-datasets"));
        $this->I->assertTrue(str_contains($output," -1 test-gigadb-datasets"));
    }

    /**
     * @When I run the command to download file :file from the :env environment
     */
    public function iRunTheCommandToDownloadFileFromTheEnvironment($file, $env)
    {
        $outputDir =  (new DateTimeImmutable())->format('Y-m-d-H')."-".getmypid();
        system("rclone --config=/project/tests/_output/developer.conf copy wasabiTest:gigadb-datasets/$env/$file /project/tests/_output/$outputDir/$env/$file", $status);
        $this->I->assertEquals(self::EXIT_CODE_OK, $status);
    }

    /**
     * @Then I can see :file on my local filesystem
     */
    public function iCanSeeOnMyLocalFilesystem($file)
    {
        $outputDir =  (new DateTimeImmutable())->format('Y-m-d-H')."-".getmypid();
        $this->I->assertFileExists("/project/tests/_output/$outputDir/$file");
    }

    /**
     * @When I run the command to upload a file to the :env environment
     */
    public function iRunTheCommandToUploadAFileToTheEnvironment($env)
    {
        $targetDir =  getenv("REPO_NAME")."/".(new DateTimeImmutable())->format('Y-m-d-H.i.A');
        system("rclone --config=/project/tests/_output/developer.conf copy --s3-no-check-bucket /project/tests/_data/sample.txt wasabiTest:gigadb-datasets/$env/tests/$targetDir", $status);
        $this->I->assertEquals(self::EXIT_CODE_OK, $status);
    }

    /**
     * @Then I can see that file on the remote filesystem under :root
     */
    public function iCanSeeThatFileOnTheRemoteFilesystemUnder($root)
    {
        $targetDir =  getenv("REPO_NAME")."/".(new DateTimeImmutable())->format('Y-m-d-H.i.A');
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:$root/tests/$targetDir");
        $this->I->assertTrue(str_contains($output,"sample.txt"));
    }

    /**
     * @When I run the command to delete a file on the :env environment
     */
    public function iRunTheCommandToDeleteAFileOnTheEnvironment($env)
    {
        system("rclone --config=/project/tests/_output/developer.conf delete --s3-no-check-bucket wasabiTest:gigadb-datasets/$env/test.txt",$status);
        $this->I->assertNotEquals(self::EXIT_CODE_OK, $status);
    }


    /**
     * @Then the file is not deleted
     */
    public function theFileIsNotDeleted()
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf ls wasabiTest:gigadb-datasets/live/test.txt");
        $this->I->assertTrue(str_contains($output,"  34 test.txt"));
    }


}