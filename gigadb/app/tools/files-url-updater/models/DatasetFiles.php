<?php

namespace app\models;

use \Yii;
use yii\base\ErrorException;
use yii\base\UserException;
use yii\db\Exception;

/**
 * Class DatasetFiles interact with database to retrieve data for dataset and files
 *
 */
class DatasetFiles extends \Yii\base\BaseObject {

    /**
     * @const the new url host to use
     */
    public const NEW_HOST = "https://ftp.cngb.org";

    /**
     * @const the url where to download test data
     */
    public const TESTDATA_HOST = "https://gigascience-testdata.rija.dev";

    /**
     * @const to indicate that we want to run the command in dry run mode
     */
    public const DRYRUN_ON = true;

    /**
     * @var bool $dryRun flag to indicate whether the dry run mode is activated (true) or not (false, the default)
     */
    public bool $dryRun = false;

    /**
     * Factory for this class
     *
     * @param bool $dryRun
     * @return DatasetFiles
     */
    public static function build(bool $dryRun = false): DatasetFiles
    {
        return new DatasetFiles(["dryRun" => $dryRun]);
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
            ->scalar();
    }

    /**
     * Query the files associated with the given dataset id
     * It does not retrieve all the result, as we need to the freedom
     * to count, retrieve all or batch retrieve the result
     *
     * @param int $dataset_id
     * @return \yii\db\Query
     */
    public function queryFilesForDataset(int $dataset_id): \yii\db\Query
    {

        $query1 = ( new Yii\db\Query())
            ->from('file')
            ->where(['dataset_id' => $dataset_id])
            ->andWhere(['like','location','ftp.cngb.org']);

        $query2 = ( new Yii\db\Query())
            ->from('file')
            ->where(['dataset_id' => $dataset_id])
            ->andWhere(['like','location','genomics.cn']);


        return $query1->union($query2);
    }

    /**
     * Replace ftp_site in dataset with one starting with https://ftp.cngb.org for a given dataset id
     *
     * Success and no-op return the value saved in database.
     * Failure return null and an error is logged
     *
     * @param int $dataset_id
     * @param array &audit array containing old ftp site and new ftp site (optional)
     * @return int|null number of row updated (1 is only successful number, 0 means there's a problem), null if no replacement was needed
     * @throws UserException
     */
    public function replaceDatasetFTPSite(int $dataset_id, array &$audit = []): ?int
    {
        $oldFTPSite=$this->getFTPSite($dataset_id);
        $uriParts = parse_url(ltrim($oldFTPSite));
        $auditRow = ["id" => $dataset_id, "old" => $oldFTPSite, "new" => null, "updated" => false];
        if( "https" === $uriParts['scheme'] and $oldFTPSite === ltrim($oldFTPSite)) {
            error_log("dataset $dataset_id has ftp_site starting with https already");
            return null; //no need for replacement if url already starts with https and doesn't need whitespace removal
        }

        if("ftp.cngb.org" === $uriParts['host']) { //this is for some records that were starting with https but there were whitespace before
            $newFTPSite = self::NEW_HOST.$uriParts['path'];
        }
        else {
            $path = mb_split("/pub", $uriParts['path'])[1];
            $newFTPSite = self::NEW_HOST."/pub/gigadb/pub".$path;
        }
        $auditRow['new'] = $newFTPSite;
        $updatedRows = $this->updateDbForTable("dataset",$newFTPSite, $dataset_id);
        if (1 === $updatedRows)
            $auditRow['updated'] = true;
        $audit = $auditRow;
        return $updatedRows;

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
            $uriParts = parse_url(ltrim($oldLocation));


            if( "https" === $uriParts['scheme']) {
                error_log("file record {$file['id']} has location starting with https already");
                continue; //no need for replacement if url already starts with https
            }

            switch($uriParts['host']) { # replace ftp urls with https based ones
                case "ftp.cngb.org":
                    $newLocation = self::NEW_HOST.$uriParts['path'];
                    break;
                case "parrot.genomics.cn":
                case "climb.genomics.cn":
                    $path = mb_split("/pub", $uriParts['path'])[1];
                    $newLocation = self::NEW_HOST."/pub/gigadb/pub".$path;
                    break;
                default:
                    error_log("No need to replace location for file {$file['id']}");
                    continue 2;
            }

            # Remove duplicate DOI from location
            $locationToSave = $newLocation;
            if ( preg_match("/(\/\d{6}\/)\d{6}\//", $newLocation, $matches)) {
                $parts = mb_split( #only keep left and right parts of the pattern
                    $matches[0],
                    $newLocation
                );
                $locationToSave = implode( # put the single /DOI/ in the middle
                    "",
                    [$parts[0],$matches[1],$parts[1]]
                );

            }

            $auditRow = ["id" => $file['id'], "old" => $oldLocation, "new" => $locationToSave, "updated" => false];
            $updatedRows = $this->updateDbForTable("file",$locationToSave, $file['id']);
            if (1 === $updatedRows) {
                $auditRow["updated"] = true;
                $processed++;
            }
            $audit []= $auditRow;

        }
        return $processed;
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
     * @param string $newLocation
     * @param int $file_id
     * @return int
     * @throws Exception
     */
    private function updateDbLocationTable(string $newLocation, int $file_id): int
    {
        return Yii::$app->db
            ->createCommand()
            ->update('file',
                ['location' => $newLocation],
                'id = :id',
                [':id' => $file_id]
            )
            ->execute();
    }

