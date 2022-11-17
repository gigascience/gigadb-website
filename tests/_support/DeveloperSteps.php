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

    /** @const url of cnhk-infra variables  */
    const MISC_VARIABLES_URL = "https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables";

    public function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
    }

    /**
     * @Given I configure rclone with a Developer account
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
        throw new \PHPUnit\Framework\IncompleteTestError("Step `I run the command to list buckets` is not defined");
    }

    /**
     * @Then I should see buckets:
     */
    public function iShouldSeeBuckets()
    {
        throw new \PHPUnit\Framework\IncompleteTestError("Step `I should see buckets:` is not defined");
    }

}