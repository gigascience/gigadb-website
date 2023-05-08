<?php

namespace app\components;

use Yii;
use yii\base\Component;
use Aws\Result;
use Aws\Iam\IamClient;

/**
 * Component class for creating and attaching policies in Wasabi
 */
class WasabiUserComponent extends Component
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public array $credentials;

    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
        $this->credentials = [
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        ];
    }


    /**
     * Create a new user account in Wasabi
     *
     * @param string $userName Wasabi username of the author.
     * @return Result AWS result object
     */
    public function create(string $userName): Result
    {
        //Establish connection to wasabi
        $iam = new IamClient($this->credentials);
        $result = $iam->createUser([
            'UserName' => "$userName"
        ]);
        return $result;
    }

    /**
     * List user accounts
     *
     * @return Result AWS result object
     */
    public function listUsers(): Result
    {
        $iam = new IamClient($this->credentials);
        $result = $iam->listUsers();
        return $result;
    }

    /**
     * Deletes a user account specified by its username
     *
     * @param string $userName Wasabi username
     * @return Result AWS result object
     */
    public function deleteUser($userName): Result
    {
        $iam = new IamClient($this->credentials);
        $result = $iam->deleteUser([
            'UserName' => "$userName"
        ]);
        return $result;
    }

    /**
     * Create access key for user
     *
     * @param string $userName Wasabi username
     * @return Result AWS result object
     */
    public function createAccessKey($userName): Result
    {
        $iam = new IamClient($this->credentials);
        $result = $iam->createAccessKey([
            'UserName' => "$userName"
        ]);
        return $result;
    }

    /**
     * Delete access key for user
     *
     * @param string $userName Wasabi username
     * @return Result AWS result object
     */
    public function deleteAccessKey($accessKeyId, $userName): Result
    {
        $iam = new IamClient($this->credentials);
        $result = $iam->deleteAccessKey([
            'AccessKeyId' => "$accessKeyId",
            'UserName' => "$userName",
        ]);
        return $result;
    }
}
