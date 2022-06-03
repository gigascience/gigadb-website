<?php

namespace console\controllers;

use common\models\EMReportJob;
use League\Flysystem\FilesystemException;
use Yii;
use \yii\helpers\Console;
use \yii\console\Controller;
use yii\console\ExitCode;
use League\Flysystem\Filesystem;
use League\Flysystem\PhpseclibV2\SftpConnectionProvider;
use League\Flysystem\PhpseclibV2\SftpAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Console controller that fetch editorial manager reports
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FetchReportsController extends Controller
{

    public function actionFetch(): int
    {

        $report_types = [
            "EM_MANUSCRIPTS" => "em-manuscripts-latest.xlsx",
            "EM_AUTHORS" => "em-authors-latest.xlsx",
            "EM_REVIEWERS" => "em-reviewers-latest.xlsx",
            "EM_REVIEWERS_QUESTIONS" => "em-questions-latest.xlsx",
            "EM_REVIEWS" => "em-reviews-latest.xlsx",
        ];

        $queues = [
            "EM_MANUSCRIPTS" => "em_manuscripts_q",
            "EM_AUTHORS" => "em_authors_q",
            "EM_REVIEWERS" => "em_reviewers_q",
            "EM_REVIEWERS_QUESTIONS" => "em_questions_q",
            "EM_REVIEWS" => "em_reviews_q",
        ];

        $filesystem = new Filesystem(new SftpAdapter(
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

        foreach ($report_types as $reportType => $fileName ) {
            $this->stdout("Fetching $reportType report...\n", Console::BOLD);
            try {
                $reportFile = $fileName;
                $content = $filesystem->read($reportFile);
                $this->stdout("Got content for $reportFile".PHP_EOL);
                $jobId = Yii::$app->{$queues[$reportType]}->push(
                    new EMReportJob([
                        'content' => $content,
                        'effectiveDate' =>  (new \DateTime('yesterday'))->format('c'),
                        'fetchDate' => (new \DateTime())->format('c'),
                        'scope' => "$reportType"
                    ])
                );
                $this->stdout("Pushed a new job with ID $jobId for report $reportType to {$queues[$reportType]}".PHP_EOL);

            }
            catch (FilesystemException | UnableToReadFile $exception) {
                Yii::error($exception->getMessage());
                $this->stderr($exception->getMessage().PHP_EOL);
                continue;
            }
        }

        return ExitCode::OK;
    }

    public function options($actionID)
    {
        // $actionId might be used in subclasses to provide options specific to action id
        return ['color', 'interactive', 'help'];
    }

}