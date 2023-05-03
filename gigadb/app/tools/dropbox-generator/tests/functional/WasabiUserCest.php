<?php

use yii\console\Controller;
use yii\console\ExitCode;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;

class WasabiUserCest
{
    /**
     * Teardown code that is run after each test
     *
     * Currently just removes the readme file for dataset DOI 100005.
     *
     * @return void
     */
    public function _after()
    {
        // Get list of Wasabi users
        $users = $this->listWasabiUsers();
        // If user giga-d-23-00288 exists
        if (in_array("giga-d-23-00288", $users)) {
            echo "Got user";
            $this->deleteWasabiUser("giga-d-23-00288");
        }
    }

    /**
     * Test actionCreate function in ReadmeController
     *
     * @param FunctionalTester $I
     */
    public function tryCreate(FunctionalTester $I)
    {
//        $I->runShellCommand("/app/yii_test wasabi/creategigadbuser --manuscriptId giga-d-23-00288");
//        $I->seeInShellOutput("[DOI] 10.5524/100005");
//        $I->runShellCommand("ls /home/curators");
//        $I->seeInShellOutput("readme_100005.txt");
    }

    private function listWasabiUsers()
    {
        $credentials = [
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        ];

        //Establish connection to wasabi via access and secret keys
        $iam = new IamClient($credentials);

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
        $credentials = [
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        ];

        //Establish connection to wasabi via access and secret keys
        $iam = new IamClient($credentials);

        try {
            $result = $iam->deleteUser(array(
                'UserName' => '$username'
            ));
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
