<?php
class DatabaseSearch extends CApplicationComponent {

	public function findFile($keyword,$filetypes = array(),$formats = array(), $size = array()) {
		$command = Yii::app()->db->createCommand();
		$command->select = "f.id, f.dataset_id, fs.sample_id";
		$command->from = "file f";
		$command->join = "
			left join file_sample fs on f.id = fs.file_id 
			left join dataset d on d.id = f.dataset_id
			left join file_attributes fa on f.id = fa.file_id 
			left join attribute a on a.id = fa.attribute_id 
		";

		$command->where(array('like', 'lower(f.name)', '%'.$keyword.'%'));
		$command->orWhere(array('like', 'lower(f.description)', '%'.$keyword.'%'));
		$command->orWhere(array('like', 'lower(a.attribute_name)', '%'.$keyword.'%'));
		$command->orWhere(array('like', 'lower(fa.value)', '%'.$keyword.'%'));

		if($filetypes)
			$command->andWhere(array('in', 'type_id', $filetypes));
		if($formats)
			$command->andWhere(array('in', 'format_id', $formats));
		
		if($size['min'] != 0 && $size['max']!=0) {
			$command->andWhere("size >= :s and size <= :m", array(':s'=>$size['min'], ':m'=>$size['max']));
		}
		elseif($size['min'] != 0)
			$command->andWhere("size >= :s", array(':s'=>$size['min']));
		elseif($size['max'] != 0)
			$command->andWhere("size <= :s", array(':s'=>$size['max']));

		$command->andWhere("d.upload_status = 'Published'", array());
		return $command->queryAll();
	}

	public function findSample($keyword, $ids = array(), $names = array()) {
		
	    $command = Yii::app()->db->createCommand();
	    $command->selectDistinct("s.id, ds.dataset_id");
	    $command->from = "sample s";
	    $command->join = "
	    	left join dataset_sample ds on ds.sample_id = s.id 
	    	left join species sp on sp.id = s.species_id  
	    	left join dataset d on d.id = ds.dataset_id
	    	left join sample_attribute sa on sa.sample_id = s.id 
	    	left join attribute a on sa.attribute_id = a.id 
	    ";

	    $command->where(array('like', 'lower(s.name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(s.consent_document)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(s.contact_author_name)', '%'.$keyword.'%'));
	    //$command->orWhere(array('like', 's.contact_author_email', '%'.$keyword.'%'));
	    //$command->orWhere(array('like', 's.sampling_protocol', '%'.$keyword.'%'));
	    //$command->orWhere(array('like', 'sattrs.definition', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(sp.common_name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(sp.genbank_name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(sp.scientific_name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(sp.scientific_name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(a.attribute_name)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(sa.value)', '%'.$keyword.'%'));


	    if($ids)
	    	$command->orWhere(array('in', 's.id', $ids));
	    if($names)
	    	$command->andWhere(array('in', 'sp.id', $names));
	    $command->andWhere("d.upload_status = 'Published'", array());

	    return $command->queryAll();

	}

