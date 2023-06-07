<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use GigaDB\services\URLsService;
use GuzzleHttp\Client;
use PHPUnit\Runner\Exception;
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

    /**
     * @var string a string replacing portion of URL
     */
    public string $prefix;

    /**
     * @var string a short string that separates the half of the URL
     *      to be kept from the other half of the URL to be removed
     */
    public string $separator;

    /**
     * @var array a list of DOIs to be excluded from URL updates
     */
    public array $excludedDois;

    /**
     * @const the new url host to use
     */
    public const NEW_HOST = "https://s3.ap-northeast-1.wasabisys.com";

    /**
     * @const to indicate that we want to run the command in dry run mode
     */
    public const APPLY_OFF = true;

    /**
     * @var bool $apply flag to indicate whether the apply mode is activated (true) or not (false, the default)
     */
    public bool $apply = false;

    /**
     * Factory for this class
     *
     * @param bool $apply
     * @return DatasetFilesUpdater
     */
    public static function build(bool $apply = false): DatasetFilesUpdater
    {
        return new DatasetFilesUpdater(["apply" => $apply]);
    }

    /**
     * Replaces substring of a URL with a new prefix for all files in a dataset
     *
     * @return int returns number of files that have been successfully updated
     */
    public function replaceFileUrlSubstringWithPrefix($doi, $separator, $prefix): int
    {
        # Record how many files with their URL locations updated
        $success = 0;
        # Get dataset object whose file URLs we need to update
        $dataset = Dataset::find()
            ->where(["identifier" => $doi])
            ->one();

        # Update ftp_site attribute in dataset object
        # Should look like this https://ftp.cngb.org/pub/gigadb/pub/10.5524/102001_103000/102404/
        $oldFTPSite = $dataset->ftp_site;
        $uriParts = parse_url(ltrim($oldFTPSite));
        $path = mb_split("/pub", $uriParts['path'])[1];
        $newFTPSite = self::NEW_HOST . "/gigadb-datasets/live/pub" . $path;
        $dataset->ftp_site = $newFTPSite;
        if ($this->apply === true) {
            if (!$dataset->save()) {
                throw new Exception("Problem saving ftp_site attribute value in dataset");
            }
        }

        # Get all files belonging to dataset
        $files =  File::find()
            ->where(["dataset_id" => $dataset->id])
            ->all();
        # Update each location URL with new prefix
        foreach ($files as $file) {
            $url = $file->location;
            if (str_contains($url, $separator)) {
                # Remove substring after separator
                $newUrl = substr(
                    $url,
                    strrpos($url, $separator) + strlen($separator),
                    strlen($url)
                ) . PHP_EOL;
                $newUrl = $prefix . "$separator" . $newUrl;
                $file->location = $newUrl;
                if ($this->apply === true) {
                    if ($file->save()) {
                        $success++;
                    }
                }
            }
        }
        return $success;
    }

    /**
     * Get next batch of pending datasets
     *
     * @param int $next batch size
     * @return array List of DOIs requiring dataset file URLs to be updated
     */
    public function getNextPendingDatasets(int $next, array $optExcludedDois = []): array
    {
        $rows = (new \yii\db\Query())
            ->select('dataset.identifier')
            ->from('dataset')
            ->rightJoin('file', 'dataset.id = file.dataset_id')
            ->andWhere([
                'or',
                ['like', 'file.location', 'ftp://parrot.genomics'],
                ['like','file.location','ftp://ftp.cngb.org'],
                ['like','file.location','ftp://climb.genomics'],
                ['like', 'file.location', 'https://ftp.cngb.org']
            ])
            ->andWhere([
                'not',
                ['in', 'dataset.identifier', $optExcludedDois],
            ])
            ->orderBy('dataset.identifier')
            ->distinct()
            ->limit($next)
            ->all();
        return array_column($rows, 'identifier');
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
