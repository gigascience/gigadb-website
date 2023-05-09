<?php

namespace app\commands;

use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Aws\S3\Exception\S3Exception;

/**
 * Provides Yii2 console commands to manage buckets in Wasabi.
 */
class WasabiBucketController extends Controller
{
    /**
     * @var string Name of bucket.
     */
    public string $bucketName = '';

    /**
     * @var string A file to be uploaded into bucket.
     */
    public string $key = '';

    /**
     * @var string The path to the file to be uploaded into bucket.
     */
    public string $filePath = '';

    /**
     * @var string Part of the credentials required to call Wasabi API.
     */
    public string $accessKey = '';

    /**
     * @var string Part of the credentials required to call Wasabi API.
     */
    public string $accessSecret = '';

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
            'key',
            'filePath',
            'accessKey',
            'accessSecret'
        ];
    }

    public function init()
    {
        parent::init();
    }

    /**
     * Create new bucket
     * @return int Exit code
     */
    public function actionCreate(): int
    {
        $optBucketName = $this->bucketName;
        // Return usage unless mandatory options are passed.
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-bucket/create --bucketName theBucketName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiBucketComponent->create($optBucketName);
            if ($result) {
                // Log result output
                Yii::info($result);
                $this->stdout($result->get('Location') . PHP_EOL, Console::FG_GREEN);
            }
        } catch (S3Exception $e) {
            // Handle any S3Exception bubbled up from WasabiBucketComponent
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            // Log error message
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }

    /**
     * Lists all buckets
     * @return int Exit code
     */
    public function actionListBuckets(): int
    {
        try {
            $result = Yii::$app->WasabiBucketComponent->listBuckets();
            Yii::info($result);
            $buckets = $result->get("Buckets");
            foreach ($buckets as $bucket) {
                $this->stdout($bucket["Name"] . PHP_EOL, Console::FG_GREEN);
            }
        } catch (S3Exception $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }

    /**
     * Delete bucket given a bucket name
     * @return int Exit code
     */
    public function actionDelete(): int
    {
        $optBucketName = $this->bucketName;
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-bucket/delete --bucketName theBucketName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiBucketComponent->deleteBucket($optBucketName);
            Yii::info($result);
            $statusCode = $result->get("@metadata")["statusCode"];
            if ($statusCode != 200) {
                throw new Exception("Delete bucket did not return HTTP 204 No Content status response code!");
            }
            $this->stdout("Bucket deleted" . PHP_EOL, Console::FG_GREEN);
        } catch (S3Exception | Exception $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }

    /**
     * Upload file into bucket
     *
     * Allows a different user to transfer file into a bucket.
     *
     * @return int Exit code
     */
    public function actionPutObject(): int
    {
        $optBucketName   = $this->bucketName;
        // File name
        $optKey          = $this->key;
        $optFilePath     = $this->filePath;
        $optAccessKey    = $this->accessKey;
        $optAccessSecret = $this->accessSecret;
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-bucket/putObject --bucketName theBucketName --key theKey --file-path theFilePath --access-key theAccessKey --access-secret theAccessSecret" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiBucketComponent->putObject(
                $optBucketName,
                $optKey,
                $optFilePath,
                $optAccessKey,
                $optAccessSecret
            );
            Yii::info($result);
            $statusCode = $result->get("@metadata")["statusCode"];
            codecept_debug("Put object status code: {$statusCode}");
            if ($statusCode != 200) {
                throw new Exception("Put object in bucket did not return HTTP 200 No Content status response code!");
            }
            $this->stdout("$optKey placed in bucket" . PHP_EOL, Console::FG_GREEN);
        } catch (S3Exception | Exception $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }
}
