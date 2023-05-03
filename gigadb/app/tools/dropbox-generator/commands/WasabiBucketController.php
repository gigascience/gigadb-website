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
    public $bucket = '';

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
            'bucket',
            'filePath',
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
