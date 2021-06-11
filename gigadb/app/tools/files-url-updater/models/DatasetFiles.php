<?php

namespace app\models;

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
     * Get all pending datasets
     *
     * @return array
     */
    public function getAllPendingDatasets() {
        return ( new \yii\db\Query())
            ->select(['dataset_id'])
            ->from('file')
            ->where(['like','location','ftp://parrot.genomics'])
            ->orWhere(['like','location','ftp://ftp.cngb.org'])
            ->orWhere(['like','location','ftp://climb.genomics'])
            ->groupBy('dataset_id')
            ->orderBy('dataset_id')
            ->all();
    }

    /**
     *
     * Get next batch of pending datasets
     *
     *
     * TODO: param int $after dataset id after wich to start fetching the list
     * @param int $next batch size
     * @return array
     */
    public function getNextPendingDatasets(int $next) {
        return ( new \yii\db\Query())
            ->select(['dataset_id'])
            ->from('file')
            ->where(['like','location','ftp://parrot.genomics'])
            ->orWhere(['like','location','ftp://ftp.cngb.org'])
            ->orWhere(['like','location','ftp://climb.genomics'])
            ->groupBy('dataset_id')
            ->orderBy('dataset_id')
            ->limit($next)
            ->all();
    }

    //TODO: function replaceDatasetFTPSite(int $dataset_id): bool
    //TODO: function replaceFileLocation(int $file_id): bool
}