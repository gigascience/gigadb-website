<?php if($type =='json'){
header('Content-Type: application/json');  
}else{
header("Content-Type: text/xml");
} ?>
<?php 
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entry id=\"$model->id\" doi=\"$model->identifier\">";
$xml.="<dataset>";


//submitter

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


//submission type

$submission_type= $model->excelfile?"excel":"online";
$excelfile_name=$model->excelfile?$model->excelfile:"None";
$xml.="<submission type=\"$submission_type\">";
$xml.="<excel_filename excelfile=\"$excelfile_name\" md5sum=\"$model->excelfile_md5\"/>";
$xml.="</submission>";

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
    
  $xml.="<author id=\"$author->id\">";  
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
if(isset($external_links)){
foreach($external_links as $external_link)
{
    $external_link_type=  ExternalLinkType::model()->findByAttributes(array('id'=>$external_link->external_link_type_id));
    $xml.="<external_link id=\"$external_link->id\" type=\"$external_link_type->name\">$external_link->url</external_link>";
    
}
}
$xml.="</external_links>";

$xml.="<project_links>";
$project_links=$model->projects;
if(isset($project_links)){
foreach($project_links as $project){
    $dataset_project=  DatasetProject::model()->findByAttributes(array('project_id'=>$project->id));
    $xml.="<project_link id=\"$dataset_project->id\">";
    $xml.="<project_name id=\"$project->id\">$project->name</project_name>";
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
    $xml.="<related_DOI relationship_id=\"$relationship->id\" relationship=\"$relationship->name\">$relation->related_doi</related_DOI>";
}
}
$xml.="</internal_links>";


$xml.="<manuscript_links>";
$manuscripts=$model->manuscripts;
if(isset($manuscripts)){
foreach($manuscripts as $manuscript){
    
    $xml.="<manuscript_link id=\"$manuscript->id\">";
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
    $xml.="<alternative_identifier id=\"$link->id\" is_primary=\"$link->is_primary\" prefix=\"$linkname[0]\">$link->link</alternative_identifier>";

}
}
$xml.="</alternative_identifiers>";



$xml.="<funding_links>";
$dataset_funders=$model->datasetFunders;
if(isset($dataset_funders)){
foreach($dataset_funders as $dataset_funder){
    $xml.="<grant id=\"$dataset_funder->id\">";
    $funder=Funder::model()->findByAttributes(array('id'=>$dataset_funder->funder_id));
    $xml.="<funder_name id=\"$funder->id\">$funder->primary_name_display</funder_name>";
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


//samples

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
$xml.="</files>";


$xml.="</gigadb_entry>";

$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);



$output= simplexml_load_string($xml);

if($type=='xml'){
echo $output->asXML();
}

if($type=='json'){
$json=  json_encode($output);
echo $json;
}






/*
echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>-
<?php 
    echo "<Dataset>";
    echo "<".CHtml::encode($model->getAttributeLabel('identifier')).">"; 
    echo CHtml::encode($model->identifier);
    echo "</".CHtml::encode($model->getAttributeLabel('identifier')).">"; 
    
    echo "<".CHtml::encode($model->getAttributeLabel('title')).">"; 
    echo CHtml::encode($model->title);
    echo "</".CHtml::encode($model->getAttributeLabel('title')).">"; 
        
    echo "<".CHtml::encode($model->getAttributeLabel('description')).">"; 
    echo CHtml::encode($model->description);
    echo "</".CHtml::encode($model->getAttributeLabel('description')).">";  
    
    echo "<Images>";
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('location'))).">"; 
    echo CHtml::encode($model->image->location);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('location'))).">"; 
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('photographer'))).">"; 
    echo CHtml::encode($model->image->photographer);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('photographer'))).">";
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('url'))).">"; 
    echo CHtml::encode($model->image->url);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('url'))).">";
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('tag'))).">"; 
    echo CHtml::encode($model->image->tag);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('tag'))).">";
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('license'))).">"; 
    echo CHtml::encode($model->image->license);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('license'))).">";
    echo "<".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('source'))).">"; 
    echo CHtml::encode($model->image->source);
    echo "</".str_replace(" ", "", CHtml::encode($model->image->getAttributeLabel('source'))).">";
    echo "</Images>";
    
   
    
    echo "<Projects TotalProjects='".count($model->projects)."'>";
    foreach ($model->projects as $project) {
        echo "<Project>";
        echo "<".str_replace(" ", "", CHtml::encode($project->getAttributeLabel('name'))).">";
        echo CHtml::encode($project->name);
        echo "</".str_replace(" ", "", CHtml::encode($project->getAttributeLabel('name'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($project->getAttributeLabel('url'))).">";
        echo CHtml::encode($project->url);
        echo "</".str_replace(" ", "", CHtml::encode($project->getAttributeLabel('url'))).">"; 
        echo "</Project>";
    }
    echo "</Projects>";

    echo "<Manuscripts TotalManuscripts='".count($model->manuscripts)."'>";
    foreach ($model->manuscripts as $manuscript) {
        echo "<Manuscript>";
        echo "<".str_replace(" ", "", CHtml::encode($manuscript->getAttributeLabel('identifier'))).">";
        echo CHtml::encode($manuscript->identifier);
        echo "</".str_replace(" ", "", CHtml::encode($manuscript->getAttributeLabel('identifier'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($manuscript->getAttributeLabel('pmid'))).">";
        echo CHtml::encode($manuscript->pmid);
        echo "</".str_replace(" ", "", CHtml::encode($manuscript->getAttributeLabel('pmid'))).">"; 
        echo "</Manuscript>";
    }
    echo "</Manuscripts>";

  
    
    echo "<Files TotalFiles='".count($model->files)."'>";
    foreach ($model->files as $file) {
        echo "<File>";
        echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('name'))).">";
        echo CHtml::encode($file->name);
        echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('name'))).">"; 
        echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('location'))).">";
        echo CHtml::encode($file->location);
        echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('location'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('extension'))).">";
        echo CHtml::encode($file->extension);
        echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('extension'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('size'))).">";
        echo CHtml::encode(File::staticBytesToSize($file->size));
        echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('size'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('description'))).">";
        echo CHtml::encode($file->description);
        echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('description'))).">";         
        echo "</File>";
    }
    echo "</Files>";    
    
    echo "<Publisher>";    
    echo "<".str_replace(" ", "",CHtml::encode($model->publisher->getAttributeLabel('name'))).">"; 
    echo CHtml::encode($model->publisher->name);
    echo "</".str_replace(" ", "",CHtml::encode($model->publisher->getAttributeLabel('name'))).">";      
    echo "<".str_replace(" ", "",CHtml::encode($model->publisher->getAttributeLabel('description'))).">"; 
    echo CHtml::encode($model->publisher->description);
    echo "</".str_replace(" ", "",CHtml::encode($model->publisher->getAttributeLabel('description'))).">"; 
    echo "</Publisher>"; 
        
    echo "<".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('publication_date'))).">"; 
    echo CHtml::encode($model->publication_date);
    echo "</".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('publication_date'))).">"; 
?>
<?php echo "</Dataset>"; ?>
 * */
 
