<?php
header('Content-Type: text/xml');
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<gigadb_entrys>";
if(!empty($sampleids)){
foreach($sampleids as $sampleid)
{
$sample=  Sample::model()->findByPK($sampleid);
$datasetid;
foreach($sample->datasets as $dataset)
{
    $datasetid=$dataset->identifier;
}
    $xml.="<gigadb_entry>";
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
    $xml.="</gigadb_entry>";
    }
}

$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();