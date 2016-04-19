<?php
function __autoload_elastica ($class) {
  $path = str_replace('\\', '/', $class);
  $path = Yii::getPathOfAlias('Elastica') . "/{$path}.php";
  if (file_exists($path)) {
    require_once($path);
  }
}

spl_autoload_unregister(array('YiiBase','autoload'));
spl_autoload_register('__autoload_elastica');
spl_autoload_register(array('YiiBase','autoload'));

class ElasticSearch {
    public function newTerm($key, $val) {
        $termFilter = new \Elastica\Filter\Term();
        $termFilter->setTerm($key, $val);
        return $termFilter;
    }

    public function newTerms($key, $vals) {
        $termFilter = new \Elastica\Filter\Terms();
        $termFilter->setTerms($key, $vals);
        return $termFilter;
    }

    public function searchDataset($criteria, $sorts = array(), $offset = 0, $limit = 0, $operator = 'OR') {
        if($limit <= 0) {
            $limit = Yii::app()->params['es_search']['limits']['default'];
        }

        $keyword = $criteria['keyword'];

        $eQueryString = new \Elastica\Query\QueryString();
        $eQueryString->setDefaultOperator($operator);
        $eQueryString->setQuery($keyword);

        $eQuery = new \Elastica\Query();
        $eQuery->setQuery($eQueryString);

        $eQuery->setFrom($offset);
        $eQuery->setLimit($limit);

        $filterAnd = new \Elastica\Filter\BoolAnd;


        if(isset($criteria['dataset_type'])) {
            $filterAnd->addFilter($this->newTerms('typenames', $criteria['dataset_type']));
        }

        if(isset($criteria['project'])) {
            $filterAnd->addFilter($this->newTerms('projects', $criteria['project']));
        }

        if(isset($criteria['external_link_type'])) {
            $filterAnd->addFilter($this->newTerms('link_types', $criteria['external_link_type']));
        }

        if(isset($criteria['pubdate_from']) && isset($criteria['pubdate_to'])) {
            $pubfilter = new \Elastica\Filter\Range();
            $pubfilter->addField('publication_date', array('from'=>$criteria['pubdate_from'], 'to'=>$criteria['pubdate_to']));
            $filterAnd->addFilter($pubfilter);
        }
        elseif(isset($criteria['pubdate_from'])) {
            $pubfilter = new \Elastica\Filter\Range();
            $pubfilter->addField('publication_date', array('from'=>$criteria['pubdate_from']));
            $filterAnd->addFilter($pubfilter);
        }
        elseif(isset($criteria['pubdate_to'])) {
            $pubfilter = new \Elastica\Filter\Range();
            $pubfilter->addField('publication_date', array('to'=>$criteria['pubdate_to']));
            $filterAnd->addFilter($pubfilter);
        }

        $arrayAnd = $filterAnd;
        $arrayAnd = $arrayAnd->toArray();
        
        if(count($arrayAnd['and']) > 0)
            $eQuery->setPostFilter($filterAnd);

        $sortList = array_merge(array('_score'=>array('order'=>'desc')), $sorts);
        $eQuery->setSort($sortList);

        $index= Yii::app()->elastic->client->getIndex('gigadb');
        $type = $index->getType('dataset');        

        $eResultSet = $type->search($eQuery);
        $results = $eResultSet->getResults();
        $total = $eResultSet->getTotalHits();

        $data = array();
        foreach($results as $result) {
            if($result)
                $data[] = $result->getData();
        }
        
        $re = array('data' => $data, 'total' => $total);
        //Yii::log(print_r($eResultSet, true), 'debug');
        return $re;
    }

    public function convertSize($size, $unit) {
        try {
            if($unit==1){
                $size*=1024;
            }else if($unit==2){
                $size*=1024*1024;
            }else if($unit==3){
                $size*=1024*1024*1024;
            }else if($unit==4){
                $size*=1024*1024*1024*1024;
            }else {
                $size=0;
            }
            return $size;
        }
        catch(Exception $e) {
            return 0;
        }
    }

