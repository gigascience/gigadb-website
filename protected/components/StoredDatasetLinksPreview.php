<?php


class StoredDatasetLinksPreview extends DatasetComponents
{
    private $_id;
    private $_db;
    private $_web;

    public function __construct (int $id, CDbConnection $db_connection, GuzzleHttp\Client $webClient)
    {
        parent::__construct();
        $this->_id = $id;
        $this->_db = $db_connection;
        $this->_web = $webClient;
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
        $sql = "select identifier as short_doi, identifier as url, title, description, img.url as image_url from dataset dd, image img  where dd.id = img.id and dd.id = :id";
        $command = $this->_db->createCommand($sql);
        $command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
        $rows = $command->queryAll();
        $results = [];

        foreach ( $rows as $result) {
            $result['url'] = "https://doi.org/10.5524/".$result['url'];
            $results[]=$result;
        }

        return $results;
    }

}