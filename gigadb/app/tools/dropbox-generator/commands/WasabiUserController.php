<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use \Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;

/**
 * Read file in bucket
 *
 */
class WasabiUserController extends Controller
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Username for Wasabi account
     *
     * @var string $username
     */
    public $username = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'username',
        ];
    }

    public function init()
    {
        parent::init();
        $this->credentials = array(
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            // 'debug'   => true
        );
    }

    /**
     * Create user account
     * @return int Exit code
     */
    public function actionCreategigadbuser()
    {
        $optUserName   = $this->username;

        // Return usage unless mandatory options are passed.
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi/creategigadbuser --username theUsername" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi
        $iam = new IamClient($this->credentials);

        try {
            // An Aws Result object is returned
            $result = $iam->createUser([
                'UserName' => "$optUserName"
            ]);
            var_dump($result);
        } catch (IamException $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        return ExitCode::OK;
    }
}
