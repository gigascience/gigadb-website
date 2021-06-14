<?php

namespace app\models;

use \Yii;
use yii\db\Exception;

/**
 * Class DatasetFiles interact with database to retrieve data for dataset and files
 *
 */
class DatasetFiles extends \Yii\base\BaseObject {

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

    private function getFTPSite(int $dataset_id): string
    {
        return ( new \yii\db\Query())
            ->select('ftp_site')
            ->from('dataset')
            ->where(['id' => $dataset_id])
            ->one()['ftp_site'];
    }

    public function replaceDatasetFTPSite(int $dataset_id): bool
    {
        $oldFTPSite=$this->getFTPSite($dataset_id);
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
            return false;
        }

        return $this->getFTPSite($dataset_id) == $newFTPSite;
    }
    //TODO: function replaceFileLocation(int $file_id): bool
}