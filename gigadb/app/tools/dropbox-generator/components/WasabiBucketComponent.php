<?php

namespace app\components;

use Yii;
use yii\base\Component;
use Exception;
use Aws\Result;
use Aws\S3\S3Client;

/**
 * Component class for creating and attaching policies in Wasabi
 */
class WasabiBucketComponent extends Component
{
    /**
     * @var array For storing credentials to access Wasabi
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
                'key'    => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint'                => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region'                  => Yii::$app->params['wasabi']['bucket_region'],
            'version'                 => 'latest',
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
     * List buckets in account
     *
     * @throws Exception
     */
    public function listBuckets(): Result
    {
        $s3Client = new S3Client($this->credentials);
        return $s3Client->listBuckets();
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

    /**
     * Upload file to bucket
     *
     * @throws Exception
     */
    public function putObject($bucketName, $key, $filePath, $authorAccessKey, $authorAccessSecret)
    {
        $author_credentials = [
            'credentials' => [
                'key'    => $authorAccessKey,
                'secret' => $authorAccessSecret
            ],
            'endpoint'                => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region'                  => Yii::$app->params['wasabi']['bucket_region'],
            'version'                 => 'latest',
            'use_path_style_endpoint' => true,
        ];

        $s3Client = new S3Client($author_credentials);
        $result = $s3Client->putObject([
            'Bucket' => $bucketName,
            'Key' => $key,
            'SourceFile' => $filePath,
        ]);
        return $result;
    }

    /**
     * Delete file in bucket
     *
     * @throws Exception
     */
    public function deleteObject($bucketName, $key, $authorAccessKey, $authorAccessSecret)
    {
        $author_credentials = [
            'credentials' => [
                'key'    => $authorAccessKey,
                'secret' => $authorAccessSecret
            ],
            'endpoint'                => Yii::$app->params['wasabi']['bucket_endpoint'],
            'region'                  => Yii::$app->params['wasabi']['bucket_region'],
            'version'                 => 'latest',
            'use_path_style_endpoint' => true,
        ];

        $s3Client = new S3Client($author_credentials);
        $result = $s3Client->deleteObject([
            'Bucket' => $bucketName,
            'Key'    => $key,
        ]);
        return $result;
    }
}
