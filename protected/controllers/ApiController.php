<?php

class ApiController extends Controller
{
    // Members
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	
        public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('Dataset','File' , 'Sample','Search','Dump'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
        
        public function actionDump()
        {
            $status='Published';
            $datasets = Dataset::model()-> findAllByAttributes(array('upload_status'=>$status));
            set_time_limit(0);
            $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
foreach($datasets as $model)
{
$xml.="<gigadb_entry>";
$xml.="<dataset id=\"$model->id\" doi=\"$model->identifier\">";
$submitter_id=$model->submitter->id;
$xml.="<submitter>";
$submitter_first_name=$model->submitter->first_name;
$xml.="<first_name>$submitter_first_name</first_name>";
$submitter_last_name=$model->submitter->last_name;
$xml.="<last_name>$submitter_last_name</last_name>";
$submitter_affiliation=$model->submitter->affiliation;
$xml.="<affiliation>$submitter_affiliation</affiliation>";
$submitter_username=$model->submitter->username;
$xml.="<username>$submitter_username</username>";
$submitter_email=$model->submitter->email;
$xml.="<email>$submitter_email</email>";
$xml.="</submitter>";
//title,description,
$xml.="<title>$model->title </title>";
$model->description=  str_replace("<br>","<br />", $model->description);
$model->description= htmlspecialchars($model->description, ENT_XML1, 'UTF-8');
$xml.="<description> $model->description</description>";
//author
$xml.="<authors>";
$authors=$model->authors;
usort($authors, function($a, $b){
    return $a['id'] - $b['id'];
});
foreach ($authors as $author) {
    
  $xml.="<author>";  
  $xml.="<firstname>$author->first_name</firstname>";
  $xml.="<middlename>$author->middle_name</middlename>"; 
  $xml.="<surname>$author->surname</surname>";
  $xml.="<orcid>$author->orcid</orcid>";
  $xml.="</author>";
    
    
}
$xml.="</authors>";
//data_types
//$xml.="";
$xml.="<data_types>";
$dataset_types=$model->datasetTypes;
foreach($dataset_types as $dataset_type) {
    
    $dataset_type_id=DatasetType::model()->findByAttributes(array(
        'type_id'=>$dataset_type->id,
        'dataset_id'=>$model->id,
    ));
    $xml.="<dataset_type>";
    $xml.="<type_name>$dataset_type->name</type_name>";
    $xml.="<type_id>$dataset_type->id</type_id>";
    $xml.="</dataset_type>";   
}
$xml.="</data_types>";
//image
$image=$model->image;
$xml.="<image>";
$xml.="<image_filename>$image->location</image_filename>";
$xml.="<tag>$image->tag</tag>";
$xml.="<license>$image->license</license>";
$xml.="<source>$image->source</source>";
$xml.="<credit>$image->photographer</credit>";
$xml.="</image>";
//size, ftp, date
$xml.="<dataset_size units=\"bytes\">$model->dataset_size</dataset_size>";
$xml.="<ftp_site>$model->ftp_site</ftp_site>";
$xml.="<publication date=\"$model->publication_date\">";
$xml.="<publisher name=\"GigaScience database\"/>";
$xml.="<modification_date>$model->modification_date</modification_date>";
$xml.="<fair_use date=\"$model->fairnuse\"/>";
$xml.="</publication>";
//links
$xml.="<links>";
$xml.="<external_links>";
$external_links=$model->externalLinks;
if(isset($external_links)){
foreach($external_links as $external_link)
{
    $external_link_type=  ExternalLinkType::model()->findByAttributes(array('id'=>$external_link->external_link_type_id));
    $xml.="<external_link type=\"$external_link_type->name\">$external_link->url</external_link>";
    
}
}
$xml.="</external_links>";
$xml.="<project_links>";
$project_links=$model->projects;
if(isset($project_links)){
foreach($project_links as $project){
    $dataset_project=  DatasetProject::model()->findByAttributes(array('project_id'=>$project->id));
    $xml.="<project_link>";
    $xml.="<project_name>$project->name</project_name>";
    $xml.="<project_url>$project->url</project_url>";
    $xml.="</project_link>";    
}
}
$xml.="</project_links>";
$xml.="<internal_links>";
$internal_links=$model->relations;
if(isset($internal_links)){
foreach($internal_links as $relation)
{
    $relationship=  Relationship::model()->findByAttributes(array('id'=>$relation->relationship_id));
    $xml.="<related_DOI relationship=\"$relationship->name\">$relation->related_doi</related_DOI>";
}
}
$xml.="</internal_links>";
$xml.="<manuscript_links>";
$manuscripts=$model->manuscripts;
if(isset($manuscripts)){
foreach($manuscripts as $manuscript){
    
    $xml.="<manuscript_link>";
    $xml.="<manuscript_DOI>$manuscript->identifier</manuscript_DOI>";
    $xml.="<manuscript_pmid>$manuscript->pmid</manuscript_pmid>";
    $xml.="</manuscript_link>";
    
}
}
$xml.="</manuscript_links>";
$xml.="<alternative_identifiers>";
$alternative_identifiers=$model->links;
if(isset($alternative_identifiers)){
foreach($alternative_identifiers as $link){
    $linkname=explode(":", $link->link);
    $xml.="<alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$link->link</alternative_identifier>";
}
}
$xml.="</alternative_identifiers>";
$xml.="<funding_links>";
$dataset_funders=$model->datasetFunders;
if(isset($dataset_funders)){
foreach($dataset_funders as $dataset_funder){
    $xml.="<grant>";
    $funder=Funder::model()->findByAttributes(array('id'=>$dataset_funder->funder_id));
    $xml.="<funder_name>$funder->primary_name_display</funder_name>";
    $xml.="<award>$dataset_funder->grant_award</award>";
    $xml.="<comment>$dataset_funder->comments</comment>";
    $xml.="</grant>";
}
}
$xml.="</funding_links>";
$xml.="</links>";
//dataset attribute
$xml.="<ds_attributes>";
$dataset_attributes=$model->datasetAttributes;
if(isset($dataset_attributes)){
foreach($dataset_attributes as $dataset_attribute)
{
    if(isset($dataset_attribute->value) && $dataset_attribute->value!=""){
    $xml.="<attribute>";
    $datasetattribute=Attribute::model()->findByAttributes(array('id'=>$dataset_attribute->attribute_id));
    if(isset($datasetattribute)){
    $xml.="<key>$datasetattribute->attribute_name</key>";
    }else{
    $xml.="<key></key>";    
    }
    $xml.="<value>$dataset_attribute->value</value>";
    $dataset_unit= Unit::model()->findByAttributes(array('id'=>$dataset_attribute->units_id));
    if(isset($dataset_unit)){
    $xml.="<unit id=\"$dataset_unit->id\"></unit>";}
    else{
    $xml.="<unit></unit>";    
    }
    $xml.="</attribute>";
    }
    
}
}
$xml.="</ds_attributes>";
$xml.="</dataset>";
//samples
$xml.="<samples>";
$samples=$model->samples;
foreach($samples as $sample){
    $xml.="<sample submission_date=\"$sample->submission_date\" id=\"$sample->id\">";
    $xml.="<name>$sample->name</name>";
    $species=$sample->species;
    $xml.="<species>";
    $xml.="<tax_id>$species->tax_id</tax_id>";
    $xml.="<common_name>$species->common_name</common_name>";
    $xml.="<genbank_name>$species->genbank_name</genbank_name>";
    $xml.="<scientific_name>$species->scientific_name</scientific_name>";
    $xml.="<eol_link>$species->eol_link</eol_link>";
    $xml.="</species>";
    $xml.="<sampling_protocol>$sample->sampling_protocol</sampling_protocol>";
    $xml.="<consent_doc>$sample->consent_document</consent_doc>";
    $xml.="<contact_author>";
    $xml.="<name>$sample->contact_author_name</name>";
    $xml.="<email>$sample->contact_author_email</email>";
    $xml.="</contact_author>";
    
    $relsamples=$sample->sampleRels;
    $xml.="<related_samples>";
    foreach($relsamples as $relsample )
    {
        $sample_temp=Sample::model()->findByAttributes(array('id'=>$relsample->related_sample_id));
        $sample_rel=  Relationship::model()->findByAttributes(array('id'=>$relsample->relationship_id));
        $xml.="<related_sample relationship_type=\"$sample_rel->name\">$sample_temp->name</related_sample>";
    }
    $xml.="</related_samples>";
    
    $xml.="<sample_attributes>";
    $sa_attributes=  SampleAttribute::model()->findAllByAttributes(array('sample_id'=>$sample->id));
    foreach($sa_attributes as $sa_attribute){
        $saattribute=  Attribute::model()->findByAttributes(array('id'=>$sa_attribute->attribute_id));
        $xml.="<attribute>";
        $xml.="<key>$saattribute->attribute_name</key>";
        $xml.="<value>$sa_attribute->value</value>";
        $sample_unit=  Unit::model()->findByAttributes(array('id'=>$sa_attribute->unit_id));
        if(isset($sample_unit)){
        $xml.="<unit id=\"$sa_attribute->unit_id\">$sample_unit->name</unit>";}
        else{
        $xml.="<unit id=\"$sa_attribute->unit_id\"></unit>";}
        $xml.="</attribute>";
    }
    $xml.="</sample_attributes>";
    
    
  
    $xml.="</sample>";
    
    
    
    }
$xml.="</samples>";
//experiment
$xml.="<experiments>";
$xml.="</experiments>";
//file
$files=$model->files;
$xml.="<files>";
foreach($files as $file){
$xml.="<file id=\"$file->id\" index4blast=\"$file->index4blast\" download_count=\"$file->download_count\" >";
$xml.="<name>$file->name</name>";
$xml.="<location>$file->location</location>";
$xml.="<description>$file->description</description>";
$xml.="<extension>$file->extension</extension>";
$xml.="<size units=\"bytes\">$file->size</size>";
$xml.="<release_date>$file->date_stamp</release_date>";
$file_type= FileType::model()->findByAttributes(array('id'=>$file->type_id));
$xml.="<type id=\"$file->type_id\">$file_type->name</type>";
$file_format= FileFormat::model()->findByAttributes(array('id'=>$file->format_id));
$xml.="<format id=\"$file->format_id\">$file_format->name</format>";
$xml.="<linked_samples>";
$filesamples=$file->fileSamples;
foreach($filesamples as $filesample)
{
    $fi_sample=  Sample::model()->findByAttributes(array('id'=>$filesample->sample_id));
    if(isset($fi_sample)){
    $xml.="<linked_sample sample_id=\"$filesample->sample_id\">$fi_sample->name </linked_sample>";}
    
}
$xml.="</linked_samples>";
/*
$xml.="<file_attributes>";
$fileattributes=$file->fileAttributes;
foreach($fileattributes as $fileattribute){
    $xml.="<attribute>";
    $file_att=  Attribute::model()->findByAttributes(array('id'=>$fileattribute->attribute_id));
    $xml.="<key>$file_att->name</key>";
    $xml.="<value>$fileattribute->value</value>";
    $file_unit=  Unit::model()->findByAttributes(array('id'=>$fileattribute->unit_id));
    if(isset($file_unit)){
    $xml.="<unit id=\"$file_unit->id\">$file_unit->name</unit>";}
    else{
    $xml.="<unit></unit>";    
    }
        
    $xml.="</attribute>";
    
}
$xml.="</file_attributes>";*/
$xml.="<related_file></related_file>";
$xml.="</file>";
}
$xml.="</files>";
$xml.="</gigadb_entry>";
}
$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
file_put_contents("/files/database_dump.xml", $xml);
            
        }
        public function actionDataset()
	{
                $status='Published';
                $id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for dataset id <b>%s</b>',$id) );
                   }
                    if(!isset($model))
                   {
                        ob_end_clean();
                        $this->_sendResponse(404, 
                            sprintf('No items where found for dataset id <b>%s</b>',$id) );
                   }
                }
                else{
                    try{
                    $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                    catch(CDbException $e)
                   {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for dataset doi <b>%s</b>',$doi) );
                   }
                   if(!isset($model))
                   {
                        ob_end_clean(); 
                       $this->_sendResponse(404, 
                            sprintf('No items where found for dataset doi <b>%s</b>',$doi) );
                   }
                   
                }
                
                ob_end_clean();
                $this->renderPartial('singledataset',array(
			'model'=>$model,
		));
	}
        
