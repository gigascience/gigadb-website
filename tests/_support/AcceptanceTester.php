<?php

use Aws\Exception\AwsException;
use Aws\Sts\StsClient;
use Facebook\WebDriver\WebDriverKeys;
use Codeception\Util\Locator;

/**
 * Code for implementing generic steps of feature files (non-generic should be in their support class)
 *
 * stubs copied from (after gherkin scenario steps are created):
 * docker-compose run --rm test ./vendor/codeception/codeception/codecept g:snippets acceptance
 *
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
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /** @const url of cnhk-infra variables  */
    public const MISC_VARIABLES_URL = "https://gitlab.com/api/v4/projects/gigascience%2Fcnhk-infra/variables";

    /** @const url of Forks variables  */
    public const FORKS_VARIABLES_URL = "https://gitlab.com/api/v4/groups/3501869/variables" ;

   /**
    * Define custom actions here
    */

    /**
     * @Given I am on :page
     */
    public function iAmOn($page)
    {
        $this->amOnPage("$page");
    }

    /**
     * @When I follow :link
     */
    public function iFollow($link)
    {
        $this->click($link);
    }

    /**
     * @Then I should see :text
     */
    public function iShouldSee($text)
    {
        $this->see($text);
    }

    /**
     * @Then I should not see :text
     */
    public function iShouldNotSee($text)
    {
        $this->dontSee($text);
    }

    /**
     * @Then I should see a text field :id
     */
    public function iShouldSeeATextField($id)
    {
        $this->seeElement('input', ['id' => $id, 'type' => "text"]);
    }

    /**
     * @Then I should see a password field :id
     */
    public function iShouldSeeAPasswordField($id)
    {
        $this->seeElement('input', ['id' => $id, 'type' => "password"]);
    }

    /**
     * Looks for a button with a name that is the content in between
     * it's button tags.
     *
     * @Then I should see a :button button
     */
    public function iShouldSeeAButton($button)
    {
        $this->seeElement(Locator::contains('button', $button));
    }

    /**
     * @Then I should see a drop-down field :id with values
     */
    public function iShouldSeeADropdownFieldWithValues($id, \Behat\Gherkin\Node\TableNode $preferredLinks)
    {
        $this->seeElement('select', ['id' => $id]);

        foreach ($preferredLinks->getRows() as $index => $row) {
            if ($index === 0) { // first row to define fields
                $keys = $row;
                continue;
            }
            $this->seeElement('option', [ 'value' => $row[0] ]);
        }
    }

    /**
     * @Then I should see a check-box field :id
     */
    public function iShouldSeeACheckboxField($id)
    {
        $this->seeElement('input', ['id' => $id, 'type' => "checkbox"]);
    }

    /**
     * @Then I should see a link :title to :url
     */
    public function iShouldSeeALinkTo($title, $url)
    {
        $this->seeLink($title, $url);
    }

    /**
     * @Then I should see an image located in :partialPath
     */
    public function iShouldSeeAnImageLocatedIn($partialPath)
    {
        $this->seeElement("//img[contains(@src, '$partialPath')]");
    }
    /**
     * @Then I should see a submit button :value
     */
    public function iShouldSeeASubmitButton($value)
    {
        $this->seeElement('input', ['value' => $value, 'type' => "submit"]);
    }

    /**
     * @Then I should see a disabled submit button :value
     */
    public function iShouldSeeADisabledSubmitButton($value)
    {
        $this->seeElement(Locator::find('input', ['type' => 'submit', 'value' => $value, 'disabled' => 'disabled']));
    }

    /**
     * @Then I should see a button :button with curation log link
     */
    public function iShouldSeeAButtonWithLink($expectButton)
    {
        $actualButton = $this->grabTextFrom("//a[contains(@href, '/curationLog/create/id/')]");
        $this->assertEquals($actualButton, $expectButton);
    }

    /**
     * @When I fill in the field of :attribute :fieldName with :value
     */
    public function iFillInTheFieldOfWith($attribute, $fieldName, $value)
    {
        $this->fillField([$attribute => $fieldName], $value);
    }

    /**
     * @When I select :option from the field :fieldName
     */
    public function iSelectFromTheField($option, $fieldName)
    {
        $this->selectOption(["id" => $fieldName], $option);
    }

    /**
     * @When I check the field :fieldName
     */
    public function iCheckTheField($fieldName)
    {
        $this->checkOption(["id" => $fieldName]);
    }

    /**
     * @When I press the button :buttonName
     */
    public function iPressTheButton($buttonName)
    {
        $this->click($buttonName);
    }

    /**
     * @Then I should not see an affiliate login option for :provider
     */
    public function iShouldNotSeeLoginOption($provider)
    {
        $this->dontSeeElement("//a[contains(@href, '/opauth/$provider')]");
        $this->dontSeeElement("//img[contains(@src, '/images/new_interface_image/$provider.png')]");
    }
    /**
     * @Then I make a screenshot called :arg1
     */
    public function iMakeAScreenshot($arg1)
    {
        $this->makeScreenshot($arg1);
    }

    /**
     * @Then I go to a page tab :arg1
     */
    public function iGoToAPageTab($arg1)
    {
        $this->amOnPage($arg1);
        $this->reloadPage();
        $this->canSeeCurrentUrlEquals($arg1);
    }

    /**
     * @When I press return on the element :elementXPath
     */
    public function iPressReturnOnTheElement($elementXPath)
    {
        $this->pressKey($elementXPath, WebDriverKeys::RETURN_KEY);
    }

    /**
     * @When I wait :numberOf seconds
     */
    public function iWaitSeconds($numberOf)
    {
        $this->wait($numberOf);
    }

    /**
     * @Then I should see an element has id :id with class :class
     */
    public function iShouldSeeAnElementWith($id, $class)
    {
        $this->seeElement(['id' => $id], ['class' => $class]);
    }

    /**
     * Files need to be made available for tests in _data directory
     *
     * @When I attach the file :file to the file input element :file_input_element_name
     */
    public function iAttachTheFileToTheFileInputElement($file, $file_input_element_name)
    {
        $this->attachFile("input[name=$file_input_element_name]", $file);
    }

    /**
     * @Then I should see an image :image is linked to :expectedUrl
     */
    public function iShouldSeeAnImageIsLinkedTo($image, $expectedUrl)
    {
        $this->seeElement("//img[@src='$image']");
        $actualUrl = $this->grabAttributeFrom("//img[@src='$image']/parent::*", "href");
        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * @Then I should see an image with alternate text :alt is linked to :expectedUrl
     */
    public function iShouldSeeAnImageWithAlternateTextIsLinkedTo($alt, $expectedUrl)
    {
        $this->seeElement("//img[@alt='$alt']");
        $actualUrl = $this->grabAttributeFrom("//img[@alt='$alt']/parent::*", "href");
        $this->assertEquals($expectedUrl, $actualUrl);
    }

    /**
     * Open a link provided by an image with alternate text attribute
     * 
     * Beware that a web page may have multiple linked images each with alt
     * attribute.
     * 
     * @Then I click on image with alternate text :alt
     */
    public function iClickOnImageWithAlternateText($alt)
    {
        $this->seeElement("//img[@alt='$alt']");
        $this->click($alt);
    }

    /**
     * @When I click the table settings for :table
     */
    public function iClickTheTableSettings($table)
    {
        $this->click("#" . $table);
        $this->wait(1);
    }

    /**
     * @Then I should be on :path
     */
    public function iShouldBeOn($path)
    {
        $this->seeCurrentUrlEquals($path);
    }

    /**
     * @Then I should see :checkbox checkbox is not checked
     */
    public function iShouldSeeCheckboxIsNotChecked($checkbox)
    {
        $this->dontSeeCheckboxIsChecked("//input[@id='$checkbox']");
    }

    /**
     * @Then I should see :checkbox checkbox is checked
     */
    public function iShouldSeeCheckboxIsChecked($checkbox)
    {
        $this->seeCheckboxIsChecked("//input[@id='$checkbox']");
    }

    /**
     * @Then I check :checkbox checkbox
     */
    public function iCheckCheckbox($checkbox)
    {
        $this->checkOption("//input[@id='$checkbox']");
    }

    /**
     * @Then I uncheck :checkbox checkbox
     */
    public function iUncheckCheckbox($checkbox)
    {
        $this->uncheckOption("//input[@id='$checkbox']");
    }

    /**
     * @Then I should see current url contains :value
     */
    public function iShouldSeeCurrentUrlContains($value)
    {
        $this->seeInCurrentUrl($value);
    }

    /**
     * @When I confirm to :message
     */
    public function iConfirmTo($message)
    {
        $this->seeInPopup($message);
        $this->acceptPopup();
    }

    /**
     * @When I go to the new tab
     */
    public function iGoToTheNewTab()
    {
        $this->switchToNextTab();
    }

    /**
     * @Then I should see an input button :button
     */
    public function iShouldSeeAnInputButton($button)
    {
        $this->seeElement('input', ['type' => "button", 'value' => $button]);
    }

    /**
     * @Then I should not see an input button :button
     */
    public function iShouldNotSeeAnInputButton($button)
    {
        $this->dontSeeElement('input', ['type' => "button", 'value' => $button]);
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
            case "Test curator":
                $accessKeyToRetrieve = "TEST_CODECEPTCUR_WASABI_ACCESS_KEY_ID";
                $secretKeyToRetrieve = "TEST_CODECEPTCUR_WASABI_SECRET_ACCESS_KEY";
                break;
        }

        list($accessKeyId, $secretKey) = $this->getWasabiCredentials(
            self::FORKS_VARIABLES_URL,
            $accessKeyToRetrieve,
            $secretKeyToRetrieve
        );

        $this->renderRcloneConfig($accessKeyId, $secretKey);

        $this->assertFileExists("/project/tests/_output/developer.conf");
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
     * @When I run the command to create bucket :bucket
     */
    public function iRunTheCommandToCreateBucket($bucket)
    {
        shell_exec("rclone --config=/project/tests/_output/developer.conf mkdir wasabiTest:$bucket");
    }

    /**
     * @Then I should see the bucket :bucket
     */
    public function iShouldSeeTheBuckets($bucket)
    {
        $output = shell_exec("rclone --config=/project/tests/_output/developer.conf lsd wasabiTest:");
        $this->assertTrue(str_contains($output, " -1 " . $bucket));
    }

    /**
     * @Then I cannot delete the bucket :bucket
     */
    public function iRunTheCommandToDeleteBucket($bucket)
    {
        system("rclone --config=/project/tests/_output/developer.conf purge wasabiTest:$bucket", $status);
        $this->assertNotEquals(0, $status);
    }
}
