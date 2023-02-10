<?php

use CompatibilityHelper;

class DatabaseSearch extends CApplicationComponent
{
    public function findFile($keyword, $filetypes = array(), $formats = array(), $size = array())
    {
        $command = Yii::app()->db->createCommand();
        $command->select = "f.id, f.name, f.location, f.size, f.dataset_id, f.sample_id, f.file_type, f.file_format";
        $command->from = "file_finder f";
        $command->where("to_tsvector('english',f.document) @@ to_tsquery('$keyword')");

        if ($filetypes) {
            $command->andWhere(array('in', 'f.type_id', $filetypes));
        }
        if ($formats) {
            $command->andWhere(array('in', 'f.format_id', $formats));
        }

        if ($size['min'] != 0 && $size['max'] != 0) {
            $command->andWhere("f.size >= :s and f.size <= :m", array(':s' => $size['min'], ':m' => $size['max']));
        } elseif ($size['min'] != 0) {
            $command->andWhere("f.size >= :s", array(':s' => $size['min']));
        } elseif ($size['max'] != 0) {
            $command->andWhere("f.size <= :s", array(':s' => $size['max']));
        }

        $command->andWhere("f.upload_status = 'Published'", array());
        return $command->queryAll();
    }

    public function findSample($keyword, $ids = array(), $names = array())
    {

        $command = Yii::app()->db->createCommand();
        $command->selectDistinct("s.id, s.dataset_id, s.name,s.species_common_name, s.species_tax_id");
        $command->from = "sample_finder s";

        $searchQuery = "$keyword";
        if ($names) {
            $namesStr = implode(" ", $names);
            $searchQuery .= " &  $namesStr";
        }
        $command->where("to_tsvector('english',s.document) @@ to_tsquery('$searchQuery')");

        $command->andWhere("s.upload_status = 'Published'", array());

        $sampleResults = $command->queryAll();
        $resultsIds = $this->getListByKey($sampleResults);

        $extraResults = [];
        if ($ids) {
            $newIdsToAdd = array_diff($ids, $resultsIds);
            $extraResults = Yii::app()->db->createCommand()->selectDistinct("s.id, s.dataset_id, s.name,s.species_common_name, s.species_tax_id")
            ->from("sample_finder s")
            ->where(array('in', 's.id', $newIdsToAdd))
            ->queryAll();
        }

        return array_merge($sampleResults, $extraResults);
    }

    public function findDataset($keyword, $author_id = '', $ids = array(), $types = array(), $projects = array(), $links = array(), $pubs = array())
    {

        $command = Yii::app()->db->createCommand();
        $command->selectDistinct("d.id, d.shorturl, d.identifier, d.authornames, d.title, d.description");
        $command->from = "dataset_finder d";

        if (count($types) > 0) {
            return $command->join("dataset_type dt", "d.id = dt.dataset_id")
                ->join("type t", "dt.type_id=t.id")
                ->where(array('in', 't.name', $types))
                ->andWhere("d.upload_status = 'Published'")
                ->queryAll();
        }

        if ($author_id) {
            return $command->join("dataset_author da", "d.id = da.dataset_id")
                ->where('da.author_id=:id', array(':id' => $author_id))
                ->andWhere("d.upload_status = 'Published'")
                ->queryAll();
        }

        $searchQuery = "$keyword";
        if ($types) {
            $typesStr = implode(" ", $types);
            $searchQuery .= " & $typesStr";
        }
        if ($projects) {
            $projectsStr = implode(" ", $projects);
            $searchQuery .= " & $projectsStr";
        }
        if ($links) {
            $linksStr = implode(" ", $links);
            $searchQuery .= " & $linksStr";
        }

        if ($author_id) {
            $authorName = Author::model()->findByPk($author_id)->getDisplayName();
            $searchQuery .= " & $authorName";
        }
        $command->where("to_tsvector('english',d.document) @@ to_tsquery('$searchQuery')");



        if ($pubs['start'] && $pubs['end']) {
            $command->andWhere("d.publication_date >= :d and d.publication_date <= :e", array(':d' => $pubs['start'], ':e' => $pubs['end']));
        } elseif ($pubs['start']) {
            $command->andWhere("d.publication_date >= :d", array(':d' => $pubs['start']));
        } elseif ($pubs['end']) {
            $command->andWhere("d.publication_date <= :d", array(':d' => $pubs['end']));
        }

        $command->andWhere("d.upload_status = 'Published'");
        $command->order(array('d.id desc'));

        $datasetResults = $command->queryAll();
        $resultsIds = $this->getListByKey($datasetResults);

        $extraResults = [];
        if ($ids) {
            $newIdsToAdd = array_diff($ids, $resultsIds);
            $extraResults = Yii::app()->db->createCommand()->selectDistinct("d.id, d.shorturl, d.identifier, d.authornames, d.title, d.description")
                ->from("dataset_finder d")
                ->where(array('in', 'd.id', $newIdsToAdd))
                ->queryAll();
        }

        return array_merge($datasetResults, $extraResults);
    }

    public function getListByKey($values, $key = 'id')
    {
        $l = array();
        foreach ($values as $v) {
            if (isset($v[$key])) {
                $l[] = $v[$key];
            }
        }
        return array_values(array_unique($l));
    }

    public function convert_size($size, $unit)
    {
        try {
            if ($unit == 1) {
                $size *= 1024;
            } elseif ($unit == 2) {
                $size *= 1024 * 1024;
            } elseif ($unit == 3) {
                $size *= 1024 * 1024 * 1024;
            } elseif ($unit == 4) {
                $size *= 1024 * 1024 * 1024 * 1024;
            } else {
                $size = 0;
            }
            return $size;
        } catch (Exception $e) {
            return 0;
        }
    }


