<?php


class StoredDatasetLinksPreview extends DatasetComponents implements DatasetLinksPreviewInterface
{
    private $_id;
    private $_db;
//    private $_web;

    public function __construct (int $id, CDbConnection $db_connection)
    {
        parent::__construct();
        $this->_id = $id;
        $this->_db = $db_connection;
//        $this->_web = $webClient;
    }

    /**
     * return the dataset ID
     *
     * @return int
     */
    public function getDatasetId(): int
    {
        return $this->_id;
    }

    /**
     * return the dataset identifier (DOI)
     *
     * @return string
     */
    public function getDatasetDOI(): string
    {
        return $this->getDOIfromId($this->_db, $this->_id);
    }

    public function getImageUrl(): array
    {
        $sql = "select url from image where id = :id";

        $command = $this->_db->createCommand($sql);
        $command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
        $results = $command->queryAll();
        return $results;
    }

    public function getPreviewDataForLinks(): array
    {
        $sql = "select identifier as short_doi, exl.url as external_url, ext.name as type, title, description, img.url as image_url from dataset dd, image img, external_link exl, external_link_type ext  where dd.id = img.id and dd.id = exl.dataset_id and exl.external_link_type_id = ext.id and dd.id = :id;";
        $command = $this->_db->createCommand($sql);
        $command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
        $rows = $command->queryAll();
//        print_r($rows, true);
        $results = [];

        foreach ( $rows as $result) {
            $results[]=$result;
        }

        return $results;

    }

}