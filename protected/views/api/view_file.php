<?php if($type =='json'){
header('Content-Type: application/json');  
}else{
header("Content-Type: text/xml");
} ?>
<?php 
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entry id=\"$model->id\" doi=\"$model->identifier\">";



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

$filename=dirname(Yii::app()->getBasePath())."/files/api/".$model->identifier;
if(!file_exists($filename))
{
    $file=fopen($filename,'w');
    fwrite($file, $xml);
}


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
 

