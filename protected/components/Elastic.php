<?php

class Elastic extends CApplicationComponent {

    public $host;
    public $port;
    public $client;
    public $index;

    public function init() {
        parent::init();
        $this->client = new \Elastica\Client(
            array('host'=>$this->host, 'port'=>$this->port)
        );
        $this->createIndex();
    }
    
    public function createIndex() {
        $index = $this->client->getIndex('gigadb');
        if (!$index->exists()) {
            $index->create(
                array(
                    'number_of_shards' => 1,
                    'number_of_replicas' => 1,
                    'analysis' => array(
                        'analyzer' => array(
                            'indexAnalyzer' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => array('lowercase', 'GigaSnowball', 'asciifolding', 'GigaPartial')
                            ),
                            'searchAnalyzer' => array(
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => array('standard', 'lowercase', 'GigaSnowball', 'asciifolding', 'GigaPartial')
                            )
                        ),
                        'filter' => array(
                            'GigaSnowball' => array(
                                'type' => 'snowball'
                            ),
                            'GigaPartial' => array(
                                'type' => 'ngram',
                                'min_gram' => 3,
                                'max_gram' => 8,
                            ),
                        )
                    )
                ),
                true
            );
            $this->index = $index;
        }
    }

    function createDatasetMapping() {
        // create type
        $index = $this->index;
        $type = $index->getType('dataset');

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        //$mapping->setParam('_boost', array('name' => 'chinese_name', 'null_value' => ''));

        // Set mapping
        $mapping->setProperties(array(
            'id'    => array('type' => 'integer', 'include_in_all' => TRUE),
            'dataset_id'    => array('type' => 'integer'),
            'excelfile' => array('type' => 'string'),
            'ftp_site'  => array('type' => 'string'),            
            'title' => array('type' => 'string'),
            'description'   => array('type' => 'string'),
            'identifier'    => array('type' => 'string'),
            'upload_status' => array('type' => 'string'),
            'authors'   => array('type' => 'string'),
            'projects'  => array('type' => 'string'),
            'attr_value'    => array('type' => 'string'),
            'attr_def'  => array('type' => 'string'),
            'link_types' => array('type' => 'string'),
            'typenames' => array('type' => 'string'),
            'manuscripts'   => array('type' => 'string'),
            'pub_name'  => array('type' => 'string'),
            'pub_desc'  => array('type' => 'string'),
            'publication_date'    => array('type' => 'date'),
            'modification_date'     => array('type' => 'date')
        ));
        $mapping->send();
    }

    function createSampleMapping() {
        // create type
        $index = $this->index;
        $type = $index->getType('sample');

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        //$mapping->setParam('_boost', array('name' => 'chinese_name', 'null_value' => ''));

        // Set mapping
        $mapping->setProperties(array(
            'id'    => array('type' => 'integer', 'include_in_all' => TRUE),
            'dataset_id'    => array('type' => 'integer'),
            'name'  => array('type' => 'string'),
            'consent_document' => array('type' => 'string'),
            'contact_author_name'   => array('type' => 'string'),
            'contact_author_email'  => array('type' => 'string'),
            'sampling_protocol' => array('type' => 'string'),
            'attr_value'    => array('type' => 'string'),
            'attr_def'  => array('type' => 'string'),
            'common_name'   => array('type' => 'string'),
            'genbank_name'  => array('type' => 'string'),
            'scientific_name'   => array('type' => 'string'),
            'eol_link'  => array('type' => 'string'),
            'exps'  => array('type' => 'string'),
            'exp_attrs' => array('type' => 'string'),
            'exp_attr_def'  => array('type' => 'string'),
            'submission_date'   => array('type' => 'date')
        ));
        $mapping->send();

    }

    function createFileMapping() {
        // create a type
        $index = $this->index;
        $type = $index->getType('file');

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($type);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        //$mapping->setParam('_boost', array('name' => 'chinese_name', 'null_value' => ''));

        // Set mapping
        $mapping->setProperties(array(
            'id'    => array('type' => 'integer', 'include_in_all' => TRUE),
            'dataset_id'    => array('type' => 'integer'),
            'name'  => array('type' => 'string'),
            'size'  => array('type' => 'long'),
            'description'   => array('type' => 'string'),           
            'filetype'  => array('type' => 'string'),
            'format'=> array('type' => 'string'),
        ));
        $mapping->send();
    }

    public function createDocument($id, $doc) {
        return new \Elastica\Document($id, $doc);
    }    
}
