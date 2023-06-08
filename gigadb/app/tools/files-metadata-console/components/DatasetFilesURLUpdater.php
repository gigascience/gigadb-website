<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use PHPUnit\Runner\Exception;
use Yii;
use yii\base\Component;

/**
 * For updating file URLs belonging to datasets
 */
final class DatasetFilesURLUpdater extends Component
{
    /**
     * @var string Dataset identifier for the dataset whose files need to be operated on
     */
    public string $doi;

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
    public const APPLY_OFF = false;

    /**
     * @var bool flag to indicate whether the apply mode is activated (true) or not (false, the default)
     */
    public bool $apply = false;

    /**
     * Factory for this class
     *
     * @param bool $apply
     * @return DatasetFilesURLUpdater
     */
    public static function build(bool $apply = false): DatasetFilesURLUpdater
    {
        return new DatasetFilesURLUpdater(["apply" => $apply]);
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
        $oldFTPSite = $dataset->ftp_site;
        $uriParts = parse_url(ltrim($oldFTPSite));
        $path = mb_split("/pub", $uriParts['path'])[1];
        $newFTPSite = self::NEW_HOST . "/gigadb-datasets/live/pub" . $path;
        if ($this->apply === true) {
            $this->updateDbDatasetTable($newFTPSite, $dataset->id);
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
     * @param string $newFTPSite
     * @param int $dataset_id
     * @return int
     * @throws Exception
     */
    private function updateDbDatasetTable(string $newFTPSite, int $dataset_id): int
    {
        return Yii::$app->db
            ->createCommand()
            ->update('dataset',
                ['ftp_site' => $newFTPSite],
                'id = :id',
                [':id' => $dataset_id]
            )
            ->execute();
    }

    /**
     * Get next batch of pending datasets using Yii2 Query
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
}