    /**
     * @param string $tableName
     * @param string $newString
     * @param int $table_id
     * @return int Number of rows updated
     */
    private function updateDbForTable(string $tableName, string $newString, int $table_id): int
    {
        switch ($tableName){
            case "dataset":
                try {
                    return $this->dryRun ? 1 : $this->updateDbDatasetTable($newString, $table_id);
                } catch (\Yii\Db\Exception $e) {
                    error_log($e->getMessage());
                    return 0;
                }
            case "file":
                try {
                    return $this->dryRun ? 1 : $this->updateDbLocationTable($newString, $table_id);
                } catch (\Yii\Db\Exception $e) {
                    error_log($e->getMessage());
                    return 0;
                }
            default:
                error_log("The supplied table name $tableName is not valid") ;
                return 0;
        }
    }

    /**
     * @param string $dateStr
     * @param bool $useTest
     */
    public static function reloadDb(string $dateStr, bool $useTest = false): void
    {
        $dbConfig = \Yii::$app->db->attributes;
        $dbUser = \Yii::$app->db->username;
        $dbPassword = \Yii::$app->db->password;

        $dbName = $dbConfig['database'];
        if($useTest) {
            $dbName = $dbConfig['test_database'];
            var_dump($dbName);
        }

        system("head -2 /app/sql/gigadbv3_$dateStr.backup | tail -1 | cat -e | grep 9.3",$retval); # test whether we have test data or real production backup
        if(0 === $retval) {
            $restoreList = "sql/default_restore.list";
        }
        else {
            $restoreList = "sql/production8_1_restore.list";
        }

        if($dbPassword) {
            system("PGPASSWORD=$dbPassword psql -U $dbUser -h {$dbConfig['host']} -c 'drop owned by $dbUser;' 2>/app/drop_restore.log >&2");
            system("PGPASSWORD=$dbPassword pg_restore --exit-on-error --no-owner --verbose --use-list $restoreList -h {$dbConfig['host']} -U $dbUser --dbname $dbName  /app/sql/gigadbv3_{$dateStr}.backup 2>/app/drop_restore.log >&2");
        }
        else {
            system("psql -U $dbUser -h {$dbConfig['host']} -c 'drop owned by $dbUser;' 2>/app/drop_restore.log >&2");
            system("pg_restore --exit-on-error --no-owner --verbose --use-list $restoreList -h {$dbConfig['host']} -U $dbUser --dbname $dbName  /app/sql/gigadbv3_$dateStr.backup 2>/app/drop_restore.log >&2");
        }
    }

}