<?php

namespace Steps;

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
    public const EXIT_CODE_OK = 0 ;

    /** @const url of cnhk-infra variables  */
    public const MISC_VARIABLES_URL = "https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables";

    /** @const url of Forks variables  */
    public const FORKS_VARIABLES_URL = "https://gitlab.com/api/v4/groups/3501869/variables" ;

    public function __construct(\AcceptanceTester $I)
    {
        $this->I = $I;
        $this->targetDir = getenv("REPO_NAME") . "/" . (new \DateTimeImmutable())->format('Y-m-d.A');
    }


    /**
     * @When I run the command to create bucket :bucket
     */
    public function iRunTheCommandToCreateBucket($bucket)
    {
        shell_exec("rclone --config=/project/tests/_output/developer.conf mkdir wasabiTest:$bucket");
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
     * @Then I should see the bucket :bucket
     */
    public function iShouldSeeTheBuckets($bucket)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf lsd wasabiTest:");
        $this->I->assertTrue(str_contains($output, " -1 " . $bucket));
    }

    /**
     * @Then I should not see the bucket :bucket
     */
    public function iShouldNotSeeTheBucket($bucket)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf lsd wasabiTest:");
        $this->I->assertFalse(str_contains($output, " -1 " . $bucket));
    }

    /**
     * @Then I cannot delete the bucket :bucket
     */
    public function iRunTheCommandToDeleteBucket($bucket)
    {
        system("rclone --config=/project/tests/_output/developer.conf purge wasabiTest:$bucket", $status);
        $this->I->assertNotEquals(self::EXIT_CODE_OK, $status);
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
}
