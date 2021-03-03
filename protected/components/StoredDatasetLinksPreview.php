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

    public function __construct (int $id, CDbConnection $db_connection, \GuzzleHttp\Client $webClient)
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
            $response = null;
            try {
                $this->_web = new GuzzleHttp\Client();
                $response = $this->_web->request('GET', 'https://www.nature.com/articles/d41586-021-00419-y');
//                $response = get_meta_tags($result['url']);
//                $response = get_meta_tags('https://www.nature.com/articles/d41586-021-00419-y');
            }
            catch (RequestException $e) {
                Yii::log( Psr7\str($e->getRequest()) , "error");
                if ($e->hasResponse()) {
                    Yii::log( Psr7\str($e->getResponse()), "error");
                }
            }
            $contents = $response !== null ? (string) $response->getBody() : null;
            $metas = [];
            if (preg_match_all('~<\s*meta\s+name="(twitter:[^"]+)"\s+content="([^"]*)~i', $contents, $matches)) {
                for($i=0;$i<count($matches[1]);$i++) {
                    $metas[$matches[1][$i]]=$matches[2][$i];
                }
            }
//            print_r($metas);
//            file_put_contents('test-body.txt', print_r($contents, true));
            $result['external_title'] = $metas['twitter:title'];
            $result['external_description'] = $metas['twitter:description'];
            $result['external_imageUrl'] = $metas['twitter:image'];
            $results[]=$result;
        }
        return $results;
    }

}