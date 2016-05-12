<?php if($type =='json'){
header('Content-Type: application/json');  
}else{
header("Content-Type: text/xml");
} ?>
<?php 
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entry id=\"$model->id\" doi=\"$model->identifier\">";

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
