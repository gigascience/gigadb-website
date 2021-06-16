<?php

namespace app\models;

use \Yii;
use yii\db\Exception;

/**
 * Class DatasetFiles interact with database to retrieve data for dataset and files
 *
 */
class DatasetFiles extends \Yii\base\BaseObject {

    public const NEW_HOST = "https://ftp.cngb.org";

    /**
     * Factory for this class
     *
     * @return DatasetFiles
     */
    public static function build() {
        return new DatasetFiles();
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
            ->groupBy('dataset_id')
            ->orderBy('dataset_id')
            ->having(['>','dataset_id', $after]);
    }

    /**
     * Get all pending datasets
     *
     * @return array
     */
    public function getAllPendingDatasets() {
        return $this->filterByFTPUrls()
            ->all();
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
    public function getNextPendingDatasets(int $after = 0, int $next) {
        return $this->filterByFTPUrls($after)
            ->limit($next)
            ->all();
    }

    /**
     * Retrieve from database the value of ftp_site for a given dataset id
     *
     * @param int $dataset_id
     * @return string
     */
    private function getFTPSite(int $dataset_id): string
    {
        return ( new \yii\db\Query())
            ->select('ftp_site')
            ->from('dataset')
            ->where(['id' => $dataset_id])
            ->one()['ftp_site'];
    }

    /**
     * Query the files associated with the given dataset id
     * It does not retrieve all the result, as we need to the freedom
     * to count, retrieve all or batch retrieve the result
     *
     * @param int $dataset_id
     * @return \yii\db\Query
     */
    private function queryFilesForDataset(int $dataset_id): \yii\db\Query
    {
        return ( new Yii\db\Query())
            ->from('file')
            ->where(['dataset_id' => $dataset_id]);
    }

    /**
     * Replace ftp_site in dataset with one starting with https://ftp.cngb.org for a given dataset id
     *
     * Success and no-op return the value saved in database.
     * Failure return null and an error is logged
     *
     * @param int $dataset_id
     * @return string|null
     */
    public function replaceDatasetFTPSite(int $dataset_id): ?string
    {
        try{
            $oldFTPSite=$this->getFTPSite($dataset_id);
        }
        catch (\Yii\base\ErrorException $e) {
            error_log("Problem retrieving ftp_site value for dataset $dataset_id: ". $e->getMessage());
            return null;
        }

        $scheme=mb_split("://", $oldFTPSite)[0];
        if( "https" === ltrim($scheme)) {
            error_log("dataset $dataset_id has ftp_site starting with https already");
            return $oldFTPSite; //no need for replacement if url already starts with https
        }

        list($host, $path) = mb_split("/pub", $oldFTPSite);
        $newHost="https://ftp.cngb.org/pub/gigadb";
        $newFTPSite = $newHost."/pub".$path;

        try {
            $updatedRows = Yii::$app->db
                ->createCommand()
                ->update('dataset',
                    ['ftp_site' => $newFTPSite],
                    'id = :id',
                    [':id' => $dataset_id]
                )
                ->execute();
        } catch (\Yii\Db\Exception $e) {
            error_log($e->getMessage());
            return null;
        }

        if ($this->getFTPSite($dataset_id) !== $newFTPSite) {
            error_log("Problem saving the new ftp_site value for dataset $dataset_id");
            return null;
        };
        return $newFTPSite;
    }

    /**
     * Replace location column for all files associated with the given dataset
     *
     * @param int $dataset_id id of dataset for which to process files
     * @param array &audit array containing old location and new locations (optional)
     * @return int The number of files successfully replaced
     */
    function replaceFilesLocationForDataset(int $dataset_id, array &$audit = []): int
    {
        $processed = 0;

        foreach ($this->queryFilesForDataset($dataset_id)->each() as $index => $file) {

            $oldLocation = $file['location'];
            $uriParts = parse_url($oldLocation);


            if( "https" === $uriParts['scheme']) {
                error_log("file record {$file['id']} has location starting with https already");
                continue; //no need for replacement if url already starts with https
            }

            if("ftp.cngb.org" === $uriParts['host'] && "ftp" === $uriParts['scheme']) {
                $newLocation = self::NEW_HOST.$uriParts['path'];
            }
            else {
                $path = mb_split("/pub", $uriParts['path'])[1];
                $newLocation = self::NEW_HOST."/pub/gigadb/pub".$path;
            }

            $auditRow = ["id" => $file['id'], "old" => $oldLocation, "new" => $newLocation, "updated" => false];
            try {
                $updatedRows = Yii::$app->db
                    ->createCommand()
                    ->update('file',
                        ['location' => $newLocation],
                        'id = :id',
                        [':id' => $file['id']]
                    )
                    ->execute();
                if (1 === $updatedRows) {
                    $auditRow["updated"] = true;
                }
                $processed++;
                $audit []= $auditRow;
            } catch (\Yii\Db\Exception $e) {
                error_log($e->getMessage());
                continue;
            }

        }
        return $processed;
    }
}