<?php
header('Content-Type: text/xml');
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
foreach($models as $model)
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
$title = strip_tags($model->title);
$xml.="<title>$title</title>";
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
    $modelurl = Prefix::model()->find("lower(prefix) = :p", array(':p'=>strtolower($name)));  
    if(isset($modelurl))
    {    
    $xml.="<alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$modelurl->url$linkname[1]</alternative_identifier>";
    }
    else
    {
    $xml.="<alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$linkname[1]</alternative_identifier>";    
    }
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
$xml.="</gigadb_entry>";
}
$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();