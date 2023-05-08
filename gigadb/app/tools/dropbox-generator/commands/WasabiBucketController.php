<?php

namespace app\commands;

use Exception;
use \Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

/**
 * Provides Yii2 console commands to create buckets in Wasabi.
 *
 */
class WasabiBucketController extends Controller
{
    /**
     * @var string Name of bucket.
     */
    public $bucketName = '';

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
                //Yii::info($result);
                var_dump($result);
                //$this->stdout('New bucket created successfully.' . PHP_EOL, Console::FG_GREEN);
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
            //Yii::info($result);
            var_dump($result);
//            $this->stdout('New bucket created successfully.' . PHP_EOL, Console::FG_GREEN);
//            $buckets = $result->get("Buckets");
//            foreach ($buckets as $bucket) {
//                $bucketNames[] = $bucket["Name"];
//            }
//            var_dump($bucketNames);
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

        // Return usage unless mandatory options are passed.
        if ($optBucketName === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-bucket/delete --bucketName theBucketName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        try {
            $result = Yii::$app->WasabiBucketComponent->deleteBucket($optBucketName);
            //Yii::info($result);
            var_dump($result);
        } catch (S3Exception $e) {
            $this->stdout($e->getMessage() . PHP_EOL, Console::FG_RED);
            Yii::error($e->getMessage());
            return ExitCode::DATAERR;
        }
        return ExitCode::OK;
    }
}