    public function search($criteria, $resultType = "ids")
    {
        $files = $this->findFile($criteria['keyword'], $criteria['filetypes'], $criteria['formats'], $criteria['size']);
        $file_ids = $this->getListByKey($files);

        $extra_samples = $this->getListByKey($files, 'sample_id');
        $file_datasets = $this->getListByKey($files, 'dataset_id');

        $samples = $this->findSample($criteria['keyword'], $extra_samples, $criteria['names']);
        $sample_datasets = $this->getListByKey($samples, 'dataset_id');

        $display = $criteria['display'];

        $extra_datasets = array_unique(array_merge($file_datasets, $sample_datasets));
        $datasets = $this->findDataset($criteria['keyword'], $criteria['author_id'], $extra_datasets, $criteria['types'], $criteria['projects'], $criteria['links'], $criteria['pubs']);
        $dataset_ids = $this->getListByKey($datasets);

        if (!in_array('dataset', $display)) {
            if (in_array('file', $display) && !in_array('sample', $display)) {
                $dataset_ids = $file_datasets;
            }

            if (!in_array('file', $display) && in_array('sample', $display)) {
                $dataset_ids = $extra_datasets;
            }
        }

        if ("full" === $resultType) {
            return array(
                'ids' =>  ["files" => $file_ids,"samples" => $sample_ids, "datasets" => $dataset_ids],
                'results' => ["files" => $files,"samples" => $samples, "datasets" => $datasets]
            );
        }
        return array('files' => $file_ids,
            'samples' => $sample_ids,
            'datasets' => $dataset_ids
        );
    }

    public function searchByKey($keyword, $searchType = "api")
    {

        $limit = Yii::app()->params['search_result_limit'];
        $model = new SearchForm();

        $criteria = array();

        if (true === CompatibilityHelper::str_contains($keyword, "&")) {
            $criteria['keyword'] = $keyword;
        } else {
            $criteria['keyword'] = preg_replace("/\s+/", " & ", $keyword);
        }

        $model->keyword = $criteria['keyword'];

        $params = array('type','dataset_type' , 'author_id','project' , 'file_type' ,
                'file_format' , 'pubdate_from' , 'pubdate_to', 'common_name'
                , 'size_from' , 'size_to' , 'exclude' , 'external_link_type' ,
                'size_from_unit' , 'size_to_unit');

        foreach ($_GET as $key => $value) {
            if (in_array($key, $params) && $value) {
                $model->$key = $value;
                if ($key == "pubdate_from" || $key == "pubdate_to") {
                    $model->$key = strftime("%Y-%m-%d", strtotime($value));
                }
            }
        }

        $criteria['filetypes'] = $model->file_type;
        $criteria['formats'] = $model->file_format;
        $criteria['size'] = array('min' => 0, 'max' => 0);

        if ($model->size_from) {
            $criteria['size']['min'] = $this->convert_size($model->size_from, $model->size_from_unit);
        }

        if ($model->size_to) {
            $criteria['size']['max'] = $this->convert_size($model->size_to, $model->size_to_unit);
        }

        $criteria['types'] = $model->dataset_type;
        $criteria['pubs'] = array('start' => $model->pubdate_from, 'end' => $model->pubdate_to);
        $criteria['links'] = $model->external_link_type;
        $criteria['projects'] = $model->project;

        $criteria['names'] = $model->common_name;
        $criteria['author_id'] = $model->author_id;

        $display = array('dataset', 'sample', 'file');
        if ($model->type) {
            $display = $model->type;
        }

        $criteria['display'] = $display;

        $model->criteria = CJSON::encode($model->attributes, true);

        $resultset = nil;
        if ("search" === $searchType) {
            $resultset = $this->search($criteria, "full");
            $total_page = ceil(count($resultset['ids']['datasets']) / $limit);
        } else {
            $result = $this->search($criteria);
            $total_page = ceil(count($result['datasets']) / $limit);
        }
        $model->query_result = CJSON::encode($result);


        $list_dataset_types = Dataset::getTypeList($result['datasets']);


        if ("search" === $searchType) {
            return  array(
                'datasets' => array('data' => $resultset['results']['datasets'], 'total' => count($resultset['ids']['datasets'])),
                'samples' => array('data' => $resultset['results']['samples'], 'total' => count($resultset['ids']['samples'])),
                'files' => array('data' => $resultset['results']['files'], 'total' => count($resultset['ids']['files'])),
                'model' => $model,
                'list_dataset_types' => $list_dataset_types,
                'display' => $display,
                'total_page' => $total_page,
                'page' => 1,
                'limit' => $limit,
            );
        } else {
            $list_projects = Dataset::getProjectList($result['datasets']);
            $list_ext_types = Dataset::getExtLinkList($result['datasets']);

            $list_common_names = Sample::getCommonList($result['samples']);
            $list_formats = File::getFormatList($result['files']);
            $list_filetypes = File::getTypeList($result['files']);
            return  array(
                'datasets' => array('data' => $result['datasets'], 'total' => count($result['datasets'])),
                'samples' => array('data' => $result['samples'], 'total' => count($result['samples'])),
                'files' => array('data' => $result['files'], 'total' => count($result['files'])),
                'model' => $model,
                'list_dataset_types' => $list_dataset_types,
                'list_projects' => $list_projects,
                'list_ext_types' => $list_ext_types,
                'list_common_names' => $list_common_names,
                'list_formats' => $list_formats,
                'list_filetypes' => $list_filetypes,
                'display' => $display,
                'total_page' => $total_page,
                'page' => 1,
                'limit' => $limit,
            );
        }
    }
}
