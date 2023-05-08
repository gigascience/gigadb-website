<?php

namespace app\components;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Yii;
use yii\base\Component;
use Aws\Result;

/**
 * Component class for creating and attaching policies in Wasabi
 */
class WasabiBucketComponent extends Component
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
            'endpoint' => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region' => Yii::$app->params['wasabi']['bucket_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
        ];
    }


    /**
     * Create a policy in Wasabi that restricts an author to their own bucket
     *
     * @param string $authorUserName Wasabi username of the author.
     * @return Result AWS result object
     * @throws Exception
     */
    public function create(string $bucketName): Result
    {
        //Establish connection to wasabi
        $s3Client = new S3Client($this->credentials);
        $result = $s3Client->createBucket([
            'Bucket' => $bucketName,
        ]);
        return $result;
    }

    /**
     * List bucket names in account
     *
     * @throws Exception
     */
    public function listBuckets(): Result
    {
        $s3Client = new S3Client($this->credentials);
        $result = $s3Client->listBuckets();
        return $result;
    }

    /**
     * Deletes a bucket specified by its bucket name
     *
     * @throws Exception
     */
    public function deleteBucket($bucketName)
    {
        $s3Client = new S3Client($this->credentials);
        $result = $s3Client->deleteBucket([
            'Bucket' => "$bucketName"
        ]);
        return $result;
    }
}
