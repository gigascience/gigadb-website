<?php
ini_set('max_execution_time',300);
header('Content-Type: text/xml');
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
if(!empty($datasetids)){
 
foreach($datasetids as $datasetid)
{
$xml.='<gigadb_entry>';   
$model=  Dataset::model()->findByPk($datasetid);
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
    $name=$linkname[0];
    $modelurl = Prefix::model()->find("lower(prefix) = :p", array(':p'=>strtolower($name)));
    $xml.="<alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$modelurl->url$linkname[1]</alternative_identifier>";
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
    $xml.="<fundref_url>$funder->uri</fundref_url>";
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
$xml.='<samples>';
foreach($sampleids as $sampleid)
{
    $sample=  Sample::model()->findByPK($sampleid);
    $datasetid1;
    foreach($sample->datasets as $dataset1)
{
    $datasetid1=$dataset1->id;
    $datasetdoi=$dataset1->identifier;
}
if($datasetid1 == $datasetid)
{
    $sampleids= array_diff($sampleids, array($sampleid));
    $xml.="<sample submission_date=\"$sample->submission_date\" id=\"$sample->id\" doi=\"$datasetdoi\">";
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
}
$xml.='</samples>';
$xml.='<files>';
foreach($fileids as $fileid){
$file=  File::model()->findByPK($fileid);
$dataset2=  Dataset::model()->findByPK($file->dataset_id);
if($dataset2->id == $datasetid)
{
$key= array_search($fileid, $fileids);
unset($fileids[$key]);   
$xml.="<file id=\"$file->id\" doi=\"$dataset2->identifier\" index4blast=\"$file->index4blast\" download_count=\"$file->download_count\" >";
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

$xml.="<file_attributes>";
$fileattributes=$file->fileAttributes;
foreach($fileattributes as $fileattribute){
    $xml.="<attribute id=\"$fileattribute->id\">";
    $file_att=  Attribute::model()->findByAttributes(array('id'=>$fileattribute->attribute_id));
    $xml.="<key id=\"$file_att->id\">$file_att->name</key>";
    $xml.="<value>$fileattribute->value</value>";
    $file_unit=  Unit::model()->findByAttributes(array('id'=>$fileattribute->unit_id));
    if(isset($file_unit)){
    $xml.="<unit id=\"$file_unit->id\">$file_unit->name</unit>";}
    else{
    $xml.="<unit></unit>";    
    }
        
    $xml.="</attribute>";
    
}
$xml.="</file_attributes>";

$xml.="<related_file></related_file>";

$xml.="</file>";   
}
}
$xml.='</files>';


$xml.='</gigadb_entry>';  
}      
}  



if(!empty($sampleids)){
$xml.='<samples>'; 
foreach($sampleids as $sampleid)
{
$sample=  Sample::model()->findByPK($sampleid);
$datasetid;
foreach($sample->datasets as $dataset)
{
    $datasetid=$dataset->identifier;
}
  $xml.="<sample submission_date=\"$sample->submission_date\" id=\"$sample->id\" doi=\"$datasetid\">";
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
}


if(!empty($fileids)){
$xml.='<files>';   
foreach($fileids as $fileid){
$file=  File::model()->findByPK($fileid);
$dataset=  Dataset::model()->findByPK($file->dataset_id);
$xml.="<file id=\"$file->id\" doi=\"$dataset->identifier\" index4blast=\"$file->index4blast\" download_count=\"$file->download_count\" >";
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

$xml.="<file_attributes>";
$fileattributes=$file->fileAttributes;
foreach($fileattributes as $fileattribute){
    $xml.="<attribute id=\"$fileattribute->id\">";
    $file_att=  Attribute::model()->findByAttributes(array('id'=>$fileattribute->attribute_id));
    $xml.="<key id=\"$file_att->id\">$file_att->name</key>";
    $xml.="<value>$fileattribute->value</value>";
    $file_unit=  Unit::model()->findByAttributes(array('id'=>$fileattribute->unit_id));
    if(isset($file_unit)){
    $xml.="<unit id=\"$file_unit->id\">$file_unit->name</unit>";}
    else{
    $xml.="<unit></unit>";    
    }
        
    $xml.="</attribute>";
    
}
$xml.="</file_attributes>";

$xml.="<related_file></related_file>";

$xml.="</file>";
}   
    
$xml.='</files>';     
}
$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();