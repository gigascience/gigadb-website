
<h1>View File #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Files', array('admin')) ?>]
</div>
<? } ?>

<?php
$sample_id = FileSample::model()->find('file_id=:file_id', array(':file_id'=>$model->id));
 if(isset($sample_id))
 {
 $sample_name= Sample::model()->find('id=:id',array(':id'=>$sample_id->sample_id));
 $attribute = FileAttributes::model()->find('file_id=:file_id', array(':file_id'=>$sample_id->file_id));
 }
 
 $name="Not Set";
 $attribute_id = "Not Set";
 
 if(isset($sample_id)&&isset($sample_name)&&isset($attribute))
 {
     $name=$sample_name->name;
     $attribute_id = $attribute->id;
 }
 $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		'name',
		'location',
		'extension',
		'size',
		'description',
		'date_stamp',
		'format_id',
		'type_id',
		  array(
                    'name'=>'Sample',
                    'value'=> $name,
                    
                ),
          array(
                  'name'=>'FileAttribute ID',
                  'value'=>$attribute_id,
          )
	),
)); ?>
