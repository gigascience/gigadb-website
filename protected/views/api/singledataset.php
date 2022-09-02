<?php
header("Content-Type: text/xml");
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$xml.="<gigadb_entry>\n";
$xml.=" <dataset id=\"$model->id\" doi=\"$model->identifier\">\n";
$submitter_id=$model->submitter->id;
$xml.="  <submitter>\n";
$submitter_first_name=$model->submitter->first_name;
$xml.="   <first_name>$submitter_first_name</first_name>\n";
$submitter_last_name=$model->submitter->last_name;
$xml.="   <last_name>$submitter_last_name</last_name>\n";
$submitter_affiliation=$model->submitter->affiliation;
$xml.="   <affiliation>$submitter_affiliation</affiliation>\n";
$submitter_username=$model->submitter->username;
$xml.="   <username>$submitter_username</username>\n";
$submitter_email=$model->submitter->email;
$xml.="   <email>$submitter_email</email>\n";
$xml.="  </submitter>\n";
//title,description,
$title = strip_tags($model->title);
$xml.="  <title>$title</title>\n";
$model->description=  str_replace("<br>","<br />", $model->description);
$model->description= htmlspecialchars($model->description, ENT_XML1, 'UTF-8');
$xml.="  <description> $model->description</description>\n";
//author
$xml.="  <authors>\n";
$authors=$model->authors;
usort($authors, function($a, $b){
    return $a['id'] - $b['id'];
});
foreach ($authors as $author) {
    
  $xml.="   <author>\n";  
  $xml.="    <firstname>$author->first_name</firstname>\n";
  $xml.="    <middlename>$author->middle_name</middlename>\n"; 
  $xml.="    <surname>$author->surname</surname>\n";
  $xml.="    <orcid>$author->orcid</orcid>\n";
  $xml.="   </author>\n";
    
    
}
$xml.="  </authors>\n";
//data_types
//$xml.="";
$xml.="  <data_types>\n";
$dataset_types=$model->datasetTypes;
foreach($dataset_types as $dataset_type) {
    
    $dataset_type_id=DatasetType::model()->findByAttributes(array(
        'type_id'=>$dataset_type->id,
        'dataset_id'=>$model->id,
    ));
    $xml.="   <dataset_type>\n";
    $xml.="    <type_name>$dataset_type->name</type_name>\n";
    $xml.="    <type_id>$dataset_type->id</type_id>\n";
    $xml.="   </dataset_type>\n";   
}
$xml.="  </data_types>\n";
//image
$image=$model->image;
$xml.="  <image>\n";
$xml.="   <image_filename>$image->location</image_filename>\n";
$xml.="   <tag>$image->tag</tag>\n";
$xml.="   <license>$image->license</license>\n";
$xml.="   <source>$image->source</source>\n";
$xml.="   <credit>$image->photographer</credit>\n";
$xml.="  </image>\n";
//size, ftp, date
$xml.="  <dataset_size units=\"bytes\">$model->dataset_size</dataset_size>\n";
$xml.="  <ftp_site>$model->ftp_site</ftp_site>\n";
$xml.="  <publication date=\"$model->publication_date\">\n";
$xml.="   <publisher name=\"GigaScience database\"/>\n";
$xml.="   <modification_date>$model->modification_date</modification_date>\n";
$xml.="   <fair_use date=\"$model->fairnuse\"/>\n";
$xml.="  </publication>\n";
//links
$xml.="  <links>\n";
$xml.="   <external_links>\n";
$external_links=$model->externalLinks;
if(isset($external_links)){
foreach($external_links as $external_link)
{
    $external_link_type=  ExternalLinkType::model()->findByAttributes(array('id'=>$external_link->external_link_type_id));
    $xml.="    <external_link type=\"$external_link_type->name\">$external_link->url</external_link>\n";
    
}
}
$xml.="   </external_links>\n";
$xml.="   <project_links>\n";
$project_links=$model->projects;
if(isset($project_links)){
foreach($project_links as $project){
    $dataset_project=  DatasetProject::model()->findByAttributes(array('project_id'=>$project->id));
    $xml.="    <project_link>\n";
    $xml.="     <project_name>$project->name</project_name>\n";
    $xml.="     <project_url>$project->url</project_url>\n";
    $xml.="    </project_link>\n";    
}
}
$xml.="   </project_links>\n";
$xml.="   <internal_links>\n";
$internal_links=$model->relations;
if(isset($internal_links)){
foreach($internal_links as $relation)
{
    $relationship=  Relationship::model()->findByAttributes(array('id'=>$relation->relationship_id));
    $xml.="    <related_DOI relationship=\"$relationship->name\">$relation->related_doi</related_DOI>\n";
}
}
$xml.="   </internal_links>\n";
$xml.="   <manuscript_links>\n";
$manuscripts=$model->manuscripts;
if(isset($manuscripts)){
foreach($manuscripts as $manuscript){
    
    $xml.="    <manuscript_link>\n";
    $xml.="     <manuscript_DOI>$manuscript->identifier</manuscript_DOI>\n";
    $xml.="     <manuscript_pmid>$manuscript->pmid</manuscript_pmid>\n";
    $xml.="    </manuscript_link>\n";
    
}
}
$xml.="   </manuscript_links>\n";
$xml.="   <alternative_identifiers>\n";
$alternative_identifiers=$model->links;
if(isset($alternative_identifiers)){
foreach($alternative_identifiers as $link){
    $linkname=explode(":", $link->link);
    $name=$linkname[0];
    $modelurl = Prefix::model()->find("lower(prefix) = :p", array(':p'=>strtolower($name)));  
    if(isset($modelurl))
    {    
    $xml.="    <alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$modelurl->url$linkname[1]</alternative_identifier>\n";
    }
    else
    {
    $xml.="    <alternative_identifier is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$linkname[1]</alternative_identifier>\n";    
    }
}
}
$xml.="   </alternative_identifiers>\n";
$xml.="   <funding_links>\n";
$dataset_funders=$model->datasetFunders;
if(isset($dataset_funders)){
foreach($dataset_funders as $dataset_funder){
    $xml.="    <grant>\n";
    $funder=Funder::model()->findByAttributes(array('id'=>$dataset_funder->funder_id));
    $xml.="    <funder_name>$funder->primary_name_display</funder_name>\n";
    $xml.="    <fundref_url>$funder->uri</fundref_url>\n";
    $xml.="    <award>$dataset_funder->grant_award</award>\n";
    $xml.="    <comment>$dataset_funder->comments</comment>\n";
    $xml.="    </grant>\n";
}
}
$xml.="   </funding_links>\n";
$xml.="  </links>\n";
//dataset attribute
$xml.="  <ds_attributes>\n";
$dataset_attributes=$model->datasetAttributes;
if(isset($dataset_attributes)){
foreach($dataset_attributes as $dataset_attribute)
{
    if(isset($dataset_attribute->value) && $dataset_attribute->value!=""){
    $xml.="   <attribute>\n";
    $datasetattribute=Attribute::model()->findByAttributes(array('id'=>$dataset_attribute->attribute_id));
    if(isset($datasetattribute)){
    $xml.="    <key>$datasetattribute->attribute_name</key>\n";
    }else{
    $xml.="    <key></key>\n";    
    }
    $xml.="    <value>$dataset_attribute->value</value>\n";
    $dataset_unit= Unit::model()->findByAttributes(array('id'=>$dataset_attribute->units_id));
    if(isset($dataset_unit)){
    $xml.="    <unit id=\"$dataset_unit->id\"></unit>\n";}
    else{
    $xml.="    <unit></unit>\n";    
    }
    $xml.="   </attribute>\n";
    }
    
}
}
$xml.="  </ds_attributes>\n";
$xml.=" </dataset>\n";
//samples
$xml.=" <samples>\n";
$samples=$model->samples;
foreach($samples as $sample){
    $xml.="  <sample submission_date=\"$sample->submission_date\" id=\"$sample->id\">\n";
    $xml.="   <name>$sample->name</name>\n";
    $species=$sample->species;
    $xml.="   <species>\n";
    $xml.="    <tax_id>$species->tax_id</tax_id>\n";
    $xml.="    <common_name>$species->common_name</common_name>\n";
    $xml.="    <genbank_name>$species->genbank_name</genbank_name>\n";
    $xml.="    <scientific_name>$species->scientific_name</scientific_name>\n";
    $xml.="    <eol_link>$species->eol_link</eol_link>\n";
    $xml.="   </species>\n";
    $xml.="   <sampling_protocol>$sample->sampling_protocol</sampling_protocol>\n";
    $xml.="   <consent_doc>$sample->consent_document</consent_doc>\n";
    $xml.="   <contact_author>\n";
    $xml.="    <name>$sample->contact_author_name</name>\n";
    $xml.="    <email>$sample->contact_author_email</email>\n";
    $xml.="   </contact_author>\n";
    
    $relsamples=$sample->sampleRels;
    $xml.="   <related_samples>\n";
    foreach($relsamples as $relsample )
    {
        $sample_temp=Sample::model()->findByAttributes(array('id'=>$relsample->related_sample_id));
        $sample_rel=  Relationship::model()->findByAttributes(array('id'=>$relsample->relationship_id));
        $xml.="    <related_sample relationship_type=\"$sample_rel->name\">$sample_temp->name</related_sample>\n";
    }
    $xml.="   </related_samples>\n";
    
    $xml.="   <sample_attributes>\n";
    $sa_attributes=  SampleAttribute::model()->findAllByAttributes(array('sample_id'=>$sample->id));
    foreach($sa_attributes as $sa_attribute){
        $saattribute=  Attribute::model()->findByAttributes(array('id'=>$sa_attribute->attribute_id));
        $xml.="    <attribute>\n";
        $xml.="     <key>$saattribute->attribute_name</key>\n";
        $xml.="     <value>$sa_attribute->value</value>\n";
        $sample_unit=  Unit::model()->findByAttributes(array('id'=>$sa_attribute->unit_id));
        if(isset($sample_unit)){
        $xml.="     <unit id=\"$sa_attribute->unit_id\">$sample_unit->name</unit>\n";}
        else{
        $xml.="     <unit id=\"$sa_attribute->unit_id\"></unit>\n";}
        $xml.="    </attribute>\n";
    }
    $xml.="   </sample_attributes>\n";
    
    
  
    $xml.="  </sample>\n";
    
    
    
    }