	public function findDataset($keyword, $author_id = '', $ids = array(), $types = array(), $projects = array(), $links = array(), $pubs = array())
	{
		
		$command = Yii::app()->db->createCommand();
		$command->selectDistinct("d.id");
		$command->from = "dataset d";
		$command->join = "
			left join (select dataset_id, string_agg(p.name, ',') as names from dataset_project dp, project p where dp.project_id = p.id group by dataset_id) dprojects on d.id = dprojects.dataset_id 
		    left join (select dataset_id, string_agg(a.surname||', '||substring(a.first_name,1,1)||', '||substring(a.middle_name,1,1), ';') as author_names from dataset_author da, author a where da.author_id = a.id group by dataset_id) dauthors on d.id = dauthors.dataset_id 
   		    left join (select dataset_id, string_agg(a.surname || ' '||a.first_name||' ' || a.middle_name, ';') as author_names from dataset_author da, author a where da.author_id = a.id group by dataset_id) dnames on d.id = dnames.dataset_id 
		    left join manuscript m on d.id = m.dataset_id 
		    left join external_link el on el.dataset_id = d.id 
		    left join dataset_project dp on dp.dataset_id = d.id 
		    left join dataset_author da on da.dataset_id = d.id 
		    left join dataset_type dt on dt.dataset_id = d.id 
		    left join dataset_funder df on df.dataset_id = d.id 
		    left join funder_name fn on fn.id = df.funder_id 

		";

		$command->where(array('like', 'd.identifier', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(d.title)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(dauthors.author_names)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(dnames.author_names)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(d.description)', '%'.$keyword.'%'));
	    //$command->orWhere(array('like', 'd.ftp_site', '%'.$keyword.'%'));

	    $command->orWhere(array('like', 'lower(dprojects.names)', '%'.$keyword.'%'));	    
	    $command->orWhere(array('like', 'm.identifier', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'cast(m.pmid as varchar)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(df.grant_award)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(df.comments)', '%'.$keyword.'%'));
	    $command->orWhere(array('like', 'lower(fn.primary_name_display)', '%'.$keyword.'%'));

	    if($ids)
	    	$command->orWhere(array('in', 'd.id', $ids));
	    if($types)
	    	$command->andWhere(array('in', 'dt.type_id', $types));
	    if($projects)
	    	$command->andWhere(array('in','dp.project_id', $projects));
	    if($links)
	    	$command->andWhere(array('in', 'el.external_link_type_id', $links));

	    if($pubs['start'] && $pubs['end']) {
	    	$command->andWhere("d.publication_date >= :d and d.publication_date <= :e", array(':d'=>$pubs['start'], ':e'=>$pubs['end']));
	    }
	    elseif($pubs['start'])
	    	$command->andWhere("d.publication_date >= :d", array(':d'=>$pubs['start']));
	    elseif($pubs['end'])
	    	$command->andWhere("d.publication_date <= :d", array(':d'=>$pubs['end']));

	    if($author_id)
	    	$command->andWhere("da.author_id = :aid", array(':aid'=>$author_id));

	    $command->andWhere("d.upload_status = 'Published'");
	    return $command->queryAll();
	}

	public function getListByKey($values, $key = 'id') {
		$l = array();
		foreach($values as $v) {
			if(isset($v[$key]))
				$l[] = $v[$key];
		}
		return array_values(array_unique($l));
	}

	public function convert_size($size, $unit) {
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


	public function search($criteria) {
		$files = $this->findFile($criteria['keyword'], $criteria['filetypes'], $criteria['formats'], $criteria['size']);
		$file_ids = $this->getListByKey($files);

		$extra_samples = $this->getListByKey($files, 'sample_id');
		$file_datasets = $this->getListByKey($files, 'dataset_id');

		$samples = $this->findSample($criteria['keyword'], $extra_samples, $criteria['names']);
		$sample_ids = $this->getListByKey($samples);
		$sample_datasets = $this->getListByKey($samples, 'dataset_id');

		$display = $criteria['display'];				
		
		$extra_datasets = array_unique(array_merge($file_datasets, $sample_datasets));
		$datasets = $this->findDataset($criteria['keyword'], $criteria['author_id'], $extra_datasets, $criteria['types'], $criteria['projects'], $criteria['links'], $criteria['pubs']);
		$dataset_ids = $this->getListByKey($datasets);		
		
		if(!in_array('dataset', $display)) {
			
			if(in_array('file', $display) && !in_array('sample', $display))
				$dataset_ids = $file_datasets;

			if(!in_array('sample', $display) && in_array('sample', $display))
				$dataset_ids = $extra_datasets;
		}

		return array('files'=>$file_ids,
					'samples'=>$sample_ids,
					'datasets'=>$dataset_ids
				);

	}

	public function searchByKey($keyword, $page = 1) {
		if(!$_GET['keyword']) {
            Yii::app()->user->setFlash('keyword','Keyword can not be blank');
            $this->redirect(array("/site/index"));
        }
        $limit = 10;
        $keyword = $_GET['keyword'];

        $model = new SearchForm;

        $criteria = array();
        $criteria['keyword'] = strtolower($keyword);
        $model->keyword = $keyword;

        $params = array('type','dataset_type' , 'author_id','project' , 'file_type' ,
                'file_format' , 'pubdate_from' , 'pubdate_to', 'common_name'
                , 'size_from' , 'size_to' , 'exclude' , 'external_link_type' ,
                'size_from_unit' , 'size_to_unit');

        $dates = array('pubdate_from', 'pubdate_to');
        foreach($_GET as $key => $value){
            if(in_array($key , $params) && $value){ 
            	$model->$key = $value;
            	if($key == "pubdate_from" || $key == "pubdate_to")
            		$model->$key = strftime("%Y-%m-%d", strtotime($value));     
            }
        }

        $criteria['filetypes'] = $model->file_type;
        $criteria['formats'] = $model->file_format;
        $criteria['size'] = array('min'=>0, 'max'=>0);

        if($model->size_from)
            $criteria['size']['min'] = $this->convert_size($model->size_from, $model->size_from_unit);

        if($model->size_to)
            $criteria['size']['max'] = $this->convert_size($model->size_to, $model->size_to_unit);

        $criteria['types'] = $model->dataset_type;
        $criteria['pubs'] = array('start'=>$model->pubdate_from, 'end'=>$model->pubdate_to);
        $criteria['links'] = $model->external_link_type;
        $criteria['projects'] = $model->project;

        $criteria['names'] = $model->common_name;
        $criteria['author_id'] = $model->author_id;

        $display = array('dataset', 'sample', 'file');
        if($model->type)
            $display = $model->type;

        $criteria['display'] = $display;

        $model->criteria = CJSON::encode($model->attributes, true);

        $result = $this->search($criteria);
        $model->query_result = CJSON::encode($result);
        
        //Yii::log(print_r($result, true), 'debug');
        $total_page = ceil(count($result['datasets'])/$limit);

        $list_dataset_types = Dataset::getTypeList($result['datasets']);
        $list_projects = Dataset::getProjectList($result['datasets']);
        $list_ext_types = Dataset::getExtLinkList($result['datasets']);

        $list_common_names = Sample::getCommonList($result['samples']);
        $list_formats = File::getFormatList($result['files']);
        $list_filetypes = File::getTypeList($result['files']);

        

        return  array(
                    'datasets' => array('data'=>$result['datasets'], 'total'=>count($result['datasets'])), 
                    'samples'=> array('data'=> $result['samples'], 'total' => count($result['samples'])),
                    'files'=> array('data'=> $result['files'], 'total' => count($result['files'])),
                    'model'=>$model,                   
                    'list_dataset_types'=>$list_dataset_types,
                    'list_projects'=>$list_projects,
                    'list_ext_types'=>$list_ext_types,
                    'list_common_names'=>$list_common_names,
                    'list_formats'=>$list_formats,
                    'list_filetypes'=>$list_filetypes,
                    'display' => $display,
                    'total_page'=>$total_page, 
                    'page'=>1,
                    'limit'=> 10,                
                    );
	}
}