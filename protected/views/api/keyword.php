<?php if($type =='json'){
header('Content-Type: application/json');  
}else{
header("Content-Type: text/xml");
} ?>
<?php
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
?>
<?php
foreach($data as $dataset)
{ 
$model= Dataset::model()->with('authors')->findByAttributes(array(
        'id'=>$dataset));

$xml.="<gigadb_entry id=\"$model->id\" doi=\"$model->identifier\">";
$xml.="<dataset>";

$submitter_id=$model->submitter->id;
$xml.="<submitter id=\"$submitter_id\" >";
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

$submission_type= $model->excelfile?"excel":"online";
$excelfile_name=$model->excelfile?$model->excelfile:"None";
$xml.="<submission type=\"$submission_type\">";
$xml.="<excel_filename excelfile=\"$excelfile_name\" md5sum=\"$model->excelfile_md5\"/>";
$xml.="</submission>";

$xml.="<title>$model->title</title>";
$model->description=  str_replace("<br>","<br />", $model->description);
$model->description= htmlspecialchars($model->description, ENT_XML1, 'UTF-8');
$xml.="<description> $model->description</description>";

$xml.="<authors>";
$authors=$model->authors;
usort($authors, function($a, $b){
    return $a['id'] - $b['id'];
});
foreach ($authors as $author) {
    
  $xml.="<author id=\"$author->id\">";  
  $xml.="<firstname>$author->first_name</firstname>";
  $xml.="<middlename>$author->middle_name</middlename>"; 
  $xml.="<surname>$author->surname</surname>";
  $xml.="<orcid>$author->orcid</orcid>";
  $xml.="</author>";
    
    
}
$xml.="</authors>";

$xml.="<data_types>";
$dataset_types=$model->datasetTypes;

foreach($dataset_types as $dataset_type) {
    
    $dataset_type_id=DatasetType::model()->findByAttributes(array(
        'type_id'=>$dataset_type->id,
        'dataset_id'=>$model->id,
    ));
    $xml.="<dataset_type id=\"$dataset_type_id->id\">";
    $xml.="<type_name>$dataset_type->name</type_name>";
    $xml.="<type_id>$dataset_type->id</type_id>";
    $xml.="</dataset_type>";   
}

$xml.="</data_types>";

//image

$image=$model->image;
$xml.="<image id=\"$image->id\">";
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
foreach($external_links as $external_link)
{
    $external_link_type=  ExternalLinkType::model()->findByAttributes(array('id'=>$external_link->external_link_type_id));
    $xml.="<external_link id=\"$external_link->id\" type=\"$external_link_type->name\">$external_link->url</external_link>";
    
}
$xml.="</external_links>";

$xml.="<project_links>";
$project_links=$model->projects;
foreach($project_links as $project){
    $dataset_project=  DatasetProject::model()->findByAttributes(array('project_id'=>$project->id));
    $xml.="<project_link id=\"$dataset_project->id\">";
    $xml.="<project_name id=\"$project->id\">$project->name</project_name>";
    $xml.="<project_url>$project->url</project_url>";
    $xml.="</project_link>";    
}
$xml.="</project_links>";

$xml.="<internal_links>";
$internal_links=$model->relations;
if(isset($internal_links)){
foreach($internal_links as $relation)
{
    $relationship=  Relationship::model()->findByAttributes(array('id'=>$relation->relationship_id));
    $xml.="<related_DOI relationship_id=\"$relationship->id\" relationship=\"$relationship->name\">$relation->related_doi</related_DOI>";
}
}
$xml.="</internal_links>";


$xml.="<manuscript_links>";
$manuscripts=$model->manuscripts;
foreach($manuscripts as $manuscript){
    
    $xml.="<manuscript_link id=\"$manuscript->id\">";
    $xml.="<manuscript_DOI>$manuscript->identifier</manuscript_DOI>";
    $xml.="<manuscript_pmid>$manuscript->pmid</manuscript_pmid>";
    $xml.="</manuscript_link>";
    
}
$xml.="</manuscript_links>";

$xml.="<alternative_identifiers>";
$alternative_identifiers=$model->links;
foreach($alternative_identifiers as $link){
    $linkname=explode(":", $link->link);
    $xml.="<alternative_identifier id=\"$link->id\" is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$link->link</alternative_identifier>";

}
$xml.="</alternative_identifiers>";

$xml.="<funding_links>";
$dataset_funders=$model->datasetFunders;
foreach($dataset_funders as $dataset_funder){
    $xml.="<grant id=\"$dataset_funder->id\">";
    $funder=Funder::model()->findByAttributes(array('id'=>$dataset_funder->funder_id));
    $xml.="<funder_name id=\"$funder->id\">$funder->primary_name_display</funder_name>";
    $xml.="<award>$dataset_funder->grant_award</award>";
    $xml.="<comment>$dataset_funder->comments</comment>";
    $xml.="</grant>";
}
$xml.="</funding_links>";

$xml.="</links>";

$xml.="<ds_attributes>";
$dataset_attributes=$model->datasetAttributes;
if(isset($dataset_attributes)){
foreach($dataset_attributes as $dataset_attribute)
{
    if(isset($dataset_attribute->value) && $dataset_attribute->value!=""){
    $xml.="<attribute id=\"$dataset_attribute->id\">";
    $datasetattribute=Attribute::model()->findByAttributes(array('id'=>$dataset_attribute->attribute_id));
    if(isset($datasetattribute)){
    $xml.="<key id=\"$datasetattribute->id\"></key>";
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


$xml.="<samples>";
$samples=$model->samples;
foreach($samples as $sample){
    $xml.="<sample submission_date=\"$sample->submission_date\" id=\"$sample->id\">";
    $xml.="<name>$sample->name</name>";
    $xml.="<submitted_id>$sample->submitted_id</submitted_id>";
    $species=$sample->species;
    $xml.="<species species_id=\"$species->id\">";
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
        $xml.="<related_sample sample_rel_id=\"$relsample->related_sample_id\" relationship_type=\"$sample_rel->name\">$sample_temp->name</related_sample>";
    }
    $xml.="</related_samples>";
    
    $xml.="<sample_attributes>";
    $sa_attributes=  SampleAttribute::model()->findAllByAttributes(array('sample_id'=>$sample->id));
    foreach($sa_attributes as $sa_attribute){
        $saattribute=  Attribute::model()->findByAttributes(array('id'=>$sa_attribute->attribute_id));
        $xml.="<attribute id=\"$sa_attribute->id\">";
        $xml.="<key id=\"$saattribute->id\">$saattribute->attribute_name</key>";
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



$xml.="</gigadb_entry>";







}
$xml.="</gigadb_entrys>";

$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);


$output= simplexml_load_string($xml);



if($type=='xml'){
echo $output->asXML();

}

if($type=='json'){
$json=  json_encode($output);
echo $json;
}
