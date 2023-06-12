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
     * @const the bucket name and subdirectories to use
     */
    public const BUCKET_DIRECTORIES = "/gigadb-datasets/live/pub/";

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
     * Replace ftp_site in dataset with Wasabi URL
     * 
     * Most file locations will look like this:
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/readme.txt
     * The above URL will be updated to a Wasabi link:
     * https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/readme.txt
     *
     * @param string $doi Dataset identifier
     * @param string $separator A substring providing position to split current file location URL
     * @return string new file URL location
     */
    public function replaceLocationsForDatasetFiles(string $doi, string $separator)
    {
        $newFTPLocationPrefix = self::NEW_HOST . self::BUCKET_DIRECTORIES;

        # Record how many files with their URL locations updated
        $processed = 0;
        # Get all files belonging to dataset
        $dataset = Dataset::find()->where(["identifier" => $doi])->one();
        $files =  File::find()->where(["dataset_id" => $dataset->id])->all();
        # Update each file's location URL
        foreach ($files as $file) {
            $currentFileLocation = $file['location'];
            $uriParts = parse_url(ltrim($currentFileLocation));
            if ("https" === $uriParts['scheme'] && "s3.ap-northeast-1.wasabisys.com" === $uriParts['host']) {
                # Nothing to do as it's already a Wasabi URL
                continue;
            } elseif ("ftp" === $uriParts['scheme'] || "ftp.cngb.org" === $uriParts['host']) {
                // Update ftp_site as it starts with ftp:// or contains ftp.cngb.org
                $tokens = explode($separator, $uriParts['path']);
                $newFileLocation = $newFTPLocationPrefix . end($tokens);
                if ($this->apply === true) {
                    $this->updateDbFileTable($newFileLocation, $file->id);
                    $processed++;
                }
            } else {
                throw new Exception('File has unexpected URL location: ' . $currentFileLocation);
            }
        }
        return $processed;
    }

    /**
     * Replace ftp_site in dataset with Wasabi URL
     *
     * @param string Dataset DOI
     * @return int Number of ftp_site changes
     */
    public function replaceFTPSiteForDataset($doi)
    {
        $success = 0;
        $newFTPSitePrefix = self::NEW_HOST . self::BUCKET_DIRECTORIES;;

        $dataset =  Dataset::find()->where(["identifier" => $doi])->one();
        $currentFTPSite = $dataset['ftp_site'];
        $uriParts = parse_url(ltrim($dataset['ftp_site']));
        // Update ftp_site if it starts with ftp:// or contains ftp.cngb.org
        if ("ftp" === $uriParts['scheme'] || "ftp.cngb.org" === $uriParts['host']) {
            $path = mb_split("/pub", $uriParts['path'])[1];
            $newFTPSite = $newFTPSitePrefix . $path;
            if ($this->apply === true) {
                $this->updateDbDatasetTable($newFTPSite, $dataset->id);
                $success++;
            }
        }
        else {
            throw new Exception("Dataset has unexpected ftp_site: " . $currentFTPSite);
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
        try {
            return Yii::$app->db
                ->createCommand()
                ->update(
                    'dataset',
                    ['ftp_site' => $newFTPSite],
                    'id = :id',
                    [':id' => $dataset_id]
                )
                ->execute();
        } catch (\Yii\Db\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @param string $newFileLocation
     * @param int $file_id
     * @return int
     * @throws Exception
     */
    private function updateDbFileTable(string $newFileLocation, int $file_id): int
    {
        try {
            return Yii::$app->db
                ->createCommand()
                ->update(
                    'file',
                    ['location' => $newFileLocation],
                    'id = :id',
                    [':id' => $file_id]
                )
                ->execute();
        } catch (\Yii\Db\Exception $e) {
            throw new Exception($e->getMessage());
        }
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
