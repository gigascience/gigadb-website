<?php

declare(strict_types=1);

namespace app\components;

use GigaDB\models\Dataset;
use GigaDB\models\File;
use PHPUnit\Runner\Exception;
use Yii;
use yii\base\Component;
use yii\db\ActiveRecord;

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
     * Replace all file locations in dataset with Wasabi URL
     *
     * Most file locations will look like this:
     * https://ftp.cngb.org/pub/gigadb/pub/10.5524/100001_101000/100020/readme.txt
     * The above URL will be updated to a Wasabi link:
     * https://s3.ap-northeast-1.wasabisys.com/gigadb-datasets/live/pub/10.5524/100001_101000/100020/readme.txt
     *
     * @param string $doi Dataset identifier
     * @param string $separator A substring providing position to split current file location URL
     * @return int Number of file locations updated
     */
    public function replaceFileLocationsForDataset(string $doi, string $separator)
    {
        $newFTPLocationPrefix = self::NEW_HOST . self::BUCKET_DIRECTORIES;

        # Record how many files with their URL locations updated
        $processed = 0;
        # Get all files belonging to dataset
        $dataset = Dataset::find()->where(["identifier" => $doi])->one();
        $files = $this->queryFilesForDataset($doi);
        # Update each file's location URL
        foreach ($files as $file) {
            $currentFileLocation = $file['location'];
            $uriParts = parse_url(ltrim($currentFileLocation));
            if ("https" === $uriParts['scheme'] && "s3.ap-northeast-1.wasabisys.com" === $uriParts['host']) {
                # Nothing to do as it's already a Wasabi URL
                continue;
            } elseif ("http" === $uriParts['scheme'] && str_contains($uriParts['host'], 'amazonaws.com')) {
                # Nothing to do as it looks like an AWS S3 URL
                continue;
            } elseif ("http" === $uriParts['scheme'] && str_contains($uriParts['host'], 'dx.doi.org')) {
                # Nothing to do as it looks like a DOI URL
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'doi.org')) {
                # Nothing to do as it looks like a DOI URL
                continue;
            } elseif ("http" === $uriParts['scheme'] && str_contains($uriParts['host'], 'ac.uk')) {
                # Nothing to do as it looks like a UK academic URL, e.g. http://repos.tgac.ac.uk/vms/Galaxy_with_GeneSeqToFamily.ova
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'ebi.ac.uk')) {
                # Nothing to do as it looks like a EBI URL, e.g. https://www.ebi.ac.uk/ena/data/view/PRJEB23358
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'inra.fr')) {
                # Nothing to do as it looks like a UK academic URL, e.g. https://urgi.versailles.inra.fr/download/wheat/3B/ta3bAllScaffoldsV443.genom.fa.gz
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'ncbi.nlm.nih.gov')) {
                # Nothing to do as it looks like a NCBI URL, e.g. https://www.ncbi.nlm.nih.gov/Traces/wgs/LSYQ01/LSYQ01000006
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'figshare.com')) {
                # Nothing to do as it looks like a Figshare URL, e.g. https://ndownloader.figshare.com/files/13392434
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'commonwl.org')) {
                # Nothing to do as it looks like a Common WL URL, e.g. https://view.commonwl.org/workflows/github.com/pitagora-network/pitagora-cwl/blob/master/workflows/hisat2-cufflinks/paired_end/hisat2-cufflinks_wf_pe.cwl
                continue;
            } elseif ("http" === $uriParts['scheme'] && str_contains($uriParts['host'], 'embl.de')) {
                # Nothing to do as it looks like a EMBL URL, e.g. http://eggnog.embl.de/orthobench2/orthobench2.all.data.tar.gz
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'cloud.google.com')) {
                # Nothing to do as it looks like a Google Cloud URL, e.g. https://console.cloud.google.com/storage/browser/genomics-public-data/resources/broad/hg38/v0
                continue;
            } elseif ("https" === $uriParts['scheme'] && str_contains($uriParts['host'], 'globus.org')) {
                # Nothing to do as it looks like a Globus URL, e.g. https://g-624536.53220.5898.data.globus.org/11/published/publication_437/submitted_data/Q22/03_Videos_mpgFiles/Q22ILi2_of1_inj01_20040128_009_012_003_002_001.mpg
                continue;
            } elseif ("ftp" === $uriParts['scheme'] || "ftp.cngb.org" === $uriParts['host']) {
                // Update ftp_site as it starts with ftp:// or contains ftp.cngb.org
                $tokens = explode($separator, $uriParts['path']);
                $newFileLocation = $newFTPLocationPrefix . end($tokens);
                if ($this->apply === true) {
                    $this->updateDbFileTable($newFileLocation, $file->id);
                }
                $processed++;
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
        $newFTPSitePrefix = self::NEW_HOST . self::BUCKET_DIRECTORIES;

        $dataset =  Dataset::find()->where(["identifier" => $doi])->one();
        $currentFTPSite = $dataset['ftp_site'];
        $uriParts = parse_url(ltrim($dataset['ftp_site']));
        // Update ftp_site if it starts with ftp:// or contains ftp.cngb.org
        if ("ftp" === $uriParts['scheme'] || "ftp.cngb.org" === $uriParts['host']) {
            $path = mb_split("/pub/", $uriParts['path'])[1];
            $newFTPSite = $newFTPSitePrefix . $path;
            if ($this->apply === true) {
                $this->updateDbDatasetTable($newFTPSite, $dataset->id);
            }
            $success++;
        } else {
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
                ['like', 'file.location', 'ftp://ftp.cngb.org'],
                ['like', 'file.location', 'ftp://climb.genomics'],
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

    /**
     * Query the files associated with the given dataset id
     * It does not retrieve all the result, as we need to the freedom
     * to count, retrieve all or batch retrieve the result
     *
     * @param string $dataset_doi
     * @return ActiveRecord[]
     */
    public function queryFilesForDataset(string $doi): array
    {
        $dataset = Dataset::find()->where(["identifier" => $doi])->one();
        return File::find()->where(["dataset_id" => $dataset->id])->all();
    }
}
