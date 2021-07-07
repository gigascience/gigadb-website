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
            $response = null;
            try {
                $response = $this->_web->request('GET', $result['external_url']);
            }
            catch (RequestException $e) {
                Yii::log( Psr7\str($e->getRequest()) , "error");
                if ($e->hasResponse()) {
                    Yii::log( Psr7\str($e->getResponse()), "error");
                }
            }
            $contents = $response !== null ? (string) $response->getBody() : null;
            $metas = [];

            // To store either twitter or og meta tags into metas array
            if (preg_match_all('~<\s*meta\s+name="(twitter:[^"]+)"\s+content="([^"]*)~i', $contents, $matches)) {
                for($i=0;$i<count($matches[1]);$i++) {
                    $metas[$matches[1][$i]]=$matches[2][$i];
                }
            } elseif (preg_match_all('~<\s*meta\s+property="(og:[^"]+)"\s+content="([^"]*)~i', $contents, $matches)) {
                for ($i=0;$i<count($matches[1]);$i++) {
                    $metas[$matches[1][$i]]=$matches[2][$i];
                }
            }

            // Add new keys and values to the expected output array
            if (array_key_exists('twitter:title', $metas)) {
                $result['external_title'] = $metas['twitter:title'];
                $result['external_description'] = $metas['twitter:description'];
                $result['external_imageUrl'] = $metas['twitter:image'];
            } else {
                $result['external_title'] = $metas['og:title'];
                $result['external_description'] = $metas['og:description'];
                $result['external_imageUrl'] = $metas['og:image'];
            }
            $results[]=$result;
        }
        return $results;
    }

}