<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use Yii;
use yii\base\Component;

/**
 * DatasetFilesUpdater
 *
 * encapsulate business logic for updating the file table
 */
final class DatasetFilesUpdater extends Component
{
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;
    /**
     * @var URLsService URLs helper functions (here we interested in batch grab of specific response header)
     */
    public URLsService $us;
    /**
     * @var \GuzzleHttp\Client web client needed for URLsService
     */
    public \GuzzleHttp\Client $webClient;

    public string $prefix;
    public string $separator;
    public array $excludedDois;

    /**
     * Replace substring of a URL with a new prefix for all files in a dataset
     *
     * @return int returns number of files that have been successfully updated
     */
    public function replaceFileUrlSubstringWithPrefix(): int
    {
        # Record how many files with their URL locations updated
        $success = 0;
        # Get dataset whose file URLs we need to update
        $dataset = Dataset::find()
            ->where(["identifier" => $this->doi])
            ->one();
        # Get all files belonging to dataset
        $files =  File::find()
            ->where(["dataset_id" => $dataset->id])
            ->all();
        # Update each location URL with new prefix
        foreach ($files as $file) {
            $url = $file->location;
            if (str_contains($url, $this->separator)) {
                # Remove substring after separator
                $newUrl = substr(
                    $url,
                    strrpos($url, $this->separator) + strlen($this->separator),
                    strlen($url)
                ) . PHP_EOL;
                $newUrl = $this->prefix . "$this->separator" . $newUrl;
                $file->location = $newUrl;
                if ($file->save()) {
                    $success++;
                }
            }
        }
        return $success;
    }

    /**
     *
     * Get next batch of pending datasets
     *
     *
     * @param int $after dataset id after which to start fetching the list
     * @param int $next batch size
     * @return array
     */
    public function getNextPendingDatasets(string $doi, int $next)
    {
        $dois = ['100142','100039'];

        $rows = (new \yii\db\Query())
            ->select('dataset.id, dataset.identifier, file.dataset_id, file.location')
            ->from('dataset')
            ->rightJoin('file', 'dataset.id = file.dataset_id')
            ->where(['dataset.identifier' => $dois])
            ->andWhere(['like','file.location','ftp'])
            ->all();
        print_r($rows);

        return [];
    }

    /**
     * Make a Yii2 DB query that filter datasets based on whether their urls need replacing
     *
     * @param int $after return datasets listed after the one specified here
     * @return \yii\db\Query
     */
    public function filterByFTPUrls(int $after = 0): \yii\db\Query
    {
        return ( new \yii\db\Query())
            ->select(['dataset_id'])
            ->from('file')
            ->where(['like','location','ftp://parrot.genomics'])
            ->orWhere(['like','location','ftp://ftp.cngb.org'])
            ->orWhere(['like','location','ftp://climb.genomics'])
            ->orWhere(['like','location','https://ftp.cngb.org'])
            ->groupBy('dataset_id')
            ->orderBy('dataset_id')
            ->having(['>','dataset_id', $after]);
    }

    /**
     * Method to update the file size for the all the files of the dataset identified with $doi
     *
     * @return int returns the number of files that has been successfully updated
     */
    public function updateFileSize(): int
    {
        $success = 0;
        $zeroOutRedirectsAndDirectories = function ($response, $url) {
            if (301 === $response->getStatusCode() || str_ends_with($url, "/")) {
                return 0;
            }
            return null;
        };


        $d = Dataset::find()->where(["identifier" => $this->doi])->one();

        $urls =  File::find()
                ->select(["location"])
                ->where(["dataset_id" => $d->id])
                ->asArray(true)
                ->all();
        $values = function ($item) {
            return $item["location"];
        };
        $flatURLs = array_map($values, $urls);
        $this->us->urls = $flatURLs;
        $contentLengthList = $this->us->fetchResponseHeader(
            "Content-Length",
            $this->webClient,
            $zeroOutRedirectsAndDirectories
        );
        foreach ($contentLengthList as $location => $contentLength) {
            $f = File::find()->where(["location" => $location])->one();
            $f->size = (int) $contentLength;
            if ($f->save()) {
                $success++;
            }
        }

        return $success;
    }
}
