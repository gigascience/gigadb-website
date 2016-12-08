<?php
header("Content-Type: text/xml");
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<datasets>";
foreach($models as $model){
 $xml.="<doi prefix=\"10.5524\">".$model->identifier."</doi>";  
    
}
$xml.="</datasets>";

$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();