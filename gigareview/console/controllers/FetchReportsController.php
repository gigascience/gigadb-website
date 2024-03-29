<?php

namespace console\controllers;

use common\models\EMReportJob;
use common\models\Ingest;
use League\Flysystem\FilesystemException;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToReadFile;
use Yii;
use yii\db\Exception;
use \yii\helpers\Console;
use \yii\console\Controller;
use yii\console\ExitCode;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Console controller that fetch editorial manager reports and publsh them to the appropriate message queue
 * the list of report types is set in Yii2 configuration file and loaded here to be looped over.
 * The other piece of configuration are the sftpd connection details, also loaded from Yii2 configuration file
 *
 * @use gigareview/common/config/params.php
 * @use gigareview/environments/dev/console/config/main-local.php
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
final class FetchReportsController extends Controller
{
    /** @var Filesystem $fs class property to store handle to the Flysystem filesystem object */
    private Filesystem $fs;

    public function init()
    {
        $this->fs = new Filesystem(new SftpAdapter(
            new SftpConnectionProvider(
                Yii::$app->params['sftp']['host'], // host (required)
                Yii::$app->params['sftp']['username'], // username (required)
                Yii::$app->params['sftp']['password'],
            ),
            Yii::$app->params['sftp']['baseDirectory'], // root path (required)
            PortableVisibilityConverter::fromArray([
                'file' => [
                    'public' => 0640,
                    'private' => 0604,
                ],
                'dir' => [
                    'public' => 0740,
                    'private' => 7604,
                ],
            ])
        ));
    }

    /**
     * Command to list all files on sftp that match the given pattern
     *
     * @param string $filePattern
     * @return int
     * @throws FilesystemException
     *
     */
    public function actionList($filePattern): int
    {
        $allFiles = $this->listRemoteFiles($filePattern);
        array_map(fn($file) => print $file.PHP_EOL, $allFiles);
        return ExitCode::OK;
    }

    /**
     * Command to download reports from sftp and publish the content to the appropriate message queue
     *
     * @return int
     * @throws \ErrorException
     */
    public function actionFetch(): int
    {
        foreach (Yii::$app->params['reportsTypesFilenamePatterns'] as $reportType => $fileNamePattern ) {
            $ingest = new Ingest([
                "file_name" => $fileNamePattern,
                "report_type" => Ingest::REPORT_TYPES[$reportType]
            ]);
            $ingest->save();
            $this->stdout("Fetching $reportType report...\n", Console::BOLD);
            try {
                $reportFile = $this->getLatestOfType($reportType);
                $reportFileName = pathinfo($reportFile)['filename'].".".pathinfo($reportFile)['extension'];
                $ingest->file_name = $reportFileName;
                $ingest->fetch_status = Ingest::FETCH_STATUS_FOUND;
                $ingest->save();

                $content = $this->fs->read(
                    $reportFile
                );
                $this->stdout("Got content for $reportFile".PHP_EOL);
                $ingest->fetch_status = Ingest::FETCH_STATUS_DOWNLOADED;
                if(!$ingest->save()) {
                    throw new \ErrorException("Failed saving status FETCH_STATUS_DOWNLOADED");
                }

                $jobId = Yii::$app->{$reportType."_q"}->push(
                    new EMReportJob([
                        'content' => $content,
                        'effectiveDate' =>  (new \DateTime('yesterday'))->format('c'),
                        'fetchDate' => (new \DateTime())->format('c'),
                        'scope' => "$reportType",
                        'reportFileName' => "$reportFileName"
                    ])
                );
                $this->stdout("Pushed a new job with ID $jobId for report $reportType to ${reportType}_q".PHP_EOL);
                $ingest->fetch_status = Ingest::FETCH_STATUS_DISPATCHED;
                $ingest->save();
            }
            catch (FilesystemException | UnableToReadFile | \ErrorException $exception) {
                Yii::error($exception->getMessage());
                $this->stderr($exception->getMessage().PHP_EOL);
                $ingest->fetch_status = Ingest::FETCH_STATUS_ERROR;
                $ingest->save();
                continue;
            }
        }

        return ExitCode::OK;
    }

    /**
     * Command to remove files remotely that have already been processed successfully
     *
     * @TODO: implement this when the database schema is done
     * @return int
     */
    public function actionClearProcessedReports(): int
    {
        return ExitCode::OK;
    }

    /**
     * Command to show files the system knows that are neither failed or completed
     *
     * @TODO: implement this when the database schema is done
     * @return int
     */
    public function actionShowInProgress(): int
    {
        return ExitCode::OK;
    }


    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help'];
    }

    /**
     * private method to return the file name for the latest version of a given type of report
     *
     * @param string $type
     * @return string|null
     * @throws FilesystemException
     */
    private function getLatestOfType(string $type): ?string
    {
        $filePattern = Yii::$app->params['reportsTypesFilenamePatterns'][$type];
        $matches = $this->listRemoteFiles($filePattern);
        return end($matches);
    }

    /**
     * @param $filePattern
     * @return array
     * @throws FilesystemException
     */
    public function listRemoteFiles($filePattern): array
    {
        $matches = $this->fs->listContents('')
            ->filter(fn(StorageAttributes $attributes) => $attributes->isFile())
            ->filter(fn(StorageAttributes $attributes) => preg_match("/$filePattern/", $attributes->path()))
            ->sortByPath()
            ->map(fn(StorageAttributes $attributes) => $attributes->path())
            ->toArray();
        return $matches;
    }

}