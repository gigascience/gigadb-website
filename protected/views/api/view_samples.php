<?php header("Content-type: text/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php        
    echo "<Samples TotalSamples='".count($models)."'>";
    foreach ($models as $model){
        echo "<Sample>";
        echo "<".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('species_id'))).">";
        echo CHtml::encode($model->species_id);
        echo "</".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('species_id'))).">";        
        echo "<".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('code'))).">";
        echo CHtml::encode($model->code);
        echo "</".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('code'))).">";         
        echo "<".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('s_attrs'))).">";
        echo CHtml::encode($model->s_attrs);
        echo "</".str_replace(" ", "", CHtml::encode($model->getAttributeLabel('s_attrs'))).">"; 
        echo "<".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('common_name'))).">";
        echo CHtml::encode($model->species->common_name);
        echo "</".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('common_name'))).">"; 
        echo "<".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('genbank_name'))).">";
        echo CHtml::encode($model->species->genbank_name);
        echo "</".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('genbank_name'))).">"; 
        echo "<".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('scientific_name'))).">";
        echo CHtml::encode($model->species->scientific_name);
        echo "</".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('scientific_name'))).">"; 
        echo "<".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('tax_id'))).">";
        echo CHtml::encode($model->species->tax_id);
        echo "</".str_replace(" ", "", CHtml::encode($model->species->getAttributeLabel('tax_id'))).">";         
        echo "</Sample>";   
    }
    echo "</Samples>";
?>