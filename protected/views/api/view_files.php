<?php header("Content-type: text/xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php 
        echo "<Files TotalFiles='".count($model)."'>";
        foreach ($model as $file) {
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
            echo CHtml::encode($file->size);
            echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('size'))).">";         
            echo "<".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('description'))).">";
            echo CHtml::encode($file->description);
            echo "</".str_replace(" ", "", CHtml::encode($file->getAttributeLabel('description'))).">";         
            echo "</File>";
        }
        echo "</Files>";    
?>