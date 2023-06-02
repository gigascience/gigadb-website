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
     * @return int returns the number of files that has been successfully updated
     */
    public function replaceFileUrlSubstringWithPrefix(): int
    {
        $success = 0;

        # Get dataset object whose file URLs we need to update
        $dataset = Dataset::find()
            ->where(["identifier" => $this->doi])
            ->one();

        $urls =  File::find()
            ->select(["location"])
            ->where(["dataset_id" => $dataset->id])
            ->asArray(true)
            ->all();
        $values = function ($item) {
            return $item["location"];
        };
        $flatURLs = array_map($values, $urls);

        # Update each URL with new prefix
        foreach ($flatURLs as $url) {
            if (str_contains($url, $this->separator)) {
                # Remove substring after separator
                $newUrl = substr($url, strrpos($url, $this->separator) + strlen($this->separator), strlen($url)) . PHP_EOL;
                $newUrl = $this->prefix . "$this->separator" . $newUrl;
                # Update location attribute in file object
                $file = File::find()
                    ->where(['location' => $url, 'dataset_id' => $dataset->id])
                    ->one();
                $file->location = $newUrl;
                print_r($file->location);
                if ($file->save()) {
                    $success++;
                }
            }
        }

        return $success;
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