$xml.=" </samples>\n";
//experiment
$xml.=" <experiments>\n";
$xml.=" </experiments>\n";
//file
$files=$model->files;
$xml.=" <files>\n";
foreach($files as $file){
$xml.="  <file id=\"$file->id\" index4blast=\"$file->index4blast\" download_count=\"$file->download_count\" >\n";
$xml.="   <name>$file->name</name>\n";
$xml.="   <location>$file->location</location>\n";
$fdescription=preg_replace('/[<>]/', '', $file->description);
$xml.="   <description>$fdescription</description>\n";
$xml.="   <extension>$file->extension</extension>\n";
$xml.="   <size units=\"bytes\">$file->size</size>\n";
$xml.="   <release_date>$file->date_stamp</release_date>\n";
$file_type= FileType::model()->findByAttributes(array('id'=>$file->type_id));
$xml.="   <type id=\"$file->type_id\">$file_type->name</type>\n";
$file_format= FileFormat::model()->findByAttributes(array('id'=>$file->format_id));
$xml.="   <format id=\"$file->format_id\">$file_format->name</format>\n";
$xml.="   <linked_samples>\n";
$filesamples=$file->fileSamples;
foreach($filesamples as $filesample)
{
    $fi_sample=  Sample::model()->findByAttributes(array('id'=>$filesample->sample_id));
    if(isset($fi_sample)){
    $xml.="    <linked_sample sample_id=\"$filesample->sample_id\">$fi_sample->name </linked_sample>\n";}
    
}
$xml.="   </linked_samples>\n";
$xml.="   <file_attributes>\n";
$fileattributes=$file->fileAttributes;
foreach($fileattributes as $fileattribute){
    $xml.="   <attribute>\n";
    $file_att=  Attribute::model()->findByAttributes(array('id'=>$fileattribute->attribute_id));
    $xml.="    <key>$file_att->attribute_name</key>\n";
    $xml.="    <value>$fileattribute->value</value>\n";
    $file_unit=  Unit::model()->findByAttributes(array('id'=>$fileattribute->unit_id));
    if(isset($file_unit)){
    $xml.="    <unit id=\"$file_unit->id\">$file_unit->name</unit>\n";}
    else{
    $xml.="    <unit></unit>\n";    
    }
        
    $xml.="   </attribute>\n";
    
}
$xml.="   </file_attributes>\n";
$xml.="   <related_file></related_file>\n";
$xml.="  </file>\n";
}
$xml.=" </files>\n";
$xml.="</gigadb_entry>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();