    public function searchFile($criteria, $dataset, $sorts = array(), $operator = 'OR') {
        /*if($limit <= 0) {
            $limit = Yii::app()->params['es_search']['limits']['default'];
        }*/

        $keyword = $criteria['keyword'];


        $eQueryString = new \Elastica\Query\QueryString();
        $eQueryString->setDefaultOperator($operator);
        $eQueryString->setQuery($keyword);

        $eQuery = new \Elastica\Query();
        $eQuery->setQuery($eQueryString);
        //$eQuery->setFrom($offset);
        //$eQuery->setLimit($limit);

        $filterAnd = new \Elastica\Filter\BoolAnd;

        if(is_array($dataset) && !empty($dataset)) {
            $ids = new \Elastica\Filter\Ids();
            $ids->setIds($dataset);
            $filterAnd->addFilter($ids);
        }

        if(isset($criteria['file_format'])) {
            $filterAnd->addFilter($this->newTerms('format', $criteria['file_format']));
        }

        if(isset($criteria['file_type'])) {
            $filterAnd->addFilter($this->newTerms('filetype', $criteria['file_type']));
        }

        if(isset($criteria['size_from']) && isset($criteria['size_to'])) {
            $sizefilter = new \Elastica\Filter\Range();
            $from = $this->convertSize($criteria['size_from'], $criteria['size_from_unit']);
            $to = $this->convertSize($criteria['size_to'], $criteria['size_to_unit']);
            $sizefilter->addField('size', array('gte'=>$from, 'lte'=>$to));
            $filterAnd->addFilter($sizefilter);
        }
        elseif(isset($criteria['size_from'])) {
            $sizefilter = new \Elastica\Filter\Range();
            $from = $this->convertSize($criteria['size_from'], $criteria['size_from_unit']);            
            $sizefilter->addField('size', array('gte'=>$from));
            $filterAnd->addFilter($sizefilter);
        }
        elseif(isset($criteria['size_to'])) {
            $sizefilter = new \Elastica\Filter\Range();
            $to = $this->convertSize($criteria['size_to'], $criteria['size_to_unit']);
            $sizefilter->addField('size', array('gte'=>0, 'lte'=>$to));
            $filterAnd->addFilter($sizefilter);
        }

        $arrayAnd = $filterAnd;
        $arrayAnd = $arrayAnd->toArray();
        //Yii::log(print_r($arrayAnd, true), 'debug');

        if(count($arrayAnd['and']) > 0)
            $eQuery->setPostFilter($filterAnd);

        $sortList = array_merge(array('_score'=>array('order'=>'desc')), $sorts);
        $eQuery->setSort($sortList);

        $index= Yii::app()->elastic->client->getIndex('gigadb');
        $type = $index->getType('file');

        

        $eResultSet = $type->search($eQuery);
        $results = $eResultSet->getResults();
        $total = $eResultSet->getTotalHits();

        $data = array();
        foreach($results as $result) {
            if($result)
                $data[] = $result->getData();
        }
        
        $re = array('data' => $data, 'total' => $total);
        //Yii::log(print_r($re, true), 'debug');
        return $re;
    }

    public function searchSample($criteria, $dataset, $sorts = array(), $operator = 'OR') {
        /*if($limit <= 0) {
            $limit = Yii::app()->params['es_search']['limits']['default'];
        }*/

        $keyword = $criteria['keyword'];


        $eQueryString = new \Elastica\Query\QueryString();
        $eQueryString->setDefaultOperator($operator);
        $eQueryString->setQuery($keyword);

        $eQuery = new \Elastica\Query();
        $eQuery->setQuery($eQueryString);
        //$eQuery->setFrom($offset);
        //$eQuery->setLimit($limit);

        $filterAnd = new \Elastica\Filter\BoolAnd;


        if(isset($criteria['common_name'])) {
            $filterAnd->addFilter($this->newTerms('common_name', $criteria['common_name']));
        }

        if(is_array($dataset) && !empty($dataset)) {
            $ids = new \Elastica\Filter\Ids();
            $ids->setIds($dataset);
            $filterAnd->addFilter($ids);
        }

        $arrayAnd = $filterAnd;
        $arrayAnd = $arrayAnd->toArray();
        
        if(count($arrayAnd['and']) > 0)
            $eQuery->setPostFilter($filterAnd);

        $sortList = array_merge(array('_score'=>array('order'=>'desc')), $sorts);
        $eQuery->setSort($sortList);

        $index= Yii::app()->elastic->client->getIndex('gigadb');
        $type = $index->getType('sample');        

        $eResultSet = $type->search($eQuery);
        $results = $eResultSet->getResults();
        $total = $eResultSet->getTotalHits();

        $data = array();
        foreach($results as $result) {
            if($result)
                $data[] = $result->getData();
        }
        
        $re = array('data' => $data, 'total' => $total);
        //Yii::log(print_r($re, true), 'debug');
        return $re;
    }
}
