<?php
header("Content-Type: text/xml");
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
foreach($models as $model)
{ 

$xml.="<gigadb_entry id=\"$model->id\" doi=\"$model->identifier\">";

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
    $xml.="<key id=\"$file_att->id\">$file_att->attribute_name</key>";
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
}
$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();
