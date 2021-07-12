
<h1>View File #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Files', array('admin')) ?>]
</div>
<? } ?>

<?php
$sample_id = FileSample::model()->find('file_id=:file_id', array(':file_id'=>$model->id));
$fileAttributes = FileAttributes::model()->findAll('file_id=:file_id', array(':file_id'=>$model->id));

if(isset($sample_id))
{
    $sample_name= Sample::model()->find('id=:id',array(':id'=>$sample_id->sample_id));
}

$name="Not Set";
 
if(isset($sample_id)&&isset($sample_name))
{
    $name=$sample_name->name;
}

$attributes = array(
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
    )
);

if(!empty($fileAttributes))
{
    foreach ($fileAttributes as $fileAttribute)
    {
        array_push($attributes, array('name' => 'FileAttribute', 'value' => $fileAttribute->value));
    }
}

$this->widget('zii.widgets.CDetailView', array(
        'data'=>$model,
        'attributes'=> $attributes
));
