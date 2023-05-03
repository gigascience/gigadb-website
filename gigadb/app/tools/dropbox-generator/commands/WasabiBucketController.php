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

/**
 * Read file in bucket
 *
 */
class WasabiBucketController extends Controller
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
    public $bucketName = '';

    /**
     * Path to file to be read
     *
     * @var string $filePath
     */
    public $filePath = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'bucketName',
//            'filePath',
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
            'endpoint' => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region' => Yii::$app->params['wasabi']['bucket_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            // 'debug'   => true
        );
    }

    /**
     * Create new bucket
     * @return int Exit code
     */
    public function actionCreate()
    {
        $optBucketName = $this->bucketName;

        // Return usage unless mandatory options are passed.
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-bucket/create --bucketName theBucketName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi
        $s3Client = new S3Client($this->credentials);
        try {
            $result = $s3Client->createBucket([
                'Bucket' => $optBucketName,
            ]);
            var_dump($result);
        } catch (S3Exception $e) {
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
        $optBucketName = $this->bucketName;
        $optFilePath   = $this->filePath;

        // Return usage unless mandatory options are passed.
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi/read --bucketName theBucketName --filePath path/to/file" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        //Establish connection to wasabi via access and secret keys
        $s3Client = new S3Client($this->credentials);
        try {
            //Read object
            $result = $s3Client->getObject([
                'Bucket' => $optBucketName,
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
