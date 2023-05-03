<?php

use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;

class WasabiUserCest
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Setup code that is run before each test
     *
     * @return void
     */
    public function _before()
    {
        $this->credentials = array(
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        );
    }

    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the Wasabi user that was created by this test
     *
     * @return void
     */
    public function _after()
    {
        $users = $this->listWasabiUsers();
        if (in_array("giga-d-23-00288", $users)) {
            $this->deleteWasabiUser("giga-d-23-00288");
        }
    }

    /**
     * Test actionCreategigadbuser function in WasabiController
     *
     * @param FunctionalTester $I
     */
    public function tryCreateWasabiUser(FunctionalTester $I)
    {
        $I->runShellCommand("/app/yii_test wasabi/creategigadbuser --manuscriptId giga-d-23-00288");
        # If above console command is successful then we should see the username in output
        $I->seeInShellOutput("giga-d-23-00288");
    }

    private function listWasabiUsers()
    {
        //Establish Wasabi connection
        $iam = new IamClient($this->credentials);

        $usernames = array();
        try {
            $result = $iam->listUsers();
            $users = $result->get("Users");
            foreach ($users as $user) {
                $usernames[] = $user["UserName"];
            }
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
        return $usernames;
    }

    private function deleteWasabiUser($username)
    {
        $iam = new IamClient($this->credentials);

        try {
            $iam->deleteUser([
                'UserName' => "$username"
            ]);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
