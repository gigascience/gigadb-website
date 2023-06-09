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
        $newFTPSite = $this->replaceDatasetFTPSitePrefix($oldFTPSite);
        if ($this->apply === true) {
            $this->updateDbDatasetTable($newFTPSite, $dataset->id);
        }

        # Get all files belonging to dataset
        $files =  File::find()->where(["dataset_id" => $dataset->id])->all();
        # Update each file's location URL
        foreach ($files as $file) {
            $url = $file->location;
            $newUrl = $this->replaceFileLocationPrefix($url, $separator, $prefix);
            $file->location = $newUrl;
            if ($this->apply === true) {
                $this->updateDbFileTable($newUrl, $file->id);
                $success++;
            }
        }
        return $success;
    }

    /**
     * Replace ftp_site in dataset with Wasabi URL
     *
     * Success and no-op return the value saved in database.
     * Failure return null and an error is logged
     *
     * @param string $oldFileLocation old file URL location
     * @return string new file URL location
     */
    public function replaceFileLocationPrefix($oldFileLocation, $separator, $prefix)
    {
        $newUrl = substr(
            $oldFileLocation,
            strrpos($oldFileLocation, $separator) + strlen($separator),
            strlen($oldFileLocation)
        ) . PHP_EOL;
        $newFileLocation = $prefix . "$separator" . $newUrl;
        return $newFileLocation;
    }

    /**
     * Replace ftp_site in dataset with Wasabi URL
     *
     * Success and no-op return the value saved in database.
     * Failure return null and an error is logged
     *
     * @param string old ftp_site
     * @return string new ftp_site
     */
    public function replaceDatasetFTPSitePrefix($oldFTPSite)
    {
        $newFTPSitePrefix = self::NEW_HOST . '/gigadb-datasets/live/pub';

        $uriParts = parse_url(ltrim($oldFTPSite));
        var_dump($uriParts);
        // Update ftp_site if it starts with ftp://climb.genomics.cn or https://ftp.cngb.org
        if ("ftp" === $uriParts['scheme'] || "ftp.cngb.org" === $uriParts['host']) {
            $path = mb_split("/pub", $uriParts['path'])[1];
            $newFTPSite = $newFTPSitePrefix . $path;
            return $newFTPSite;
        }
        else {
            error_log("dataset has unexpected ftp_site: " . $oldFTPSite);
            return null;
        }
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
            ->update(
                'dataset',
                ['ftp_site' => $newFTPSite],
                'id = :id',
                [':id' => $dataset_id]
            )
            ->execute();
    }

    /**
     * @param string $newFileLocation
     * @param int $file_id
     * @return int
     * @throws Exception
     */
    private function updateDbFileTable(string $newFileLocation, int $file_id): int
    {
        return Yii::$app->db
            ->createCommand()
            ->update(
                'file',
                ['location' => $newFileLocation],
                'id = :id',
                [':id' => $file_id]
            )
            ->execute();
    }

    /**
     * Get next batch of pending datasets using Yii2 Query
     *
     * @param int $next batch size
     * @return array List of DOIs requiring dataset file URLs to be updated
     */
    public function getNextPendingDatasets(int $next, array $excludedDois = []): array
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
                ['in', 'dataset.identifier', $excludedDois],
            ])
            ->orderBy('dataset.identifier')
            ->distinct()
            ->limit($next)
            ->all();
        return array_column($rows, 'identifier');
    }
}