        public function actionFile()
	{       
                $status='Published';
		$id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   
                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for file id <b>%s</b>',$id) );
                   }
                    if(!isset($model))
                   {
                         ob_end_clean();
                        $this->_sendResponse(404, 
                            sprintf('No items where found for file id <b>%s</b>',$id) );
                   }
                }
                else{
                   try{ 
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                   catch(CDbException $e)
                   {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for file doi <b>%s</b>',$doi) );
                   }
                    if(!isset($model))
                   {
                        ob_end_clean();
                        $this->_sendResponse(404, 
                            sprintf('No items where found for file doi <b>%s</b>',$doi) );
                   }
                  
                }
                
                ob_end_clean();
                $this->renderPartial('singlefile',array(
			'model'=>$model,
		));

	}
        
         public function actionSample()
	{
		$status='Published';
                $id = Yii::app()->request->getParam('id');
                $doi= Yii::app()->request->getParam('doi');
                if(isset($id))
                {
                   
                   try{
                   $model=  Dataset::model()->findByAttributes(array('id'=>$id,'upload_status'=>$status));}
                   catch(CDbException $e)
                        {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for sample id <b>%s</b>',$id) );
                        }
                     if(!isset($model))
                   {
                        ob_end_clean(); 
                        $this->_sendResponse(404, 
                            sprintf('No items where found for sample id <b>%s</b>',$id) );
                   }
                }
                else{
                   try{
                   $model=  Dataset::model()->findByAttributes(array('identifier'=>$doi,'upload_status'=>$status));}
                   catch(CDbException $e)
                        {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for sample doi <b>%s</b>',$doi) );
                        }
                    if(!isset($model))
                   {
                         ob_end_clean();
                        $this->_sendResponse(404, 
                            sprintf('No items where found for sample doi <b>%s</b>',$doi) );
                   }
                   
                }
                
                ob_end_clean();
                $this->renderPartial('singlesample',array(
			'model'=>$model,
		));
                

	}
        
        
         public function actionSearch()
	{
		$status='Published';
                ini_set('log_errors', true);
                ini_set('error_log', dirname(__FILE__).'/php_errors.log');
                $keyword = Yii::app()->request->getParam('keyword');
                $result= Yii::app()->request->getParam('result');
                $taxno= Yii::app()->request->getParam('taxno');
                $taxname= Yii::app()->request->getParam('taxname');
                $author= Yii::app()->request->getParam('author');
                $manuscript= Yii::app()->request->getParam('manuscript');
                $token= Yii::app()->request->getParam('token');
                $datasettype= Yii::app()->request->getParam('datasettype');
                $project= Yii::app()->request->getParam('project');
                $connection=Yii::app()->db;
                if(!isset($result))
                {
                  $result='dataset'; 
                }
                
                if(isset($keyword))
                {
                    if(strpos($keyword, ':'))
                    {
                        $pieces = explode(":", $keyword);
                        $sql="SELECT * from dataset where ".$pieces[0]." like '%".$pieces[1]."%'";  
                        try{
                        $models= Dataset::model()->findAllBySql($sql);}
                        catch(CDbException $e)
                        {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for keyword <b>%s</b>',$keyword) );
                        }
                     
                                            ob_end_clean();
                  
                    switch ($result) {
                        case "dataset":
                            
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":
                          
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":
                            
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    }else {
                       
                        $ds = new DatabaseSearch();        
                        $data = $ds->searchByKey($keyword);
                        $datasets=[];
                        $samples=[];
                        $files=[];
                       
                        /*
                        if(isset($_GET['type']))
                        {
                            $type=$_GET['type'];
                             for($int=0;$int<count($type);$int++)
                             {
                                 if($type[$int]=='sample'){
                                    
                                 foreach($data['samples']['data'] as $sampleid)
                                 {
                                     $id = DatasetSample::model()->findByAttributes(array('sample_id'=>$sampleid));
                                     $datasets[] = $id->dataset_id;
                                 }
                                // $datasets[] = $data['samples']['data'];
                                 continue;
                                 }
                                 if($type[$int]=='file'){
                                  foreach($data['files']['data'] as $fileid)
                                 {
                                     $id = File::model()->findByAttributes(array('id'=>$fileid));
                                     $datasets[] = $id->dataset_id;
                                 }
                                 continue;
                                 }
                                 if($type[$int]=='dataset'){
                                 foreach($data['datasets']['data'] as $datasetid)
                                 {
                                     
                                     $datasets[] = $datasetid;
                                 }
                                 continue;
                                 }
                                 if($type[$int] !=='sample' || $type[$int] !=='file' || $type[$int] !=='dataset'){
                                 ob_end_clean();
                                 $this->_sendResponse(404, 
                                 sprintf('Parameter type[] is wrong <b>%s</b>',$type[$int]) );
                                 }
                             }
                        }*/
                        foreach($data['datasets']['data'] as $datasetid)
                          {
                                     
                                     $datasets[] = $datasetid;
                          }
                        
                        foreach($data['samples']['data'] as $sampleid)
                          {
                                     
                                     $samples[] = $sampleid;
                          }
                        foreach($data['files']['data'] as $fileid)
                          {
                                     
                                     $files[] = $fileid;
                          }
                        }
                        
                        if(empty($datasets)&&empty($samples)&&empty($files)){
                            
                          ob_end_clean();
                          $this->_sendResponse(404, 
                          sprintf('No items where found for keyword <b>%s</b>',$keyword) );   
                        }
                       // print_r($datasets);
                       // print_r($samples);
                      //  print_r($files);
                        
  
                    ob_end_clean();
                    
                    if(!isset($_GET['result']))
                    {
                        
                         $this->renderPartial('keyword',array(
                            'datasetids'=>$datasets,
                            'sampleids'=>$samples,
                            'fileids'=>$files));
                        
                    }
                    else{
                    switch ($result) {
                        case "dataset":
                             if(empty($datasets)){
                                 
                               
                          $this->_sendResponse(404, 
                          sprintf('No items where found for keyword <b>%s</b> in dataset, Please search in sample or file',$keyword) );    
                             }
                            $this->renderPartial('keywordalldataset',array(
                            'datasetids'=>$datasets,));
                            break;
                        case "sample":
                          if(empty($samples)){
                                 
                                
                          $this->_sendResponse(404, 
                          sprintf('No items where found for keyword <b>%s</b> in sample, Please search in dataset or file',$keyword) );    
                             }
                            
                            $this->renderPartial('keywordallsample',array(
                            'sampleids'=>$samples,));
                            break;
                        case "file":
                            
                            if(empty($files)){
                                 
                                
                          $this->_sendResponse(404, 
                          sprintf('No items where found for keyword <b>%s</b> in file, Please search in dataset or sample',$keyword) );    
                             }
                            $this->renderPartial('keywordallfile',array(
                            'fileids'=>$files,));
                            break;

                        default:
                            break;
                    }
                    }
                   /*     
                     $this->renderPartial('keyword',array(
                            'datasetids'=>$datasets,
                            'sampleids'=>$samples,
                            'fileids'=>$files));
                    */
                 
                      
                    }
               
                if(isset($taxno))
                {
                        
                    
                    $sql='select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and species.tax_id=:taxno and dataset.upload_status=:status;';
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":taxno",$taxno,PDO::PARAM_STR); 
                    $command->bindParam(":status",$status,PDO::PARAM_STR); 
                    $rows=$command->queryAll();
                    if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for taxno <b>%s</b>',$taxno) );
                      }
                    
                    
                    $dataset_ids="";
                    
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":
                            
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":
                          
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":
                            
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
               
                }
                
                if(isset($taxname))
                {
                    
                    $sql="select DISTINCT dataset.id from dataset,dataset_sample,sample,species where dataset.id=dataset_sample.dataset_id and dataset_sample.sample_id=sample.id and sample.species_id=species.id and upper(species.scientific_name)=:scientific_name and dataset.upload_status=:status;";
                    $uppertaxname=strtoupper($taxname);
                    $command=$connection->createCommand($sql);
                    $command->bindParam(":scientific_name",$uppertaxname,PDO::PARAM_STR);  
                    $command->bindParam(":status",$status,PDO::PARAM_STR); 
                    $rows=$command->queryAll();
                    if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for taxname <b>%s</b>',$taxname) );
                      }
                    
                    $dataset_ids="";
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                    catch(CDbException $e)
                    {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for taxname <b>%s</b>',$taxname) );
                    }
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                }
                if(isset($author))
                {
                    
                    $names=  explode(" ", strtoupper($author));
                    if(count($names)>2)
                    {
                      $surname=$names[0]; 
                      $middlename=$names[1];
                      $firstname=$names[2];  
                      $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and upper(author.surname)=:surname and upper(author.first_name)=:firstname and upper(author.middle_name)=:middlename and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":surname",$surname,PDO::PARAM_STR); 
                      $command->bindParam(":firstname",$firstname,PDO::PARAM_STR); 
                      $command->bindParam(":middlename",$middlename,PDO::PARAM_STR); 
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                      $rows=$command->queryAll();
                      $dataset_ids="";
                       if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for author <b>%s</b>',$surname." ".$middlename." ".$firstname) );
                      }
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                     catch(CDbException $e)
                    {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for author <b>%s</b>',$author) );
                    }
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                      
                      
                      
                      
                      
                    }
                    else{
                      $surname=$names[0];                   
                      $firstname=$names[1];  
                      $sql='select DISTINCT dataset.id from dataset,dataset_author,author where dataset.id=dataset_author.dataset_id and dataset_author.author_id=author.id and upper(author.surname)=:surname and upper(author.first_name)=:firstname and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":surname",$surname,PDO::PARAM_STR); 
                      $command->bindParam(":firstname",$firstname,PDO::PARAM_STR);
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                      $rows=$command->queryAll();
                      $dataset_ids="";
                      if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for author <b>%s</b>',$surname." ".$firstname) );
                      }
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                     catch(CDbException $e)
                    {
                             ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for author <b>%s</b>',$author) );
                    }
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
  
                    }
  
                }
                if(isset($manuscript))
                {
                    
                      $sql='select DISTINCT dataset.id from dataset,manuscript where manuscript.dataset_id=dataset.id and manuscript.identifier=:manuscript and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":manuscript",$manuscript,PDO::PARAM_STR); 
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                       
                      $rows=$command->queryAll();
                      $dataset_ids="";
                      if(count($rows)<1)
                      {
                        if(isset($token) && $token == 'doibanner')
                        {
                            ob_end_clean();
                            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
    <body>
      <img style="-webkit-user-select: none" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
    </body>
</html>';
            echo $body;
        
                         Yii::app()->end();

                         }else{
                          
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for manuscript <b>%s</b>',$manuscript) );
                         }
                      }
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                     catch(CDbException $e)
                    {
                        if(isset($token) && $token == 'doibanner')
                        {
                         ob_end_clean();
                             $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
    <body>
      <img style="-webkit-user-select: none" src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
    </body>
</html>';
            echo $body;
        
                         Yii::app()->end();

                         }else{
                             
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for manuscript <b>%s</b>',$manuscript) );
                    }
                    }
                    
                    ob_end_clean();
                    
                    if(isset($token) && $token == 'doibanner')
                    {
                          $body = '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    </head>
    <body>
      <img style="-webkit-user-select: none" src="http://media.springer.com/lw900/springer-cms/rest/v1/content/755916/data/v1">
    </body>
    </html>';
            echo $body;
        
        Yii::app()->end();
                        
                        
                        
                    }else{

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    }
                    
                }
                if(isset($datasettype))
                {
                      $uppertype=strtoupper($datasettype);
                      $sql='select DISTINCT dataset.id from dataset,dataset_type,type where dataset_type.dataset_id=dataset.id and dataset_type.type_id=type.id and upper(type.name)=:datasettype and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":datasettype",$uppertype,PDO::PARAM_STR); 
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                       
                      $rows=$command->queryAll();
                      $dataset_ids="";
                       if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for dataset type <b>%s</b>',$datasettype) );
                      }
                   
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                     catch(CDbException $e)
                    {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for dataset type <b>%s</b>',$datasettype) );
                    }
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                    
                }
                
                if(isset($project))
                {
                      $uppertype=strtoupper($project);
                      $sql='select DISTINCT dataset.id from dataset,dataset_project,project where dataset_project.dataset_id=dataset.id and dataset_project.project_id=project.id and upper(project.name)=:project and dataset.upload_status=:status;';
                      $command=$connection->createCommand($sql);
                      $command->bindParam(":project",$uppertype,PDO::PARAM_STR);   
                      $command->bindParam(":status",$status,PDO::PARAM_STR);
                      $rows=$command->queryAll();
                      $dataset_ids="";
                      if(count($rows)<1)
                      {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for project <b>%s</b>',$project) );
                      }
                    foreach($rows as $row)
                    {
                        $dataset_ids=$dataset_ids.$row['id'].",";
                    }
                    $dataset_ids=  trim($dataset_ids,',');
                    
                    try{
                    $sql1="SELECT * from dataset where id in (".$dataset_ids.")";
                    $models= Dataset::model()->findAllBySql($sql1);
                    }
                    catch(CDbException $e)
                    {
                            ob_end_clean();
                            $this->_sendResponse(404, 
                            sprintf('No items where found for project <b>%s</b>',$project) );
                    }
                    
                    ob_end_clean();

                    switch ($result) {
                        case "dataset":                         
                            $this->renderPartial('keyworddataset',array(
                            'models'=>$models,));
                            break;
                        case "sample":                         
                            $this->renderPartial('keywordsample',array(
                            'models'=>$models,));
                            break;
                        case "file":                           
                            $this->renderPartial('keywordfile',array(
                            'models'=>$models,));
                            break;

                        default:
                            break;
                    }
                    
                    
                }
                
          

	}
        
        public function actionView()
	{
		$this->render('view',array(
			'model'=>$this->loadModel(),
		));
	}
        
         private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
        {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);
        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';
            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }
            // servers don't always have a signature turned on 
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
            // this should be templated in a real-world solution
            $body = '
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
    </head>
    <body>
        <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
        <p>' . $message . '</p>
        <hr />
        <address>' . $signature . '</address>
    </body>
    </html>';
            echo $body;
        }
        Yii::app()->end();
    }
    
    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
    

        
}

