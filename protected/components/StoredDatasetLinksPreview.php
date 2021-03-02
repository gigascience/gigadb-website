<?php
/**
 * To
 * Class StoredDatasetLinksPreview
 *
 * @param int $id The dataset ID
 * @param CDbConnection $db_connection The database connection object
 * @param GuzzleHttp\Client $webClient The web client to get html response
 */

class StoredDatasetLinksPreview extends DatasetComponents implements DatasetLinksPreviewInterface
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


    /**
     * Get doi from dataset, external link url and external link type from external_link and external_link_type
     * Then browse the external link url, and get title, description and image url
     * @return array
     * @uses \GuzzleHttp\Client
     */
    public function getPreviewDataForLinks(): array
    {
        $sql = "select identifier as short_doi, exl.url as external_url, ext.name as type
            from dataset dd, external_link exl, external_link_type ext  
            where dd.id = exl.dataset_id 
            and exl.external_link_type_id = ext.id 
            and dd.id = :id;";
        $command = $this->_db->createCommand($sql);
        $command->bindParam( ":id", $this->_id , PDO::PARAM_INT);
        $rows = $command->queryAll();
        $results = [];

        foreach ( $rows as $result) {
//            $response = null;
//            try {
//                $this->_web = new \GuzzleHttp\Client();
////                $response = $this->_web->request('GET', $result['url']);
//                $response = $this->_web->request('GET', 'https://www.nature.com/articles/d41586-021-00419-y');
//            }
//            catch (RequestException $e) {
//                Yii::log( Psr7\str($e->getRequest()) , "error");
//                if ($e->hasResponse()) {
//                    Yii::log( Psr7\str($e->getResponse()), "error");
//                }
//            }
//            $result['response'] = $response !== null ? (string) $response->getBody() : null;

            $url = 'https://www.nature.com/articles/d41586-021-00419-y';
            $meta_tags = get_meta_tags($url);
//            print_r($meta_tags, true);
            $result['external_title'] = $meta_tags['twitter:title'];
            $result['external_description'] = $meta_tags['twitter:description'];
            $result['external_imageUrl'] = $meta_tags['twitter:image'];
            $results[]=$result;
        }
        return $results;
    }

}