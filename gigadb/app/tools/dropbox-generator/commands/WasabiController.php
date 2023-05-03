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
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;

/**
 * Read file in bucket
 *
 */
class WasabiController extends Controller
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Name of bucket
     *
     * @var string $bucket
     */
    public $bucket = '';

    /**
     * Path to file to be read
     *
     * @var string $filePath
     */
    public $filePath = '';

    /**
     * Manuscript identifier
     *
     * @var string $manuscriptId
     */
    public $manuscriptId = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'bucket',
            'filePath',
            'manuscriptId',
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
            'endpoint' => Yii::$app->params['wasabi']['endpoint'],
            'region' => Yii::$app->params['wasabi']['region'],
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
        // Use manuscript identifier as the username for new user account
        // Bucket name will therefore be "bucket-$optUserName"
        $optUserName   = $this->manuscriptId;

        // Return usage unless mandatory options are passed.
        if ($optUserName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi/creategigadbuser --manuscriptId theManuscriptId" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        $credentials = array(
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        );

        //Establish connection to wasabi
        $iam = new IamClient($credentials);

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

    /**
     * Read file in bucket
     * @return int Exit code
     */
    public function actionRead()
    {
        $optBucket   = $this->bucket;
        $optFilePath = $this->filePath;

        // Return usage unless mandatory options are passed.
        if ($optBucket === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi/read --bucket bucket-name --filePath path/to/file" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi via access and secret keys
        $s3 = new S3Client($this->credentials);

        try {
            //Read object
            $result = $s3->getObject([
                'Bucket' => $optBucket,
                'Key' => $optFilePath,
            ]);

            //Print file contents
            echo $result['Body'];
        } catch (S3Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }

        return ExitCode::OK;
    }
}
